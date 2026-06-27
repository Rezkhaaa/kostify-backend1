@extends('layouts.admin')
@section('title', 'Pendaftaran Pemilik Kos')
@section('subtitle', 'Approve atau tolak calon pemilik kos yang ingin menggunakan Kostify')

@section('content')
<div class="bg-blue-50 border border-blue-100 text-blue-800 rounded-3xl p-4 mb-5 text-sm font-bold">
    <i class="fa fa-circle-info mr-2"></i>
    Menu ini khusus Super Admin. Jika pendaftaran disetujui, sistem otomatis membuat data kos dan akun Admin Kos.
</div>

<div class="grid gap-4">
    @forelse($requests as $item)
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="text-lg font-black text-slate-900">{{ $item->property_name }}</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-black {{ $item->status === 'pending' ? 'bg-amber-100 text-amber-700' : ($item->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">{{ strtoupper($item->status) }}</span>
                    </div>
                    <div class="text-sm text-slate-600 grid sm:grid-cols-2 gap-x-6 gap-y-1">
                        <p><b>Pemilik:</b> {{ $item->owner_name }}</p>
                        <p><b>Email Admin:</b> {{ $item->email }}</p>
                        <p><b>No. HP:</b> {{ $item->phone ?? '-' }}</p>
                        <p><b>Jumlah Kamar:</b> {{ $item->room_count ?? '-' }}</p>
                        <p><b>Jenis Kos:</b> {{ strtoupper($item->gender_type ?? 'campuran') }}</p>
                    </div>
                    @if($item->property_address)<p class="text-sm text-slate-500 mt-2"><b>Alamat Kos:</b> {{ $item->property_address }}</p>@endif
                    @if($item->admin_notes)<p class="text-sm text-slate-500 mt-2"><b>Catatan:</b> {{ $item->admin_notes }}</p>@endif
                    @if($item->createdProperty || $item->createdAdmin)
                        <p class="text-sm text-green-700 mt-2 font-bold">Dibuat: {{ $item->createdProperty->name ?? '-' }} • {{ $item->createdAdmin->email ?? '-' }}</p>
                    @endif
                </div>

                @if($item->status === 'pending')
                    <div class="flex flex-col gap-2 lg:w-[420px] w-full">
                        <form method="POST" action="{{ route('admin.registrations.approve', $item) }}">
                            @csrf
                            <button class="w-full px-5 py-3 rounded-2xl bg-teal-600 text-white font-black" onclick="return confirm('Setujui pendaftaran dan buat akun Admin Kos?')">Approve & Buat Admin Kos</button>
                        </form>
                        <form method="POST" action="{{ route('admin.registrations.reject', $item) }}" class="flex gap-2">
                            @csrf
                            <input name="admin_notes" placeholder="Alasan penolakan" class="min-w-0 flex-1 px-4 py-3 rounded-2xl border border-slate-200 text-sm">
                            <button class="px-5 py-3 rounded-2xl bg-red-100 text-red-700 font-black">Tolak</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-3xl p-8 text-center text-slate-500 font-bold">Belum ada pendaftaran pemilik kos.</div>
    @endforelse

    <div>{{ $requests->links() }}</div>
</div>
@endsection
