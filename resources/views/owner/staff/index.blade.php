@extends('layouts.admin')

@section('title', 'Manajemen Staf & Admin Toko — Owner Rap-iPhone')
@section('page_title', 'Manajemen Akun Staf & Admin')

@section('content')
<div class="space-y-6">

    <!-- 1. Page Header -->
    <x-admin.page-header
        title="Manajemen Akun Staf &amp; Admin Toko"
        subtitle="Kelola akun karyawan operasional (admin) dan pemilik usaha (owner), atur hak akses panel inventaris/pesanan, serta perbarui kata sandi keamanan staf."
        eyebrow="Kontrol Akses Internal Toko (Owner Exclusive)"
        icon="shield-check"
    >
        <x-slot:actions>
            <a href="{{ route('owner.dashboard') }}" class="adm-btn adm-btn-secondary adm-btn-sm inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
                <span>Kembali ke Portal Finansial</span>
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
            <p class="font-bold flex items-center gap-2 text-rose-900">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                <span>Terdapat kesalahan pada input formulir:</span>
            </p>
            <ul class="list-disc list-inside text-rose-700 pl-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <x-admin.stat-card
            label="Total Tim Internal"
            :value="($counts['total_staff'] ?? 0) . ' Staf'"
            icon="users"
            meta="Akses Panel Manajemen"
            color="blue"
        />

        <x-admin.stat-card
            label="Pemilik Usaha (Owner)"
            :value="($counts['owners'] ?? 1) . ' Akun'"
            icon="crown"
            meta="Hak Akses Finansial Penuh"
            color="amber"
        />

        <x-admin.stat-card
            label="Admin Operasional"
            :value="($counts['admins'] ?? 0) . ' Akun'"
            icon="shield"
            meta="Katalog, Pesanan & Trade-In"
            color="emerald"
        />

        <x-admin.stat-card
            label="Pelanggan Terdaftar"
            :value="($counts['customers'] ?? 0) . ' User'"
            icon="user-check"
            meta="Role: customer"
            color="slate"
        />
    </div>

    <!-- 3. Main Content: Form Tambah Staf (Left) + Daftar Staf (Right) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Left Column: Form Tambah Staf / Admin Baru -->
        <x-admin.card title="Tambah Akun Staf / Admin Baru" subtitle="Registrasi Hak Akses Karyawan Baru" class="h-fit">
            <form action="{{ route('owner.staff.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label for="name" class="adm-label">Nama Lengkap Staf <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Raka Pratama (Admin Store)"
                           class="adm-input">
                </div>

                <div>
                    <label for="email" class="adm-label">Alamat Email Resmi <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="staf@rap-iphone.com"
                           class="adm-input">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="phone_number" class="adm-label">Nomor WhatsApp</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                               placeholder="0812xxxxxxxx"
                               class="adm-input">
                    </div>
                    <div>
                        <label for="role" class="adm-label">Peran Hak Akses <span class="text-rose-500">*</span></label>
                        <select id="role" name="role" required class="adm-select">
                            <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                            <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Pemilik Usaha (Owner)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="city" class="adm-label">Cabang / Kota Penempatan</label>
                    <input type="text" id="city" name="city" value="{{ old('city', 'Jakarta Selatan') }}"
                           placeholder="Jakarta Selatan"
                           class="adm-input">
                </div>

                <div>
                    <label for="password" class="adm-label">Kata Sandi Awal (Min. 8 Karakter) <span class="text-rose-500">*</span></label>
                    <input type="password" id="password" name="password" required minlength="8"
                           placeholder="Minimal 8 karakter aman"
                           class="adm-input">
                </div>

                <button type="submit" class="adm-btn adm-btn-primary w-full inline-flex items-center justify-center gap-2 py-2.5 mt-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span>Simpan &amp; Aktifkan Akun Staf</span>
                </button>
            </form>
        </x-admin.card>

        <!-- Right 2 Columns: Daftar Akun Staf & Admin -->
        <div class="xl:col-span-2">
            <x-admin.card title="Daftar Akun Tim Internal" subtitle="Kelola dan verifikasi akun staf aktif">
                <x-slot:actions>
                    <form action="{{ route('owner.staff.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="role" value="{{ $roleFilter }}">
                        <div class="relative">
                            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / email staf..."
                                   class="adm-input !py-1.5 !pl-8 !pr-3 text-xs w-44 sm:w-56">
                        </div>
                        <button type="submit" class="adm-btn adm-btn-secondary adm-btn-sm">Cari</button>
                    </form>
                </x-slot:actions>

                <!-- Filter Tabs -->
                <div class="flex flex-wrap items-center gap-1.5 pb-4 border-b border-slate-100 -mt-1 mb-4">
                    @php
                        $tabs = [
                            'all' => 'Semua Staf (' . ($counts['total_staff'] ?? 0) . ')',
                            'admin' => 'Admin Operasional (' . ($counts['admins'] ?? 0) . ')',
                            'owner' => 'Owner (' . ($counts['owners'] ?? 0) . ')',
                        ];
                    @endphp
                    @foreach($tabs as $k => $lbl)
                        <a href="{{ route('owner.staff.index', ['role' => $k, 'q' => $search]) }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $roleFilter === $k ? 'bg-[#0071E3] text-white shadow-sm shadow-[#0071E3]/25' : 'bg-slate-100 text-slate-600 hover:text-slate-900 hover:bg-slate-200/80' }}">
                            {{ $lbl }}
                        </a>
                    @endforeach
                </div>

                <!-- Staff List -->
                <div class="space-y-3">
                    @forelse($staffMembers as $staff)
                        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-slate-300 transition-all space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-11 h-11 rounded-2xl {{ $staff->role === 'owner' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-[#0066CC] border border-blue-200' }} flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-bold text-slate-900 text-sm truncate">{{ $staff->name }}</span>
                                            @if($staff->role === 'owner')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                                    Owner
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                                    Admin Operasional
                                                </span>
                                            @endif
                                            @if($staff->id === Auth::id())
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Akun Anda
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5 truncate">
                                            {{ $staff->email }} &bull; WA: {{ $staff->phone_number ?: '-' }} &bull; {{ $staff->city ?: 'Pusat' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button"
                                            onclick="document.getElementById('edit-staff-{{ $staff->id }}').classList.toggle('hidden')"
                                            class="adm-btn adm-btn-secondary adm-btn-sm inline-flex items-center gap-1.5 text-xs">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-slate-500"></i>
                                        <span>Edit / Password</span>
                                    </button>

                                    @if($staff->id !== Auth::id())
                                        <form action="{{ route('owner.staff.destroy', $staff) }}" method="POST"
                                              onsubmit="return confirm('Hapus akun staf {{ addslashes($staff->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm inline-flex items-center gap-1">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Collapsible Edit Form -->
                            <div id="edit-staff-{{ $staff->id }}" class="hidden pt-3 border-t border-slate-100">
                                <form action="{{ route('owner.staff.update', $staff) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                                    @csrf
                                    @method('PUT')

                                    <div>
                                        <label class="adm-label">Nama Lengkap</label>
                                        <input type="text" name="name" value="{{ $staff->name }}" required
                                               class="adm-input">
                                    </div>

                                    <div>
                                        <label class="adm-label">Email</label>
                                        <input type="email" name="email" value="{{ $staff->email }}" required
                                               class="adm-input">
                                    </div>

                                    <div>
                                        <label class="adm-label">No. WhatsApp</label>
                                        <input type="text" name="phone_number" value="{{ $staff->phone_number }}"
                                               class="adm-input">
                                    </div>

                                    <div>
                                        <label class="adm-label">Role Hak Akses</label>
                                        <select name="role" class="adm-select">
                                            <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                                            <option value="owner" {{ $staff->role === 'owner' ? 'selected' : '' }}>Owner</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="adm-label">Kota / Cabang</label>
                                        <input type="text" name="city" value="{{ $staff->city }}"
                                               class="adm-input">
                                    </div>

                                    <div>
                                        <label class="adm-label">Password Baru (Kosongkan jika tetap)</label>
                                        <input type="password" name="password" minlength="8" placeholder="Min. 8 karakter"
                                               class="adm-input">
                                    </div>

                                    <div class="sm:col-span-2 lg:col-span-3 flex justify-end gap-2 pt-1">
                                        <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">
                                            Simpan Perubahan Staf
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <x-admin.empty-state
                            icon="users"
                            title="Tidak Ditemukan Akun Staf"
                            description="Tidak ditemukan akun staf yang sesuai kriteria pencarian Anda."
                        />
                    @endforelse
                </div>

                @if($staffMembers->hasPages())
                    <div class="pt-4 border-t border-slate-100">
                        {{ $staffMembers->links() }}
                    </div>
                @endif
            </x-admin.card>
        </div>

    </div>

</div>
@endsection
