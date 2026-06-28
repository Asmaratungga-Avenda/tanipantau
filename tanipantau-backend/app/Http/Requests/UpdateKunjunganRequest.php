<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk mengupdate data Kunjungan
 */
class UpdateKunjunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lahan_id' => 'sometimes|required|exists:lahan,id',
            'tanggal_kunjungan' => 'sometimes|required|date',
            'kondisi_lahan' => 'sometimes|required|in:Sangat Baik,Baik,Sedang,Kurang Baik,Sangat Kurang Baik',
            'catatan' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
            'status_tindak_lanjut' => 'sometimes|required|in:Aman,Perlu Pemantauan,Perlu Tindakan',
        ];
    }

    public function messages(): array
    {
        return [
            'lahan_id.exists' => 'Lahan tidak ditemukan',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi',
            'kondisi_lahan.required' => 'Kondisi lahan wajib dipilih',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran gambar maksimal 5MB',
        ];
    }
}
