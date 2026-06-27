<?php

namespace App\Services;

use App\Models\ActivityHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityService
{
    public static function log(string $action, string $module, string $description, $subject = null, array $meta = []): void
    {
        $user = Auth::user();
        $propertyId = $subject?->property_id ?? $user?->property_id ?? null;

        ActivityHistory::create([
            'property_id'   => $propertyId,
            'user_id'       => $user?->id,
            'action'        => $action,
            'module'        => $module,
            'description'   => $description,
            'subject_id'    => $subject?->id,
            'subject_type'  => $subject ? get_class($subject) : null,
            'meta'          => $meta,
            'ip_address'    => Request::ip(),
        ]);
    }
}
