@extends('layouts.admin')
@section('title', 'Buat Tagihan Baru')

@section('content')
<div class="py-2 max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('admin.billings.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Penghuni</label>
                    <select name="user_id" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        <option value="">-- Pilih Penghuni --</option>
                        @foreach($penghunis as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kamar</label>
                    <select name="unit_id" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        <option value="">-- Pilih Kamar Kos --</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Tagihan</label>
                <input type="text" name="title" required value="{{ old('title') }}"
                       placeholder="contoh: Sewa Bulan Juli 2025"
                       class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah (Rp)</label>
                    <input type="number" name="amount" required min="0" value="{{ old('amount') }}"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pajak (Rp)</label>
                    <input type="number" name="tax" min="0" value="{{ old('tax', 0) }}"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Periode Mulai</label>
                    <input type="date" name="billing_period_start" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Periode Akhir</label>
                    <input type="date" name="billing_period_end" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Jatuh Tempo</label>
                    <input type="date" name="due_date" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan (opsional)</label>
                <textarea name="notes" rows="3"
                          class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                          placeholder="Catatan tambahan..."></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fa fa-save"></i> Simpan Tagihan
                </button>
                <a href="{{ route('admin.billings.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection