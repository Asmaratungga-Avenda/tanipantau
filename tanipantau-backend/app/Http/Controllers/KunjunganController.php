<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKunjunganRequest;
use App\Http\Requests\UpdateKunjunganRequest;
use App\Http\Resources\KunjunganResource;
use App\Models\Kunjungan;
use App\Models\Lahan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller untuk mengelola Kunjungan Lapangan
 * 
 * Menangani CRUD operations untuk data Kunjungan termasuk upload foto
 */
class KunjunganController extends Controller
{
    /**
     * Menampilkan semua data Kunjungan
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = Kunjungan::with('lahan.petani', 'petugas');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('lahan', function ($q2) use ($s) {
                    $q2->where('nama_lahan', 'like', "%{$s}%");
                })->orWhere('catatan', 'like', "%{$s}%");
            });
        }

        $kunjungan = $query->latest('tanggal_kunjungan')->paginate(15);

        return KunjunganResource::collection($kunjungan);
    }

    /**
     * Menyimpan data Kunjungan baru dengan foto
     * 
     * @param StoreKunjunganRequest $request
     * @return JsonResponse
     */
    public function store(StoreKunjunganRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            /** @var User $user */
            $user = Auth::user();

            // Hanya petugas yang boleh membuat kunjungan
            if ($user->role !== 'petugas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya petugas yang dapat membuat kunjungan',
                ], 403);
            }

            $data['petugas_id'] = $user->id;

            // Petugas hanya boleh membuat kunjungan untuk lahannya sendiri
            $lahan = Lahan::find($data['lahan_id']);
            if (!$lahan || $lahan->petugas_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat membuat kunjungan untuk lahan yang ditugaskan kepada Anda'
                ], 403);
            }
            
            $file = $request->file('foto');
            if ($file) {
                $filename = Str::uuid() . '.' . $file->extension();
                $data['foto'] = $file->storeAs('kunjungan/' . date('Y') . '/' . date('m'), $filename, 'public');
            }

            $kunjungan = Kunjungan::create($data);
            $kunjungan->load('lahan', 'petugas');

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil ditambahkan',
                'data' => new KunjunganResource($kunjungan)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan data Kunjungan spesifik
     * 
     * @param Kunjungan $kunjungan
     * @return JsonResponse
     */
    public function show(Kunjungan $kunjungan): JsonResponse
    {
        $kunjungan->load('lahan', 'petugas');

        return response()->json([
            'success' => true,
            'message' => 'Detail kunjungan berhasil diambil',
            'data' => new KunjunganResource($kunjungan)
        ]);
    }

    /**
     * Mengupdate data Kunjungan
     * 
     * @param UpdateKunjunganRequest $request
     * @param Kunjungan $kunjungan
     * @return JsonResponse
     */
    public function update(UpdateKunjunganRequest $request, Kunjungan $kunjungan): JsonResponse
    {
        try {
            // Petugas hanya boleh edit kunjungan miliknya sendiri
            /** @var User $user */
            $user = Auth::user();
            if ($user->role === 'petugas' && $kunjungan->petugas_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat mengedit kunjungan milik sendiri'
                ], 403);
            }

            $data = $request->validated();
            
            $file = $request->file('foto');
            if ($file) {
                if ($kunjungan->foto && Storage::disk('public')->exists($kunjungan->foto)) {
                    Storage::disk('public')->delete($kunjungan->foto);
                }
                $filename = Str::uuid() . '.' . $file->extension();
                $data['foto'] = $file->storeAs('kunjungan/' . date('Y') . '/' . date('m'), $filename, 'public');
            }

            $kunjungan->update($data);
            $kunjungan->load('lahan', 'petugas');

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil diperbarui',
                'data' => new KunjunganResource($kunjungan)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus data Kunjungan
     * 
     * @param Kunjungan $kunjungan
     * @return JsonResponse
     */
    public function destroy(Kunjungan $kunjungan): JsonResponse
    {
        try {
            // Hapus foto jika ada
            if ($kunjungan->foto && Storage::disk('public')->exists($kunjungan->foto)) {
                Storage::disk('public')->delete($kunjungan->foto);
            }

            $kunjungan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }
}
