<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\{Property, TenantPropertyStatus, User};
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index()
    {
        $admin = auth()->user();

        $query = User::where('role', 'tenant')->with(['property', 'propertyStatuses']);

        if (! $admin->isSuperAdmin()) {
            $propertyId = (int) $admin->property_id;
            $query->where(function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId)
                    ->orWhereHas('bookings', fn ($b) => $b->where('property_id', $propertyId))
                    ->orWhereHas('billings', fn ($b) => $b->where('property_id', $propertyId))
                    ->orWhereHas('complaints', fn ($c) => $c->where('property_id', $propertyId))
                    ->orWhereHas('maintenances', fn ($m) => $m->where('property_id', $propertyId))
                    ->orWhereHas('propertyStatuses', fn ($s) => $s->where('property_id', $propertyId));
            });
        }

        $tenants = $query->latest()->paginate(15);

        $tenants->getCollection()->transform(function (User $tenant) use ($admin) {
            $tenant->access_status = $admin->isSuperAdmin()
                ? ($tenant->status ?: 'active')
                : $tenant->statusForProperty((int) $admin->property_id);

            return $tenant;
        });

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $properties = Property::active()->orderBy('name')->get();
        return view('admin.tenants.create', ['tenant' => null, 'properties' => $properties]);
    }

    public function store(Request $request)
    {
        $admin = auth()->user();
        $rules = $this->rules();
        $rules['email'] = 'required|email|max:255|unique:users,email';
        $rules['password'] = 'required|string|min:6|confirmed';

        if ($admin->isSuperAdmin()) {
            $rules['property_id'] = 'nullable|exists:properties,id';
        }

        $data = $request->validate($rules);
        $propertyId = $admin->isSuperAdmin()
            ? ($data['property_id'] ?? null)
            : $admin->property_id;

        $accessStatus = $data['tenant_access_status'] ?? 'active';
        unset($data['tenant_access_status']);

        $data['property_id'] = $propertyId;
        $data['role'] = 'tenant';
        $data['status'] = $accessStatus;
        $data['password'] = Hash::make($data['password']);

        $tenant = User::create($data);

        if ($propertyId) {
            TenantPropertyStatus::updateOrCreate(
                ['user_id' => $tenant->id, 'property_id' => $propertyId],
                ['status' => $accessStatus]
            );
        }

        ActivityService::log('create', 'tenant', "Admin menambahkan pengguna {$tenant->name}", $tenant);

        return redirect()->route('admin.tenants.index')->with('success', 'Akun pengguna berhasil dibuat. Pengguna dapat login dari aplikasi mobile.');
    }

    public function show(User $tenant)
    {
        abort_unless($tenant->isTenant(), 404);
        $this->ensureTenantVisible($tenant);

        $admin = auth()->user();
        $propertyId = $this->contextPropertyId($tenant);
        $tenant->access_status = $admin->isSuperAdmin()
            ? ($tenant->status ?: 'active')
            : $tenant->statusForProperty($propertyId);

        $tenant->load(['property']);

        if ($admin->isSuperAdmin()) {
            $tenant->load(['bookings.unit', 'billings', 'complaints', 'maintenances', 'activeOccupancy.unit']);
        } else {
            $tenant->load([
                'bookings' => fn ($q) => $q->where('property_id', $propertyId)->with('unit'),
                'billings' => fn ($q) => $q->where('property_id', $propertyId),
                'complaints' => fn ($q) => $q->where('property_id', $propertyId),
                'maintenances' => fn ($q) => $q->where('property_id', $propertyId),
                'activeOccupancy' => fn ($q) => $q->where('property_id', $propertyId)->with('unit'),
            ]);
        }

        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(User $tenant)
    {
        abort_unless($tenant->isTenant(), 404);
        $this->ensureTenantVisible($tenant);

        $properties = Property::active()->orderBy('name')->get();
        $admin = auth()->user();
        $tenant->access_status = $admin->isSuperAdmin()
            ? ($tenant->status ?: 'active')
            : $tenant->statusForProperty((int) $admin->property_id);

        return view('admin.tenants.edit', compact('tenant', 'properties'));
    }

    public function update(Request $request, User $tenant)
    {
        abort_unless($tenant->isTenant(), 404);
        $this->ensureTenantVisible($tenant);

        $admin = auth()->user();
        $rules = $this->rules();
        $rules['email'] = ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($tenant->id)];
        $rules['password'] = 'nullable|string|min:6|confirmed';

        if ($admin->isSuperAdmin()) {
            $rules['property_id'] = 'nullable|exists:properties,id';
        }

        $data = $request->validate($rules);
        $accessStatus = $data['tenant_access_status'] ?? 'active';
        unset($data['tenant_access_status']);

        if (! $admin->isSuperAdmin()) {
            if (! $tenant->property_id) {
                $data['property_id'] = $admin->property_id;
            } else {
                unset($data['property_id']);
            }
        }

        $data['role'] = 'tenant';
        $data['status'] = $accessStatus;

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $tenant->update($data);

        $propertyId = $admin->isSuperAdmin()
            ? ($tenant->property_id ?: null)
            : $admin->property_id;

        if ($propertyId) {
            TenantPropertyStatus::updateOrCreate(
                ['user_id' => $tenant->id, 'property_id' => $propertyId],
                ['status' => $accessStatus]
            );
        }

        ActivityService::log('update', 'tenant', "Admin memperbarui pengguna {$tenant->name}", $tenant);

        return redirect()->route('admin.tenants.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleStatus(User $tenant)
    {
        abort_unless($tenant->isTenant(), 404);
        $this->ensureTenantVisible($tenant);

        $admin = auth()->user();

        if ($admin->isSuperAdmin()) {
            $newStatus = ($tenant->status ?: 'active') === 'active' ? 'inactive' : 'active';
            $tenant->update(['status' => $newStatus]);

            ActivityService::log(
                $newStatus === 'inactive' ? 'inactive' : 'active',
                'tenant',
                $newStatus === 'inactive'
                    ? "Super Admin menonaktifkan pengguna {$tenant->name}"
                    : "Super Admin mengaktifkan pengguna {$tenant->name}",
                $tenant
            );

            return back()->with('success', $newStatus === 'inactive' ? 'Pengguna berhasil dinonaktifkan.' : 'Pengguna berhasil diaktifkan kembali.');
        }

        $propertyId = (int) $admin->property_id;
        $currentStatus = $tenant->statusForProperty($propertyId);
        $newStatus = $currentStatus === 'active' ? 'inactive' : 'active';

        TenantPropertyStatus::updateOrCreate(
            ['user_id' => $tenant->id, 'property_id' => $propertyId],
            [
                'status' => $newStatus,
                'disabled_by' => $newStatus === 'inactive' ? $admin->id : null,
                'disabled_at' => $newStatus === 'inactive' ? now() : null,
                'reason' => $newStatus === 'inactive' ? 'Dinonaktifkan dari dashboard Admin Kos' : null,
            ]
        );

        ActivityService::log(
            $newStatus === 'inactive' ? 'inactive' : 'active',
            'tenant',
            $newStatus === 'inactive'
                ? "Admin menonaktifkan akses {$tenant->name} untuk kos ini"
                : "Admin mengaktifkan kembali akses {$tenant->name} untuk kos ini",
            $tenant
        );

        return back()->with(
            'success',
            $newStatus === 'inactive'
                ? 'Pengguna dinonaktifkan untuk kos Anda. Ia masih bisa mengakses kos lain yang aktif.'
                : 'Pengguna diaktifkan kembali untuk kos Anda.'
        );
    }

    private function rules(): array
    {
        return [
            'property_id' => 'nullable|exists:properties,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:putra,putri',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
            'id_card_number' => 'nullable|string|max:100',
            'tenant_access_status' => 'nullable|in:active,inactive',
        ];
    }

    private function contextPropertyId(User $tenant): ?int
    {
        $admin = auth()->user();
        return $admin->isSuperAdmin() ? ($tenant->property_id ? (int) $tenant->property_id : null) : (int) $admin->property_id;
    }

    private function ensureTenantVisible(User $tenant): void
    {
        $admin = auth()->user();
        if ($admin->isSuperAdmin()) {
            return;
        }

        $propertyId = (int) $admin->property_id;

        $visible = (int) $tenant->property_id === $propertyId
            || $tenant->bookings()->where('property_id', $propertyId)->exists()
            || $tenant->billings()->where('property_id', $propertyId)->exists()
            || $tenant->complaints()->where('property_id', $propertyId)->exists()
            || $tenant->maintenances()->where('property_id', $propertyId)->exists()
            || $tenant->propertyStatuses()->where('property_id', $propertyId)->exists();

        abort_unless($visible, 403, 'Pengguna ini tidak memiliki aktivitas pada kos Anda.');
    }
}
