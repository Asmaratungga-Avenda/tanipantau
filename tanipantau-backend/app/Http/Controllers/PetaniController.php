<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePetaniRequest;
use App\Http\Requests\UpdatePetaniRequest;
use App\Http\Resources\PetaniResource;
use App\Models\Petani;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller untuk mengelola Petani
 * 
 * Menangani CRUD operations untuk data Petani
 */
class PetaniController extends Controller
{
    /**
     * Menampilkan semua data Petani
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = Petani::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('desa', 'like', "%{$s}%")
                  ->orWhere('kecamatan', 'like', "%{$s}%");
            });
        }

        $petani = $query->latest()->paginate(15);

        return PetaniResource::collection($petani);
    }

    /**
     * Menyimpan data Petani baru
     * 
     * @param StorePetaniRequest $request
     * @return JsonResponse
     */
    public function store(StorePetaniRequest $request): JsonResponse
    {
        try {
            $petani = Petani::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data petani berhasil ditambahkan',
                'data' => new PetaniResource($petani)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan petani: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan data Petani spesifik
     * 
     * @param Petani $petani
     * @return JsonResponse
     */
    public function show(Petani $petani): JsonResponse
    {
        $petani->load('lahan');

        return response()->json([
            'success' => true,
            'message' => 'Detail petani berhasil diambil',
            'data' => new PetaniResource($petani)
        ]);
    }

    /**
     * Mengupdate data Petani
     * 
     * @param UpdatePetaniRequest $request
     * @param Petani $petani
     * @return JsonResponse
     */
    public function update(UpdatePetaniRequest $request, Petani $petani): JsonResponse
    {
        try {
            $petani->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data petani berhasil diperbarui',
                'data' => new PetaniResource($petani)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui petani: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus data Petani
     * 
     * @param Petani $petani
     * @return JsonResponse
     */
    public function destroy(Petani $petani): JsonResponse
    {
        try {
            $petani->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data petani berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus petani: ' . $e->getMessage()
            ], 500);
        }
    }
}
