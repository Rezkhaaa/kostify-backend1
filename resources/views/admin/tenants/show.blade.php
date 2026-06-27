@extends('layouts.admin')
@section('title', 'Profil Penyewa (Pengguna)')
@section('subtitle', 'Detail informasi data diri pengguna kamar kos')

@section('content')
<div class="py-2 max-w-4xl">
    <div class="grid grid-cols-3 gap-6">
        
        <div class="col-span-1 bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex flex-col items-center text-center space-y-4">
            <div class="w-32 h-32 rounded-full border-4 border-slate-50 overflow-hidden shadow-inner">
                @if($tenant->photo)
                    <img src="{{ asset('storage/' . $tenant->photo) }}" alt="Foto {{ $tenant->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-3xl uppercase">
                        {{ substr($tenant->name, 0, 2) }}
                    </div>
                @endif
            </div>
            
            <div>
                <h3 class="font-bold text-lg text-slate-800">{{ $tenant->name }}</h3>
                <p class="text-sm text-slate-400">Terdaftar sejak {{ $tenant->created_at->format('d M Y') }}</p>
            </div>

            <div class="w-full pt-2">
                <span class="w-full block py-2 text-center rounded-lg text-sm font-semibold
                    {{ ($tenant->access_status ?? 'active') === 'active' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-50 text-slate-700 border border-slate-200' }}">
                    Status di kos ini: {{ ($tenant->access_status ?? 'active') === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <div class="col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-6 space-y-6">
            <div>
                <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Informasi Pribadi</h4>
                <div class="grid grid-cols-2 gap-y-4 text-sm">
                    <div>
                        <p class="text-slate-400 font-medium mb-0.5">Alamat Email</p>
                        <p class="text-slate-800 font-medium">{{ $tenant->email }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-medium mb-0.5">No. Telepon / WhatsApp</p>
                        <p class="text-slate-800 font-medium">{{ $tenant->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-medium mb-0.5">Nomor KIK / No. KTP</p>
                        <p class="text-slate-800 font-mono font-medium">{{ $tenant->id_card_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 font-medium mb-0.5">Alamat Asal</p>
                        <p class="text-slate-800 font-medium">{{ $tenant->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3 border-b border-slate-100 pb-2">Kamar Kos Terikat</h4>
                @if($tenant->activeOccupancy)
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-blue-900 text-sm">{{ $tenant->activeOccupancy->unit->name }}</p>
                            <p class="text-xs text-blue-700">Mulai Sewa: {{ $tenant->activeOccupancy->start_date->format('d M Y') }}</p>
                        </div>
                        <span class="text-xs font-semibold bg-blue-600 text-white px-2.5 py-1 rounded">Sewa Aktif</span>
                    </div>
                @else
                    <p class="text-sm text-slate-400 bg-slate-50 rounded-lg p-3 text-center">Saat ini pengguna belum/tidak sedang menyewa kamar kos.</p>
                @endif
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('admin.tenants.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                    <i class="fa fa-arrow-left"></i> Kembali ke Daftar Pengguna
                </a>
                
                <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}">
                    @csrf
                    
                    <button type="submit" class="px-4 py-2 border rounded-lg text-xs font-medium transition-colors
                        {{ ($tenant->access_status ?? 'active') === 'active' ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                        {{ ($tenant->access_status ?? 'active') === 'active' ? 'Nonaktifkan di Kos Ini' : 'Aktifkan di Kos Ini' }}
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection