@extends('layouts.admin')
@section('title','Detail Kamar')
@section('subtitle',$unit->unit_code)
@section('content')
<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div><p class="font-mono text-teal-600 font-bold">{{ $unit->unit_code }}</p><h2 class="text-3xl font-black text-slate-900">{{ $unit->name }}</h2><p class="text-slate-500 mt-2">{{ $unit->description }}</p></div>
            <a href="{{ route('admin.units.edit',$unit) }}" class="px-4 py-2 rounded-2xl bg-amber-100 text-amber-700 font-bold">Edit</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Status</p><b>{{ ucfirst($unit->status) }}</b></div>
            <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Harga</p><b>Rp {{ number_format($unit->price,0,',','.') }}</b></div>
            <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Luas</p><b>{{ $unit->area }} m²</b></div>
            <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Lantai</p><b>{{ $unit->floor }}</b></div>
        </div>
        <div class="mt-6"><h3 class="font-black text-slate-800 mb-2">Lokasi</h3><p class="text-slate-600">{{ $unit->address ?: '-' }}</p></div>
    </div>
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
        <h3 class="font-black text-slate-800 mb-4">Fasilitas Kamar</h3>
        <div class="flex flex-wrap gap-2">
            @forelse(($unit->facilities ?? []) as $facility)
                <span class="px-3 py-2 rounded-xl bg-teal-50 text-teal-700 text-sm font-bold">{{ $facility }}</span>
            @empty
                <p class="text-slate-500">Belum ada fasilitas.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
