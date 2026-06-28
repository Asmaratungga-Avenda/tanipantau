<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource untuk Kunjungan
 * 
 * Mengformat response data Kunjungan ke format JSON yang konsisten
 */
class KunjunganResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isPrivileged = $request->user() && in_array($request->user()->role, ['admin', 'manajer']);

        return [
            'id' => $this->id,
            'lahan_id' => $this->lahan_id,
            'petugas_id' => $this->petugas_id,
            'tanggal_kunjungan' => $this->tanggal_kunjungan,
            'kondisi_lahan' => $this->kondisi_lahan,
            'catatan' => $this->catatan,
            'foto' => $this->foto,
            'foto_url' => $this->foto_url,
            'status_tindak_lanjut' => $this->status_tindak_lanjut,
            'lahan' => new LahanResource($this->whenLoaded('lahan')),
            'user' => $this->when($this->relationLoaded('petugas') && $this->petugas, function () use ($isPrivileged) {
                return [
                    'id' => $this->petugas->id,
                    'name' => $this->petugas->name,
                    'email' => $this->when($isPrivileged, $this->petugas->email),
                    'role' => $this->when($isPrivileged, $this->petugas->role),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
