<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin - Kostify</title>
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
                <h1>Kostify</h1>
                <p>Masuk untuk mengelola kos, kamar, booking, tagihan, dan konfirmasi pembayaran.</p>
            </div>

            @if($errors->any())
                <div class="kostify-alert kostify-alert-danger"><span>!</span>{{ $errors->first() }}</div>
            @endif
            @if(session('success'))
                <div class="kostify-alert kostify-alert-success"><span>✓</span>{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="kostify-alert kostify-alert-danger"><span>!</span>{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="kostify-login-form" autocomplete="off">
                @csrf
                <div class="kostify-field">
                    <label for="email">Email</label>
                    <div class="kostify-input-wrap">
                        <span class="kostify-input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5v2Z"/></svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="nama@email.com" required autofocus>
                    </div>
                </div>

                <div class="kostify-field">
                    <label for="password">Kata Sandi</label>
                    <div class="kostify-input-wrap">
                        <span class="kostify-input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M17 8h-1V6a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v10h14V10a2 2 0 0 0-2-2Zm-7 0V6a2 2 0 0 1 4 0v2h-4Z"/></svg>
                        </span>
                        <input id="password" type="password" name="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required>
                        <button type="button" class="kostify-eye-btn" onclick="togglePassword()" aria-label="Lihat kata sandi">
                            <svg id="eyeIcon" viewBox="0 0 24 24"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-2.2A1.8 1.8 0 1 0 12 10a1.8 1.8 0 0 0 0 3.8Z"/></svg>
                        </button>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;margin-top:-6px;margin-bottom:10px;">
                    <a href="{{ route('admin.password.forgot') }}" style="color:#0f766e;font-weight:800;text-decoration:none;font-size:14px;">Lupa kata sandi?</a>
                </div>

                <button type="submit" class="kostify-main-btn">Masuk</button>
            </form>

            <div class="kostify-login-footer">
                <a href="{{ route('admin.owner-register.create') }}">Daftar pemilik kos baru</a>
                <p>Pengajuan akun pemilik akan diperiksa terlebih dahulu.</p>
            </div>
        </section>
    </main>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
        }
    </script>
</body>
</html>
