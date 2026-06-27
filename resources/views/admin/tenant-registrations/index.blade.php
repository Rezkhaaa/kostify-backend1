@extends('layouts.admin')
@section('title','Pendaftaran Pengguna')
@section('subtitle','Persetujuan akun pengguna baru dari aplikasi mobile, termasuk pendaftaran via Google')
@section('content')
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm align-top">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-3 text-left font-black text-slate-600">Pendaftar</th>
                    <th class="p-3 text-left font-black text-slate-600">Kontak</th>
                    <th class="p-3 text-left font-black text-slate-600">Metode</th>
                    <th class="p-3 text-left font-black text-slate-600">Status</th>
                    <th class="p-3 text-left font-black text-slate-600 min-w-[280px]">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($requests as $item)
                @php
                    $statusClass = $item->status === 'approved' ? 'bg-green-100 text-green-700' : ($item->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
                @endphp
                <tr class="border-t hover:bg-slate-50">
                    <td class="p-3">
                        <div class="font-bold text-slate-800">{{ $item->name }}</div>
                        <div class="text-xs text-slate-500">{{ $item->email }}</div>
                        <div class="text-xs text-slate-400 mt-1">{{ $item->created_at?->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="p-3 text-xs text-slate-600">
                        <div>No. HP: {{ $item->phone ?: '-' }}</div>
                        <div>Kategori: {{ $item->gender ?: '-' }}</div>
                        <div>Alamat: {{ $item->address ?: '-' }}</div>
                    </td>
                    <td class="p-3 text-xs text-slate-600">
                        <div class="font-bold uppercase">{{ $item->requested_via ?: 'manual' }}</div>
                        <div>{{ $item->google_id ? 'Terhubung Google' : 'Daftar biasa' }}</div>
                    </td>
                    <td class="p-3">
                        <span class="px-3 py-1 rounded-full text-xs font-black {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                        @if($item->approver)
                            <div class="text-xs text-slate-400 mt-2">oleh {{ $item->approver->name }}</div>
                        @endif
                        @if($item->admin_notes)
                            <div class="text-xs text-slate-500 italic mt-2">{{ $item->admin_notes }}</div>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($item->status === 'pending')
                            <div class="space-y-2">
                                <form method="POST" action="{{ route('admin.tenant-registrations.approve', $item) }}" onsubmit="return confirm('Setujui pendaftaran pengguna ini?')">
                                    @csrf
                                    <textarea name="admin_notes" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" placeholder="Catatan admin opsional"></textarea>
                                    <button class="mt-2 w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-xl text-xs font-black">Setujui</button>
                                </form>
                                <form method="POST" action="{{ route('admin.tenant-registrations.reject', $item) }}" onsubmit="return confirm('Tolak pendaftaran pengguna ini?')">
                                    @csrf
                                    <textarea name="admin_notes" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" placeholder="Alasan penolakan"></textarea>
                                    <button class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-xl text-xs font-black">Tolak</button>
                                </form>
                            </div>
                        @else
                            <span class="text-slate-500 text-xs font-bold">Sudah diproses.</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-400">Belum ada pendaftaran pengguna.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $requests->links() }}</div>
@endsection
