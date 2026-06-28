<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\Lahan;
use App\Models\User;
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
        /** @var User $user */
        $user = Auth::user();

        $query = Kunjungan::with(['lahan.petani', 'petugas']);

        // Petugas hanya melihat kunjungan pada lahan miliknya
        if ($user->role === 'petugas') {
            $query->whereHas('lahan', function ($q) use ($user) {
                $q->where('petugas_id', $user->id);
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('lahan', function ($q2) use ($s) {
                    $q2->where('nama_lahan', 'like', "%{$s}%");
                })->orWhere('catatan', 'like', "%{$s}%");
            });
        }

        $kunjungan = $query->latest('tanggal_kunjungan')->paginate(15)->withQueryString();

        // Dropdown lahan — petugas hanya melihat lahannya sendiri
        $lahanQuery = Lahan::with('petani');
        if ($user->role === 'petugas') {
            $lahanQuery->where('petugas_id', $user->id);
        }
        $lahanList = $lahanQuery->orderBy('nama_lahan')->get();

        $statusMap = [
            'Aman'               => ['label' => 'Aman',               'class' => 'bg-success'],
            'Perlu Pemantauan'   => ['label' => 'Perlu Pemantauan',   'class' => 'bg-warning'],
            'Perlu Tindakan'     => ['label' => 'Perlu Tindakan',     'class' => 'bg-danger'],
        ];

        $kondisiMap = [
            'Sangat Baik'        => ['label' => 'Sangat Baik',        'class' => 'bg-success'],
            'Baik'               => ['label' => 'Baik',               'class' => 'bg-info'],
            'Sedang'             => ['label' => 'Sedang',             'class' => 'bg-warning'],
            'Kurang Baik'        => ['label' => 'Kurang Baik',        'class' => 'bg-secondary'],
            'Sangat Kurang Baik' => ['label' => 'Sangat Kurang Baik', 'class' => 'bg-danger'],
        ];

        return view('admin.kunjungan.index', compact(
            'kunjungan', 'lahanList', 'statusMap', 'kondisiMap'
        ));
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($user->role !== 'petugas') {
            abort(403, 'Hanya petugas yang dapat menambah kunjungan');
        }

        $data = $request->validate([
            'lahan_id'             => 'required|exists:lahan,id',
            'tanggal_kunjungan'    => 'required|date',
            'kondisi_lahan'        => 'required|in:Sangat Baik,Baik,Sedang,Kurang Baik,Sangat Kurang Baik',
            'catatan'              => 'nullable|string|max:1000',
            'foto'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status_tindak_lanjut' => 'required|in:Aman,Perlu Pemantauan,Perlu Tindakan',
        ], [
            'lahan_id.required'             => 'Lahan wajib dipilih',
            'lahan_id.exists'               => 'Lahan tidak ditemukan',
            'tanggal_kunjungan.required'    => 'Tanggal kunjungan wajib diisi',
            'kondisi_lahan.required'        => 'Kondisi lahan wajib dipilih',
            'status_tindak_lanjut.required' => 'Status tindak lanjut wajib diisi',
            'foto.image'                    => 'File harus berupa gambar',
            'foto.max'                      => 'Ukuran gambar maksimal 5MB',
        ]);

        $data['tanggal_kunjungan'] = $data['tanggal_kunjungan'] . ' ' . now()->format('H:i:s');

        // Validate that petugas only uses their own lahan
        $lahan = Lahan::findOrFail($data['lahan_id']);
        if ($lahan->petugas_id !== $user->id) {
            abort(403, 'Anda hanya dapat membuat kunjungan pada lahan yang ditugaskan kepada Anda');
        }

        $this->kunjunganService->createKunjungan($data, $request);

        return redirect()->route('admin.kunjungan.index')
            ->with('success', 'Kunjungan berhasil ditambahkan.');
    }

    public function update(Request $request, Kunjungan $kunjungan)
    {
        // Petugas hanya boleh edit kunjungan miliknya sendiri
        /** @var User $user */
        $user = Auth::user();
        if ($user->role !== 'petugas') {
            abort(403, 'Hanya petugas yang dapat mengedit kunjungan');
        }
        if ($kunjungan->petugas_id !== $user->id) {
            abort(403, 'Anda hanya dapat mengedit kunjungan milik sendiri');
        }

        $data = $request->validate([
            'lahan_id'             => 'required|exists:lahan,id',
            'tanggal_kunjungan'    => 'required|date',
            'kondisi_lahan'        => 'required|in:Sangat Baik,Baik,Sedang,Kurang Baik,Sangat Kurang Baik',
            'catatan'              => 'nullable|string|max:1000',
            'foto'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status_tindak_lanjut' => 'required|in:Aman,Perlu Pemantauan,Perlu Tindakan',
            'hapus_foto'           => 'nullable|boolean',
        ], [
            'lahan_id.required'             => 'Lahan wajib dipilih',
            'lahan_id.exists'               => 'Lahan tidak ditemukan',
            'tanggal_kunjungan.required'    => 'Tanggal kunjungan wajib diisi',
            'kondisi_lahan.required'        => 'Kondisi lahan wajib dipilih',
            'status_tindak_lanjut.required' => 'Status tindak lanjut wajib diisi',
            'foto.image'                    => 'File harus berupa gambar',
            'foto.max'                      => 'Ukuran gambar maksimal 5MB',
        ]);

        $data['tanggal_kunjungan'] = $data['tanggal_kunjungan'] . ' ' . now()->format('H:i:s');

        $this->kunjunganService->updateKunjungan($kunjungan, $data, $request);

        return redirect()->route('admin.kunjungan.index')
            ->with('success', 'Kunjungan berhasil diperbarui.');
    }

    public function destroy(Kunjungan $kunjungan)
    {
        // Hanya petugas yang boleh menghapus kunjungan miliknya
        /** @var User $user */
        $user = Auth::user();
        if ($user->role !== 'petugas') {
            abort(403, 'Hanya petugas yang dapat menghapus kunjungan');
        }
        if ($kunjungan->petugas_id !== $user->id) {
            abort(403, 'Anda hanya dapat menghapus kunjungan milik sendiri');
        }

        $this->kunjunganService->deleteKunjungan($kunjungan);

        return redirect()->route('admin.kunjungan.index')
            ->with('success', 'Kunjungan berhasil dihapus.');
    }

    public function hapusFoto(Kunjungan $kunjungan)
    {
        // Check authorization
        /** @var User $user */
        $user = Auth::user();
        if ($user->role !== 'petugas' || $kunjungan->petugas_id !== $user->id) {
            abort(403, 'Anda hanya dapat menghapus foto kunjungan milik sendiri');
        }

        $this->kunjunganService->updateKunjungan($kunjungan, ['foto' => null], null);

        return redirect()->route('admin.kunjungan.index')
            ->with('success', 'Foto kunjungan berhasil dihapus.');
    }
}
