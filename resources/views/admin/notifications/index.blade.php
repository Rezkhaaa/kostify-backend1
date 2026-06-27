@extends('layouts.admin')
@section('title', 'Log Notifikasi')
@section('content')
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
        <h3 class="font-black text-slate-900">Riwayat Notifikasi</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-black">Waktu</th>
                    <th class="px-4 py-3 text-left font-black">Penghuni</th>
                    <th class="px-4 py-3 text-left font-black">Kos</th>
                    <th class="px-4 py-3 text-left font-black">Judul</th>
                    <th class="px-4 py-3 text-left font-black">Pesan</th>
                    <th class="px-4 py-3 text-left font-black">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-t border-slate-100 align-top">
                        <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 font-bold text-slate-800">{{ $log->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $log->property->name ?? '-' }}</td>
                        <td class="px-4 py-3 font-bold text-slate-900">{{ $log->title }}</td>
                        <td class="px-4 py-3 text-slate-600 max-w-md">{{ $log->message }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusClass = match($log->status) {
                                    'sent' => 'bg-green-50 text-green-700',
                                    'failed' => 'bg-red-50 text-red-700',
                                    'skipped' => 'bg-yellow-50 text-yellow-700',
                                    default => 'bg-blue-50 text-blue-700',
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full font-black text-xs {{ $statusClass }}">{{ strtoupper($log->status) }}</span>
                            @if($log->error_message)
                                <p class="text-xs text-red-600 mt-2">{{ $log->error_message }}</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500 font-bold">Belum ada notifikasi yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">{{ $logs->links() }}</div>
@endsection
