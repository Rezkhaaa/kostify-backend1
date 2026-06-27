<form method="POST" action="{{ $tenant ? route('admin.tenants.update', $tenant) : route('admin.tenants.store') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 max-w-5xl">
    @csrf
    @if($tenant) @method('PUT') @endif

    <div class="grid md:grid-cols-2 gap-4">
        @if(auth()->user()->isSuperAdmin())
            <div>
                <label class="text-sm font-bold text-slate-700">Kos</label>
                <select name="property_id" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white">
                    <option value="">Pilih Kos</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" @selected(old('property_id', $tenant->property_id ?? '') == $property->id)>{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <label class="text-sm font-bold text-slate-700">Nama Pengguna</label>
            <input name="name" value="{{ old('name', $tenant->name ?? '') }}" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
        </div>

        <div>
            <label class="text-sm font-bold text-slate-700">Email Login Mobile</label>
            <input type="email" name="email" value="{{ old('email', $tenant->email ?? '') }}" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
        </div>

        <div>
            <label class="text-sm font-bold text-slate-700">Kategori Pengguna</label>
            <select name="gender" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white">
                <option value="putra" @selected(old('gender', $tenant->gender ?? '') === 'putra')>Pengguna Putra</option>
                <option value="putri" @selected(old('gender', $tenant->gender ?? '') === 'putri')>Pengguna Putri</option>
            </select>
            <p class="text-xs text-slate-400 mt-1">Dipakai backend untuk mencocokkan Kos Putra, Kos Putri, dan Kos Campuran.</p>
        </div>

        <div>
            <label class="text-sm font-bold text-slate-700">No. HP</label>
            <input name="phone" value="{{ old('phone', $tenant->phone ?? '') }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
        </div>

        <div>
            <label class="text-sm font-bold text-slate-700">Nomor Identitas</label>
            <input name="id_card_number" value="{{ old('id_card_number', $tenant->id_card_number ?? '') }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
        </div>

        <div>
            <label class="text-sm font-bold text-slate-700">Status Akses di Kos Ini</label>
            <select name="tenant_access_status" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white">
                <option value="active" @selected(old('tenant_access_status', $tenant->access_status ?? 'active') === 'active')>Aktif di kos ini</option>
                <option value="inactive" @selected(old('tenant_access_status', $tenant->access_status ?? '') === 'inactive')>Nonaktif di kos ini</option>
            </select>
            <p class="text-xs text-slate-400 mt-1">Status ini hanya berlaku untuk kos Anda. Pengguna tetap bisa memakai kos lain yang masih aktif.</p>
        </div>

        <div>
            <label class="text-sm font-bold text-slate-700">Password {{ $tenant ? '(kosongkan jika tidak diganti)' : '' }}</label>
            <input type="password" name="password" {{ $tenant ? '' : 'required' }} class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
        </div>

        <div>
            <label class="text-sm font-bold text-slate-700">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" {{ $tenant ? '' : 'required' }} class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
        </div>
    </div>

    <div class="mt-4">
        <label class="text-sm font-bold text-slate-700">Alamat</label>
        <textarea name="address" rows="3" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">{{ old('address', $tenant->address ?? '') }}</textarea>
    </div>

    <div class="flex gap-3 mt-6">
        <button class="bg-gradient-to-r from-blue-700 to-teal-600 text-white px-6 py-3 rounded-2xl font-black"><i class="fa fa-save mr-2"></i>Simpan</button>
        <a href="{{ route('admin.tenants.index') }}" class="px-6 py-3 rounded-2xl font-bold bg-slate-100 text-slate-600">Kembali</a>
    </div>
</form>
