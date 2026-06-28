<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk menyimpan data Kunjungan baru
 */
class StoreKunjunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lahan_id' => 'required|exists:lahan,id',
            'tanggal_kunjungan' => 'required|date',
            'kondisi_lahan' => 'required|in:Sangat Baik,Baik,Sedang,Kurang Baik,Sangat Kurang Baik',
            'catatan' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
            'status_tindak_lanjut' => 'required|in:Aman,Perlu Pemantauan,Perlu Tindakan',
        ];
    }

    public function messages(): array
    {
        return [
            'lahan_id.required' => 'Lahan wajib dipilih',
            'lahan_id.exists' => 'Lahan tidak ditemukan',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi',
            'kondisi_lahan.required' => 'Kondisi lahan wajib dipilih',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran gambar maksimal 5MB',
        ];
    }
}
