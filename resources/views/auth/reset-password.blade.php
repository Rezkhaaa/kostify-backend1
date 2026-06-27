<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi Admin - Kostify</title>
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
                <h1>Buat Kata Sandi Baru</h1>
                <p>Masukkan kode dari email, lalu buat kata sandi baru secara mandiri.</p>
            </div>

            @if($errors->any())
                <div class="kostify-alert kostify-alert-danger"><span>!</span>{{ $errors->first() }}</div>
            @endif
            @if(session('success'))
                <div class="kostify-alert kostify-alert-success"><span>✓</span>{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}" class="kostify-login-form" autocomplete="off">
                @csrf
                <div class="kostify-field">
                    <label for="email">Email</label>
                    <div class="kostify-input-wrap">
                        <span class="kostify-input-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5v2Z"/></svg></span>
                        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" placeholder="nama@email.com" required>
                    </div>
                </div>
                <div class="kostify-field">
                    <label for="code">Kode Reset</label>
                    <div class="kostify-input-wrap">
                        <span class="kostify-input-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v10h16V11a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5Zm3 8H9V6a3 3 0 0 1 6 0v3Z"/></svg></span>
                        <input id="code" type="text" name="code" value="{{ old('code') }}" inputmode="numeric" maxlength="6" placeholder="6 digit kode" required>
                    </div>
                </div>
                <div class="kostify-field">
                    <label for="password">Kata Sandi Baru</label>
                    <div class="kostify-input-wrap">
                        <span class="kostify-input-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M17 8h-1V6a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v10h14V10a2 2 0 0 0-2-2Zm-7 0V6a2 2 0 0 1 4 0v2h-4Z"/></svg></span>
                        <input id="password" type="password" name="password" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="kostify-field">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <div class="kostify-input-wrap">
                        <span class="kostify-input-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M17 8h-1V6a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v10h14V10a2 2 0 0 0-2-2Zm-7 0V6a2 2 0 0 1 4 0v2h-4Z"/></svg></span>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi kata sandi" required>
                    </div>
                </div>
                <button type="submit" class="kostify-main-btn">Simpan Kata Sandi</button>
            </form>

            <div class="kostify-login-footer">
                <a href="{{ route('admin.password.forgot') }}">Kirim ulang kode</a>
                <p>Kode berlaku 15 menit.</p>
            </div>
        </section>
    </main>
</body>
</html>
