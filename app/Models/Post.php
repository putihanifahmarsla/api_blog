<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class post extends Model
{
    use SoftDeletes;
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'user_id',
        'category_id',
        'thumbnail',
        'published_at',
        'status',
        'meta_title',
        'meta_description'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tags::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function activities()
    {
        return $this->hasMany(PostsActivities::class);
    }
}
