<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\{ActivityHistory, Billing, Booking, Complaint, Maintenance, Property, PropertyRegistrationRequest, Unit, User};

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth()->user();

        $stats = [
            'total_properties' => $admin->isSuperAdmin() ? Property::count() : 1,
            'active_properties' => $admin->isSuperAdmin() ? Property::active()->count() : 1,
            'total_property_admins' => $admin->isSuperAdmin() ? User::whereIn('role', ['property_admin', 'admin'])->count() : 0,
            'total_units' => Unit::visibleTo($admin)->count(),
            'available_units' => Unit::visibleTo($admin)->where('status', 'available')->count(),
            'occupied_units' => Unit::visibleTo($admin)->where('status', 'occupied')->count(),
            'total_tenants' => User::where('role', 'tenant')->visibleTo($admin)->count(),
            'pending_bookings' => Booking::visibleTo($admin)->where('status', 'pending')->count(),
            'total_bookings' => Booking::visibleTo($admin)->count(),
            'unpaid_billings' => Billing::visibleTo($admin)->where('status', 'unpaid')->count(),
            'total_revenue' => Billing::visibleTo($admin)->where('status', 'paid')->sum('total_amount'),
            'open_complaints' => Complaint::visibleTo($admin)->whereIn('status', ['pending', 'in_progress', 'approved'])->count(),
            'open_maintenances' => Maintenance::visibleTo($admin)->whereIn('status', ['pending', 'approved', 'in_progress'])->count(),
            'pending_registrations' => $admin->isSuperAdmin() ? PropertyRegistrationRequest::where('status', 'pending')->count() : 0,
        ];

        $recent_activities = ActivityHistory::visibleTo($admin)->with('user')->latest()->take(8)->get();
        $recent_bookings = Booking::visibleTo($admin)->with('user', 'unit.property')->latest()->take(5)->get();
        $pendingRegistrations = $admin->isSuperAdmin()
            ? PropertyRegistrationRequest::where('status', 'pending')->latest()->take(5)->get()
            : collect();
        $properties = $admin->isSuperAdmin()
            ? Property::withCount(['units', 'admins', 'tenants'])->latest()->take(6)->get()
            : collect();

        return view('admin.dashboard', compact('stats', 'recent_activities', 'recent_bookings', 'pendingRegistrations', 'properties'));
    }
}
