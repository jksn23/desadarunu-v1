<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public function log(array $data): ActivityLog
    {
        $user = $data['user'] ?? auth()->user();

        return ActivityLog::create([
            'user_id' => $user?->id,
            'role' => $data['role'] ?? $user?->role,
            'action' => $data['action'] ?? 'unknown',
            'module' => $data['module'] ?? null,
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'ip_address' => $data['ip_address'] ?? Request::ip(),
            'user_agent' => $data['user_agent'] ?? Request::userAgent(),
        ]);
    }
}
