<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Services\PasswordResetCodeService;
use Illuminate\Http\Request;

class PasswordResetRequestController extends Controller
{
    public function index()
    {
        $requests = PasswordResetRequest::visibleTo(auth()->user())
            ->with('property', 'handler')
            ->latest()
            ->paginate(12);

        return view('admin.password-reset-requests.index', compact('requests'));
    }

    public function sendForUser(User $user, PasswordResetCodeService $resetService)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        abort_if($user->isSuperAdmin(), 403, 'Reset password Super Admin tidak dapat dikirim dari menu ini.');

        $resetService->sendCodeToUser(
            $user,
            auth()->user(),
            'Super Admin mengirim bantuan reset password. Pengguna tetap membuat password baru sendiri melalui kode email.'
        );

        return back()->with('success', 'Kode reset password sudah dikirim ke email '.$user->email.'. Super Admin tidak mengetahui password baru pengguna.');
    }

    public function reject(Request $request, PasswordResetRequest $passwordResetRequest)
    {
        $this->ensureVisibleToAdmin($passwordResetRequest);

        if ($passwordResetRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses.');
        }

        $data = $request->validate(['admin_notes' => 'nullable|string|max:1000']);

        $passwordResetRequest->update([
            'status' => 'rejected',
            'admin_notes' => $data['admin_notes'] ?? 'Ditolak admin.',
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);

        return back()->with('success', 'Permintaan reset password ditolak.');
    }
}
