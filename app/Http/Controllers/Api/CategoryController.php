<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ActivityLogger;

class CategoryController extends Controller
{
    use ActivityLogger;

    // customer
    public function index()
    {
        $categories = Category::select('name', 'slug')->get();

        return response()->json([
            'status' => true,
            'message' => 'Kategori ditemukan',
            'data' => $categories,
        ]);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        return response()->json([
            'status' => true,
            'message' => 'Detail kategori',
            'data' => $category,
        ]);
    }

    // Admin
    public function adminIndex()
    {
        $categories = Category::latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'slug' => 'required|string|max:50|unique:categories,slug',
        ]);

        $category = Category::create($validated);

        $this->logActivity('create', 'category', $category->id, 'success', $category->toArray());

        return response()->json([
            'status' => true,
            'message' => 'Kategori berhasil dibuat',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $category->id,
            'slug' => 'required|string|max:50|unique:categories,slug,' . $category->id,
        ]);

        $category->update($validated);

        $this->logActivity('update', 'category', $category->id, 'success', $category->toArray());

        return response()->json([
            'status' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data' => $category,
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        $this->logActivity('delete', 'category', $category->id, 'success');

        return response()->json([
            'status' => true,
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}
