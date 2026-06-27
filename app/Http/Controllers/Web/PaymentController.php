<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(private BillingService $billingService)
    {
    }

    public function index()
    {
        $payments = Payment::visibleTo(auth()->user())
            ->with([
                'user',
                'billing.unit',
                'confirmedBy',
                'property',
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function confirm(Request $request, Payment $payment)
    {
        $this->ensurePaymentVisibleToAdmin($payment);

        $data = $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        try {
            $this->billingService->confirmPayment($payment, Auth::user(), $data['admin_note'] ?? null);

            return back()->with('success', 'Pembayaran berhasil dikonfirmasi. Tagihan otomatis menjadi lunas.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Payment $payment)
    {
        $this->ensurePaymentVisibleToAdmin($payment);

        $data = $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        try {
            $this->billingService->rejectPayment($payment, Auth::user(), $data['admin_note'] ?? null);

            return back()->with('success', 'Pembayaran berhasil ditolak. Penghuni bisa mengirim ulang bukti pembayaran.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function ensurePaymentVisibleToAdmin(Payment $payment): void
    {
        $admin = auth()->user();

        abort_if(
            ! $admin->isSuperAdmin()
            && (int) $payment->property_id !== (int) $admin->property_id,
            403,
            'Pembayaran ini bukan milik kos Anda.'
        );
    }
}
