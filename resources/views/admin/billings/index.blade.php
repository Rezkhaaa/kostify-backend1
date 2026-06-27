@extends('layouts.admin')
@section('title', 'Manajemen Tagihan')
@section('subtitle', 'Pantau dan kelola tagihan penghuni')

@section('content')
<div class="py-2">
    <div class="mb-4 flex justify-between items-center gap-3">
        <a href="{{ route('admin.billings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-700 to-teal-600 text-white rounded-2xl text-sm font-black transition-colors shadow-lg shadow-blue-700/10">
            <i class="fa fa-plus"></i> Buat Tagihan
        </a>
        <span class="text-xs font-bold text-slate-500">Tagihan lunas dapat dihapus agar daftar tetap rapi.</span>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-black text-slate-600">Judul</th>
                        <th class="px-4 py-3 text-left font-black text-slate-600">Penghuni</th>
                        <th class="px-4 py-3 text-left font-black text-slate-600">Kamar</th>
                        <th class="px-4 py-3 text-left font-black text-slate-600">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-left font-black text-slate-600">Total</th>
                        <th class="px-4 py-3 text-left font-black text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left font-black text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($billings as $billing)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-bold text-slate-800">{{ $billing->title }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $billing->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $billing->unit->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $billing->due_date?->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-bold text-slate-800">Rp {{ number_format($billing->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full text-xs font-black {{ $billing->status === 'paid' ? 'bg-green-100 text-green-700' : ($billing->status === 'overdue' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ $billing->status === 'paid' ? 'Lunas' : ($billing->status === 'overdue' ? 'Terlambat' : 'Belum Bayar') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.billings.show', $billing) }}" class="text-blue-700 bg-blue-50 px-3 py-1.5 rounded-xl text-xs font-black flex items-center gap-1">
                                    <i class="fa fa-eye"></i> Detail
                                </a>
                                @if($billing->status === 'paid')
                                    <form method="POST" action="{{ route('admin.billings.destroy', $billing) }}" onsubmit="return confirm('Hapus tagihan lunas ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-700 bg-red-50 px-3 py-1.5 rounded-xl text-xs font-black flex items-center gap-1">
                                            <i class="fa fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Belum ada data tagihan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">{{ $billings->links() }}</div>
    </div>
</div>
@endsection
