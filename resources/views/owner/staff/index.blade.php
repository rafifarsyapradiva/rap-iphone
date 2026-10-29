@extends('layouts.admin')

@section('title', 'Manajemen Staf & Admin Toko — Owner Rap-iPhone')
@section('page_title', 'Manajemen Akun Staf & Admin')

@section('content')
<div class="adm-page">

    <!-- 1. Page Header -->
    <x-admin.page-header
        title="Manajemen Akun Staf &amp; Admin Toko"
        subtitle="Kelola akun karyawan operasional (admin) dan pemilik usaha (owner), atur hak akses panel inventaris/pesanan, serta perbarui kata sandi keamanan staf."
        eyebrow="Kontrol Akses Internal Toko (Owner Exclusive)"
        icon="shield-check">
        <x-slot:actions>
            <a href="{{ route('owner.dashboard') }}" class="adm-btn adm-btn-secondary">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Portal Finansial</span>
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    @if($errors->any())
        <div class="p-4 rounded text-xs space-y-1" style="background:var(--adm-paper);border:1px solid var(--adm-rose);color:var(--adm-rose)">
            <p class="font-bold flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                <span>Terdapat kesalahan pada input formulir:</span>
            </p>
            <ul class="list-disc list-inside pl-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
            color="green"
        />

        <x-admin.stat-card
            label="Pelanggan Terdaftar"
            :value="($counts['customers'] ?? 0) . ' User'"
            icon="user-check"
            meta="Role: customer"
            color="gray"
        />
    </div>

    <!-- 3. Main Content: Form Tambah Staf (Left) + Daftar Staf (Right) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Left Column: Form Tambah Staf / Admin Baru -->
        <x-admin.card title="Tambah Akun Staf / Admin Baru" subtitle="Registrasi Hak Akses Karyawan Baru" class="h-fit">
            <form action="{{ route('owner.staff.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label for="name" class="adm-label">Nama Lengkap Staf <span style="color:var(--adm-rose)">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Raka Pratama (Admin Store)"
                           class="adm-input">
                </div>

                <div>
                    <label for="email" class="adm-label">Alamat Email Resmi <span style="color:var(--adm-rose)">*</span></label>
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
                        <label for="role" class="adm-label">Peran Hak Akses <span style="color:var(--adm-rose)">*</span></label>
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
                    <label for="password" class="adm-label">Kata Sandi Awal (Min. 8 Karakter) <span style="color:var(--adm-rose)">*</span></label>
                    <input type="password" id="password" name="password" required minlength="8"
                           placeholder="Minimal 8 karakter aman"
                           class="adm-input">
                </div>

                <button type="submit" class="adm-btn adm-btn-primary w-full justify-center py-2.5 mt-2">
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
                            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2" style="color:var(--adm-muted)"></i>
                            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / email staf..."
                                   class="adm-input !py-1 !pl-8 !pr-3 text-xs w-44 sm:w-56">
                        </div>
                        <button type="submit" class="adm-btn adm-btn-secondary" style="padding:.25rem .625rem;font-size:.75rem">Cari</button>
                    </form>
                </x-slot:actions>

                <!-- Filter Tabs -->
                <div class="flex flex-wrap items-center gap-1.5 pb-4 -mt-1 mb-4" style="border-bottom:1px solid var(--adm-hairline)">
                    @php
                        $tabs = [
                            'all' => 'Semua Staf (' . ($counts['total_staff'] ?? 0) . ')',
                            'admin' => 'Admin Operasional (' . ($counts['admins'] ?? 0) . ')',
                            'owner' => 'Owner (' . ($counts['owners'] ?? 0) . ')',
                        ];
                    @endphp
                    @foreach($tabs as $k => $lbl)
                        <a href="{{ route('owner.staff.index', ['role' => $k, 'q' => $search]) }}"
                           class="px-3 py-1.5 rounded text-xs font-medium transition-colors"
                           style="{{ $roleFilter === $k ? 'background:var(--adm-ink);color:var(--adm-surface);' : 'background:var(--adm-paper);color:var(--adm-muted);border:1px solid var(--adm-hairline);' }}">
                            {{ $lbl }}
                        </a>
                    @endforeach
                </div>

                <!-- Staff List -->
                <div class="space-y-3">
                    @forelse($staffMembers as $staff)
                        <div class="p-4 rounded space-y-3" style="background:var(--adm-surface);border:1px solid var(--adm-hairline)">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded flex items-center justify-center font-bold text-sm shrink-0"
                                         style="{{ $staff->role === 'owner' ? 'background:var(--adm-paper);color:var(--adm-ochre);border:1px solid var(--adm-hairline)' : 'background:var(--adm-paper);color:var(--adm-accent);border:1px solid var(--adm-hairline)' }}">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-medium text-sm truncate" style="color:var(--adm-ink)">{{ $staff->name }}</span>
                                            @if($staff->role === 'owner')
                                                <x-admin.status-badge tone="amber" label="Owner" :dot="false" />
                                            @else
                                                <x-admin.status-badge tone="blue" label="Admin Operasional" :dot="false" />
                                            @endif
                                            @if($staff->id === Auth::id())
                                                <x-admin.status-badge tone="green" label="Akun Anda" :dot="false" />
                                            @endif
                                        </div>
                                        <p class="text-xs mt-0.5 truncate" style="color:var(--adm-muted)">
                                            {{ $staff->email }} &bull; WA: {{ $staff->phone_number ?: '-' }} &bull; {{ $staff->city ?: 'Pusat' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button"
                                            onclick="document.getElementById('edit-staff-{{ $staff->id }}').classList.toggle('hidden')"
                                            class="adm-btn adm-btn-secondary" style="padding:.25rem .625rem;font-size:.75rem">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        <span>Edit / Password</span>
                                    </button>

                                    @if($staff->id !== Auth::id())
                                        <form action="{{ route('owner.staff.destroy', $staff) }}" method="POST"
                                              onsubmit="return confirm('Hapus akun staf {{ addslashes($staff->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="adm-btn adm-btn-danger" style="padding:.25rem .625rem;font-size:.75rem">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Collapsible Edit Form -->
                            <div id="edit-staff-{{ $staff->id }}" class="hidden pt-3" style="border-top:1px solid var(--adm-hairline)">
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
                                        <button type="submit" class="adm-btn adm-btn-primary" style="padding:.35rem .75rem;font-size:.75rem">
                                            Simpan Perubahan Staf
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center" style="color:var(--adm-muted)">
                            <p class="text-sm">Tidak Ditemukan Akun Staf</p>
                            <p class="text-xs mt-1">Tidak ditemukan akun staf yang sesuai kriteria pencarian Anda.</p>
                        </div>
                    @endforelse
                </div>

                @if($staffMembers->hasPages())
                    <div class="pt-4" style="border-top:1px solid var(--adm-hairline)">
                        {{ $staffMembers->links() }}
                    </div>
                @endif
            </x-admin.card>
        </div>

    </div>

</div>
@endsection
