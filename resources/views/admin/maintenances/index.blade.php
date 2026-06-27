@extends('layouts.admin')
@section('title', 'Jadwal Perbaikan Kamar')
@section('subtitle', 'Pantau perbaikan aset fisik kamar kos')

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Kamar</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Kategori</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Estimasi</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Penanggung</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Tanggal</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($maintenances as $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-bold text-slate-800">{{ $item->unit->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-slate-700">{{ ucfirst($item->category) }}</td>
                    <td class="px-4 py-3 text-slate-800 font-mono text-xs">Rp {{ number_format($item->cost ?? 0, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-slate-700">{{ $item->cost_payer_label }}</td>
                    <td class="px-4 py-3 text-xs text-slate-500">{{ $item->scheduled_date ? $item->scheduled_date->format('d M Y') : 'Belum Dijadwalkan' }}</td>
                    <td class="px-4 py-3">
                        @php($statusClass = $item->status === 'completed' ? 'bg-green-100 text-green-700' : ($item->status === 'rejected' ? 'bg-red-100 text-red-700' : ($item->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')))
                        <span class="px-3 py-1 rounded-full text-xs font-black {{ $statusClass }}">{{ ucfirst(str_replace('_',' ', $item->status)) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('admin.maintenances.show', $item) }}" class="text-blue-700 bg-blue-50 px-3 py-1.5 rounded-xl text-xs font-black flex items-center gap-1"><i class="fa fa-pencil"></i> Atur</a>
                            @if(in_array($item->status, ['completed','rejected']))
                                <form method="POST" action="{{ route('admin.maintenances.destroy', $item) }}" onsubmit="return confirm('Hapus perbaikan selesai ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700 bg-red-50 px-3 py-1.5 rounded-xl text-xs font-black flex items-center gap-1"><i class="fa fa-trash"></i> Hapus</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Belum ada agenda perbaikan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $maintenances->links() }}</div>
@endsection
