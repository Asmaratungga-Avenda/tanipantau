<?php

namespace App\Http\Requests;

use App\Models\Lahan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request untuk mengupdate data Lahan
 */
class UpdateLahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'petani_id' => 'sometimes|required|exists:petani,id',
            'petugas_id' => 'nullable|exists:users,id',
            'nama_lahan' => 'sometimes|required|string|max:255',
            'komoditas' => 'sometimes|required|string|max:100',
            'luas_lahan' => 'sometimes|required|numeric|min:0.01',
            'garis_lintang' => 'required|numeric|between:-90,90',
            'garis_bujur' => 'required|numeric|between:-180,180',
            'tanggal_tanam' => 'nullable|date',
            'fase_lahan' => 'sometimes|required|in:Persiapan,Penanaman,Pertumbuhan,Panen,Panen Selesai',
            'status_aktif' => 'sometimes|boolean',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten' => 'sometimes|required|string|max:255',
            'kecamatan' => 'sometimes|required|string|max:255',
            'desa' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $garisLintang = $this->input('garis_lintang');
            $garisBujur = $this->input('garis_bujur');
            $currentLahan = $this->route('lahan');

            if ($garisLintang && $garisBujur) {
                $exists = Lahan::where('garis_lintang', $garisLintang)
                    ->where('garis_bujur', $garisBujur)
                    ->where('id', '!=', $currentLahan?->id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('garis_lintang', 'Koordinat ini sudah digunakan oleh lahan lain.');
                    $validator->errors()->add('garis_bujur', 'Koordinat ini sudah digunakan oleh lahan lain.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'petani_id.exists' => 'Petani tidak ditemukan',
            'nama_lahan.required' => 'Nama lahan wajib diisi',
            'komoditas.required' => 'Komoditas wajib diisi',
            'luas_lahan.required' => 'Luas lahan wajib diisi',
            'luas_lahan.min' => 'Luas lahan minimal 0.01 hektar',
            'fase_lahan.required' => 'Fase lahan wajib dipilih',
        ];
    }
}
