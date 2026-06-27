<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PropertyRegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OwnerRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.owner-register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email|unique:property_registration_requests,email',
            'phone' => 'nullable|string|max:30',
            'property_name' => 'required|string|max:255',
            'property_address' => 'nullable|string|max:1000',
            'gender_type' => 'required|in:putra,putri,campuran',
            'room_count' => 'nullable|integer|min:1|max:10000',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status'] = 'pending';
        PropertyRegistrationRequest::create($data);

        return redirect()
            ->route('admin.login')
            ->with('success', 'Pendaftaran pemilik kos berhasil dikirim. Tunggu approval dari Super Admin.');
    }
}
