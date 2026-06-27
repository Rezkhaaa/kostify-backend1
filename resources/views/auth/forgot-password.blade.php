<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi Admin - Kostify</title>
    <link rel="stylesheet" href="{{ asset('css/kostify-admin.css') }}">
</head>
<body class="kostify-login-body">
    <div class="kostify-login-bg"></div>
    <main class="kostify-login-shell">
        <section class="kostify-login-card natural-login">
            <div class="kostify-login-brand">
                <div class="kostify-login-logo">
                    <img src="{{ asset('images/kostify-logo.png') }}" onerror="this.style.display='none'; document.getElementById('logoFallback').style.display='grid';" alt="Kostify">
                    <span id="logoFallback" style="display:none;">KF</span>
                </div>
                <h1>Lupa Kata Sandi</h1>
                <p>Masukkan email admin yang terdaftar. Sistem akan mengirimkan kode reset ke email tersebut.</p>
            </div>

            @if($errors->any())
                <div class="kostify-alert kostify-alert-danger"><span>!</span>{{ $errors->first() }}</div>
            @endif
            @if(session('success'))
                <div class="kostify-alert kostify-alert-success"><span>✓</span>{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}" class="kostify-login-form" autocomplete="off">
                @csrf
                <div class="kostify-field">
                    <label for="email">Email Admin</label>
                    <div class="kostify-input-wrap">
                        <span class="kostify-input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5v2Z"/></svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                    </div>
                </div>
                <button type="submit" class="kostify-main-btn">Kirim Kode Reset</button>
            </form>

            <div class="kostify-login-footer">
                <a href="{{ route('admin.login') }}">Kembali ke halaman masuk</a>
                <p>Admin tidak akan mengetahui kata sandi baru Anda.</p>
            </div>
        </section>
    </main>
</body>
</html>
