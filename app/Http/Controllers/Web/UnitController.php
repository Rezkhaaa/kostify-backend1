<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\{Property, Unit};
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        $units = Unit::visibleTo($admin)->with('property')->latest()->paginate(12);
        $unitStatsQuery = Unit::visibleTo($admin);
        return view('admin.units.index', compact('units', 'unitStatsQuery'));
    }

    public function create()
    {
        $properties = Property::active()->orderBy('name')->get();
        return view('admin.units.create', ['unit' => null, 'properties' => $properties]);
    }

    public function store(Request $request)
    {
        $admin = auth()->user();
        $rules = $this->rules();
        if ($admin->isSuperAdmin()) $rules['property_id'] = 'required|exists:properties,id';
        $request->validate($rules);

        $data = $request->all();
        $data['property_id'] = $admin->isSuperAdmin() ? $request->property_id : $admin->property_id;

        $property = Property::findOrFail($data['property_id']);
        if ($property->max_units && $property->units()->count() >= $property->max_units) {
            return back()->withErrors(['max_units' => 'Jumlah kamar sudah mencapai batas yang ditentukan pada Data Kos.'])->withInput();
        }

        $data['unit_code'] = 'KOS-' . strtoupper(Str::random(6));
        $data['facilities'] = $request->facilities ? array_values(array_filter(array_map('trim', explode(',', $request->facilities)))) : [];
        $data['capacity'] = $request->capacity ?: 1;

        $unit = Unit::create($data);
        ActivityService::log('create', 'unit', "Admin menambahkan {$unit->name}", $unit);

        return redirect()->route('admin.units.index')->with('success', 'Kamar kos berhasil ditambahkan.');
    }

    public function show(Unit $unit)
    {
        $this->ensureVisibleToAdmin($unit);
        $unit->load('property', 'activeOccupancy.user');
        return view('admin.units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $this->ensureVisibleToAdmin($unit);
        $properties = Property::active()->orderBy('name')->get();
        return view('admin.units.edit', compact('unit', 'properties'));
    }

    public function update(Request $request, Unit $unit)
    {
        $this->ensureVisibleToAdmin($unit);
        $admin = auth()->user();
        $rules = $this->rules();
        if ($admin->isSuperAdmin()) $rules['property_id'] = 'required|exists:properties,id';
        $request->validate($rules);

        $data = $request->all();
        if (! $admin->isSuperAdmin()) $data['property_id'] = $admin->property_id;
        $data['facilities'] = $request->facilities ? array_values(array_filter(array_map('trim', explode(',', $request->facilities)))) : [];
        $data['capacity'] = $request->capacity ?: 1;

        $unit->update($data);
        ActivityService::log('update', 'unit', "Admin memperbarui {$unit->name}", $unit);

        return redirect()->route('admin.units.index')->with('success', 'Data kamar kos berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $this->ensureVisibleToAdmin($unit);
        $unit->delete();
        return redirect()->route('admin.units.index')->with('success', 'Kamar kos berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'price_period' => 'required|in:bulanan,tahunan',
            'status' => 'required|in:available,occupied,maintenance,inactive',
            'floor' => 'nullable|integer|min:1',
            'area' => 'nullable|numeric|min:1',
            'capacity' => 'nullable|integer|min:1',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'facilities' => 'nullable|string',
        ];
    }


    protected function ensureVisibleToAdmin($unit): void
    {
        $admin = auth()->user();

        if (! $admin || (! $admin->isSuperAdmin() && (int) $unit->property_id !== (int) $admin->property_id)) {
            abort(403, 'Data kamar ini bukan milik kos Anda.');
        }
    }
}
