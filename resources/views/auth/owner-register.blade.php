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

    <div style="text-align:center;margin-bottom:30px;">
        <div class="kostify-login-logo" style="margin:0 auto 15px;">
            <img src="{{ asset('images/kostify-logo.png') }}" alt="Kostify">
        </div>

        <h1 style="margin:0;font-size:34px;font-weight:700;">
            Daftar Pemilik Kos
        </h1>

        <p style="color:#64748b;margin-top:10px;">
            Data akan diperiksa Super Admin sebelum akun Admin Kos aktif.
        </p>
    </div>

    @if($errors->any())
        <div class="kostify-alert kostify-alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.owner-register.store') }}">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">

            <div>
                <label>Nama Pemilik</label>
                <input type="text" name="owner_name" value="{{ old('owner_name') }}" required>
            </div>

            <div>
                <label>Email Admin</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div>
                <label>No. HP / WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
            </div>

            <div>
                <label>Nama Kos</label>
                <input type="text" name="property_name" value="{{ old('property_name') }}" required>
            </div>

            <div>
                <label>Jenis Kos</label>

                <select name="gender_type">
                    <option value="campuran">Kos Campuran</option>
                    <option value="putra">Kos Putra</option>
                    <option value="putri">Kos Putri</option>
                </select>
            </div>

            <div>
                <label>Jumlah Kamar</label>
                <input type="number" name="room_count" min="1" value="{{ old('room_count') }}">
            </div>

            <div style="grid-column:1 / span 2;">
                <label>Alamat Kos</label>
                <input type="text" name="property_address" value="{{ old('property_address') }}">
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div>
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required>
            </div>

        </div>

        <div style="margin-top:25px;display:flex;gap:15px;">

            <button class="kostify-main-btn" style="flex:1;">
                Kirim Pendaftaran
            </button>

            <a href="{{ route('admin.login') }}"
               style="flex:1;
               text-align:center;
               padding:14px;
               background:#e2e8f0;
               border-radius:12px;
               text-decoration:none;
               font-weight:700;
               color:#334155;">
                Kembali ke Login
            </a>

        </div>

    </form>

</div>

</body>
</html>