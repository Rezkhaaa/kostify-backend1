@extends('layouts.admin')
@section('title', auth()->user()->isSuperAdmin() ? 'Profil Super Admin' : 'Profil Admin Kos')
@section('subtitle', auth()->user()->isSuperAdmin() ? 'Akun pusat untuk mengelola platform Kostify' : 'Akun pengelola operasional kos')

@section('content')
@php($isSuper = $adminUser->isSuperAdmin())
<div class="grid xl:grid-cols-3 gap-5 max-w-7xl">
    <div class="bg-gradient-to-br {{ $isSuper ? 'from-blue-800 to-slate-950' : 'from-teal-700 to-blue-900' }} rounded-3xl p-6 text-white shadow-lg xl:col-span-1">
        <div class="w-20 h-20 rounded-3xl bg-white/15 border border-white/20 grid place-items-center text-2xl font-black mb-4">
            {{ strtoupper(substr($adminUser->name ?? 'AD', 0, 2)) }}
        </div>
        <h3 class="text-2xl font-black leading-tight">{{ $adminUser->name }}</h3>
        <p class="text-white/70 text-sm mt-1">{{ $isSuper ? 'Super Admin Platform' : 'Admin Kos / Property Manager' }}</p>
        <div class="mt-5 space-y-2 text-sm text-white/80">
            <p><i class="fa fa-envelope w-5"></i>{{ $adminUser->email }}</p>
            <p><i class="fa fa-phone w-5"></i>{{ $adminUser->phone ?: 'No. HP belum diisi' }}</p>
            @if(! $isSuper)
                <p><i class="fa fa-building w-5"></i>{{ $adminUser->property->name ?? 'Kos belum terhubung' }}</p>
            @else
                <p><i class="fa fa-shield-halved w-5"></i>Akses semua data kos</p>
            @endif
        </div>
        <div class="mt-6 rounded-2xl bg-white/10 p-4 text-sm leading-relaxed">
            @if($isSuper)
                Super Admin hanya mengelola platform: data kos, admin kos, pendaftaran pemilik kos, dan status akun.
            @else
                Admin Kos hanya mengelola data milik kos sendiri: kamar, penghuni, booking, tagihan, komplain, dan maintenance.
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.profile.update') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 xl:col-span-1">
        @csrf
        @method('PUT')
        <h3 class="text-lg font-black mb-4">Data Profil</h3>
        <div class="space-y-4">
            <div>
                <label class="text-sm font-bold text-slate-700">Nama</label>
                <input name="name" value="{{ old('name', $adminUser->name) }}" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-700">Email Login</label>
                <input value="{{ $adminUser->email }}" disabled class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-slate-50 text-slate-500">
                <p class="text-xs text-slate-400 mt-1">Email tidak diubah dari halaman profil agar keamanan akun tetap terjaga.</p>
            </div>
            <div>
                <label class="text-sm font-bold text-slate-700">No. HP</label>
                <input name="phone" value="{{ old('phone', $adminUser->phone) }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-700">Alamat</label>
                <textarea name="address" rows="3" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">{{ old('address', $adminUser->address) }}</textarea>
            </div>

            @if(! $isSuper && $adminUser->property)
                <div class="pt-4 mt-2 border-t border-slate-100 md:col-span-1">
                    <p class="text-sm font-black text-slate-800 mb-3">Data Kos Saya</p>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-bold text-slate-700">Nama Kos</label>
                            <input name="property_name" value="{{ old('property_name', $adminUser->property->name) }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">Jenis Kos</label>
                            <select name="property_gender_type" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white">
                                <option value="putra" @selected(old('property_gender_type', $adminUser->property->gender_type) === 'putra')>Kos Putra</option>
                                <option value="putri" @selected(old('property_gender_type', $adminUser->property->gender_type) === 'putri')>Kos Putri</option>
                                <option value="campuran" @selected(old('property_gender_type', $adminUser->property->gender_type) === 'campuran')>Kos Campuran</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1">Pilihan ini sinkron dengan filter Putra/Putri/Campuran di aplikasi mobile.</p>
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">Jumlah/Kapasitas Kamar</label>
                            <input type="number" name="property_max_units" value="{{ old('property_max_units', $adminUser->property->max_units) }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">No. HP Kos</label>
                            <input name="property_phone" value="{{ old('property_phone', $adminUser->property->phone) }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">Alamat Kos</label>
                            <textarea name="property_address" rows="3" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">{{ old('property_address', $adminUser->property->address) }}</textarea>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <button class="mt-5 bg-gradient-to-r from-blue-700 to-teal-600 text-white px-6 py-3 rounded-2xl font-black"><i class="fa fa-save mr-2"></i>Simpan Profil</button>
    </form>

    <form method="POST" action="{{ route('admin.profile.password') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 xl:col-span-1">
        @csrf
        @method('PUT')
        <h3 class="text-lg font-black mb-4">Ganti Password</h3>
        <p class="text-sm text-slate-500 mb-4">Gunakan password kuat. Jangan gunakan password yang sama dengan akun lain.</p>
        <div class="space-y-4">
            <div>
                <label class="text-sm font-bold text-slate-700">Password Lama</label>
                <input type="password" name="current_password" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-700">Password Baru</label>
                <input type="password" name="password" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-700">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
            </div>
        </div>
        <button class="mt-5 bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-2xl font-black"><i class="fa fa-key mr-2"></i>Ganti Password</button>
    </form>
</div>
@endsection
