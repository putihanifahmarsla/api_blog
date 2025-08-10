<?php

namespace App\Traits;

use App\Models\LogActivity;

trait ActivityLogger
{
    public function logActivity($action, $entity, $entity_id, $status = 'success', $details = null, $error_message = null)
    {
        $request = request();
        $user = auth('sanctum')->user(); 

        LogActivity::create([
            'user_id'        => $user ? $user->id : null,
            'action'         => $action,
            'entity'         => $entity,
            'entity_id'      => $entity_id,
            'status'         => $status,
            'details'        => is_array($details) ? json_encode($details) : $details,
            'error_message'  => $error_message,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->header('User-Agent'),
            'module'         => 'Admin Panel',
            'request_method' => $request->method(),
            'url_accessed'   => $request->fullUrl(),
        ]);
    }
}
