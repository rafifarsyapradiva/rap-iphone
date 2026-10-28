@extends('layouts.admin')

@section('title', 'Manajemen Staf & Admin Toko — Owner Rap-iPhone')
@section('page_title', 'Manajemen Akun Staf & Admin')

@section('content')
<div class="space-y-8">

    <!-- 1. Top Header Banner -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 glass-card-elevated border border-white/15">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-[#5AC8FA]/20 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0071E3]/20 border border-[#0071E3]/40 text-[#5AC8FA] text-xs font-semibold mb-3">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    <span>Kontrol Akses Internal Toko (Owner Exclusive)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Manajemen Akun Staf &amp; Admin Toko
                </h1>
                <p class="text-xs sm:text-sm text-[#8E8E93] mt-1 max-w-2xl">
                    Kelola akun karyawan operasional (<code class="text-[#5AC8FA]">admin</code>) dan pemilik usaha (<code class="text-[#FF9F0A]">owner</code>), atur hak akses panel inventaris/pesanan, serta perbarui kata sandi keamanan staf.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('owner.dashboard') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Portal Finansial</span>
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-[#FF453A]/15 border border-[#FF453A]/35 text-xs text-white space-y-1">
            <p class="font-bold text-[#FF453A] flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                <span>Terdapat kesalahan pada input formulir:</span>
            </p>
            <ul class="list-disc list-inside text-[#E5E5EA] pl-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="glass-card rounded-2xl p-5 border border-white/10">
            <span class="text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider block">Total Tim Internal</span>
            <p class="text-2xl font-extrabold text-white mt-2">
                {{ $counts['total_staff'] ?? 0 }} <span class="text-xs font-normal text-[#8E8E93]">Staf Aktif</span>
            </p>
            <span class="text-[11px] text-[#34C759] mt-2 inline-block">Akses Panel Manajemen</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/10">
            <span class="text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider block">Pemilik Usaha (Owner)</span>
            <p class="text-2xl font-extrabold text-[#FF9F0A] mt-2">
                {{ $counts['owners'] ?? 1 }} <span class="text-xs font-normal text-[#8E8E93]">Akun</span>
            </p>
            <span class="text-[11px] text-[#8E8E93] mt-2 inline-block">Hak Akses Finansial Penuh</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/10">
            <span class="text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider block">Admin Operasional</span>
            <p class="text-2xl font-extrabold text-[#5AC8FA] mt-2">
                {{ $counts['admins'] ?? 0 }} <span class="text-xs font-normal text-[#8E8E93]">Akun</span>
            </p>
            <span class="text-[11px] text-[#8E8E93] mt-2 inline-block">Katalog, Pesanan &amp; Trade-In</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/10">
            <span class="text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider block">Pelanggan Toko</span>
            <p class="text-2xl font-extrabold text-white mt-2">
                {{ $counts['customers'] ?? 0 }} <span class="text-xs font-normal text-[#8E8E93]">Pelanggan</span>
            </p>
            <span class="text-[11px] text-[#8E8E93] mt-2 inline-block">Role: customer</span>
        </div>
    </div>

    <!-- 3. Main Content Grid: Form Tambah Staf (Left) + Daftar Staf & Admin (Right) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Left Column: Form Tambah Staf / Admin Baru -->
        <div class="glass-card rounded-3xl p-6 border border-white/10 space-y-5 h-fit">
            <div class="border-b border-white/10 pb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#0071E3] block">Registrasi Internal</span>
                <h2 class="text-base font-bold text-white flex items-center gap-2 mt-0.5">
                    <i data-lucide="user-plus" class="w-4 h-4 text-[#5AC8FA]"></i>
                    <span>Tambah Akun Staf / Admin Baru</span>
                </h2>
            </div>

            <form action="{{ route('owner.staff.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label for="name" class="block text-[#E5E5EA] font-semibold mb-1.5">Nama Lengkap Staf <span class="text-[#FF453A]">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Raka Pratama (Admin Store)"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/15 text-white placeholder-[#8E8E93]/60 focus:outline-none focus:border-[#0071E3]">
                </div>

                <div>
                    <label for="email" class="block text-[#E5E5EA] font-semibold mb-1.5">Alamat Email Resmi <span class="text-[#FF453A]">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="staf@rap-iphone.com"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/15 text-white placeholder-[#8E8E93]/60 focus:outline-none focus:border-[#0071E3]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="phone_number" class="block text-[#E5E5EA] font-semibold mb-1.5">Nomor WhatsApp</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                               placeholder="0812xxxxxxxx"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/15 text-white placeholder-[#8E8E93]/60 focus:outline-none focus:border-[#0071E3]">
                    </div>
                    <div>
                        <label for="role" class="block text-[#E5E5EA] font-semibold mb-1.5">Peran Hak Akses <span class="text-[#FF453A]">*</span></label>
                        <select id="role" name="role" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-[#161B22] border border-white/15 text-white focus:outline-none focus:border-[#0071E3]">
                            <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                            <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Pemilik Usaha (Owner)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="city" class="block text-[#E5E5EA] font-semibold mb-1.5">Cabang / Kota Penempatan</label>
                    <input type="text" id="city" name="city" value="{{ old('city', 'Jakarta Selatan') }}"
                           placeholder="Jakarta Selatan"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/15 text-white placeholder-[#8E8E93]/60 focus:outline-none focus:border-[#0071E3]">
                </div>

                <div>
                    <label for="password" class="block text-[#E5E5EA] font-semibold mb-1.5">Kata Sandi Awal (Min. 8 Karakter) <span class="text-[#FF453A]">*</span></label>
                    <input type="password" id="password" name="password" required minlength="8"
                           placeholder="Minimal 8 karakter aman"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/15 text-white placeholder-[#8E8E93]/60 focus:outline-none focus:border-[#0071E3]">
                </div>

                <button type="submit" class="glass-btn glass-btn-primary w-full flex items-center justify-center gap-2 py-2.5">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span>Simpan &amp; Aktifkan Akun Staf</span>
                </button>
            </form>
        </div>

        <!-- Right 2 Columns: Daftar Akun Staf & Admin -->
        <div class="xl:col-span-2 glass-card rounded-3xl p-6 border border-white/10 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
                <div class="flex items-center gap-2">
                    @php
                        $tabs = [
                            'all' => 'Semua Staf (' . ($counts['total_staff'] ?? 0) . ')',
                            'admin' => 'Admin Operasional (' . ($counts['admins'] ?? 0) . ')',
                            'owner' => 'Owner (' . ($counts['owners'] ?? 0) . ')',
                        ];
                    @endphp
                    @foreach($tabs as $k => $lbl)
                        <a href="{{ route('owner.staff.index', ['role' => $k, 'q' => $search]) }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $roleFilter === $k ? 'bg-[#0071E3] text-white' : 'bg-white/5 text-[#8E8E93] hover:text-white border border-white/10' }}">
                            {{ $lbl }}
                        </a>
                    @endforeach
                </div>

                <form action="{{ route('owner.staff.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="role" value="{{ $roleFilter }}">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama / email staf..."
                           class="px-3 py-1.5 rounded-xl bg-white/5 border border-white/15 text-xs text-white placeholder-[#8E8E93] focus:outline-none focus:border-[#0071E3]">
                    <button type="submit" class="glass-btn glass-btn-secondary glass-btn-sm">Cari</button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse($staffMembers as $staff)
                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 hover:border-white/20 transition-all space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-2xl {{ $staff->role === 'owner' ? 'bg-gradient-to-tr from-[#FF9F0A] to-[#FF453A]' : 'bg-gradient-to-tr from-[#0071E3] to-[#34C759]' }} flex items-center justify-center text-white font-extrabold text-sm shrink-0">
                                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-white text-sm">{{ $staff->name }}</span>
                                        @if($staff->role === 'owner')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-[#FF9F0A]/20 text-[#FF9F0A] border border-[#FF9F0A]/30">
                                                Owner
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-[#0071E3]/20 text-[#5AC8FA] border border-[#0071E3]/30">
                                                Admin Operasional
                                            </span>
                                        @endif
                                        @if($staff->id === Auth::id())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#34C759]/20 text-[#34C759]">
                                                Akun Anda
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#8E8E93] mt-0.5">
                                        {{ $staff->email }} &bull; WA: {{ $staff->phone_number ?: '-' }} &bull; {{ $staff->city ?: 'Pusat' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button"
                                        onclick="document.getElementById('edit-staff-{{ $staff->id }}').classList.toggle('hidden')"
                                        class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-1.5 text-xs">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-[#5AC8FA]"></i>
                                    <span>Edit / Password</span>
                                </button>

                                @if($staff->id !== Auth::id())
                                    <form action="{{ route('owner.staff.destroy', $staff) }}" method="POST"
                                          onsubmit="return confirm('Hapus akun staf {{ addslashes($staff->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-[#FF453A]/15 hover:bg-[#FF453A]/30 border border-[#FF453A]/30 text-[#FF453A] text-xs font-semibold transition-colors flex items-center gap-1">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Collapsible Edit Form -->
                        <div id="edit-staff-{{ $staff->id }}" class="hidden pt-3 border-t border-white/10">
                            <form action="{{ route('owner.staff.update', $staff) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="block text-[#8E8E93] mb-1">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ $staff->name }}" required
                                           class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/15 text-white">
                                </div>

                                <div>
                                    <label class="block text-[#8E8E93] mb-1">Email</label>
                                    <input type="email" name="email" value="{{ $staff->email }}" required
                                           class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/15 text-white">
                                </div>

                                <div>
                                    <label class="block text-[#8E8E93] mb-1">No. WhatsApp</label>
                                    <input type="text" name="phone_number" value="{{ $staff->phone_number }}"
                                           class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/15 text-white">
                                </div>

                                <div>
                                    <label class="block text-[#8E8E93] mb-1">Role Hak Akses</label>
                                    <select name="role" class="w-full px-3 py-2 rounded-xl bg-[#161B22] border border-white/15 text-white">
                                        <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                                        <option value="owner" {{ $staff->role === 'owner' ? 'selected' : '' }}>Owner</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[#8E8E93] mb-1">Kota / Cabang</label>
                                    <input type="text" name="city" value="{{ $staff->city }}"
                                           class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/15 text-white">
                                </div>

                                <div>
                                    <label class="block text-[#8E8E93] mb-1">Password Baru (Kosongkan jika tetap)</label>
                                    <input type="password" name="password" minlength="8" placeholder="Min. 8 karakter"
                                           class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/15 text-white">
                                </div>

                                <div class="sm:col-span-2 lg:col-span-3 flex justify-end gap-2 pt-1">
                                    <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm">
                                        Simpan Perubahan Staf
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-[#8E8E93]">
                        Tidak ditemukan akun staf yang sesuai kriteria pencarian.
                    </div>
                @endforelse
            </div>

            @if($staffMembers->hasPages())
                <div class="pt-4 border-t border-white/10">
                    {{ $staffMembers->links() }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

