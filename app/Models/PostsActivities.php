<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostsActivities extends Model
{
    protected $table = 'post_activities';

    protected $fillable = [
        'post_id',
        'ip',
        'userAgent',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
