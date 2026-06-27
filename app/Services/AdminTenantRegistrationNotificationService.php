<?php

namespace App\Services;

use App\Models\TenantRegistrationRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminTenantRegistrationNotificationService
{
    public function notifySubmitted(TenantRegistrationRequest $registration): void
    {
        $message = $this->message($registration);
        $this->sendEmail($registration, $message);
        $this->sendTelegram($message);
    }

    private function message(TenantRegistrationRequest $registration): string
    {
        return "Ada pendaftar pengguna baru di Kostify.\n\n"
            . "Nama: {$registration->name}\n"
            . "Email: {$registration->email}\n"
            . "No. HP: " . ($registration->phone ?: '-') . "\n"
            . "Kategori: " . ($registration->gender ?: '-') . "\n"
            . "Daftar via: " . strtoupper($registration->requested_via ?: 'manual') . "\n"
            . "Status: Pending\n\n"
            . "Silakan buka Dashboard Admin > Pendaftaran Pengguna untuk melakukan persetujuan.";
    }

    private function sendEmail(TenantRegistrationRequest $registration, string $message): void
    {
        $email = config('services.admin_payment.email');
        if (blank($email)) {
            Log::info('Email admin pendaftaran tenant belum diset.', ['registration_id' => $registration->id]);
            return;
        }

        try {
            Mail::raw($message, function ($mail) use ($email, $registration) {
                $mail->to($email)->subject('Pendaftar Pengguna Baru - ' . $registration->email);
            });
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email notifikasi pendaftaran tenant', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendTelegram(string $message): void
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (blank($botToken) || blank($chatId)) {
            Log::info('Telegram pendaftaran tenant belum dikonfigurasi.');
            return;
        }

        try {
            Http::asForm()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim Telegram notifikasi pendaftaran tenant', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
