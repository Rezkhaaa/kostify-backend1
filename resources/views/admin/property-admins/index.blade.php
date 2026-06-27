@extends('layouts.admin')
@section('title','Manajemen Admin Kos')
@section('subtitle','Super Admin dapat membuat akun, reset password, serta aktif/nonaktifkan Admin Kos')
@section('content')
<div class="flex justify-between items-center mb-5">
    <div class="bg-white rounded-2xl px-5 py-4 border border-slate-200"><p class="text-xs text-slate-500">Total Admin Kos</p><b class="text-2xl">{{ $admins->total() }}</b></div>
    <a href="{{ route('admin.property-admins.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-2xl font-black"><i class="fa fa-plus mr-2"></i>Tambah Admin Kos</a>
</div>
<div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3 text-left">Nama</th><th class="p-3 text-left">Email</th><th class="p-3 text-left">Kos</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Aksi</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($admins as $adminUser)
                <tr>
                    <td class="p-3 font-bold">{{ $adminUser->name }}</td>
                    <td class="p-3">{{ $adminUser->email }}</td>
                    <td class="p-3">{{ $adminUser->property->name ?? '-' }}</td>
                    <td class="p-3"><span class="px-3 py-1 rounded-full text-xs font-black {{ $adminUser->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">{{ $adminUser->status === 'active' ? 'AKTIF' : 'NONAKTIF' }}</span></td>
                    <td class="p-3">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.property-admins.edit',$adminUser) }}" class="px-3 py-2 rounded-xl bg-amber-100 text-amber-700 font-bold text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.password-resets.send', $adminUser) }}" onsubmit="return confirm('Kirim kode reset password ke email Admin Kos ini?')">
                                @csrf
                                <button class="px-3 py-2 rounded-xl bg-teal-100 text-teal-700 font-bold text-xs">Kirim Reset Password</button>
                            </form>
                            <form method="POST" action="{{ route('admin.property-admins.toggle',$adminUser) }}">
                                @csrf
                                <button class="px-3 py-2 rounded-xl {{ $adminUser->status === 'active' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} font-bold text-xs">{{ $adminUser->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td class="p-6 text-center text-slate-500" colspan="5">Belum ada admin kos.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $admins->links() }}</div>
@endsection
