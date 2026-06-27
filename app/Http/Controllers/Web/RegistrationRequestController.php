<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\{ActivityHistory, Property, PropertyRegistrationRequest, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrationRequestController extends Controller
{
    public function index()
    {
        $requests = PropertyRegistrationRequest::with(['createdProperty', 'createdAdmin'])
            ->latest()
            ->paginate(12);

        return view('admin.registration-requests.index', compact('requests'));
    }

    public function approve(PropertyRegistrationRequest $registrationRequest)
    {
        if ($registrationRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        if (User::where('email', $registrationRequest->email)->exists()) {
            $registrationRequest->update([
                'status' => 'rejected',
                'admin_notes' => 'Email sudah digunakan akun lain.',
                'rejected_at' => now(),
            ]);

            return back()->with('error', 'Email sudah digunakan akun lain.');
        }

        DB::transaction(function () use ($registrationRequest) {
            $property = Property::create([
                'name' => $registrationRequest->property_name,
                'slug' => Str::slug($registrationRequest->property_name) . '-' . strtolower(Str::random(4)),
                'owner_name' => $registrationRequest->owner_name,
                'phone' => $registrationRequest->phone,
                'address' => $registrationRequest->property_address,
                'gender_type' => $registrationRequest->gender_type ?: 'campuran',
                'status' => 'active',
                'package_name' => 'Basic',
                'max_units' => $registrationRequest->room_count,
                'notes' => 'Dibuat otomatis dari pendaftaran pemilik kos.',
            ]);

            $adminUser = User::create([
                'property_id' => $property->id,
                'name' => $registrationRequest->owner_name,
                'email' => $registrationRequest->email,
                'password' => $registrationRequest->password,
                'role' => 'property_admin',
                'phone' => $registrationRequest->phone,
                'address' => $registrationRequest->property_address,
                'gender_type' => $registrationRequest->gender_type ?: 'campuran',
                'status' => 'active',
            ]);

            $registrationRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'created_property_id' => $property->id,
                'created_admin_id' => $adminUser->id,
            ]);

            ActivityHistory::create([
                'property_id' => $property->id,
                'user_id' => auth()->id(),
                'action' => 'approve',
                'module' => 'pendaftaran_kos',
                'description' => 'Super Admin menyetujui pendaftaran ' . $property->name . ' dan membuat akun Admin Kos.',
                'subject_id' => $registrationRequest->id,
                'subject_type' => PropertyRegistrationRequest::class,
            ]);
        });

        return back()->with('success', 'Pendaftaran disetujui. Data kos dan akun Admin Kos berhasil dibuat.');
    }

    public function reject(Request $request, PropertyRegistrationRequest $registrationRequest)
    {
        if ($registrationRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        $data = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $registrationRequest->update([
            'status' => 'rejected',
            'admin_notes' => $data['admin_notes'] ?? 'Ditolak Super Admin.',
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran pemilik kos ditolak.');
    }
}
