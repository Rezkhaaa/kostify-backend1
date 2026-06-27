<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TenantRegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantRegistrationController extends Controller
{
    public function index()
    {
        $requests = TenantRegistrationRequest::with('approver')->latest()->paginate(15);
        return view('admin.tenant-registrations.index', compact('requests'));
    }

    public function approve(Request $request, TenantRegistrationRequest $tenantRegistration)
    {
        if ($tenantRegistration->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        DB::transaction(function () use ($request, $tenantRegistration) {
            $user = User::create([
                'name' => $tenantRegistration->name,
                'email' => $tenantRegistration->email,
                'phone' => $tenantRegistration->phone,
                'address' => $tenantRegistration->address,
                'password' => $tenantRegistration->password ?: Hash::make(Str::random(40)),
                'google_id' => $tenantRegistration->google_id,
                'avatar' => $tenantRegistration->avatar,
                'gender' => $tenantRegistration->gender,
                'role' => 'tenant',
                'status' => 'active',
            ]);

            $tenantRegistration->update([
                'status' => 'approved',
                'admin_notes' => $request->input('admin_notes'),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'created_user_id' => $user->id,
            ]);
        });

        return back()->with('success', 'Pendaftaran pengguna disetujui dan akun tenant berhasil dibuat.');
    }

    public function reject(Request $request, TenantRegistrationRequest $tenantRegistration)
    {
        if ($tenantRegistration->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $tenantRegistration->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes'),
            'approved_by' => auth()->id(),
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran pengguna berhasil ditolak.');
    }
}
