<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PushNotificationLog;

class NotificationLogController extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        PushNotificationLog::cleanupExpired();

        PushNotificationLog::markAdminRead($admin);

        $logs = PushNotificationLog::visibleTo($admin)
            ->recent()
            ->with('user', 'property')
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('logs'));
    }
}
