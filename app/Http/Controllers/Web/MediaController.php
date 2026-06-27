<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Maintenance;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function paymentProof(Payment $payment)
    {
        $this->authorizePropertyAccess($payment->property_id);
        abort_unless($payment->proof_image, 404, 'Bukti pembayaran tidak ditemukan.');
        return $this->streamPublicFile($payment->proof_image);
    }

    public function complaintPhoto(Complaint $complaint)
    {
        $this->authorizePropertyAccess($complaint->property_id);
        abort_unless($complaint->photo, 404, 'Foto komplain tidak ditemukan.');
        return $this->streamPublicFile($complaint->photo);
    }

    public function maintenancePhoto(Maintenance $maintenance)
    {
        $this->authorizePropertyAccess($maintenance->property_id);
        abort_unless($maintenance->photo, 404, 'Foto perbaikan tidak ditemukan.');
        return $this->streamPublicFile($maintenance->photo);
    }

    protected function authorizePropertyAccess(?int $propertyId): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        abort_unless($user, 403);

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        abort_if((int) $user->property_id !== (int) $propertyId, 403, 'Anda tidak memiliki akses ke file ini.');
    }

    protected function streamPublicFile(string $path)
    {
        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404, 'File tidak ditemukan.');

        $absolutePath = $disk->path($path);
        $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
