<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Resources\LahanResource;
use App\Models\Lahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LahanController extends Controller
{
    public function index(Request $request)
    {
        $query = Lahan::with(['petani'])
            ->where('petugas_id', Auth::id());

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_lahan', 'like', "%{$s}%")
                  ->orWhere('komoditas', 'like', "%{$s}%")
                  ->orWhereHas('petani', function ($qPetani) use ($s) {
                      $qPetani->where('nama', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('fase')) {
            $query->where('fase_lahan', $request->fase);
        }

        $lahan = $query->latest()->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data lahan saya berhasil diambil',
                'data' => LahanResource::collection($lahan),
                'meta' => [
                    'current_page' => $lahan->currentPage(),
                    'last_page' => $lahan->lastPage(),
                    'per_page' => $lahan->perPage(),
                    'total' => $lahan->total(),
                ],
            ]);
        }

        return view('petugas.lahan.index', compact('lahan'));
    }

    public function show(Request $request, Lahan $lahan)
    {
        if ($lahan->petugas_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke lahan ini',
                ], 403);
            }
            abort(403);
        }

        $lahan->load(['petani', 'kunjungan' => function($q) { $q->latest('tanggal_kunjungan')->take(5); }]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Detail lahan berhasil diambil',
                'data' => new LahanResource($lahan),
            ]);
        }

        return view('petugas.lahan.show', compact('lahan'));
    }
}
