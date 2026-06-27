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

<main class="owner-register-shell">

    <section class="owner-register-card">

        <div class="owner-register-brand">
            <div class="kostify-login-logo">
                <img src="{{ asset('images/kostify-logo.png') }}"
                     onerror="this.style.display='none';document.getElementById('logoFallback').style.display='grid';"
                     alt="Kostify">

                <span id="logoFallback" style="display:none;">KF</span>
            </div>

            <h1>Daftar Pemilik Kos</h1>

            <p>
                Data akan diperiksa Super Admin sebelum akun Admin Kos aktif.
            </p>
        </div>

        @if($errors->any())
            <div class="kostify-alert kostify-alert-danger">
                <span>!</span>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.owner-register.store') }}"
              class="kostify-login-form">

            @csrf

            <div class="kostify-field">
                <label>Nama Pemilik</label>
                <input
                    type="text"
                    name="owner_name"
                    value="{{ old('owner_name') }}"
                    required>
            </div>

            <div class="kostify-field">
                <label>Email Admin</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required>
            </div>

            <div class="kostify-field">
                <label>No. HP / WhatsApp</label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}">
            </div>

            <div class="kostify-field">
                <label>Nama Kos</label>
                <input
                    type="text"
                    name="property_name"
                    value="{{ old('property_name') }}"
                    required>
            </div>

            <div class="kostify-field">
                <label>Jenis Kos</label>

                <select name="gender_type" required>
                    <option value="campuran">Kos Campuran</option>
                    <option value="putra">Kos Putra</option>
                    <option value="putri">Kos Putri</option>
                </select>
            </div>

            <div class="kostify-field">
                <label>Jumlah Kamar</label>

                <input
                    type="number"
                    name="room_count"
                    value="{{ old('room_count') }}">
            </div>

            <div class="kostify-field">
                <label>Alamat Kos</label>

                <input
                    type="text"
                    name="property_address"
                    value="{{ old('property_address') }}">
            </div>

            <div class="kostify-field">
                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    required>
            </div>

            <div class="kostify-field">
                <label>Konfirmasi Password</label>

                <input
                    type="password"
                    name="password_confirmation"
                    required>
            </div>

            <button
                type="submit"
                class="kostify-main-btn"
                style="margin-top:20px;">
                Kirim Pendaftaran
            </button>

        </form>

        <div class="kostify-login-footer">
            <a href="{{ route('admin.login') }}">
                Kembali ke Login
            </a>
        </div>

    </section>

</main>

</body>
</html>