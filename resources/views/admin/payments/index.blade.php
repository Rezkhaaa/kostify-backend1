@extends('layouts.admin')
@section('title','Verifikasi Pembayaran')
@section('subtitle','Cek bukti transfer penghuni lalu ubah status menjadi Success atau Fail')
@section('content')
<div class="grid md:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
        <p class="text-xs font-bold text-slate-500">Alur Pembayaran</p>
        <p class="text-sm text-slate-600 mt-1">Penghuni transfer manual lalu upload bukti dari aplikasi mobile.</p>
    </div>
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
        <p class="text-xs font-bold text-slate-500">Checking Admin</p>
        <p class="text-sm text-slate-600 mt-1">Status awal setelah bukti pembayaran dikirim.</p>
    </div>
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
        <p class="text-xs font-bold text-slate-500">Success</p>
        <p class="text-sm text-slate-600 mt-1">Tagihan otomatis menjadi lunas setelah dikonfirmasi.</p>
    </div>
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
        <p class="text-xs font-bold text-slate-500">Fail</p>
        <p class="text-sm text-slate-600 mt-1">Bukti palsu/tidak sesuai. Penghuni bisa upload ulang.</p>
    </div>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm align-top">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-3 text-left font-black text-slate-600">Pembayaran</th>
                    <th class="p-3 text-left font-black text-slate-600">Penghuni</th>
                    <th class="p-3 text-left font-black text-slate-600">Invoice</th>
                    <th class="p-3 text-left font-black text-slate-600">Transfer</th>
                    <th class="p-3 text-left font-black text-slate-600">Bukti</th>
                    <th class="p-3 text-left font-black text-slate-600">Status</th>
                    <th class="p-3 text-left font-black text-slate-600 min-w-[280px]">Aksi Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    @php
                        $statusClass = $p->status === 'success' ? 'bg-green-100 text-green-700' : ($p->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
                        $statusLabel = $p->status === 'success' ? 'Success' : ($p->status === 'failed' ? 'Fail' : 'Checking Admin');
                        $proofUrl = $p->proof_image ? route('admin.media.payments.proof', $p) : null;
                        $isPdf = $proofUrl && str_ends_with(strtolower($p->proof_image ?? ''), '.pdf');
                    @endphp
                    <tr class="border-t hover:bg-slate-50">
                        <td class="p-3">
                            <div class="font-mono text-xs text-slate-700">{{ $p->payment_code }}</div>
                            <div class="text-xs text-slate-400 mt-1">{{ $p->created_at?->format('d/m/Y H:i') }}</div>
                            <div class="mt-2 font-black text-teal-700">Rp {{ number_format($p->amount,0,',','.') }}</div>
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-slate-800">{{ $p->user->name ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $p->user->email ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $p->user->phone ?? '-' }}</div>
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-slate-700">{{ $p->billing->invoice_number ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $p->billing->title ?? '-' }}</div>
                            <div class="text-xs text-slate-400">Kamar: {{ $p->billing->unit->name ?? '-' }}</div>
                        </td>
                        <td class="p-3 text-xs text-slate-600 leading-6">
                            <div><b>Tujuan:</b> {{ $p->bank_name ?: '-' }} - {{ $p->bank_account_number ?: '-' }}</div>
                            <div><b>a.n.</b> {{ $p->bank_account_name ?: '-' }}</div>
                            <div><b>Pengirim:</b> {{ $p->sender_name ?: '-' }}</div>
                            <div><b>Bank:</b> {{ $p->sender_bank ?: '-' }}</div>
                            <div><b>Tanggal:</b> {{ $p->transfer_date?->format('d/m/Y') ?: '-' }}</div>
                            @if($p->notes)<div class="mt-1 italic text-slate-500">Catatan: {{ $p->notes }}</div>@endif
                        </td>
                        <td class="p-3">
                            @if($proofUrl)
                                @if($isPdf)
                                    <a href="{{ $proofUrl }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 text-slate-700 font-black text-xs">
                                        <i class="fa fa-file-pdf"></i> Lihat PDF
                                    </a>
                                @else
                                    <a href="{{ $proofUrl }}" target="_blank">
                                        <img src="{{ $proofUrl }}" alt="Bukti pembayaran" class="w-24 h-24 object-cover rounded-2xl border border-slate-200 shadow-sm">
                                    </a>
                                @endif
                            @else
                                <span class="text-slate-400 text-xs">Tidak ada bukti</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <span class="px-3 py-1 rounded-full text-xs font-black {{ $statusClass }}">{{ $statusLabel }}</span>
                            @if($p->confirmedBy)
                                <div class="text-xs text-slate-400 mt-2">oleh {{ $p->confirmedBy->name }}</div>
                            @endif
                            @if($p->confirmed_at)
                                <div class="text-xs text-slate-400">{{ $p->confirmed_at->format('d/m/Y H:i') }}</div>
                            @endif
                            @if($p->admin_note)
                                <div class="mt-2 text-xs text-slate-500 italic">{{ $p->admin_note }}</div>
                            @endif
                        </td>
                        <td class="p-3">
                            @if($p->status==='pending')
                                <div class="space-y-2">
                                    <form method="POST" action="{{ route('admin.payments.confirm',$p) }}" onsubmit="return confirm('Konfirmasi pembayaran ini? Tagihan akan berubah menjadi lunas.')">
                                        @csrf
                                        <textarea name="admin_note" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" placeholder="Catatan admin opsional"></textarea>
                                        <button class="mt-2 w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-xl text-xs font-black">Success & Lunaskan</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.payments.reject',$p) }}" onsubmit="return confirm('Tolak pembayaran ini?')">
                                        @csrf
                                        <textarea name="admin_note" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" placeholder="Alasan ditolak, misal bukti tidak valid"></textarea>
                                        <button class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-xl text-xs font-black">Fail / Tolak</button>
                                    </form>
                                </div>
                            @elseif($p->status==='success')
                                <span class="text-green-600 font-bold text-xs">Sudah Success</span>
                            @else
                                <span class="text-red-500 font-bold text-xs">Sudah Fail</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-slate-400" colspan="7">Belum ada pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $payments->links() }}</div>
@endsection
