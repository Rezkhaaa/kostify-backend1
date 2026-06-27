<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $billings = Billing::where('user_id', $request->user()->id)
            ->whereNotIn('property_id', $request->user()->inactivePropertyIds())
            ->with('unit.property', 'payment')
            ->latest()
            ->get();
        return response()->json(['billings' => $billings]);
    }

    public function show(Billing $billing, Request $request)
    {
        if ($billing->user_id !== $request->user()->id) return response()->json(['message' => 'Forbidden'], 403);
        if (! $request->user()->isActiveForProperty((int) $billing->property_id)) {
            return response()->json(['message' => 'Akses tagihan kos ini sedang dinonaktifkan oleh admin kos.'], 403);
        }
        return response()->json(['billing' => $billing->load('unit.property', 'payment')]);
    }
}
