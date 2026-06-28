<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource untuk Lahan
 * 
 * Mengformat response data Lahan ke format JSON yang konsisten
 */
class LahanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isPrivileged = $request->user() && in_array($request->user()->role, ['admin', 'manajer']);

        return [
            'id' => $this->id,
            'petani_id' => $this->petani_id,
            'petugas_id' => $this->petugas_id,
            'nama_lahan' => $this->nama_lahan,
            'komoditas' => $this->komoditas,
            'luas_lahan' => $this->luas_lahan,
            'garis_lintang' => $this->garis_lintang,
            'garis_bujur' => $this->garis_bujur,
            'tanggal_tanam' => $this->tanggal_tanam,
            'fase_lahan' => $this->fase_lahan,
            'status_aktif' => $this->status_aktif,
            'provinsi' => $this->provinsi ?? 'Jawa Timur',
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'desa' => $this->desa,
            'petani' => new PetaniResource($this->whenLoaded('petani')),
            'petugas' => $this->whenLoaded('petugas', fn() => [
                'id' => $this->petugas->id,
                'name' => $this->petugas->name,
                'email' => $this->when($isPrivileged, $this->petugas->email),
            ]),
            'kunjungan' => KunjunganResource::collection($this->whenLoaded('kunjungan')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
