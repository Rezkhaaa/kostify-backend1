<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\{Property, User};
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PropertyAdminController extends Controller
{
    public function index()
    {
        $admins = User::whereIn('role', ['property_admin', 'admin'])->with('property')->latest()->paginate(15);
        return view('admin.property-admins.index', compact('admins'));
    }

    public function create()
    {
        $properties = Property::active()->orderBy('name')->get();
        return view('admin.property-admins.create', ['adminUser' => null, 'properties' => $properties]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|in:active,inactive',
        ]);

        $adminUser = User::create([
            'property_id' => $data['property_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'property_admin',
            'status' => $data['status'],
        ]);

        ActivityService::log('create', 'property_admin', "Super Admin membuat akun Admin Kos {$adminUser->name}", $adminUser);
        return redirect()->route('admin.property-admins.index')->with('success', 'Akun Admin Kos berhasil dibuat.');
    }

    public function edit(User $propertyAdmin)
    {
        abort_unless($propertyAdmin->isPropertyAdmin(), 404);
        $properties = Property::active()->orderBy('name')->get();
        return view('admin.property-admins.edit', ['adminUser' => $propertyAdmin, 'properties' => $properties]);
    }

    public function update(Request $request, User $propertyAdmin)
    {
        abort_unless($propertyAdmin->isPropertyAdmin(), 404);

        $data = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($propertyAdmin->id)],
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'required|in:active,inactive',
        ]);

        $payload = collect($data)->except(['password', 'password_confirmation'])->toArray();
        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $propertyAdmin->update($payload);
        ActivityService::log('update', 'property_admin', "Super Admin memperbarui akun Admin Kos {$propertyAdmin->name}", $propertyAdmin);
        return redirect()->route('admin.property-admins.index')->with('success', 'Akun Admin Kos berhasil diperbarui.');
    }

    public function toggle(User $propertyAdmin)
    {
        abort_unless($propertyAdmin->isPropertyAdmin(), 404);
        $propertyAdmin->update(['status' => $propertyAdmin->status === 'active' ? 'inactive' : 'active']);
        ActivityService::log('toggle', 'property_admin', "Super Admin mengubah status Admin Kos {$propertyAdmin->name} menjadi {$propertyAdmin->status}", $propertyAdmin);
        return back()->with('success', 'Status Admin Kos berhasil diperbarui.');
    }
}
