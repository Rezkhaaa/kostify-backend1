<?php $__env->startSection('title', 'Manajemen Pengguna'); ?>
<?php $__env->startSection('subtitle', 'Kelola akun pengguna yang login melalui aplikasi mobile'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <div class="bg-white rounded-2xl px-5 py-4 border border-slate-200">
        <p class="text-xs text-slate-500 font-bold">Total Pengguna</p>
        <b class="text-2xl"><?php echo e($tenants->total()); ?></b>
    </div>
    <a href="<?php echo e(route('admin.tenants.create')); ?>" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-2xl font-black text-center">
        <i class="fa fa-plus mr-2"></i>Tambah Pengguna
    </a>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Email</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Kos</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">No. Telepon</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-bold text-slate-800"><?php echo e($tenant->name); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($tenant->email); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e(auth()->user()->property->name ?? ($tenant->property->name ?? '-')); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($tenant->phone ?? '-'); ?></td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full text-xs font-black <?php echo e(($tenant->access_status ?? 'active') === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700'); ?>">
                                <?php echo e(($tenant->access_status ?? 'active') === 'active' ? 'AKTIF DI KOS INI' : 'NONAKTIF DI KOS INI'); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="<?php echo e(route('admin.tenants.show', $tenant)); ?>" class="px-3 py-2 rounded-xl bg-blue-100 text-blue-700 font-bold text-xs">Detail</a>
                                <a href="<?php echo e(route('admin.tenants.edit', $tenant)); ?>" class="px-3 py-2 rounded-xl bg-amber-100 text-amber-700 font-bold text-xs">Edit</a>
                                <?php if(auth()->user()->isSuperAdmin()): ?>
                                    <form method="POST" action="<?php echo e(route('admin.password-resets.send', $tenant)); ?>" onsubmit="return confirm('Kirim kode reset password ke email pengguna ini?')">
                                        <?php echo csrf_field(); ?>
                                        <button class="px-3 py-2 rounded-xl bg-teal-100 text-teal-700 font-bold text-xs">Kirim Reset Password</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="<?php echo e(route('admin.tenants.toggle', $tenant)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="px-3 py-2 rounded-xl <?php echo e(($tenant->access_status ?? 'active') === 'active' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'); ?> font-bold text-xs">
                                        <?php echo e(($tenant->access_status ?? 'active') === 'active' ? 'Nonaktifkan di Kos Ini' : 'Aktifkan di Kos Ini'); ?>

                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">Belum ada pengguna terdaftar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-slate-100">
        <?php echo e($tenants->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kostify-fiX13\kostify-backend\resources\views/admin/tenants/index.blade.php ENDPATH**/ ?>