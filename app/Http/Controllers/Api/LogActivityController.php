<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogActivity;

class LogActivityController extends Controller
{
    /**
     * Admin: List semua log activity dengan pagination dan search.
     */
    public function index(Request $request)
    {
        $query = LogActivity::with('user');

        if ($request->has('search')) {
            $query->where('action', 'like', '%' . $request->search . '%')
                  ->orWhere('entity', 'like', '%' . $request->search . '%');
        }

        $limit = $request->get('limit', 10);
        $logs = $query->orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'status' => true,
            'message' => 'Data log activities berhasil diambil',
            'data' => $logs
        ]);
    }
}
