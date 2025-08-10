<?php

namespace App\Http\Controllers\Api;

use App\Models\Tags;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TagsController extends Controller
{
    use ActivityLogger; // aktifkan trait

    /**
     * Customer - List semua tag
     */
    public function index()
    {
        $tags = Tags::all();
        return response()->json([
            'status' => true,
            'message' => 'Tag berhasil ditemukan',
            'data' => $tags,
        ], 200);
    }

    /**
     * Admin - List untuk halaman admin
     */
    public function adminIndex()
    {
        $tags = Tags::latest()->paginate(10);
        return response()->json([
            'status' => true,
            'message' => 'Daftar tag untuk admin',
            'data' => $tags,
        ], 200);
    }

    /**
     * Admin - Tambah tag baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:tags,name',
            'slug' => 'required|string|max:50|unique:tags,slug',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tag = Tags::create($request->all());

        $this->logActivity('create', 'tag', $tag->id, 'success', $tag->toArray());

        return response()->json([
            'status' => true,
            'message' => 'Tag berhasil dibuat',
            'data' => $tag,
        ], 201);
    }

    /**
     * Customer - Detail 1 tag berdasarkan ID
     */
    public function show(string $id)
    {
        $tag = Tags::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Detail tag berhasil ditemukan',
            'data' => $tag,
        ], 200);
    }

    /**
     * Admin - Update tag
     */
    public function update(Request $request, string $id)
    {
        $tag = Tags::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
            'slug' => 'required|string|max:50|unique:tags,slug,' . $tag->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tag->update($request->all());

        $this->logActivity('update', 'tag', $tag->id, 'success', $tag->toArray());

        return response()->json([
            'status' => true,
            'message' => 'Tag berhasil diperbarui',
            'data' => $tag,
        ], 200);
    }

    /**
     * Admin - Hapus tag
     */
    public function destroy(string $id)
    {
        $tag = Tags::findOrFail($id);
        $tag->delete();

        $this->logActivity('delete', 'tag', $tag->id, 'success');

        return response()->json([
            'status' => true,
            'message' => 'Tag berhasil dihapus',
        ], 200);
    }
}
