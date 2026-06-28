<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petani;
use Illuminate\Http\Request;

class PetaniController extends Controller
{
    public function index(Request $request)
    {
        $query = Petani::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('desa', 'like', "%{$s}%")
                  ->orWhere('kecamatan', 'like', "%{$s}%")
                  ->orWhere('kabupaten', 'like', "%{$s}%");
            });
        }

        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        $petani = $query->latest()->paginate(15)->withQueryString();

        return view('admin.petani.index', compact('petani'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'       => 'required|string|max:255',
            'nik'        => 'required|string|unique:petani,nik|max:20',
            'alamat'     => 'required|string|max:500',
            'provinsi'   => 'nullable|string|max:100',
            'kabupaten'  => 'required|string|max:255',
            'kecamatan'  => 'required|string|max:255',
            'desa'       => 'required|string|max:255',
            'nomor_hp'   => 'required|string|max:20',
        ], [
            'nama.required'      => 'Nama petani wajib diisi',
            'nik.required'       => 'NIK wajib diisi',
            'nik.unique'         => 'NIK sudah terdaftar',
            'alamat.required'    => 'Alamat wajib diisi',
            'kabupaten.required' => 'Kabupaten wajib diisi',
            'kecamatan.required' => 'Kecamatan wajib diisi',
            'desa.required'      => 'Desa wajib diisi',
            'nomor_hp.required'  => 'Nomor HP wajib diisi',
        ]);

        $data['provinsi'] = $data['provinsi'] ?? 'Jawa Timur';

        Petani::create($data);

        return redirect()->route('admin.petani.index')
            ->with('success', 'Petani berhasil ditambahkan.');
    }

    public function update(Request $request, Petani $petani)
    {
        $data = $request->validate([
            'nama'       => 'required|string|max:255',
            'nik'        => 'required|string|unique:petani,nik,' . $petani->id . '|max:20',
            'alamat'     => 'required|string|max:500',
            'provinsi'   => 'nullable|string|max:100',
            'kabupaten'  => 'required|string|max:255',
            'kecamatan'  => 'required|string|max:255',
            'desa'       => 'required|string|max:255',
            'nomor_hp'   => 'required|string|max:20',
        ], [
            'nama.required'      => 'Nama petani wajib diisi',
            'nik.required'       => 'NIK wajib diisi',
            'nik.unique'         => 'NIK sudah terdaftar',
            'alamat.required'    => 'Alamat wajib diisi',
            'kabupaten.required' => 'Kabupaten wajib diisi',
            'kecamatan.required' => 'Kecamatan wajib diisi',
            'desa.required'      => 'Desa wajib diisi',
            'nomor_hp.required'  => 'Nomor HP wajib diisi',
        ]);

        $data['provinsi'] = $data['provinsi'] ?? 'Jawa Timur';

        $petani->update($data);

        return redirect()->route('admin.petani.index')
            ->with('success', 'Data petani berhasil diperbarui.');
    }

    public function destroy(Petani $petani)
    {
        $petani->delete();

        return redirect()->route('admin.petani.index')
            ->with('success', 'Petani berhasil dihapus.');
    }
}
