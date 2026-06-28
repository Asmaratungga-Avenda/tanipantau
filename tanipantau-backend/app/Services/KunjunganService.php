<?php

namespace App\Services;

use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KunjunganService
{
    public function __construct(
        protected UploadService $uploadService
    ) {}

    public function createKunjungan(array $data, ?Request $request = null): Kunjungan
    {
        if ($request && $request->hasFile('foto')) {
            $directory = 'kunjungan/' . date('Y/m');
            $data['foto'] = $this->uploadService->uploadFile($request->file('foto'), $directory);
        }

        $data['petugas_id'] = Auth::id();
        return Kunjungan::create($data);
    }

    public function updateKunjungan(Kunjungan $kunjungan, array $data, ?Request $request = null): Kunjungan
    {
        if ($request && $request->has('hapus_foto') && $request->hapus_foto) {
            $this->uploadService->deleteFile($kunjungan->foto);
            $data['foto'] = null;
        } else if ($request && $request->hasFile('foto')) {
            $directory = 'kunjungan/' . date('Y/m');
            $data['foto'] = $this->uploadService->replaceFile($request->file('foto'), $kunjungan->foto, $directory);
        }

        $kunjungan->update($data);
        return $kunjungan;
    }

    public function deleteKunjungan(Kunjungan $kunjungan): bool
    {
        if ($kunjungan->foto) {
            $this->uploadService->deleteFile($kunjungan->foto);
        }
        return $kunjungan->delete();
    }
}
