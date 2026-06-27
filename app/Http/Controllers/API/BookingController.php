<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->whereNotIn('property_id', $request->user()->inactivePropertyIds())
            ->with('unit.property')
            ->latest()
            ->get();
        return response()->json(['bookings' => $bookings]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        try {
            $booking = $this->bookingService->createBooking($request->user(), $request->all());
            return response()->json(['message' => 'Booking berhasil dibuat', 'booking' => $booking], 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(Booking $booking, Request $request)
    {
        if ($booking->user_id !== $request->user()->id) return response()->json(['message' => 'Forbidden'], 403);
        if (! $request->user()->isActiveForProperty((int) $booking->property_id)) {
            return response()->json(['message' => 'Akses Anda untuk kos ini sedang dinonaktifkan oleh admin kos.'], 403);
        }
        return response()->json(['booking' => $booking->load('unit.property')]);
    }
}
