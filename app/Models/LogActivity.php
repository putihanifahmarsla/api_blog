<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    protected $fillable = [
        'user_id', 'action', 'entity', 'entity_id', 'details', 'status',
        'error_message', 'ip_address', 'user_agent', 'module', 'request_method', 'url_accessed'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
