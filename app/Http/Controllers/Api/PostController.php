<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Traits\ActivityLogger;

class PostController extends Controller
{
    use ActivityLogger;

    /**
     * Admin: List semua post termasuk draft dan yang sudah dihapus (soft deleted).
     */
    public function adminIndex(Request $request)
    {
        $query = Post::withTrashed()->with(['user', 'category', 'tags'])->latest();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        $limit = $request->get('limit', 10);
        $posts = $query->paginate($limit);

        return response()->json([
            'status' => true,
            'message' => 'List semua post (admin)',
            'data' => $posts
        ]);
    }

    /**
     * Customer: List post publish yang tidak dihapus.
     */
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category', 'tags'])
            ->where('status', 'publish')
            ->whereNull('deleted_at');

        $limit = $request->get('limit', 10);
        $posts = $query->paginate($limit);

        return response()->json([
            'status' => true,
            'message' => 'List post untuk pengunjung',
            'data' => $posts
        ]);
    }

    /**
     * Customer: Detail post by slug.
     */
    public function show($slug)
    {
        $post = Post::with(['user', 'category', 'tags'])
            ->where('slug', $slug)
            ->where('status', 'publish')
            ->whereNull('deleted_at')
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'message' => 'Detail post',
            'data' => $post
        ]);
    }

    /**
     * Admin: Create new post.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:posts|max:200',
            'slug'          => 'nullable|unique:posts|max:200',
            'content'       => 'required',
            'category_id'   => 'required|exists:categories,id',
            'thumbnail'     => 'nullable|string',
            'published_at'  => 'nullable|date',
            'status'        => 'required|in:publish,draft',
            'meta_title'    => 'nullable|max:100',
            'meta_description' => 'nullable|max:150',
            'tags'          => 'array|nullable',
            'tags.*'        => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        $data['user_id'] = auth('sanctum')->id();
        $data['slug'] = $request->slug ?? Str::slug($request->title);

        $post = Post::create($data);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        $this->logActivity('create', 'post', $post->id, 'success', $post->toArray());

        return response()->json([
            'status' => true,
            'message' => 'Post berhasil dibuat',
            'data' => $post->load(['user', 'category', 'tags']),
        ], 201);
    }

    /**
     * Admin: Update post by id.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'         => 'required|max:200|unique:posts,title,' . $post->id,
            'slug'          => 'nullable|unique:posts,slug,' . $post->id,
            'content'       => 'required',
            'category_id'   => 'required|exists:categories,id',
            'thumbnail'     => 'nullable|string',
            'published_at'  => 'nullable|date',
            'status'        => 'required|in:publish,draft',
            'meta_title'    => 'nullable|max:100',
            'meta_description' => 'nullable|max:150',
            'tags'          => 'array|nullable',
            'tags.*'        => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        $data['slug'] = $request->slug ?? Str::slug($request->title);

        $post->update($data);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        $this->logActivity('update', 'post', $post->id, 'success', $post->toArray());

        return response()->json([
            'status' => true,
            'message' => 'Post berhasil diperbarui',
            'data' => $post->load(['user', 'category', 'tags']),
        ]);
    }

    /**
     * Admin: Soft delete post by id.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        $this->logActivity('delete', 'post', $post->id, 'success');

        return response()->json([
            'status' => true,
            'message' => 'Post berhasil dihapus (soft delete)',
        ]);
    }
}
