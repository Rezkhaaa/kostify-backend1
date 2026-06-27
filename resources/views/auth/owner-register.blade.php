<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pemilik Kos - Kostify</title>
    <link rel="stylesheet" href="{{ asset('css/kostify-admin.css') }}">
</head>
<body class="owner-register-body">
    <div class="owner-register-bg"></div>

    <div class="owner-register-card">
        <div class="text-center mb-7">
            <h1 class="text-3xl font-black tracking-tight text-slate-950">Daftar Pemilik Kos</h1>
            <p class="text-sm text-slate-500 font-semibold mt-1">Data akan diperiksa Super Admin sebelum akun Admin Kos aktif.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-5 text-sm font-bold">
                <i class="fa fa-circle-exclamation mr-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.owner-register.store') }}" class="grid md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Nama Pemilik</label>
                <input name="owner_name" value="{{ old('owner_name') }}" required class="w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-4 focus:ring-teal-100">
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Email Admin</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-4 focus:ring-teal-100">
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">No. HP/WhatsApp</label>
                <input name="phone" value="{{ old('phone') }}" class="w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-4 focus:ring-teal-100">
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Nama Kos</label>
                <input name="property_name" value="{{ old('property_name') }}" required class="w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-4 focus:ring-teal-100">
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Jenis Kos</label>
                <select name="gender_type" required class="w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-4 focus:ring-teal-100">
                    <option value="campuran" @selected(old('gender_type', 'campuran') === 'campuran')>Kos Campuran</option>
                    <option value="putra" @selected(old('gender_type') === 'putra')>Kos Putra</option>
                    <option value="putri" @selected(old('gender_type') === 'putri')>Kos Putri</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Jumlah Kamar</label>
                <input type="number" name="room_count" value="{{ old('room_count') }}" min="1" class="w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-4 focus:ring-teal-100">
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Alamat Kos</label>
                <input name="property_address" value="{{ old('property_address') }}" class="w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-4 focus:ring-teal-100">
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Password</label>
                <input type="password" name="password" required class="w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-4 focus:ring-teal-100">
            </div>
            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-4 focus:ring-teal-100">
            </div>
            <div class="md:col-span-2 flex flex-col md:flex-row gap-3 mt-3">
                <button class="flex-1 bg-gradient-to-r from-teal-600 to-blue-700 text-white font-black py-3.5 rounded-2xl"><i class="fa fa-paper-plane mr-2"></i>Kirim Pendaftaran</button>
                <a href="{{ route('admin.login') }}" class="flex-1 text-center bg-slate-100 text-slate-700 font-black py-3.5 rounded-2xl">Kembali ke Login</a>
            </div>
        </form>
    </div>
</body>
</html>
