<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class LoggingService
{
    /**
     * Log activity for create, update, or delete actions.
     *
     * @param Request $request
     * @param string $actionType  // e.g., 'insert', 'update', 'delete'
     * @param string $tableName   // The name of the affected table
     * @param int|null $recordId  // The ID of the affected record in the table
     * @param array|null $changes // JSON data of changes, if applicable (for updates)
     */
    public static function logActivity(Request $request, string $actionType, string $tableName, ?int $recordId = null, ?array $changes = null)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        ActivityLog::create([
            'user_id' => $userId,
            'ip_addr' => $request->ip(),
            'action_type' => $actionType,
            'affected_table' => $tableName,
            'record_id' => $recordId,
            'changes' => $changes ? json_encode($changes) : null,
            'created_at' => now(),
        ]);
    }
}
