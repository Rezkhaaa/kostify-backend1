<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    /**
     * # Menampilkan semua data kos.
     * # Halaman ini khusus untuk Super Admin.
     */
    public function index()
    {
        $properties = Property::query()
            ->withCount([
                'units',
                'admins',
                'tenants',
            ])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.properties.index', compact('properties'));
    }

    /**
     * # Menampilkan form tambah data kos.
     */
    public function create()
    {
        return view('admin.properties.create', [
            'property' => null,
        ]);
    }

    /**
     * # Menyimpan data kos baru.
     * # Data kos ini nanti akan tampil di mobile tenant.
     */
    public function store(Request $request)
    {
        $data = $this->validatePropertyData($request);

        DB::transaction(function () use ($data) {
            // # Membuat slug unik untuk kos.
            // # Slug dipakai sebagai identitas URL/data agar lebih rapi.
            $data['slug'] = $this->makeUniqueSlug($data['name']);

            // # Jika status tidak dikirim dari form, otomatis dibuat active.
            $data['status'] = $data['status'] ?? 'active';

            // # Jika paket tidak dikirim dari form, otomatis dibuat Basic.
            $data['package_name'] = $data['package_name'] ?? 'Basic';

            $property = Property::create($data);

            ActivityService::log(
                'create',
                'property',
                "Super Admin menambahkan data kos {$property->name}",
                $property
            );
        });

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Data kos berhasil ditambahkan.');
    }

    /**
     * # Menampilkan form edit data kos.
     */
    public function edit(Property $property)
    {
        return view('admin.properties.edit', compact('property'));
    }

    /**
     * # Memperbarui data kos.
     * # Perubahan jenis kos putra/putri/campuran akan ikut terbaca di mobile.
     */
    public function update(Request $request, Property $property)
    {
        $data = $this->validatePropertyData($request);

        DB::transaction(function () use ($property, $data) {
            $property->update($data);

            ActivityService::log(
                'update',
                'property',
                "Super Admin memperbarui data kos {$property->name}",
                $property
            );
        });

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Data kos berhasil diperbarui.');
    }

    /**
     * # Mengaktifkan atau menonaktifkan data kos.
     * # Jika kos inactive, kos tidak ditampilkan sebagai pilihan aktif di mobile tenant.
     */
    public function toggle(Property $property)
    {
        DB::transaction(function () use ($property) {
            $newStatus = $property->status === 'active' ? 'inactive' : 'active';

            $property->update([
                'status' => $newStatus,
            ]);

            ActivityService::log(
                'toggle',
                'property',
                "Super Admin mengubah status kos {$property->name} menjadi {$newStatus}",
                $property
            );
        });

        return back()->with('success', 'Status kos berhasil diperbarui.');
    }

    /**
     * # Validasi data kos.
     * # gender_type:
     * # - putra     = hanya untuk penghuni putra
     * # - putri     = hanya untuk penghuni putri
     * # - campuran  = bisa untuk putra dan putri
     */
    private function validatePropertyData(Request $request): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'owner_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'gender_type' => [
                'required',
                Rule::in(['putra', 'putri', 'campuran']),
            ],

            'status' => [
                'nullable',
                Rule::in(['active', 'inactive']),
            ],

            'package_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'max_units' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);
    }

    /**
     * # Membuat slug unik agar tidak bentrok jika ada nama kos yang sama.
     * # Contoh:
     * # Kos Melati → kos-melati-a1b2
     */
    private function makeUniqueSlug(string $name): string
    {
        do {
            $slug = Str::slug($name) . '-' . strtolower(Str::random(4));
        } while (Property::where('slug', $slug)->exists());

        return $slug;
    }
}