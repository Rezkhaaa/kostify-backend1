@extends('layouts.admin')
@section('title', 'Daftar Komplain')
@section('subtitle', 'Pantau dan tindak lanjuti keluhan penghuni')

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Judul</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Penghuni</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Kamar</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Tanggal</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-black text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($complaints as $complaint)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-bold text-slate-800">{{ $complaint->title }}</td>
                    <td class="px-4 py-3 text-slate-700">{{ $complaint->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-slate-700">{{ $complaint->unit->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-slate-500">{{ $complaint->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        @php($statusClass = $complaint->status === 'resolved' ? 'bg-green-100 text-green-700' : ($complaint->status === 'rejected' ? 'bg-red-100 text-red-700' : ($complaint->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')))
                        <span class="px-3 py-1 rounded-full text-xs font-black {{ $statusClass }}">{{ ucfirst(str_replace('_',' ', $complaint->status)) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('admin.complaints.show', $complaint) }}" class="text-blue-700 bg-blue-50 px-3 py-1.5 rounded-xl text-xs font-black flex items-center gap-1">
                                <i class="fa fa-eye"></i> Respon
                            </a>
                            @if(in_array($complaint->status, ['resolved','rejected']))
                                <form method="POST" action="{{ route('admin.complaints.destroy', $complaint) }}" onsubmit="return confirm('Hapus komplain selesai ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700 bg-red-50 px-3 py-1.5 rounded-xl text-xs font-black flex items-center gap-1"><i class="fa fa-trash"></i> Hapus</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">Tidak ada komplain masuk</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $complaints->links() }}</div>
@endsection
