<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Occupancy;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function createBooking(User $user, array $data): Booking
    {
        if (! $user->isTenant()) {
            throw new \Exception('Akun ini bukan akun penghuni.');
        }

        return DB::transaction(function () use ($user, $data) {
            $unit = Unit::with('property')->findOrFail($data['unit_id']);

            if (! $unit->property || $unit->property->status !== 'active') {
                throw new \Exception('Kos ini sedang nonaktif.');
            }

            if (! $user->isActiveForProperty((int) $unit->property_id)) {
                throw new \Exception('Akses Anda untuk kos ini sedang dinonaktifkan oleh admin kos. Anda masih bisa memilih kos lain yang aktif.');
            }

            // # Validasi Kos Putra/Putri/Campuran WAJIB di backend.
            // # Mobile hanya membantu tampilan, tetapi keputusan aman tetap di Laravel.
            if (! $unit->property->allowsTenant($user)) {
                throw new \Exception('Jenis kos ini tidak sesuai dengan kategori penghuni Anda. Silakan pilih kos yang sesuai atau Kos Campuran.');
            }

            if (! $unit->isAvailable()) {
                throw new \Exception('Kamar tidak tersedia untuk dibooking.');
            }

            $this->checkDoubleBooking($unit->id, $data['start_date'], $data['end_date']);

            // Tenant baru boleh memilih kos mana pun. property_id tenant baru diisi setelah booking disetujui.
            $duration = Carbon::parse($data['start_date'])->diffInMonths(Carbon::parse($data['end_date']));
            $duration = max(1, $duration);

            $booking = Booking::create([
                'property_id'  => $unit->property_id,
                'booking_code' => 'BK-' . strtoupper(Str::random(8)),
                'user_id'      => $user->id,
                'unit_id'      => $unit->id,
                'start_date'   => $data['start_date'],
                'end_date'     => $data['end_date'],
                'duration'     => $duration,
                'total_price'  => $unit->price * $duration,
                'status'       => 'pending',
                'notes'        => $data['notes'] ?? null,
            ]);

            ActivityService::log('create', 'booking', "Penghuni {$user->name} melakukan booking kamar {$unit->name}", $booking);

            app(OneSignalService::class)->sendToUser(
                $user,
                'Booking berhasil dikirim',
                "Booking kamar {$unit->name} di {$unit->property?->name} sedang menunggu approval admin.",
                [
                    'type' => 'booking_created',
                    'booking_id' => $booking->id,
                    'property_id' => $booking->property_id,
                    'unit_id' => $booking->unit_id,
                ]
            );

            return $booking->load('unit');
        });
    }

    public function approveBooking(Booking $booking, User $admin): Booking
    {
        return DB::transaction(function () use ($booking, $admin) {
            $booking->load('unit');

            if ($booking->status !== 'pending') {
                throw new \Exception('Booking hanya bisa di-approve jika statusnya pending.');
            }

            if (! $admin->isSuperAdmin() && (int) $booking->property_id !== (int) $admin->property_id) {
                throw new \Exception('Booking ini bukan milik kos Anda.');
            }

            $this->checkDoubleBooking($booking->unit_id, $booking->start_date->toDateString(), $booking->end_date->toDateString(), $booking->id);

            $booking->update([
                'status'      => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);

            Occupancy::create([
                'property_id' => $booking->property_id,
                'booking_id'  => $booking->id,
                'user_id'     => $booking->user_id,
                'unit_id'     => $booking->unit_id,
                'start_date'  => $booking->start_date,
                'end_date'    => $booking->end_date,
                'status'      => 'active',
            ]);

            $booking->unit->update(['status' => 'occupied']);
            $booking->user?->update(['property_id' => $booking->property_id]);

            ActivityService::log('approve', 'booking', "Admin {$admin->name} menyetujui booking {$booking->booking_code}", $booking);

            $booking->loadMissing('user', 'unit.property');
            if ($booking->user) {
                app(OneSignalService::class)->sendToUser(
                    $booking->user,
                    'Booking disetujui',
                    "Booking kamar {$booking->unit?->name} di {$booking->unit?->property?->name} telah disetujui.",
                    [
                        'type' => 'booking_approved',
                        'booking_id' => $booking->id,
                        'property_id' => $booking->property_id,
                        'unit_id' => $booking->unit_id,
                    ]
                );
            }

            return $booking;
        });
    }

    public function rejectBooking(Booking $booking, User $admin, string $reason): Booking
    {
        if ($booking->status !== 'pending') {
            throw new \Exception('Booking hanya bisa ditolak jika statusnya pending.');
        }

        if (! $admin->isSuperAdmin() && (int) $booking->property_id !== (int) $admin->property_id) {
            throw new \Exception('Booking ini bukan milik kos Anda.');
        }

        $booking->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'approved_by'      => $admin->id,
            'approved_at'      => now(),
        ]);

        ActivityService::log('reject', 'booking', "Admin {$admin->name} menolak booking {$booking->booking_code}", $booking);

        $booking->loadMissing('user', 'unit.property');
        if ($booking->user) {
            app(OneSignalService::class)->sendToUser(
                $booking->user,
                'Booking ditolak',
                "Booking kamar {$booking->unit?->name} ditolak. Silakan pilih kamar lain.",
                [
                    'type' => 'booking_rejected',
                    'booking_id' => $booking->id,
                    'property_id' => $booking->property_id,
                    'unit_id' => $booking->unit_id,
                ]
            );
        }

        return $booking;
    }

    private function checkDoubleBooking(int $unitId, string $startDate, string $endDate, ?int $ignoreBookingId = null): void
    {
        $exists = Booking::where('unit_id', $unitId)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)->where('end_date', '>=', $endDate);
                  });
            })->exists();

        if ($exists) {
            throw new \Exception('Kamar sudah dibooking pada periode tersebut.');
        }
    }
}
