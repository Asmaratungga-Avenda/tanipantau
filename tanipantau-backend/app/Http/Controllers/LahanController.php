<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLahanRequest;
use App\Http\Requests\UpdateLahanRequest;
use App\Http\Resources\LahanResource;
use App\Models\Lahan;
use App\Models\PenugasanLahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk mengelola Lahan
 * 
 * Menangani CRUD operations untuk data Lahan
 */
class LahanController extends Controller
{
    /**
     * Menampilkan semua data Lahan
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = Lahan::with(['petani']);

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

        return LahanResource::collection($lahan);
    }

    /**
     * Menyimpan data Lahan baru
     * 
     * @param StoreLahanRequest $request
     * @return JsonResponse
     */
    public function store(StoreLahanRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $lahan = DB::transaction(function () use ($data) {
                $lahan = Lahan::create($data);

                if (!empty($data['petugas_id'])) {
                    PenugasanLahan::create([
                        'lahan_id' => $lahan->id,
                        'petugas_id' => $data['petugas_id'],
                        'assigned_by' => Auth::id(),
                        'assigned_at' => now(),
                        'status' => 'aktif',
                    ]);
                }

                return $lahan;
            });

            $lahan->load(['petani', 'petugas']);

            return response()->json([
                'success' => true,
                'message' => 'Lahan berhasil ditambahkan',
                'data' => new LahanResource($lahan)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan lahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan data Lahan spesifik
     * 
     * @param Lahan $lahan
     * @return JsonResponse
     */
    public function show(Lahan $lahan): JsonResponse
    {
        $lahan->load('petani', 'kunjungan.petugas');

        return response()->json([
            'success' => true,
            'message' => 'Detail lahan berhasil diambil',
            'data' => new LahanResource($lahan)
        ]);
    }

    /**
     * Mengupdate data Lahan
     * 
     * @param UpdateLahanRequest $request
     * @param Lahan $lahan
     * @return JsonResponse
     */
    public function update(UpdateLahanRequest $request, Lahan $lahan): JsonResponse
    {
        try {
            $data = $request->validated();

            $lahan = DB::transaction(function () use ($data, $lahan) {
                $oldPetugasId = $lahan->petugas_id;
                $lahan->update($data);

                // Catat perubahan penugasan
                $newPetugasId = $data['petugas_id'] ?? null;

                if ($oldPetugasId != $newPetugasId) {
                    // Nonaktifkan penugasan lama
                    if ($oldPetugasId) {
                        PenugasanLahan::where('lahan_id', $lahan->id)
                            ->where('status', 'aktif')
                            ->update(['status' => 'dicabut']);
                    }

                    // Buat penugasan baru
                    if ($newPetugasId) {
                        PenugasanLahan::create([
                            'lahan_id' => $lahan->id,
                            'petugas_id' => $newPetugasId,
                            'assigned_by' => Auth::id(),
                            'assigned_at' => now(),
                            'status' => 'aktif',
                        ]);
                    }
                }

                return $lahan;
            });

            $lahan->load(['petani', 'petugas']);

            return response()->json([
                'success' => true,
                'message' => 'Lahan berhasil diperbarui',
                'data' => new LahanResource($lahan)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui lahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus data Lahan
     * 
     * @param Lahan $lahan
     * @return JsonResponse
     */
    public function destroy(Lahan $lahan): JsonResponse
    {
        try {
            $lahan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lahan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus lahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
