@extends('layouts.admin')
@section('title', 'Dashboard')
@section('subtitle', auth()->user()->isSuperAdmin() ? 'Panel pengelolaan platform Kostify' : 'Ringkasan operasional kos Anda')

@section('content')
@if(auth()->user()->isSuperAdmin())
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Total Kos</p><h3 class="text-3xl font-black mt-2">{{ $stats['total_properties'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Kos Aktif</p><h3 class="text-3xl font-black mt-2 text-teal-700">{{ $stats['active_properties'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Admin Kos</p><h3 class="text-3xl font-black mt-2 text-blue-700">{{ $stats['total_property_admins'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Pendaftaran Baru</p><h3 class="text-3xl font-black mt-2 text-amber-600">{{ $stats['pending_registrations'] }}</h3></div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-black text-lg">Kos Terbaru</h3>
                <a href="{{ route('admin.properties.index') }}" class="text-sm font-black text-teal-700">Kelola Data Kos</a>
            </div>
            <div class="grid gap-3">
                @forelse($properties as $property)
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                        <b>{{ $property->name }}</b>
                        <p class="text-sm text-slate-500">{{ $property->owner_name ?: 'Pemilik belum diisi' }}</p>
                        <div class="flex gap-2 mt-3 text-xs font-black flex-wrap">
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full">{{ $property->units_count }} kamar</span>
                            <span class="bg-teal-100 text-teal-700 px-2 py-1 rounded-full">{{ $property->admins_count }} admin</span>
                            <span class="bg-slate-200 text-slate-700 px-2 py-1 rounded-full">{{ $property->tenants_count }} penghuni</span>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 font-bold">Belum ada data kos.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-black text-lg">Pendaftaran Pemilik Kos</h3>
                <a href="{{ route('admin.registrations.index') }}" class="text-sm font-black text-teal-700">Lihat</a>
            </div>
            <div class="grid gap-3">
                @forelse($pendingRegistrations as $item)
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3">
                        <div><b>{{ $item->owner_name }}</b><p class="text-sm text-slate-500">{{ $item->property_name }} • {{ $item->email }}</p></div>
                        <span class="text-xs font-black bg-amber-100 text-amber-700 px-3 py-1 rounded-full">MENUNGGU</span>
                    </div>
                @empty
                    <p class="text-slate-500 font-bold">Tidak ada pendaftaran pemilik kos baru.</p>
                @endforelse
            </div>
        </div>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Kamar</p><h3 class="text-3xl font-black mt-2">{{ $stats['total_units'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Tersedia</p><h3 class="text-3xl font-black mt-2 text-teal-700">{{ $stats['available_units'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Terisi</p><h3 class="text-3xl font-black mt-2 text-blue-700">{{ $stats['occupied_units'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Penghuni</p><h3 class="text-3xl font-black mt-2">{{ $stats['total_tenants'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Booking Pending</p><h3 class="text-3xl font-black mt-2 text-amber-600">{{ $stats['pending_bookings'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Tagihan Belum Dibayar</p><h3 class="text-3xl font-black mt-2">{{ $stats['unpaid_billings'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Komplain Aktif</p><h3 class="text-3xl font-black mt-2">{{ $stats['open_complaints'] }}</h3></div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100"><p class="text-sm text-slate-500 font-bold">Maintenance Aktif</p><h3 class="text-3xl font-black mt-2">{{ $stats['open_maintenances'] }}</h3></div>
    </div>

    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4"><h3 class="font-black text-lg">Booking Terbaru</h3><a href="{{ route('admin.bookings.index') }}" class="text-sm font-black text-teal-700">Lihat</a></div>
        <div class="grid gap-3">
            @forelse($recent_bookings as $item)
                @php
                    $badge = match($item->status) {'approved' => 'bg-green-100 text-green-700','pending' => 'bg-amber-100 text-amber-700','rejected' => 'bg-red-100 text-red-700',default => 'bg-slate-100 text-slate-700'};
                    $label = match($item->status) {'approved' => 'DISETUJUI','pending' => 'MENUNGGU','rejected' => 'DITOLAK',default => strtoupper($item->status)};
                @endphp
                <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3">
                    <div><b>{{ $item->booking_code }}</b><p class="text-sm text-slate-500">{{ $item->user->name ?? '-' }} • {{ $item->unit->name ?? '-' }}</p></div>
                    <span class="text-xs font-black {{ $badge }} px-3 py-1 rounded-full">{{ $label }}</span>
                </div>
            @empty
                <p class="text-slate-500 font-bold">Tidak ada booking.</p>
            @endforelse
        </div>
    </div>
@endif
@endsection
