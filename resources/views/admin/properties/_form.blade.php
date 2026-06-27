<form method="POST" action="{{ $property ? route('admin.properties.update', $property) : route('admin.properties.store') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 max-w-5xl">
    @csrf
    @if($property) @method('PUT') @endif
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-bold text-slate-700">Nama Kos</label>
            <input name="name" value="{{ old('name', $property->name ?? '') }}" required class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5" placeholder="Contoh: Kos Melati Residence">
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Nama Pemilik</label>
            <input name="owner_name" value="{{ old('owner_name', $property->owner_name ?? '') }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5" placeholder="Contoh: Ibu Rina">
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">No. HP</label>
            <input name="phone" value="{{ old('phone', $property->phone ?? '') }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5" placeholder="08xxxxxxxxxx">
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Jenis Kos</label>
            <select name="gender_type" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white" required>
                @foreach(['putra' => 'Kos Putra', 'putri' => 'Kos Putri', 'campuran' => 'Kos Campuran'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('gender_type', $property->gender_type ?? 'campuran') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Paket</label>
            <select name="package_name" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white">
                @foreach(['Basic','Standard','Premium','Custom'] as $pkg)
                    <option value="{{ $pkg }}" @selected(old('package_name', $property->package_name ?? 'Basic') === $pkg)>{{ $pkg }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Jumlah Kamar / Kapasitas Kos</label>
            <input type="number" name="max_units" value="{{ old('max_units', $property->max_units ?? '') }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5" placeholder="Contoh: 20">
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Status</label>
            <select name="status" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white">
                <option value="active" @selected(old('status', $property->status ?? 'active') === 'active')>Aktif</option>
                <option value="inactive" @selected(old('status', $property->status ?? '') === 'inactive')>Nonaktif</option>
            </select>
        </div>
    </div>
    <div class="mt-4">
        <label class="text-sm font-bold text-slate-700">Alamat Kos</label>
        <textarea name="address" rows="3" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">{{ old('address', $property->address ?? '') }}</textarea>
    </div>
    <div class="mt-4">
        <label class="text-sm font-bold text-slate-700">Catatan</label>
        <textarea name="notes" rows="3" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">{{ old('notes', $property->notes ?? '') }}</textarea>
    </div>
    <div class="flex gap-3 mt-6">
        <button class="bg-gradient-to-r from-blue-700 to-teal-600 text-white px-6 py-3 rounded-2xl font-black"><i class="fa fa-save mr-2"></i>Simpan</button>
        <a href="{{ route('admin.properties.index') }}" class="px-6 py-3 rounded-2xl font-bold bg-slate-100 text-slate-600">Kembali</a>
    </div>
</form>
