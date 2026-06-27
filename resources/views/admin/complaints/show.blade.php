@extends('layouts.admin')
@section('title', 'Detail Komplain')
@section('subtitle', $complaint->complaint_code)
@section('content')
<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="font-mono text-teal-600 font-bold">{{ $complaint->complaint_code }}</p>
                <h3 class="text-2xl font-black text-slate-900">{{ $complaint->title }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ $complaint->user->name ?? '-' }} · {{ $complaint->unit->name ?? '-' }} · {{ $complaint->created_at->format('d M Y H:i') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black {{ $complaint->status === 'resolved' ? 'bg-green-100 text-green-700' : ($complaint->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst(str_replace('_',' ', $complaint->status)) }}</span>
        </div>
        <div class="grid grid-cols-2 gap-3 mt-5">
            <div class="bg-slate-50 rounded-2xl p-4"><p class="text-xs text-slate-500">Kategori</p><b>{{ ucfirst($complaint->category) }}</b></div>
            <div class="bg-slate-50 rounded-2xl p-4"><p class="text-xs text-slate-500">Prioritas</p><b>{{ ucfirst($complaint->priority) }}</b></div>
        </div>
        <div class="mt-5 bg-slate-50 rounded-2xl p-5"><p class="text-xs font-black text-slate-500 uppercase mb-2">Isi Komplain</p><p class="text-slate-700 leading-relaxed">{{ $complaint->description }}</p></div>

        @php($complaintPhotoUrl = $complaint->photo ? route('admin.media.complaints.photo', $complaint) : null)
        @if($complaintPhotoUrl)
            <div class="mt-5 bg-white border border-slate-200 rounded-2xl p-5">
                <p class="text-xs font-black text-slate-500 uppercase mb-3">Foto Bukti Komplain</p>
                <a href="{{ $complaintPhotoUrl }}" target="_blank" class="inline-block">
                    <img src="{{ $complaintPhotoUrl }}" alt="Foto bukti komplain" class="max-h-72 rounded-2xl border border-slate-200 shadow-sm object-cover">
                </a>
                <p class="text-xs text-slate-400 mt-2">Klik gambar untuk membuka ukuran penuh.</p>
            </div>
        @endif

        @if($complaint->admin_response)
            <div class="mt-4 bg-teal-50 rounded-2xl p-5 text-teal-800"><p class="text-xs font-black uppercase mb-2">Respons Admin</p>{{ $complaint->admin_response }}</div>
        @endif
    </div>
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <h4 class="font-black text-slate-900 mb-4">Status Komplain</h4>
        <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="text-sm font-bold text-slate-700">Status</label>
                <select name="status" class="mt-1 w-full border border-slate-200 rounded-2xl p-3">
                    <option value="pending" @selected($complaint->status==='pending')>Menunggu</option>
                    <option value="approved" @selected($complaint->status==='approved')>Disetujui</option>
                    <option value="in_progress" @selected($complaint->status==='in_progress')>Diproses</option>
                    <option value="resolved" @selected($complaint->status==='resolved')>Selesai</option>
                    <option value="rejected" @selected($complaint->status==='rejected')>Ditolak</option>
                </select>
            </div>
            <div><label class="text-sm font-bold text-slate-700">Tanggapan ke Penghuni</label><textarea name="admin_response" rows="5" class="mt-1 w-full border border-slate-200 rounded-2xl p-3" placeholder="Tulis tanggapan admin">{{ old('admin_response', $complaint->admin_response) }}</textarea></div>
            <button class="w-full bg-gradient-to-r from-blue-700 to-teal-600 text-white rounded-2xl py-3 font-black">Simpan Status</button>
            <a href="{{ route('admin.complaints.index') }}" class="block text-center rounded-2xl py-3 font-bold bg-slate-100 text-slate-600">Kembali</a>
        </form>

        @if(in_array($complaint->status, ['resolved','rejected']))
            <form method="POST" action="{{ route('admin.complaints.destroy', $complaint) }}" class="mt-4" onsubmit="return confirm('Hapus komplain ini?')">
                @csrf @method('DELETE')
                <button class="w-full bg-red-50 text-red-700 rounded-2xl py-3 font-black">Hapus Komplain</button>
            </form>
        @endif
    </div>
</div>
@endsection
