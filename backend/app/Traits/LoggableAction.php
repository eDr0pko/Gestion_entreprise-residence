<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\Log;

trait LoggableAction
{
    /**
     * Log an action to the logs table.
     *
     * @param int|null $userId
     * @param string $action
     * @param string $message
     * @param Request $request
     * @return void
     */
    public function logAction($userId, $action, $message, Request $request)
    {
        Log::create([
            'user_id' => $userId,
            'action' => $action,
            'message' => $message,
            'ip_address' => $request->ip(),
        ]);
    }
}
