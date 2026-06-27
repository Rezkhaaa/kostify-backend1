<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Complaint, Unit};
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::where('user_id', $request->user()->id)
            ->whereNotIn('property_id', $request->user()->inactivePropertyIds())
            ->with('unit.property')
            ->latest()
            ->get();
        return response()->json(['complaints' => $complaints]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:fasilitas,keamanan,kebersihan,tetangga,lainnya',
            'priority' => 'required|in:low,medium,high,urgent',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'unit_id.required' => 'Kamar kos wajib dipilih.',
            'title.required' => 'Judul komplain wajib diisi.',
            'description.required' => 'Deskripsi komplain wajib diisi.',
            'photo.image' => 'File bukti harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $unit = Unit::findOrFail($request->unit_id);

        if (! $request->user()->isActiveForProperty((int) $unit->property_id)) {
            return response()->json(['message' => 'Akses komplain untuk kos ini sedang dinonaktifkan oleh admin kos.'], 403);
        }

        $hasApprovedBooking = $request->user()->bookings()
            ->where('unit_id', $unit->id)
            ->where('status', 'approved')
            ->exists();

        if (! $hasApprovedBooking) {
            return response()->json(['message' => 'Komplain/perbaikan hanya dapat dibuat untuk kamar yang sudah disetujui admin.'], 403);
        }

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('complaint-proofs/' . now()->format('Y/m'), 'public')
            : null;

        $complaint = Complaint::create([
            'property_id' => $unit->property_id ?? $request->user()->property_id,
            'complaint_code' => 'CMP-' . strtoupper(Str::random(8)),
            'user_id' => $request->user()->id,
            'unit_id' => $request->unit_id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'pending',
            'photo' => $photoPath,
        ]);

        ActivityService::log('create', 'complaint', "Penghuni {$request->user()->name} membuat komplain: {$complaint->title}", $complaint);

        app(\App\Services\OneSignalService::class)->sendToUser(
            $request->user(),
            'Komplain berhasil dikirim',
            "Komplain {$complaint->complaint_code} sedang menunggu proses admin.",
            [
                'type' => 'complaint_created',
                'complaint_id' => $complaint->id,
                'property_id' => $complaint->property_id,
                'unit_id' => $complaint->unit_id,
            ]
        );

        return response()->json(['message' => 'Komplain berhasil dibuat', 'complaint' => $complaint->load('unit.property')], 201);
    }

    public function show(Complaint $complaint, Request $request)
    {
        if ($complaint->user_id !== $request->user()->id) return response()->json(['message' => 'Forbidden'], 403);
        if (! $request->user()->isActiveForProperty((int) $complaint->property_id)) {
            return response()->json(['message' => 'Akses komplain kos ini sedang dinonaktifkan oleh admin kos.'], 403);
        }
        return response()->json(['complaint' => $complaint->load('unit.property')]);
    }
}
