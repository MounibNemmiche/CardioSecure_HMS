<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public static function log(
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        bool $success = true,
        ?array $metadata = null
    ): void {
        $user = Auth::user();
        $request = app(Request::class);

        AuditLog::create([
            'user_id' => $user?->id,
            'role' => $user?->getRoleNames()->first(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
            'success' => $success,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
