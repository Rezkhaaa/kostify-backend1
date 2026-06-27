<form method="POST" action="{{ $adminUser ? route('admin.property-admins.update', $adminUser) : route('admin.property-admins.store') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 max-w-5xl">
    @csrf
    @if($adminUser) @method('PUT') @endif
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-bold text-slate-700">Kos yang Dikelola</label>
            <select name="property_id" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white">
                <option value="">Pilih Kos</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}" @selected(old('property_id', $adminUser->property_id ?? '') == $property->id)>{{ $property->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Nama Admin</label>
            <input name="name" value="{{ old('name', $adminUser->name ?? '') }}" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Email Login</label>
            <input type="email" name="email" value="{{ old('email', $adminUser->email ?? '') }}" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">No. HP</label>
            <input name="phone" value="{{ old('phone', $adminUser->phone ?? '') }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
        </div>
        @if(! $adminUser)
            <div>
                <label class="text-sm font-bold text-slate-700">Password Awal</label>
                <input type="password" name="password" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
            </div>
        @else
            <div class="md:col-span-2 bg-teal-50 border border-teal-100 rounded-2xl p-4 text-sm text-teal-900">
                Untuk reset password Admin Kos, gunakan tombol <b>Kirim Reset Password</b> di halaman daftar Admin Kos. Admin membuat kata sandi baru sendiri melalui kode email.
            </div>
        @endif
        <div>
            <label class="text-sm font-bold text-slate-700">Status</label>
            <select name="status" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white">
                <option value="active" @selected(old('status', $adminUser->status ?? 'active') === 'active')>Aktif</option>
                <option value="inactive" @selected(old('status', $adminUser->status ?? '') === 'inactive')>Nonaktif</option>
            </select>
        </div>
    </div>
    <div class="mt-4">
        <label class="text-sm font-bold text-slate-700">Alamat Admin</label>
        <textarea name="address" rows="3" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">{{ old('address', $adminUser->address ?? '') }}</textarea>
    </div>
    <div class="flex gap-3 mt-6">
        <button class="bg-gradient-to-r from-blue-700 to-teal-600 text-white px-6 py-3 rounded-2xl font-black"><i class="fa fa-save mr-2"></i>Simpan</button>
        <a href="{{ route('admin.property-admins.index') }}" class="px-6 py-3 rounded-2xl font-bold bg-slate-100 text-slate-600">Kembali</a>
    </div>
</form>
