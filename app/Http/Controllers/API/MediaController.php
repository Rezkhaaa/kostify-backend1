<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Maintenance;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function paymentProof(Request $request, Payment $payment)
    {
        abort_unless((int) $payment->user_id === (int) $request->user()->id, 403, 'Anda tidak memiliki akses ke bukti pembayaran ini.');
        abort_unless($payment->proof_image, 404, 'Bukti pembayaran tidak ditemukan.');

        return $this->streamPublicFile($payment->proof_image);
    }

    public function complaintPhoto(Request $request, Complaint $complaint)
    {
        abort_unless((int) $complaint->user_id === (int) $request->user()->id, 403, 'Anda tidak memiliki akses ke foto komplain ini.');
        abort_unless($complaint->photo, 404, 'Foto komplain tidak ditemukan.');

        return $this->streamPublicFile($complaint->photo);
    }

    public function maintenancePhoto(Request $request, Maintenance $maintenance)
    {
        abort_unless((int) $maintenance->user_id === (int) $request->user()->id, 403, 'Anda tidak memiliki akses ke foto perbaikan ini.');
        abort_unless($maintenance->photo, 404, 'Foto perbaikan tidak ditemukan.');

        return $this->streamPublicFile($maintenance->photo);
    }

    protected function streamPublicFile(string $path)
    {
        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404, 'File tidak ditemukan.');

        return response()->file($disk->path($path), [
            'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
