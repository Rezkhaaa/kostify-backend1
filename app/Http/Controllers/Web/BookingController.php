<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index()
    {
        $bookings = Booking::visibleTo(auth()->user())->with('user', 'unit.property')->latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $this->ensureVisibleToAdmin($booking);
        $booking->load('user', 'unit.property', 'approvedBy');
        return view('admin.bookings.show', compact('booking'));
    }

    public function approve(Booking $booking)
    {
        $this->ensureVisibleToAdmin($booking);
        try {
            $this->bookingService->approveBooking($booking, Auth::user());
            return back()->with('success', "Booking {$booking->booking_code} berhasil di-approve.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Booking $booking)
    {
        $this->ensureVisibleToAdmin($booking);
        $request->validate(['reason' => 'required|string']);
        try {
            $this->bookingService->rejectBooking($booking, Auth::user(), $request->reason);
            return back()->with('success', "Booking {$booking->booking_code} berhasil ditolak.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
