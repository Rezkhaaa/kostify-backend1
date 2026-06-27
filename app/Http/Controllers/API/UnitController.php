<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::with(['property'])
            ->whereHas('property', fn ($q) => $q->where('status', 'active'))
            ->when($request->boolean('mine'), function ($q) use ($request) {
                $q->whereHas('bookings', function ($booking) use ($request) {
                    $booking->where('user_id', $request->user()->id)->where('status', 'approved');
                });
            })
            ->withCount([
                'bookings as active_booking_count' => fn ($q) => $q->whereIn('status', ['pending', 'approved']),
            ]);

        if ($request->filled('property_id') && $request->property_id !== 'all') {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('gender_type') && $request->gender_type !== 'all') {
            $query->whereHas('property', fn ($q) => $q->where('gender_type', $request->gender_type));
        }

        if ($request->filled('status') && $request->status !== 'all') $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('type', $request->type);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('property', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $units = $query->latest()->get()->map(fn ($unit) => $this->formatUnit($unit, $request));
        return response()->json(['units' => $units]);
    }

    public function show(Unit $unit, Request $request)
    {
        $unit->load('property', 'activeOccupancy')
            ->loadCount(['bookings as active_booking_count' => fn ($q) => $q->whereIn('status', ['pending', 'approved'])]);

        if (! $unit->property || $unit->property->status !== 'active') {
            return response()->json(['message' => 'Kos ini sedang nonaktif.'], 403);
        }

        return response()->json(['unit' => $this->formatUnit($unit, $request)]);
    }

    private function formatUnit(Unit $unit, Request $request): array
    {
        $displayStatus = $unit->status;
        if ($unit->status === 'available' && (int) ($unit->active_booking_count ?? 0) > 0) {
            $displayStatus = 'booked';
        }

        $tenant = $request->user();
        $accessStatus = $tenant->statusForProperty((int) $unit->property_id);
        $genderMatch = $unit->property?->allowsTenant($tenant) ?? false;
        $canBook = $displayStatus === 'available' && $accessStatus === 'active' && $genderMatch;

        return [
            'id' => $unit->id,
            'property_id' => $unit->property_id,
            'property' => $unit->property,
            'unit_code' => $unit->unit_code,
            'name' => $unit->name,
            'type' => $unit->type,
            'description' => $unit->description,
            'price' => (float) $unit->price,
            'price_period' => $unit->price_period,
            'floor' => $unit->floor,
            'area' => $unit->area,
            'capacity' => $unit->capacity,
            'facilities' => $unit->facilities ?: [],
            'photo' => $unit->photo,
            'status' => $unit->status,
            'display_status' => $displayStatus,
            'active_booking_count' => (int) ($unit->active_booking_count ?? 0),
            'address' => $unit->address,
            // # Data berikut dipakai mobile untuk mengunci tombol booking dengan alasan yang jelas.
            'tenant_gender' => $tenant->gender,
            'tenant_gender_label' => $tenant->genderLabel(),
            'access_status' => $accessStatus,
            'gender_match' => $genderMatch,
            'can_book' => $canBook,
            'block_reason' => $accessStatus !== 'active'
                ? 'Akses Anda untuk kos ini dinonaktifkan admin kos.'
                : ($genderMatch ? null : 'Kamar ini mengikuti jenis kos yang tidak sesuai dengan kategori Anda.'),
            'created_at' => $unit->created_at,
            'updated_at' => $unit->updated_at,
        ];
    }
}
