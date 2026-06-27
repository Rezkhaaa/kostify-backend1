<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Payment;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingService
{
    public function createBilling(array $data, User $admin): Billing
    {
        $amount = (float) $data['amount'];
        $tax = (float) ($data['tax'] ?? 0);
        $unit = Unit::findOrFail($data['unit_id']);
        $propertyId = $data['property_id'] ?? $unit->property_id ?? $admin->property_id;

        $billing = Billing::create([
            'property_id' => $propertyId,
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'user_id' => $data['user_id'],
            'unit_id' => $data['unit_id'],
            'occupancy_id' => $data['occupancy_id'] ?? null,
            'title' => $data['title'],
            'amount' => $amount,
            'tax' => $tax,
            'total_amount' => $amount + $tax,
            'billing_period_start' => $data['billing_period_start'],
            'billing_period_end' => $data['billing_period_end'],
            'due_date' => $data['due_date'],
            'status' => 'unpaid',
            'notes' => $data['notes'] ?? null,
            'created_by' => $admin->id,
        ]);

        ActivityService::log('create', 'billing', "Admin {$admin->name} membuat tagihan {$billing->invoice_number}", $billing);

        $billing->loadMissing('user', 'unit.property');
        if ($billing->user) {
            app(OneSignalService::class)->sendToUser(
                $billing->user,
                'Tagihan baru tersedia',
                "Tagihan {$billing->title} sebesar Rp " . number_format((float) $billing->total_amount, 0, ',', '.') . ' telah dibuat.',
                [
                    'type' => 'billing_created',
                    'billing_id' => $billing->id,
                    'property_id' => $billing->property_id,
                    'unit_id' => $billing->unit_id,
                ]
            );
        }

        return $billing;
    }

    public function processPayment(Billing $billing, User $tenant, array $data): Payment
    {
        return DB::transaction(function () use ($billing, $tenant, $data) {
            $billing->refresh();

            if ((int) $billing->user_id !== (int) $tenant->id) {
                throw new \Exception('Tagihan ini bukan milik akun Anda.');
            }

            if (! $tenant->isActiveForProperty((int) $billing->property_id)) {
                throw new \Exception('Akses pembayaran untuk kos ini sedang dinonaktifkan oleh admin kos.');
            }

            if ($billing->status === 'paid') {
                throw new \Exception('Tagihan sudah dibayar.');
            }

            if (Payment::where('billing_id', $billing->id)->where('status', 'pending')->exists()) {
                throw new \Exception('Pembayaran sedang Checking Admin. Tunggu admin memverifikasi atau menolak pembayaran sebelumnya.');
            }

            $payment = Payment::create([
                'property_id' => $billing->property_id,
                'payment_code' => 'PAY-' . now()->format('ymd') . '-' . strtoupper(Str::random(6)),
                'billing_id' => $billing->id,
                'user_id' => $tenant->id,
                'amount' => $data['amount'] ?? $billing->total_amount,
                'method' => 'transfer',
                'status' => 'pending',
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'sender_name' => $data['sender_name'] ?? $tenant->name,
                'sender_bank' => $data['sender_bank'] ?? null,
                'transfer_date' => $data['transfer_date'] ?? now()->toDateString(),
                'proof_image' => $data['proof_image'] ?? null,
                'bank_name' => $data['bank_name'] ?? config('services.bank_transfer.bank_name'),
                'bank_account_number' => $data['bank_account_number'] ?? config('services.bank_transfer.account_number'),
                'bank_account_name' => $data['bank_account_name'] ?? config('services.bank_transfer.account_name'),
            ]);

            ActivityService::log(
                'payment_confirmation',
                'payment',
                "Penghuni {$tenant->name} mengirim bukti pembayaran {$billing->invoice_number}. Status: Checking Admin.",
                $payment
            );

            app(OneSignalService::class)->sendToUser(
                $tenant,
                'Bukti pembayaran terkirim',
                "Bukti pembayaran {$billing->invoice_number} sudah dikirim. Mohon tunggu verifikasi admin.",
                [
                    'type' => 'payment_submitted',
                    'payment_id' => $payment->id,
                    'billing_id' => $billing->id,
                    'property_id' => $billing->property_id,
                ]
            );

            app(AdminPaymentNotificationService::class)->notifyPaymentSubmitted($payment->loadMissing('user', 'billing.unit', 'property'));

            return $payment->load('billing.unit');
        });
    }

    public function confirmPayment(Payment $payment, User $admin, ?string $adminNote = null): Payment
    {
        return DB::transaction(function () use ($payment, $admin, $adminNote) {
            $payment->refresh();

            if ($payment->status !== 'pending') {
                throw new \Exception('Pembayaran ini sudah diproses.');
            }

            if (! $admin->isSuperAdmin() && (int) $payment->property_id !== (int) $admin->property_id) {
                throw new \Exception('Pembayaran ini bukan milik kos Anda.');
            }

            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
                'confirmed_by' => $admin->id,
                'confirmed_at' => now(),
                'admin_note' => $adminNote,
            ]);

            $payment->billing()->update(['status' => 'paid']);
            ActivityService::log('confirm', 'payment', "Admin {$admin->name} mengonfirmasi pembayaran {$payment->payment_code}", $payment);

            $payment->loadMissing('user', 'billing');
            if ($payment->user) {
                app(OneSignalService::class)->sendToUser(
                    $payment->user,
                    'Pembayaran berhasil',
                    "Pembayaran {$payment->billing->invoice_number} sudah dikonfirmasi. Tagihan kamu lunas.",
                    [
                        'type' => 'payment_confirmed',
                        'payment_id' => $payment->id,
                        'billing_id' => $payment->billing_id,
                        'property_id' => $payment->property_id,
                    ]
                );
            }

            return $payment;
        });
    }

    public function rejectPayment(Payment $payment, User $admin, ?string $adminNote = null): Payment
    {
        return DB::transaction(function () use ($payment, $admin, $adminNote) {
            $payment->refresh();

            if ($payment->status !== 'pending') {
                throw new \Exception('Pembayaran ini sudah diproses.');
            }

            if (! $admin->isSuperAdmin() && (int) $payment->property_id !== (int) $admin->property_id) {
                throw new \Exception('Pembayaran ini bukan milik kos Anda.');
            }

            $payment->update([
                'status' => 'failed',
                'confirmed_by' => $admin->id,
                'confirmed_at' => now(),
                'admin_note' => $adminNote,
            ]);

            ActivityService::log('reject', 'payment', "Admin {$admin->name} menolak pembayaran {$payment->payment_code}", $payment);

            $payment->loadMissing('user', 'billing');
            if ($payment->user) {
                app(OneSignalService::class)->sendToUser(
                    $payment->user,
                    'Pembayaran ditolak',
                    "Bukti pembayaran {$payment->billing->invoice_number} ditolak. Silakan upload ulang bukti yang benar.",
                    [
                        'type' => 'payment_rejected',
                        'payment_id' => $payment->id,
                        'billing_id' => $payment->billing_id,
                        'property_id' => $payment->property_id,
                    ]
                );
            }

            return $payment;
        });
    }
}
