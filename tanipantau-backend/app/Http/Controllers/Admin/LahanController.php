<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lahan;
use App\Models\PenugasanLahan;
use App\Models\Petani;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LahanController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Lahan::with(['petani', 'petugas']);

        // Petugas hanya melihat lahan yang ditugaskan kepadanya
        if ($user->role === 'petugas') {
            $query->where('petugas_id', $user->id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_lahan', 'like', "%{$s}%")
                  ->orWhere('komoditas', 'like', "%{$s}%");
            });
        }

        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        $lahan = $query->latest()->paginate(15)->withQueryString();
        $allLahan = Lahan::with(['petani'])->get(); // Get all lahan for the map
        $petaniList = Petani::orderBy('nama')->get();
        $petugasList = User::where('role', 'petugas')->orderBy('name')->get();

        // Mapping fase
        $growthPhases = [
            'persiapan'     => ['label' => 'Persiapan',     'class' => 'bg-secondary'],
            'penanaman'     => ['label' => 'Penanaman',     'class' => 'bg-info'],
            'pertumbuhan'   => ['label' => 'Pertumbuhan',   'class' => 'bg-success'],
            'panen'         => ['label' => 'Panen',         'class' => 'bg-warning'],
            'panen_selesai' => ['label' => 'Panen Selesai', 'class' => 'bg-secondary'],
        ];

        return view('admin.lahan.index', compact('lahan', 'petaniList', 'growthPhases', 'petugasList', 'allLahan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'petani_id'     => 'required|exists:petani,id',
            'petugas_id'    => 'nullable|exists:users,id',
            'nama_lahan'    => 'required|string|max:255',
            'komoditas'     => 'required|in:Padi,Jagung,Hortikultura',
            'luas_lahan'    => 'required|numeric|min:0.01',
            'garis_lintang' => 'required|numeric|between:-90,90',
            'garis_bujur'   => 'required|numeric|between:-180,180',
            'tanggal_tanam' => 'nullable|date',
            'fase_lahan'    => 'required|in:Persiapan,Penanaman,Pertumbuhan,Panen,Panen Selesai',
            'status_aktif'  => 'sometimes|boolean',
            'provinsi'      => 'nullable|string|max:100',
            'kabupaten'     => 'required|string|max:255',
            'kecamatan'     => 'required|string|max:255',
            'desa'          => 'nullable|string|max:255',
        ], [
            'petani_id.required' => 'Petani wajib dipilih',
            'petani_id.exists'   => 'Petani tidak ditemukan',
            'nama_lahan.required' => 'Nama lahan wajib diisi',
            'komoditas.required'  => 'Komoditas wajib diisi',
            'komoditas.in'        => 'Komoditas harus Padi, Jagung, atau Hortikultura',
            'luas_lahan.required' => 'Luas lahan wajib diisi',
            'luas_lahan.min'      => 'Luas lahan minimal 0.01 hektar',
            'fase_lahan.required' => 'Fase lahan wajib dipilih',
            'kabupaten.required'  => 'Kabupaten wajib diisi',
            'kecamatan.required'  => 'Kecamatan wajib diisi',
            'garis_lintang.required' => 'Garis lintang wajib diisi',
            'garis_bujur.required'   => 'Garis bujur wajib diisi',
        ]);

        // Validate duplicate coordinates
        $garisLintang = $request->input('garis_lintang');
        $garisBujur = $request->input('garis_bujur');
        if ($garisLintang && $garisBujur) {
            $exists = Lahan::where('garis_lintang', $garisLintang)
                ->where('garis_bujur', $garisBujur)
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'garis_lintang' => 'Koordinat ini sudah digunakan oleh lahan lain.',
                    'garis_bujur' => 'Koordinat ini sudah digunakan oleh lahan lain.',
                ])->withInput();
            }
        }

        $data['status_aktif'] = $request->has('status_aktif') ? 1 : 0;
        $data['tanggal_tanam'] = $data['tanggal_tanam'] ?? now()->toDateString();

        DB::transaction(function () use ($data) {
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
        });

        return redirect()->route('admin.lahan.index')
            ->with('success', 'Lahan berhasil ditambahkan.');
    }

    public function update(Request $request, Lahan $lahan)
    {
        $data = $request->validate([
            'petani_id'     => 'required|exists:petani,id',
            'petugas_id'    => 'nullable|exists:users,id',
            'nama_lahan'    => 'required|string|max:255',
            'komoditas'     => 'required|in:Padi,Jagung,Hortikultura',
            'luas_lahan'    => 'required|numeric|min:0.01',
            'garis_lintang' => 'required|numeric|between:-90,90',
            'garis_bujur'   => 'required|numeric|between:-180,180',
            'tanggal_tanam' => 'nullable|date',
            'fase_lahan'    => 'required|in:Persiapan,Penanaman,Pertumbuhan,Panen,Panen Selesai',
            'status_aktif'  => 'sometimes|boolean',
            'provinsi'      => 'nullable|string|max:100',
            'kabupaten'     => 'required|string|max:255',
            'kecamatan'     => 'required|string|max:255',
            'desa'          => 'nullable|string|max:255',
        ], [
            'petani_id.required' => 'Petani wajib dipilih',
            'petani_id.exists'   => 'Petani tidak ditemukan',
            'nama_lahan.required' => 'Nama lahan wajib diisi',
            'komoditas.required'  => 'Komoditas wajib diisi',
            'komoditas.in'        => 'Komoditas harus Padi, Jagung, atau Hortikultura',
            'luas_lahan.required' => 'Luas lahan wajib diisi',
            'luas_lahan.min'      => 'Luas lahan minimal 0.01 hektar',
            'fase_lahan.required' => 'Fase lahan wajib dipilih',
            'kabupaten.required'  => 'Kabupaten wajib diisi',
            'kecamatan.required'  => 'Kecamatan wajib diisi',
            'garis_lintang.required' => 'Garis lintang wajib diisi',
            'garis_bujur.required'   => 'Garis bujur wajib diisi',
        ]);

        // Validate duplicate coordinates
        $garisLintang = $request->input('garis_lintang');
        $garisBujur = $request->input('garis_bujur');
        if ($garisLintang && $garisBujur) {
            $exists = Lahan::where('garis_lintang', $garisLintang)
                ->where('garis_bujur', $garisBujur)
                ->where('id', '!=', $lahan->id)
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'garis_lintang' => 'Koordinat ini sudah digunakan oleh lahan lain.',
                    'garis_bujur' => 'Koordinat ini sudah digunakan oleh lahan lain.',
                ])->withInput();
            }
        }

        $data['status_aktif'] = $request->has('status_aktif') ? 1 : 0;

        DB::transaction(function () use ($data, $lahan) {
            $oldPetugasId = $lahan->petugas_id;
            $lahan->update($data);

            $newPetugasId = $data['petugas_id'] ?? null;

            if ($oldPetugasId != $newPetugasId) {
                if ($oldPetugasId) {
                    PenugasanLahan::where('lahan_id', $lahan->id)
                        ->where('status', 'aktif')
                        ->update(['status' => 'dicabut']);
                }

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
        });

        return redirect()->route('admin.lahan.index')
            ->with('success', 'Data lahan berhasil diperbarui.');
    }

    public function destroy(Lahan $lahan)
    {
        $lahan->delete();

        return redirect()->route('admin.lahan.index')
            ->with('success', 'Lahan berhasil dihapus.');
    }
}
