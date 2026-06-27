<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::visibleTo(auth()->user())->with('user', 'unit.property')->latest()->paginate(15);
        return view('admin.maintenances.index', compact('maintenances'));
    }

    public function show(Maintenance $maintenance)
    {
        $this->ensureVisibleToAdmin($maintenance);
        $maintenance->load('user', 'unit.property', 'handledBy');
        return view('admin.maintenances.show', compact('maintenance'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $this->ensureVisibleToAdmin($maintenance);
        $data = $request->validate([
            'status' => 'required|in:pending,approved,in_progress,completed,rejected',
            'scheduled_date' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0',
            'cost_payer' => 'required|in:pengelola,tenant,belum_ditentukan',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $data['handled_by'] = auth()->id();
        $data['completed_date'] = $request->status === 'completed' ? now()->toDateString() : $maintenance->completed_date;
        $maintenance->update($data);

        ActivityService::log('update', 'maintenance', "Pengelola memperbarui status perbaikan {$maintenance->maintenance_code} menjadi {$request->status}", $maintenance);

        $maintenance->loadMissing('user', 'unit.property');
        if ($maintenance->user) {
            app(\App\Services\OneSignalService::class)->sendToUser(
                $maintenance->user,
                'Status perbaikan diperbarui',
                "Request perbaikan {$maintenance->maintenance_code} sekarang berstatus {$request->status}.",
                [
                    'type' => 'maintenance_status_updated',
                    'maintenance_id' => $maintenance->id,
                    'status' => $request->status,
                    'property_id' => $maintenance->property_id,
                ]
            );
        }

        return back()->with('success', 'Status perbaikan berhasil diperbarui.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $this->ensureVisibleToAdmin($maintenance);
        if (! in_array($maintenance->status, ['completed', 'rejected'], true)) return back()->with('error', 'Perbaikan hanya dapat dihapus jika sudah selesai atau ditolak.');
        if ($maintenance->photo) Storage::disk('public')->delete($maintenance->photo);
        $maintenance->delete();
        return redirect()->route('admin.maintenances.index')->with('success', 'Data perbaikan selesai berhasil dihapus.');
    }
}
