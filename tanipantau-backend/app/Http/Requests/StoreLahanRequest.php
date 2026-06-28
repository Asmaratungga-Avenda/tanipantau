<?php

namespace App\Http\Requests;

use App\Models\Lahan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request untuk menyimpan data Lahan baru
 */
class StoreLahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'petani_id' => 'required|exists:petani,id',
            'petugas_id' => 'nullable|exists:users,id',
            'nama_lahan' => 'required|string|max:255',
            'komoditas' => 'required|string|max:100',
            'luas_lahan' => 'required|numeric|min:0.01',
            'garis_lintang' => 'required|numeric|between:-90,90',
            'garis_bujur' => 'required|numeric|between:-180,180',
            'tanggal_tanam' => 'nullable|date',
            'fase_lahan' => 'required|in:Persiapan,Penanaman,Pertumbuhan,Panen,Panen Selesai',
            'status_aktif' => 'sometimes|boolean',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $garisLintang = $this->input('garis_lintang');
            $garisBujur = $this->input('garis_bujur');

            if ($garisLintang && $garisBujur) {
                $exists = Lahan::where('garis_lintang', $garisLintang)
                    ->where('garis_bujur', $garisBujur)
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
            'petani_id.required' => 'Petani wajib dipilih',
            'petani_id.exists' => 'Petani tidak ditemukan',
            'nama_lahan.required' => 'Nama lahan wajib diisi',
            'komoditas.required' => 'Komoditas wajib diisi',
            'luas_lahan.required' => 'Luas lahan wajib diisi',
            'luas_lahan.min' => 'Luas lahan minimal 0.01 hektar',
            'fase_lahan.required' => 'Fase lahan wajib dipilih',
            'kabupaten.required' => 'Kabupaten wajib diisi',
            'kecamatan.required' => 'Kecamatan wajib diisi',
        ];
    }
}
