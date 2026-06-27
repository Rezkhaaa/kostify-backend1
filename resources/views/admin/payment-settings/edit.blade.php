@extends('layouts.admin')
@section('title','Rekening Pembayaran')
@section('subtitle','Atur nomor rekening yang tampil di aplikasi mobile saat pengguna upload bukti bayar')
@section('content')
<div class="grid md:grid-cols-3 gap-5">
    <div class="md:col-span-1 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 h-fit">
        <h3 class="text-lg font-black text-slate-900 mb-2">Input Rekening</h3>
        <p class="text-sm text-slate-500 mb-5">Data ini dipakai pada halaman Tagihan mobile: nama bank, nomor rekening, atas nama, dan arahan transfer.</p>

        <form method="POST" action="{{ route('admin.payment-settings.update') }}" class="space-y-4">
            @csrf
            @if(auth()->user()->isSuperAdmin())
                <div>
                    <label class="block text-sm font-black text-slate-700 mb-2">Kos</label>
                    <select name="property_id" class="w-full border border-slate-200 rounded-2xl px-4 py-3 bg-white">
                        <option value="">Default Platform</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Kos tertentu akan memakai rekening sendiri. Jika kosong, dipakai sebagai default.</p>
                </div>
            @endif

            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Nama Bank</label>
                <input name="bank_name" value="{{ old('bank_name', $defaultSetting->bank_name) }}" class="w-full border border-slate-200 rounded-2xl px-4 py-3" placeholder="Contoh: BCA / BRI / Mandiri" required>
            </div>

            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Nomor Rekening</label>
                <input name="account_number" value="{{ old('account_number', $defaultSetting->account_number) }}" class="w-full border border-slate-200 rounded-2xl px-4 py-3" placeholder="Contoh: 1234567890" required>
            </div>

            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Atas Nama</label>
                <input name="account_name" value="{{ old('account_name', $defaultSetting->account_name) }}" class="w-full border border-slate-200 rounded-2xl px-4 py-3" placeholder="Contoh: Kostify Residence" required>
            </div>

            <div>
                <label class="block text-sm font-black text-slate-700 mb-2">Arahan Pembayaran</label>
                <textarea name="instructions" rows="4" class="w-full border border-slate-200 rounded-2xl px-4 py-3" placeholder="Contoh: Transfer sesuai nominal tagihan, lalu upload bukti pembayaran.">{{ old('instructions', $defaultSetting->instructions) }}</textarea>
            </div>

            <label class="flex items-center gap-2 text-sm font-bold text-slate-600">
                <input type="checkbox" name="is_active" value="1" checked>
                Aktifkan rekening ini
            </label>

            <button class="w-full bg-teal-600 hover:bg-teal-700 text-white rounded-2xl px-5 py-3 font-black">Simpan Rekening</button>
        </form>
    </div>

    <div class="md:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-lg font-black text-slate-900">Daftar Rekening Aktif</h3>
            <p class="text-sm text-slate-500">Rekening berikut akan digunakan sistem saat pengguna membuka halaman pembayaran.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-3 text-left font-black text-slate-600">Kos</th>
                        <th class="p-3 text-left font-black text-slate-600">Bank</th>
                        <th class="p-3 text-left font-black text-slate-600">Nomor Rekening</th>
                        <th class="p-3 text-left font-black text-slate-600">Atas Nama</th>
                        <th class="p-3 text-left font-black text-slate-600">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settings as $setting)
                        <tr class="border-t">
                            <td class="p-3 font-bold text-slate-800">{{ $setting->property->name ?? 'Default Platform' }}</td>
                            <td class="p-3">{{ $setting->bank_name }}</td>
                            <td class="p-3 font-mono">{{ $setting->account_number }}</td>
                            <td class="p-3">{{ $setting->account_name }}</td>
                            <td class="p-3">
                                <span class="px-3 py-1 rounded-full text-xs font-black {{ $setting->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $setting->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-6 text-center text-slate-400" colspan="5">Belum ada rekening. Isi form di sebelah kiri terlebih dahulu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
