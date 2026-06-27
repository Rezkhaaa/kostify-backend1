<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::visibleTo(auth()->user())->with('user', 'unit.property')->latest()->paginate(15);
        return view('admin.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        $this->ensureVisibleToAdmin($complaint);
        $complaint->load('user', 'unit.property');
        return view('admin.complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $this->ensureVisibleToAdmin($complaint);
        $request->validate([
            'status' => 'required|in:pending,approved,in_progress,resolved,rejected',
            'admin_response' => 'nullable|string',
        ]);

        $complaint->update([
            'status' => $request->status,
            'admin_response' => $request->admin_response,
            'handled_by' => Auth::id(),
            'resolved_at' => in_array($request->status, ['resolved', 'rejected'], true) ? now() : null,
        ]);

        ActivityService::log('update', 'complaint', "Admin memperbarui status komplain {$complaint->complaint_code} menjadi {$request->status}", $complaint);

        $complaint->loadMissing('user', 'unit.property');
        if ($complaint->user) {
            app(\App\Services\OneSignalService::class)->sendToUser(
                $complaint->user,
                'Status komplain diperbarui',
                "Komplain {$complaint->complaint_code} sekarang berstatus {$request->status}.",
                [
                    'type' => 'complaint_status_updated',
                    'complaint_id' => $complaint->id,
                    'status' => $request->status,
                    'property_id' => $complaint->property_id,
                ]
            );
        }

        return back()->with('success', 'Status komplain berhasil diperbarui.');
    }

    public function destroy(Complaint $complaint)
    {
        $this->ensureVisibleToAdmin($complaint);
        if (! in_array($complaint->status, ['resolved', 'rejected'], true)) return back()->with('error', 'Komplain hanya dapat dihapus jika sudah selesai atau ditolak.');
        if ($complaint->photo) Storage::disk('public')->delete($complaint->photo);
        $complaint->delete();
        return redirect()->route('admin.complaints.index')->with('success', 'Komplain selesai berhasil dihapus.');
    }
}
