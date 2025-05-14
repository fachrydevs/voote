<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;



class AuditService
{
    public function log($activityType, $description, $oldValues = null, $newValues = null) {
        $user = Auth::user();

        $log = new AuditLog([
            'user_id' => $user ? $user->id : null,
            'email' => $user ? $user->email : null,
            'role' => $user ? $user->role : null,
            'ip_address' => request()->ip(),
            'activity_type' => $activityType,
            'activity_description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_agent' => request()->userAgent(),
        ]);

        $log->save();

        return $log;
        

    }

}