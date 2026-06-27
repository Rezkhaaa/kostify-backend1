@extends('layouts.admin')
@section('title','Kamar Kos Management')
@section('subtitle','Kelola kamar kos, harga sewa, status, dan fasilitas')
@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 flex-1">
        <div class="bg-white border border-slate-200 rounded-2xl p-4"><p class="text-xs text-slate-500">Total Kamar</p><b class="text-2xl text-slate-900">{{ $units->total() }}</b></div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4"><p class="text-xs text-slate-500">Tersedia</p><b class="text-2xl text-emerald-600">{{ (clone $unitStatsQuery)->where('status','available')->count() }}</b></div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4"><p class="text-xs text-slate-500">Terisi</p><b class="text-2xl text-teal-600">{{ (clone $unitStatsQuery)->where('status','occupied')->count() }}</b></div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4"><p class="text-xs text-slate-500">Perbaikan</p><b class="text-2xl text-amber-600">{{ (clone $unitStatsQuery)->where('status','maintenance')->count() }}</b></div>
    </div>
    <a href="{{ route('admin.units.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-2xl font-black shadow-lg shadow-teal-600/15 whitespace-nowrap"><i class="fa fa-plus mr-2"></i>Tambah Kamar</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
@forelse($units as $unit)
    @php($color = ['available'=>'bg-emerald-50 text-emerald-700','occupied'=>'bg-teal-50 text-teal-700','maintenance'=>'bg-amber-50 text-amber-700','inactive'=>'bg-slate-100 text-slate-600'][$unit->status] ?? 'bg-slate-100')
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 hover:shadow-lg transition">
        <div class="flex justify-between gap-3"><div><p class="text-xs font-mono text-teal-600">{{ $unit->unit_code }}</p><h3 class="font-black text-lg text-slate-900 leading-tight">{{ $unit->name }}</h3>@if(auth()->user()->isSuperAdmin())<p class="text-xs text-slate-500 mt-1">{{ $unit->property->name ?? '-' }}</p>@endif</div><span class="h-fit text-xs px-3 py-1 rounded-full font-bold {{ $color }}">{{ $unit->status === 'maintenance' ? 'Perbaikan' : ucfirst($unit->status) }}</span></div>
        <p class="text-slate-500 text-sm mt-3 line-clamp-2">{{ $unit->address ?: 'Lokasi belum diisi' }}</p>
        <div class="grid grid-cols-3 gap-2 mt-4 text-center"><div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-400">Lantai</p><b>{{ $unit->floor ?: '-' }}</b></div><div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-400">Luas</p><b>{{ $unit->area ?: '-' }} m²</b></div><div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-400">Tipe</p><b>{{ ucfirst(str_replace('_',' ', $unit->type)) }}</b></div></div>
        <p class="text-teal-700 font-black text-xl mt-4">Rp {{ number_format($unit->price,0,',','.') }}<span class="text-xs text-slate-400">/{{ $unit->price_period }}</span></p>
        <div class="flex flex-wrap gap-2 mt-4"><a class="px-3 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-sm" href="{{ route('admin.units.show',$unit) }}">Detail</a><a class="px-3 py-2 rounded-xl bg-amber-100 text-amber-700 font-bold text-sm" href="{{ route('admin.units.edit',$unit) }}">Edit</a><form method="POST" action="{{ route('admin.units.destroy',$unit) }}" onsubmit="return confirm('Hapus kamar kos ini?')">@csrf @method('DELETE')<button class="px-3 py-2 rounded-xl bg-red-100 text-red-700 font-bold text-sm">Hapus</button></form></div>
    </div>
@empty
    <div class="col-span-full bg-white rounded-3xl border border-dashed border-slate-300 p-10 text-center text-slate-500">Belum ada data kamar kos.</div>
@endforelse
</div>
<div class="mt-5">{{ $units->links() }}</div>
@endsection
