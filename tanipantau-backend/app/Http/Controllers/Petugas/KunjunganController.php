<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKunjunganRequest;
use App\Http\Requests\UpdateKunjunganRequest;
use App\Http\Resources\KunjunganResource;
use App\Models\Kunjungan;
use App\Models\Lahan;
use App\Services\KunjunganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KunjunganController extends Controller
{
    public function __construct(
        protected KunjunganService $kunjunganService
    ) {}

    public function index(Request $request)
    {
        $query = Kunjungan::with(['lahan.petani', 'petugas'])
            ->where('petugas_id', Auth::id());

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('lahan', function($q2) use ($s) {
                    $q2->where('nama_lahan', 'like', "%{$s}%");
                })->orWhere('catatan', 'like', "%{$s}%");
            });
        }

        $kunjungan = $query->latest('tanggal_kunjungan')->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kunjungan saya berhasil diambil',
                'data' => KunjunganResource::collection($kunjungan),
                'meta' => [
                    'current_page' => $kunjungan->currentPage(),
                    'last_page' => $kunjungan->lastPage(),
                    'per_page' => $kunjungan->perPage(),
                    'total' => $kunjungan->total(),
                ],
            ]);
        }

        $lahanList = Lahan::where('petugas_id', Auth::id())->get();
        return view('petugas.kunjungan.index', compact('kunjungan', 'lahanList'));
    }

    public function create(Request $request)
    {
        $lahanList = Lahan::with('petani')->where('petugas_id', Auth::id())->get();
        $selectedLahanId = $request->query('lahan');
        $selectedLahan = $selectedLahanId ? Lahan::with('petani')->where('petugas_id', Auth::id())->findOrFail($selectedLahanId) : null;
        return view('petugas.kunjungan.create', compact('lahanList', 'selectedLahanId', 'selectedLahan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lahan_id' => 'required|exists:lahan,id',
            'tanggal_kunjungan' => 'required|date',
            'kondisi_lahan' => 'required|in:Sangat Baik,Baik,Sedang,Kurang Baik,Sangat Kurang Baik',
            'status_tindak_lanjut' => 'required|in:Aman,Perlu Pemantauan,Perlu Tindakan',
            'catatan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $targetLahan = Lahan::findOrFail($validated['lahan_id']);
        if($targetLahan->petugas_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berhak membuat kunjungan untuk lahan ini',
                ], 403);
            }
            abort(403);
        }

        $data = $validated;
        $kunjungan = $this->kunjunganService->createKunjungan($data, $request);

        if ($request->expectsJson()) {
            $kunjungan->load(['lahan', 'petugas']);
            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil ditambahkan',
                'data' => new KunjunganResource($kunjungan),
            ], 201);
        }
        return redirect()->route('petugas.kunjungan.index')->with('success', 'Kunjungan berhasil ditambahkan');
    }

    public function edit(Request $request, Kunjungan $kunjungan)
    {
        if ($kunjungan->petugas_id !== Auth::id()) {
            abort(403);
        }
        $lahanList = Lahan::with('petani')->where('petugas_id', Auth::id())->get();
        $selectedLahan = $kunjungan->lahan;
        return view('petugas.kunjungan.edit', compact('kunjungan', 'lahanList', 'selectedLahan'));
    }

    public function update(Request $request, Kunjungan $kunjungan)
    {
        if ($kunjungan->petugas_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak berhak mengedit kunjungan ini'], 403);
            }
            abort(403);
        }

        $validated = $request->validate([
            'lahan_id' => 'required|exists:lahan,id',
            'tanggal_kunjungan' => 'required|date',
            'kondisi_lahan' => 'required|in:Sangat Baik,Baik,Sedang,Kurang Baik,Sangat Kurang Baik',
            'status_tindak_lanjut' => 'required|in:Aman,Perlu Pemantauan,Perlu Tindakan',
            'catatan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'hapus_foto' => 'nullable|boolean',
        ]);

        $this->kunjunganService->updateKunjungan($kunjungan, $validated, $request);

        if ($request->expectsJson()) {
            $kunjungan->load(['lahan', 'petugas']);
            return response()->json(['success' => true, 'message' => 'Kunjungan berhasil diperbarui', 'data' => new KunjunganResource($kunjungan)]);
        }
        return redirect()->route('petugas.kunjungan.index')->with('success', 'Kunjungan berhasil diperbarui');
    }

    public function destroy(Request $request, Kunjungan $kunjungan)
    {
        if ($kunjungan->petugas_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak berhak menghapus kunjungan ini'],403);
            }
            abort(403);
        }

        $this->kunjunganService->deleteKunjungan($kunjungan);
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kunjungan berhasil dihapus']);
        }
        return redirect()->route('petugas.kunjungan.index')->with('success', 'Kunjungan berhasil dihapus');
    }
}
