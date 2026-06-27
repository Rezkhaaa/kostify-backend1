@extends('layouts.admin')
@section('title', 'Bantuan Reset Password')
@section('subtitle', 'Super Admin hanya mengirim kode reset. Password baru dibuat sendiri oleh pemilik akun.')
@section('content')
<div class="bg-teal-50 border border-teal-100 rounded-3xl p-5 mb-5 text-teal-900">
    <h3 class="font-black text-lg">Alur yang aman</h3>
    <p class="text-sm mt-1">Admin tidak membuatkan password baru secara manual. Sistem mengirim kode reset ke email akun, lalu pengguna membuat kata sandi baru sendiri.</p>
</div>

<div class="grid gap-4">
    @forelse($requests as $item)
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="text-lg font-black text-slate-900">{{ $item->email }}</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-black {{ $item->status === 'pending' ? 'bg-amber-100 text-amber-700' : ($item->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">{{ strtoupper($item->status) }}</span>
                    </div>
                    <p class="text-sm text-slate-600"><b>HP:</b> {{ $item->phone ?? '-' }}</p>
                    @if($item->property)<p class="text-sm text-slate-600"><b>Kos:</b> {{ $item->property->name }}</p>@endif
                    @if($item->notes)<p class="text-sm text-slate-500 mt-2">{{ $item->notes }}</p>@endif
                    @if($item->admin_notes)<p class="text-sm text-slate-500 mt-2"><b>Catatan:</b> {{ $item->admin_notes }}</p>@endif
                    @if($item->handler)<p class="text-xs text-slate-400 mt-2">Ditangani oleh {{ $item->handler->name }}</p>@endif
                </div>
                @if($item->status === 'pending')
                    <div class="xl:w-[360px] w-full">
                        <form method="POST" action="{{ route('admin.password-resets.reject', $item) }}" class="grid gap-2">
                            @csrf
                            <input name="admin_notes" placeholder="Catatan/alasan jika ditolak" class="min-w-0 px-4 py-3 rounded-2xl border border-slate-200 text-sm">
                            <button class="px-5 py-3 rounded-2xl bg-red-100 text-red-700 font-black">Tolak Permintaan</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-3xl p-8 text-center text-slate-500 font-bold">Belum ada riwayat permintaan reset password.</div>
    @endforelse
    <div>{{ $requests->links() }}</div>
</div>
@endsection
