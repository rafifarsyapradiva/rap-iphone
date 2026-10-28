<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffManagementController extends Controller
{
    /**
     * Tampilkan daftar akun staf (Admin Operasional & Owner) serta ringkasan pengguna toko.
     */
    public function index(Request $request): View
    {
        $roleFilter = (string) $request->query('role', 'all');
        $search = trim((string) $request->query('q', ''));

        $query = User::query()
            ->whereIn('role', ['admin', 'owner'])
            ->orderByRaw("CASE WHEN role = 'owner' THEN 0 ELSE 1 END")
            ->latest('created_at');

        if (in_array($roleFilter, ['admin', 'owner'], true)) {
            $query->where('role', $roleFilter);
        } else {
            $roleFilter = 'all';
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $staffMembers = $query->paginate(15)->withQueryString();

        $counts = [
            'total_staff' => User::whereIn('role', ['admin', 'owner'])->count(),
            'owners' => User::where('role', 'owner')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'customers' => User::where('role', 'customer')->count(),
        ];

        return view('owner.staff.index', compact(
            'staffMembers',
            'counts',
            'roleFilter',
            'search'
        ));
    }

    /**
     * Tambah akun Staf Admin Toko atau Co-Owner baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'owner'])],
            'password' => ['required', 'string', 'min:8'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama lengkap staf wajib diisi.',
            'email.required' => 'Alamat email resmi staf wajib diisi.',
            'email.unique' => 'Alamat email tersebut sudah terdaftar di sistem.',
            'role.in' => 'Peran staf harus berupa Admin Operasional atau Owner.',
            'password.required' => 'Kata sandi awal akun wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
        ]);

        if ($request->filled('password_confirmation') && $request->input('password') !== $request->input('password_confirmation')) {
            return back()
                ->withErrors(['password' => 'Konfirmasi kata sandi tidak cocok.'])
                ->withInput();
        }

        $staff = User::create([
            'name' => trim($validated['name']),
            'email' => Str::lower(trim($validated['email'])),
            'phone_number' => $validated['phone_number'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        $roleLabel = $staff->role === 'owner' ? 'Pemilik Usaha (Owner)' : 'Admin Operasional';

        return redirect()
            ->route('owner.staff.index')
            ->with('success', "Akun staf baru \"{$staff->name}\" ({$roleLabel}) berhasil ditambahkan.");
    }

    /**
     * Perbarui profil, peran (role), atau kata sandi akun staf/admin.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'owner'])],
            'password' => ['nullable', 'string', 'min:8'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama lengkap staf wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email tersebut sudah digunakan oleh akun lain.',
            'password.min' => 'Kata sandi baru minimal terdiri dari 8 karakter.',
        ]);

        // Cegah Owner menurunkan role akunnya sendiri yang sedang aktif login
        if ($user->id === Auth::id() && $validated['role'] !== 'owner') {
            return back()->with('error', 'Anda tidak dapat menurunkan tingkat akses (role) akun Owner Anda sendiri yang sedang aktif.');
        }

        $updateData = [
            'name' => trim($validated['name']),
            'email' => Str::lower(trim($validated['email'])),
            'phone_number' => $validated['phone_number'] ?? null,
            'role' => $validated['role'],
            'city' => $validated['city'] ?? $user->city,
            'address' => $validated['address'] ?? $user->address,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()
            ->route('owner.staff.index')
            ->with('success', "Data akun staf \"{$user->name}\" berhasil diperbarui.");
    }

    /**
     * Hapus akun staf/admin dengan pengaman agar tidak menghapus akun sendiri atau Owner terakhir.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tindakan ditolak: Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        if ($user->isOwner() && User::where('role', 'owner')->count() <= 1) {
            return back()->with('error', 'Tindakan ditolak: Sistem harus memiliki minimal satu akun Pemilik Usaha (Owner).');
        }

        if (!in_array($user->role, ['admin', 'owner'], true)) {
            return back()->with('error', 'Hanya akun staf dan admin yang dapat dihapus melalui panel ini.');
        }

        $deletedName = $user->name;
        $user->delete();

        return redirect()
            ->route('owner.staff.index')
            ->with('success', "Akun staf \"{$deletedName}\" telah berhasil dihapus dari sistem.");
    }
}

