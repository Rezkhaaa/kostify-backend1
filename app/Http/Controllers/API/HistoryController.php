<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{ActivityHistory, Payment};
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $histories = ActivityHistory::where('user_id', $request->user()->id)
            ->whereNotIn('property_id', $request->user()->inactivePropertyIds())
            ->latest()
            ->take(50)
            ->get();
        return response()->json(['histories' => $histories]);
    }

    public function bookings(Request $request)
    {
        return response()->json(['bookings' => $request->user()->bookings()
            ->whereNotIn('property_id', $request->user()->inactivePropertyIds())
            ->with('unit.property')->latest()->get()]);
    }

    public function payments(Request $request)
    {
        $data = Payment::where('user_id', $request->user()->id)
            ->whereNotIn('property_id', $request->user()->inactivePropertyIds())
            ->with('billing.unit')->latest()->get();
        return response()->json(['payments' => $data]);
    }
}
