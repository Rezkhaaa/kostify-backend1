<form method="POST" action="{{ $unit ? route('admin.units.update',$unit) : route('admin.units.store') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 max-w-5xl">
    @csrf
    @if($unit) @method('PUT') @endif
    @if(auth()->user()->isSuperAdmin())
        <div class="mb-4">
            <label class="text-sm font-bold text-slate-700">Kos / Properti</label>
            <select name="property_id" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white" required>
                <option value="">Pilih kos</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}" @selected(old('property_id', $unit->property_id ?? '') == $property->id)>{{ $property->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            <label class="text-sm font-bold text-slate-700">Nama Kamar</label>
            <input name="name" value="{{ old('name',$unit->name ?? '') }}" placeholder="Contoh: Kamar Kostify A-02" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 focus:ring-4 focus:ring-blue-100 focus:border-blue-600 outline-none" required>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Tipe Kamar</label>
            <select name="type" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5 bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-600 outline-none" required>
                <option value="">Pilih Tipe</option>
                @foreach(['standar'=>'Standar','premium'=>'Premium','deluxe'=>'Deluxe','ac'=>'Kamar AC','non_ac'=>'Kamar Non-AC','kamar_mandi_dalam'=>'Kamar Mandi Dalam','eksklusif'=>'Kos Eksklusif'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('type',$unit->type ?? '')===$value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Harga Sewa</label>
            <input type="number" name="price" value="{{ old('price',$unit->price ?? '') }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5" required>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Periode</label>
            <select name="price_period" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
                <option value="bulanan" @selected(old('price_period',$unit->price_period ?? '')==='bulanan')>Bulanan</option>
                <option value="tahunan" @selected(old('price_period',$unit->price_period ?? '')==='tahunan')>Tahunan</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-700">Status</label>
            <select name="status" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5">
                <option value="available" @selected(old('status',$unit->status ?? '')==='available')>Tersedia</option>
                <option value="occupied" @selected(old('status',$unit->status ?? '')==='occupied')>Terisi</option>
                <option value="maintenance" @selected(old('status',$unit->status ?? '')==='maintenance')>Perbaikan</option>
                <option value="inactive" @selected(old('status',$unit->status ?? '')==='inactive')>Nonaktif</option>
            </select>
        </div>
        <div><label class="text-sm font-bold text-slate-700">Lantai</label><input type="number" name="floor" value="{{ old('floor',$unit->floor ?? 1) }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5"></div>
        <div><label class="text-sm font-bold text-slate-700">Luas m²</label><input type="number" name="area" value="{{ old('area',$unit->area ?? '') }}" placeholder="Contoh: 12" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5"></div>
        <div><label class="text-sm font-bold text-slate-700">Kapasitas Penghuni</label><input type="number" name="capacity" value="{{ old('capacity',$unit->capacity ?? 1) }}" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5"></div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        <div><label class="text-sm font-bold text-slate-700">Alamat / Lokasi</label><textarea name="address" rows="3" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5" placeholder="Blok, nomor kamar, akses jalan">{{ old('address',$unit->address ?? '') }}</textarea></div>
        <div><label class="text-sm font-bold text-slate-700">Fasilitas</label><textarea name="facilities" rows="3" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5" placeholder="Pisahkan dengan koma">{{ old('facilities', isset($unit) && is_array($unit->facilities) ? implode(', ', $unit->facilities) : '') }}</textarea></div>
    </div>
    <div class="mt-4"><label class="text-sm font-bold text-slate-700">Deskripsi Kamar</label><textarea name="description" rows="4" class="mt-1 w-full border border-slate-200 rounded-2xl p-3.5" placeholder="Tuliskan kondisi kamar, fasilitas, dan keunggulan lokasi">{{ old('description',$unit->description ?? '') }}</textarea></div>
    <div class="flex gap-3 mt-6">
        <button class="bg-gradient-to-r from-blue-700 to-teal-600 hover:opacity-95 text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-blue-700/15"><i class="fa fa-save mr-2"></i>Simpan</button>
        <a href="{{ route('admin.units.index') }}" class="px-6 py-3 rounded-2xl font-bold bg-slate-100 text-slate-600">Kembali</a>
    </div>
</form>
