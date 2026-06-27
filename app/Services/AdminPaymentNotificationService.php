<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminPaymentNotificationService
{
    public function notifyPaymentSubmitted(Payment $payment): void
    {
        $payment->loadMissing('user', 'billing.unit', 'property');

        $message = $this->message($payment);

        $this->sendEmail($payment, $message);
        $this->sendTelegram($message);
    }

    private function message(Payment $payment): string
    {
        $invoice = $payment->billing->invoice_number ?? '-';
        $tenant = $payment->user->name ?? '-';
        $unit = $payment->billing->unit->name ?? '-';
        $property = $payment->property->name ?? '-';
        $amount = 'Rp ' . number_format((float) $payment->amount, 0, ',', '.');
        $date = optional($payment->transfer_date)->format('d/m/Y') ?: '-';

        return "Ada bukti pembayaran baru masuk.\n\n"
            . "Kos: {$property}\n"
            . "Penghuni: {$tenant}\n"
            . "Kamar: {$unit}\n"
            . "Invoice: {$invoice}\n"
            . "Kode Pembayaran: {$payment->payment_code}\n"
            . "Nominal: {$amount}\n"
            . "Bank Pengirim: " . ($payment->sender_bank ?: '-') . "\n"
            . "Nama Pengirim: " . ($payment->sender_name ?: '-') . "\n"
            . "Tanggal Transfer: {$date}\n"
            . "Status: Checking Admin\n\n"
            . "Silakan buka dashboard admin untuk cek bukti pembayaran lalu ubah menjadi Success atau Fail.";
    }

    private function sendEmail(Payment $payment, string $message): void
    {
        $email = config('services.admin_payment.email');

        if (blank($email)) {
            Log::info('Email admin pembayaran belum diset.', ['payment_id' => $payment->id]);
            return;
        }

        try {
            Mail::raw($message, function ($mail) use ($email, $payment) {
                $mail->to($email)
                    ->subject('Bukti Pembayaran Baru - ' . $payment->payment_code);
            });
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email notifikasi pembayaran', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendTelegram(string $message): void
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (blank($botToken) || blank($chatId)) {
            Log::info('Telegram pembayaran belum dikonfigurasi.');
            return;
        }

        try {
            Http::asForm()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim Telegram notifikasi pembayaran', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
