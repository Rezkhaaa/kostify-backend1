<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Services\BillingService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private BillingService $billingService) {}

    /**
     * Informasi rekening tujuan transfer manual yang ditampilkan di aplikasi mobile.
     */
    public function manualInfo(Request $request)
    {
        $propertyId = $request->user()?->property_id;
        $setting = PaymentSetting::forProperty($propertyId ? (int) $propertyId : null);

        return response()->json([
            'bank' => $setting->toMobileArray(),
            'rules' => [
                'max_file_mb' => 5,
                'allowed_files' => ['jpg', 'jpeg', 'png', 'webp', 'pdf'],
                'status_after_submit' => 'checking_admin',
            ],
        ]);
    }

    /**
     * Pembayaran manual: tenant transfer ke rekening admin lalu upload bukti bayar.
     * Status pembayaran selalu pending/checking admin sampai admin memverifikasi.
     */
    public function pay(Request $request, Billing $billing)
    {
        if ((int) $billing->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Tagihan ini bukan milik akun Anda.'], 403);
        }

        if (! $request->user()->isActiveForProperty((int) $billing->property_id)) {
            return response()->json(['message' => 'Akses pembayaran untuk kos ini sedang dinonaktifkan oleh admin kos.'], 403);
        }

        if ($billing->status === 'paid') {
            return response()->json(['message' => 'Tagihan sudah lunas.'], 422);
        }

        if (Payment::where('billing_id', $billing->id)->where('status', 'pending')->exists()) {
            return response()->json(['message' => 'Pembayaran sedang Checking Admin. Tunggu admin memverifikasi atau menolak pembayaran sebelumnya.'], 422);
        }

        $data = $request->validate([
            'sender_name' => 'required|string|max:120',
            'sender_bank' => 'nullable|string|max:120',
            'transfer_date' => 'nullable|date',
            'amount' => 'nullable|numeric|min:1',
            'notes' => 'nullable|string|max:1000',
            'proof_image' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'sender_name.required' => 'Nama pengirim wajib diisi.',
            'proof_image.required' => 'Bukti pembayaran wajib diupload.',
            'proof_image.mimes' => 'Bukti pembayaran harus berupa JPG, PNG, WEBP, atau PDF.',
            'proof_image.max' => 'Ukuran bukti pembayaran maksimal 5 MB.',
        ]);

        try {
            $paymentSetting = PaymentSetting::forProperty((int) $billing->property_id);

            $data['method'] = 'transfer';
            $data['proof_image'] = $request->file('proof_image')->store('payment-proofs/' . now()->format('Y/m'), 'public');
            $data['bank_name'] = $paymentSetting->bank_name;
            $data['bank_account_number'] = $paymentSetting->account_number;
            $data['bank_account_name'] = $paymentSetting->account_name;

            $payment = $this->billingService->processPayment($billing, $request->user(), $data);

            return response()->json([
                'message' => 'Bukti pembayaran terkirim. Status pembayaran sekarang Checking Admin.',
                'payment' => $payment,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

}
