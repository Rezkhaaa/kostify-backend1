<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kostify Admin - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/kostify-admin.css')); ?>">
</head>
<body class="font-sans text-slate-900">
<?php
    $currentAdmin = auth()->user();
    if ($currentAdmin) {
        \App\Models\PushNotificationLog::cleanupExpired();
    }
    $adminNotificationCount = $currentAdmin
        ? min(\App\Models\PushNotificationLog::unreadForAdmin($currentAdmin)->count(), 99)
        : 0;
    $adminIcon = function (string $name): string {
        $icons = [
            'dashboard' => '<path d="M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-5H4v5Z"/>',
            'building' => '<path d="M4 21V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v16h-2v-4H8v4H4Zm4-6h7v-2H8v2Zm0-4h7V9H8v2Zm0-4h7V5H8v2Zm10 14v-9h2v9h-2Z"/>',
            'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Zm-1-6.5 6-6-1.4-1.4-4.6 4.58-2.1-2.08L7.5 12l3.5 3.5Z"/>',
            'users' => '<path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3Zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.96 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5Z"/>',
            'plus' => '<path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5Z"/>',
            'check' => '<path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17Z"/>',
            'bed' => '<path d="M4 10V5h2v7h7V8h5a3 3 0 0 1 3 3v8h-2v-3H5v3H3v-9h1Zm4 0a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm7 2h4v-1a1 1 0 0 0-1-1h-3v2Z"/>',
            'calendar' => '<path d="M7 2h2v2h6V2h2v2h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3V2Zm13 8H4v10h16V10ZM4 8h16V6H4v2Z"/>',
            'invoice' => '<path d="M6 2h12a2 2 0 0 1 2 2v18l-4-2-4 2-4-2-4 2V4a2 2 0 0 1 2-2Zm2 5v2h8V7H8Zm0 4v2h8v-2H8Zm0 4v2h5v-2H8Z"/>',
            'card' => '<path d="M3 5h18a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Zm0 4h18V7H3v2Zm0 3v5h18v-5H3Zm3 2h5v2H6v-2Z"/>',
            'message' => '<path d="M4 4h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8l-4 4v-4H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm2 5h12V7H6v2Zm0 4h8v-2H6v2Z"/>',
            'tool' => '<path d="M22 19.6 12.4 10A5.5 5.5 0 0 0 5 3.3l3.2 3.2-1.7 1.7L3.3 5A5.5 5.5 0 0 0 10 12.4l9.6 9.6 2.4-2.4Z"/>',
            'bank' => '<path d="M12 2 2 7v2h20V7L12 2ZM4 11v7H2v2h20v-2h-2v-7h-2v7h-3v-7h-2v7h-2v-7H9v7H6v-7H4Z"/>',
            'settings' => '<path d="M19.4 13.5c.08-.49.1-.99.1-1.5s-.02-1.01-.1-1.5l2.1-1.63-2-3.46-2.48 1a7.7 7.7 0 0 0-2.6-1.5L14 2h-4l-.42 2.91c-.94.32-1.82.82-2.6 1.5l-2.48-1-2 3.46 2.1 1.63c-.08.49-.1.99-.1 1.5s.02 1.01.1 1.5l-2.1 1.63 2 3.46 2.48-1a7.7 7.7 0 0 0 2.6 1.5L10 22h4l.42-2.91c.94-.32 1.82-.82 2.6-1.5l2.48 1 2-3.46-2.1-1.63ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z"/>',
            'bell' => '<path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm8-6-2-2v-5a6 6 0 0 0-5-5.91V2h-2v1.09A6 6 0 0 0 6 9v5l-2 2v1h16v-1Z"/>',
            'key' => '<path d="M7 14a5 5 0 1 1 4.58-7h10.17v4H19v3h-3v3h-4.42A5 5 0 0 1 7 14Zm0-3a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>',
            'logout' => '<path d="M10 17v-2h4V9h-4V7l-5 5 5 5Zm2 4h8V3h-8v2h6v14h-6v2Z"/>',
            'menu' => '<path d="M4 7h16v2H4V7Zm0 4h16v2H4v-2Zm0 4h16v2H4v-2Z"/>',
        ];
        $path = $icons[$name] ?? $icons['dashboard'];
        return '<svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">'.$path.'</svg>';
    };
?>

<div class="kf-admin-shell">
    <aside class="kf-sidebar" id="kfSidebar">
        <div class="kf-sidebar-brand">
            <img src="<?php echo e(asset('images/kostify-logo.png')); ?>" onerror="this.style.display='none'" alt="Kostify">
            <div>
                <h1>Kostify</h1>
                <p><?php echo e($currentAdmin->isSuperAdmin() ? 'Super Admin' : ($currentAdmin->property->name ?? 'Admin Kos')); ?></p>
            </div>
        </div>

        <nav class="kf-menu">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <span class="kf-menu-icon"><?php echo $adminIcon('dashboard'); ?></span><span>Dashboard</span>
            </a>

            <?php if($currentAdmin->isSuperAdmin()): ?>
                <div class="kf-menu-title">Super Admin</div>
                <a href="<?php echo e(route('admin.properties.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.properties.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('building'); ?></span><span>Data Kos</span></a>
                <a href="<?php echo e(route('admin.property-admins.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.property-admins.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('shield'); ?></span><span>Admin Kos</span></a>
                <a href="<?php echo e(route('admin.tenants.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.tenants.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('users'); ?></span><span>Pengguna</span></a>
                <a href="<?php echo e(route('admin.registrations.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.registrations.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('plus'); ?></span><span>Pendaftaran Pemilik</span></a>
                <a href="<?php echo e(route('admin.tenant-registrations.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.tenant-registrations.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('check'); ?></span><span>Pendaftaran Pengguna</span></a>
                <a href="<?php echo e(route('admin.password-resets.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.password-resets.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('key'); ?></span><span>Bantuan Reset Password</span></a>
            <?php endif; ?>

            <?php if($currentAdmin->isPropertyAdmin()): ?>
                <div class="kf-menu-title">Operasional Kos</div>
                <a href="<?php echo e(route('admin.units.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.units.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('bed'); ?></span><span>Kamar</span></a>
                <a href="<?php echo e(route('admin.tenants.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.tenants.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('users'); ?></span><span>Pengguna</span></a>
                <a href="<?php echo e(route('admin.bookings.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.bookings.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('calendar'); ?></span><span>Booking</span></a>
                <a href="<?php echo e(route('admin.billings.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.billings.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('invoice'); ?></span><span>Tagihan</span></a>
                <a href="<?php echo e(route('admin.payments.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.payments.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('card'); ?></span><span>Pembayaran</span></a>
                <a href="<?php echo e(route('admin.complaints.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.complaints.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('message'); ?></span><span>Komplain</span></a>
                <a href="<?php echo e(route('admin.maintenances.index')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.maintenances.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('tool'); ?></span><span>Perbaikan</span></a>
            <?php endif; ?>

            <div class="kf-menu-title">Pengaturan</div>
            <a href="<?php echo e(route('admin.payment-settings.edit')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.payment-settings.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('bank'); ?></span><span>Rekening Pembayaran</span></a>
            <a href="<?php echo e(route('admin.profile.edit')); ?>" class="kf-menu-link <?php echo e(request()->routeIs('admin.profile.*') ? 'active' : ''); ?>"><span class="kf-menu-icon"><?php echo $adminIcon('settings'); ?></span><span>Profil & Password</span></a>
        </nav>

        <form method="POST" action="<?php echo e(route('admin.logout')); ?>" class="kf-logout-form">
            <?php echo csrf_field(); ?>
            <button type="submit" class="kf-logout-btn"><span class="kf-menu-icon"><?php echo $adminIcon('logout'); ?></span><span>Keluar</span></button>
        </form>
    </aside>

    <main class="kf-main">
        <header class="kf-topbar">
            <button type="button" class="kf-mobile-menu" onclick="document.getElementById('kfSidebar').classList.toggle('open')" aria-label="Buka menu">
                <?php echo $adminIcon('menu'); ?>

            </button>
            <div class="kf-topbar-copy">
                <h2><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h2>
                <p><?php echo $__env->yieldContent('subtitle', 'Kostify Admin'); ?></p>
            </div>
            <div class="kf-topbar-actions">
                <a href="<?php echo e(route('admin.notifications.index')); ?>" class="kf-notif-link <?php echo e(request()->routeIs('admin.notifications.*') ? 'active' : ''); ?>" title="Lihat notifikasi">
                    <?php echo $adminIcon('bell'); ?>

                    <?php if($adminNotificationCount > 0): ?>
                        <span class="kf-notif-badge"><?php echo e($adminNotificationCount > 99 ? '99+' : $adminNotificationCount); ?></span>
                    <?php endif; ?>
                </a>
                <div class="kf-admin-pill">
                    <div class="kf-admin-avatar"><?php echo e(strtoupper(substr($currentAdmin->name ?? 'AD', 0, 2))); ?></div>
                    <div>
                        <strong><?php echo e($currentAdmin->name ?? 'Administrator'); ?></strong>
                        <span><?php echo e($currentAdmin->isSuperAdmin() ? 'Super Admin' : 'Admin Kos'); ?></span>
                    </div>
                </div>
            </div>
        </header>

        <section class="kf-alert-area">
            <?php if(session('success')): ?>
                <div class="kf-alert success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="kf-alert danger"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="kf-alert danger"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>
        </section>

        <section class="kf-content">
            <?php echo $__env->yieldContent('content'); ?>
        </section>
    </main>
</div>
</body>
</html>
<?php /**PATH C:\laragon\www\kostify-fiX13\kostify-backend\resources\views/layouts/admin.blade.php ENDPATH**/ ?>