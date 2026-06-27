<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\{Billing, Unit, User};
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function __construct(private BillingService $billingService) {}

    public function index()
    {
        $billings = Billing::visibleTo(auth()->user())->with('user', 'unit.property', 'payment')->latest()->paginate(15);
        return view('admin.billings.index', compact('billings'));
    }

    public function create()
    {
        $admin = auth()->user();
        $penghunis = User::where('role', 'tenant')->where('status', 'active')->visibleTo($admin)->get();
        $units = Unit::visibleTo($admin)->whereIn('status', ['available', 'occupied'])->get();
        return view('admin.billings.create', compact('penghunis', 'units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after:billing_period_start',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $admin = auth()->user();
        $tenant = User::where('role', 'tenant')->visibleTo($admin)->findOrFail($data['user_id']);
        $unit = Unit::visibleTo($admin)->findOrFail($data['unit_id']);

        if ($tenant->property_id && $unit->property_id && (int) $tenant->property_id !== (int) $unit->property_id) {
            return back()->with('error', 'Penghuni dan kamar harus berasal dari kos yang sama.')->withInput();
        }

        $data['property_id'] = $unit->property_id ?? $tenant->property_id ?? $admin->property_id;
        $billing = $this->billingService->createBilling($data, Auth::user());

        return redirect()->route('admin.billings.show', $billing)->with('success', 'Tagihan berhasil dibuat. Penghuni dapat membayar melalui mobile.');
    }

    public function show(Billing $billing)
    {
        $this->ensureVisibleToAdmin($billing);
        $billing->load('user', 'unit.property', 'payment');
        return view('admin.billings.show', compact('billing'));
    }

    public function verify(Billing $billing)
    {
        $this->ensureVisibleToAdmin($billing);
        if ($billing->status !== 'paid') $billing->update(['status' => 'paid']);
        return back()->with('success', 'Tagihan dikonfirmasi lunas.');
    }

    public function destroy(Billing $billing)
    {
        $this->ensureVisibleToAdmin($billing);
        if ($billing->status !== 'paid') return back()->with('error', 'Tagihan hanya dapat dihapus jika statusnya sudah lunas.');
        $billing->delete();
        return redirect()->route('admin.billings.index')->with('success', 'Tagihan lunas berhasil dihapus dari daftar.');
    }
}
