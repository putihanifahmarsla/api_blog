<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostActivity;
use App\Models\Post;
use App\Models\PostsActivities;

class PostActivityController extends Controller
{
    /**
     * Customer: Simpan aktivitas kunjungan ke post.
     * POST /post-activities
     */
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $activity = PostsActivities::create([
            'post_id'   => $request->post_id,
            'ip'        => $request->ip(),
            'userAgent' => json_encode($request->header('User-Agent')),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Aktivitas berhasil dicatat',
            'data' => $activity
        ], 201);
    }

    /**
     * Admin: (Opsional) Lihat statistik aktivitas berdasarkan post_id.
     * GET /post-activities/{post_id}
     */
    public function showByPost($post_id)
    {
        $post = Post::findOrFail($post_id);

        $activities = PostsActivities::where('post_id', $post_id)->latest()->paginate(20);

        return response()->json([
            'status' => true,
            'message' => 'Statistik aktivitas post',
            'data' => [
                'post' => $post->only(['id', 'title', 'slug']),
                'activities' => $activities
            ]
        ]);
    }
}
