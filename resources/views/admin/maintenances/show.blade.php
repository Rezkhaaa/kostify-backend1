@extends('layouts.admin')
@section('title', 'Detail Perbaikan')
@section('subtitle', $maintenance->maintenance_code)
@section('content')
<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="font-mono text-teal-600 font-bold">{{ $maintenance->maintenance_code }}</p>
                <h3 class="text-2xl font-black text-slate-900">{{ $maintenance->title }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ $maintenance->user->name ?? '-' }} · {{ $maintenance->unit->name ?? '-' }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-700">{{ ucfirst(str_replace('_',' ', $maintenance->status)) }}</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-5">
            <div class="bg-slate-50 rounded-2xl p-4"><p class="text-xs text-slate-500">Kategori</p><b>{{ ucfirst($maintenance->category) }}</b></div>
            <div class="bg-slate-50 rounded-2xl p-4"><p class="text-xs text-slate-500">Prioritas</p><b>{{ ucfirst($maintenance->priority) }}</b></div>
            <div class="bg-slate-50 rounded-2xl p-4"><p class="text-xs text-slate-500">Estimasi Biaya</p><b>Rp {{ number_format($maintenance->cost ?? 0,0,',','.') }}</b></div>
            <div class="bg-slate-50 rounded-2xl p-4"><p class="text-xs text-slate-500">Penanggung</p><b>{{ $maintenance->cost_payer_label }}</b></div>
        </div>
        <div class="mt-5 bg-slate-50 rounded-2xl p-5"><p class="text-xs font-black text-slate-500 uppercase mb-2">Deskripsi Request</p><p class="text-slate-700 leading-relaxed">{{ $maintenance->description }}</p></div>
        @php($maintenancePhotoUrl = $maintenance->photo ? route('admin.media.maintenances.photo', $maintenance) : null)
        @if($maintenancePhotoUrl)
            <div class="mt-5">
                <p class="text-xs font-black text-slate-500 uppercase mb-2">Foto Bukti</p>
                <a href="{{ $maintenancePhotoUrl }}" target="_blank" class="block">
                    <img src="{{ $maintenancePhotoUrl }}" class="w-full max-h-96 rounded-3xl border border-slate-200 object-contain bg-slate-50 p-2" alt="Foto bukti perbaikan" onerror="this.closest('a').outerHTML='<div class=&quot;rounded-2xl bg-red-50 text-red-700 p-4 text-sm font-bold&quot;>Foto tidak ditemukan. Jalankan php artisan storage:link lalu refresh halaman.</div>'">
                </a>
                <p class="text-xs text-slate-400 mt-2">Klik foto untuk melihat ukuran penuh.</p>
            </div>
        @endif
        @if($maintenance->admin_notes)<div class="mt-4 bg-teal-50 rounded-2xl p-5 text-teal-800"><p class="text-xs font-black uppercase mb-2">Catatan Admin</p>{{ $maintenance->admin_notes }}</div>@endif
    </div>
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <h4 class="font-black text-slate-900 mb-4">Proses Perbaikan</h4>
        <form method="POST" action="{{ route('admin.maintenances.update', $maintenance) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="text-sm font-bold text-slate-700">Status</label>
                <select name="status" class="mt-1 w-full border border-slate-200 rounded-2xl p-3">
                    <option value="pending" @selected($maintenance->status==='pending')>Menunggu</option>
                    <option value="approved" @selected($maintenance->status==='approved')>Disetujui</option>
                    <option value="in_progress" @selected($maintenance->status==='in_progress')>Diproses</option>
                    <option value="completed" @selected($maintenance->status==='completed')>Selesai</option>
                    <option value="rejected" @selected($maintenance->status==='rejected')>Ditolak</option>
                </select>
            </div>
            <div><label class="text-sm font-bold text-slate-700">Jadwal</label><input type="date" name="scheduled_date" value="{{ optional($maintenance->scheduled_date)->format('Y-m-d') }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3"></div>
            <div><label class="text-sm font-bold text-slate-700">Estimasi Biaya</label><input type="number" name="cost" value="{{ $maintenance->cost }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3" placeholder="0"></div>
            <div>
                <label class="text-sm font-bold text-slate-700">Biaya Ditanggung</label>
                <select name="cost_payer" class="mt-1 w-full border border-slate-200 rounded-2xl p-3">
                    <option value="belum_ditentukan" @selected($maintenance->cost_payer==='belum_ditentukan')>Belum Ditentukan</option>
                    <option value="pengelola" @selected($maintenance->cost_payer==='pengelola')>Admin / Pengelola</option>
                    <option value="tenant" @selected($maintenance->cost_payer==='tenant')>Penghuni</option>
                </select>
            </div>
            <div><label class="text-sm font-bold text-slate-700">Catatan Admin</label><textarea name="admin_notes" rows="4" class="mt-1 w-full border border-slate-200 rounded-2xl p-3" placeholder="Contoh: teknisi datang pukul 10.00">{{ old('admin_notes', $maintenance->admin_notes) }}</textarea></div>
            <button class="w-full bg-gradient-to-r from-blue-700 to-teal-600 text-white rounded-2xl py-3 font-black">Simpan Progress</button>
            <a href="{{ route('admin.maintenances.index') }}" class="block text-center rounded-2xl py-3 font-bold bg-slate-100 text-slate-600">Kembali</a>
        </form>

        @if(in_array($maintenance->status, ['completed','rejected']))
            <form method="POST" action="{{ route('admin.maintenances.destroy', $maintenance) }}" class="mt-4" onsubmit="return confirm('Hapus perbaikan ini?')">
                @csrf @method('DELETE')
                <button class="w-full bg-red-50 text-red-700 rounded-2xl py-3 font-black">Hapus Perbaikan</button>
            </form>
        @endif
    </div>
</div>
@endsection
