@extends('layouts.admin')
@section('title', 'Detail Tagihan')
@section('subtitle', $billing->title)

@section('content')
<div class="py-2 max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-lg text-slate-800">{{ $billing->title }}</p>
                <p class="text-sm text-slate-500 mt-0.5">Dibuat {{ $billing->created_at->format('d M Y') }}</p>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                {{ $billing->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $billing->status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-6 border-t border-b border-slate-100 py-4">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Penghuni & Unit</p>
                <p class="font-semibold text-slate-800">{{ $billing->user->name }}</p>
                <p class="text-sm text-slate-500">Kamar: {{ $billing->unit->name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Informasi Waktu</p>
                <p class="text-sm text-slate-700">Periode: {{ $billing->billing_period_start->format('d M Y') }} - {{ $billing->billing_period_end->format('d M Y') }}</p>
                <p class="text-sm text-red-600 font-medium">Jatuh Tempo: {{ $billing->due_date->format('d M Y') }}</p>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase mb-3">Rincian Biaya</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-slate-600"><span>Biaya Pokok / Sewa</span><span>Rp {{ number_format($billing->amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-slate-600"><span>Pajak / Lain-lain</span><span>Rp {{ number_format($billing->tax, 0, ',', '.') }}</span></div>
                <div class="flex justify-between font-bold text-slate-800 border-t border-slate-100 pt-2 text-base">
                    <span>Total Tagihan</span><span>Rp {{ number_format($billing->amount + $billing->tax, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($billing->notes)
        <div class="bg-slate-50 rounded-lg p-4 text-sm text-slate-700">
            <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Catatan</p>
            {{ $billing->notes }}
        </div>
        @endif

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            @if($billing->status === 'unpaid')
            <form method="POST" action="{{ route('admin.billings.verify', $billing) }}">
                @csrf
                <button class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Konfirmasi Lunas
                </button>
            </form>
            @endif
            <a href="{{ route('admin.billings.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Kembali</a>
        </div>
    </div>
</div>
@endsection