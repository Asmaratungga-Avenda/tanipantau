<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource untuk Petani
 * 
 * Mengformat response data Petani ke format JSON yang konsisten
 */
class PetaniResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isPrivileged = $request->user() && in_array($request->user()->role, ['admin', 'manajer']);

        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nik' => $this->when($isPrivileged, $this->nik),
            'alamat' => $this->alamat,
            'provinsi' => $this->provinsi ?? 'Jawa Timur',
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'desa' => $this->desa,
            'nomor_hp' => $this->when($isPrivileged, $this->nomor_hp),
            'status_aktif' => $this->status_aktif,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
