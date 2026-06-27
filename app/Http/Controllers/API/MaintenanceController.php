<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Maintenance, Unit};
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $maintenances = Maintenance::where('user_id', $request->user()->id)
            ->whereNotIn('property_id', $request->user()->inactivePropertyIds())
            ->with('unit.property')
            ->latest()
            ->get();
        return response()->json(['maintenances' => $maintenances]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:plumbing,electrical,furniture,ac,painting,structural,lainnya',
            'priority' => 'required|in:low,medium,high,urgent',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'unit_id.required' => 'Kamar kos wajib dipilih.',
            'title.required' => 'Judul kerusakan wajib diisi.',
            'description.required' => 'Deskripsi kerusakan wajib diisi.',
            'photo.image' => 'File bukti harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $unit = Unit::findOrFail($request->unit_id);

        if (! $request->user()->isActiveForProperty((int) $unit->property_id)) {
            return response()->json(['message' => 'Akses perbaikan untuk kos ini sedang dinonaktifkan oleh admin kos.'], 403);
        }

        $hasApprovedBooking = $request->user()->bookings()
            ->where('unit_id', $unit->id)
            ->where('status', 'approved')
            ->exists();

        if (! $hasApprovedBooking) {
            return response()->json(['message' => 'Komplain/perbaikan hanya dapat dibuat untuk kamar yang sudah disetujui admin.'], 403);
        }

        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('maintenance-proofs', 'public') : null;

        $maintenance = Maintenance::create([
            'property_id' => $unit->property_id ?? $request->user()->property_id,
            'maintenance_code' => 'MNT-' . strtoupper(Str::random(8)),
            'user_id' => $request->user()->id,
            'unit_id' => $request->unit_id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'pending',
            'cost_payer' => 'belum_ditentukan',
            'photo' => $photoPath,
        ]);

        ActivityService::log('create', 'maintenance', "Penghuni {$request->user()->name} request perbaikan: {$maintenance->title}", $maintenance);

        app(\App\Services\OneSignalService::class)->sendToUser(
            $request->user(),
            'Request perbaikan terkirim',
            "Request perbaikan {$maintenance->maintenance_code} sedang menunggu proses admin.",
            [
                'type' => 'maintenance_created',
                'maintenance_id' => $maintenance->id,
                'property_id' => $maintenance->property_id,
                'unit_id' => $maintenance->unit_id,
            ]
        );

        return response()->json(['message' => 'Request perbaikan berhasil dibuat. Pengelola akan memeriksa laporan.', 'maintenance' => $maintenance->load('unit')], 201);
    }

    public function show(Maintenance $maintenance, Request $request)
    {
        if ($maintenance->user_id !== $request->user()->id) return response()->json(['message' => 'Forbidden'], 403);
        if (! $request->user()->isActiveForProperty((int) $maintenance->property_id)) {
            return response()->json(['message' => 'Akses perbaikan kos ini sedang dinonaktifkan oleh admin kos.'], 403);
        }
        return response()->json(['maintenance' => $maintenance->load('unit.property')]);
    }
}
