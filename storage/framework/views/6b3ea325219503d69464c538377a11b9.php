<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('subtitle', auth()->user()->isSuperAdmin() ? 'Panel pengelolaan platform Kostify' : 'Ringkasan operasional kos Anda'); ?>

<?php $__env->startSection('content'); ?>
<?php if(auth()->user()->isSuperAdmin()): ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Total Kos</p><h3 class="text-3xl font-black mt-2"><?php echo e($stats['total_properties']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Kos Aktif</p><h3 class="text-3xl font-black mt-2 text-teal-700"><?php echo e($stats['active_properties']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Admin Kos</p><h3 class="text-3xl font-black mt-2 text-blue-700"><?php echo e($stats['total_property_admins']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Pendaftaran Baru</p><h3 class="text-3xl font-black mt-2 text-amber-600"><?php echo e($stats['pending_registrations']); ?></h3></div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-black text-lg">Kos Terbaru</h3>
                <a href="<?php echo e(route('admin.properties.index')); ?>" class="text-sm font-black text-teal-700">Kelola Data Kos</a>
            </div>
            <div class="grid gap-3">
                <?php $__empty_1 = true; $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                        <b><?php echo e($property->name); ?></b>
                        <p class="text-sm text-slate-500"><?php echo e($property->owner_name ?: 'Pemilik belum diisi'); ?></p>
                        <div class="flex gap-2 mt-3 text-xs font-black flex-wrap">
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full"><?php echo e($property->units_count); ?> kamar</span>
                            <span class="bg-teal-100 text-teal-700 px-2 py-1 rounded-full"><?php echo e($property->admins_count); ?> admin</span>
                            <span class="bg-slate-200 text-slate-700 px-2 py-1 rounded-full"><?php echo e($property->tenants_count); ?> penghuni</span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-slate-500 font-bold">Belum ada data kos.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-black text-lg">Pendaftaran Pemilik Kos</h3>
                <a href="<?php echo e(route('admin.registrations.index')); ?>" class="text-sm font-black text-teal-700">Lihat</a>
            </div>
            <div class="grid gap-3">
                <?php $__empty_1 = true; $__currentLoopData = $pendingRegistrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3">
                        <div><b><?php echo e($item->owner_name); ?></b><p class="text-sm text-slate-500"><?php echo e($item->property_name); ?> • <?php echo e($item->email); ?></p></div>
                        <span class="text-xs font-black bg-amber-100 text-amber-700 px-3 py-1 rounded-full">MENUNGGU</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-slate-500 font-bold">Tidak ada pendaftaran pemilik kos baru.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Kamar</p><h3 class="text-3xl font-black mt-2"><?php echo e($stats['total_units']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Tersedia</p><h3 class="text-3xl font-black mt-2 text-teal-700"><?php echo e($stats['available_units']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Terisi</p><h3 class="text-3xl font-black mt-2 text-blue-700"><?php echo e($stats['occupied_units']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Penghuni</p><h3 class="text-3xl font-black mt-2"><?php echo e($stats['total_tenants']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Booking Pending</p><h3 class="text-3xl font-black mt-2 text-amber-600"><?php echo e($stats['pending_bookings']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Tagihan Belum Dibayar</p><h3 class="text-3xl font-black mt-2"><?php echo e($stats['unpaid_billings']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Komplain Aktif</p><h3 class="text-3xl font-black mt-2"><?php echo e($stats['open_complaints']); ?></h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Maintenance Aktif</p><h3 class="text-3xl font-black mt-2"><?php echo e($stats['open_maintenances']); ?></h3></div>
    </div>

    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4"><h3 class="font-black text-lg">Booking Terbaru</h3><a href="<?php echo e(route('admin.bookings.index')); ?>" class="text-sm font-black text-teal-700">Lihat</a></div>
        <div class="grid gap-3">
            <?php $__empty_1 = true; $__currentLoopData = $recent_bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $badge = match($item->status) {'approved' => 'bg-green-100 text-green-700','pending' => 'bg-amber-100 text-amber-700','rejected' => 'bg-red-100 text-red-700',default => 'bg-slate-100 text-slate-700'};
                    $label = match($item->status) {'approved' => 'DISETUJUI','pending' => 'MENUNGGU','rejected' => 'DITOLAK',default => strtoupper($item->status)};
                ?>
                <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3">
                    <div><b><?php echo e($item->booking_code); ?></b><p class="text-sm text-slate-500"><?php echo e($item->user->name ?? '-'); ?> • <?php echo e($item->unit->name ?? '-'); ?></p></div>
                    <span class="text-xs font-black <?php echo e($badge); ?> px-3 py-1 rounded-full"><?php echo e($label); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-slate-500 font-bold">Tidak ada booking.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kostify-fiX13\kostify-backend\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>