@extends('layouts.admin')
@section('title','Manajemen Data Kos')
@section('subtitle','Super Admin dapat menambahkan, mengubah, dan mengaktif/nonaktifkan kos')
@section('content')
<div class="flex justify-between items-center mb-5">
    <div class="bg-white rounded-2xl px-5 py-4 border border-slate-200"><p class="text-xs text-slate-500">Total Kos</p><b class="text-2xl">{{ $properties->total() }}</b></div>
    <a href="{{ route('admin.properties.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-2xl font-black"><i class="fa fa-plus mr-2"></i>Tambah Kos</a>
</div>
<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
@forelse($properties as $property)
    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-sm">
        <div class="flex justify-between gap-3">
            <div><h3 class="font-black text-lg">{{ $property->name }}</h3><p class="text-sm text-slate-500">{{ $property->owner_name ?: 'Pemilik belum diisi' }}</p><p class="text-xs font-black text-teal-700 mt-1">{{ strtoupper($property->gender_type ?? 'campuran') }} • {{ $property->max_units ? $property->max_units.' kamar' : 'Jumlah kamar belum dibatasi' }}</p></div>
            <span class="h-fit text-xs px-3 py-1 rounded-full font-black {{ $property->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">{{ $property->status === 'active' ? 'AKTIF' : 'NONAKTIF' }}</span>
        </div>
        <p class="text-sm text-slate-500 mt-3 line-clamp-2">{{ $property->address ?: 'Alamat belum diisi' }}</p>
        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
            <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-400">Kamar</p><b>{{ $property->units_count }}</b></div>
            <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-400">Admin</p><b>{{ $property->admins_count }}</b></div>
            <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-400">Tenant</p><b>{{ $property->tenants_count }}</b></div>
        </div>
        <div class="flex flex-wrap gap-2 mt-4">
            <a href="{{ route('admin.properties.edit',$property) }}" class="px-3 py-2 rounded-xl bg-amber-100 text-amber-700 font-bold text-sm">Edit</a>
            <form method="POST" action="{{ route('admin.properties.toggle',$property) }}">@csrf<button class="px-3 py-2 rounded-xl {{ $property->status === 'active' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} font-bold text-sm">{{ $property->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
        </div>
    </div>
@empty
    <div class="col-span-full bg-white rounded-3xl p-10 text-center text-slate-500">Belum ada data kos.</div>
@endforelse
</div>
<div class="mt-5">{{ $properties->links() }}</div>
@endsection
