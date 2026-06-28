<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk menyimpan data Petani baru
 */
class StorePetaniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nik' => 'required|numeric|digits_between:16,16|unique:petani,nik',
            'alamat' => 'required|string|max:500',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'nomor_hp' => 'required|numeric|digits_between:10,15',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama petani wajib diisi',
            'nik.required' => 'NIK wajib diisi',
            'nik.numeric' => 'NIK hanya boleh berisi angka',
            'nik.digits_between' => 'NIK harus 16 digit',
            'nik.unique' => 'NIK sudah terdaftar',
            'alamat.required' => 'Alamat wajib diisi',
            'kabupaten.required' => 'Kabupaten wajib diisi',
            'kecamatan.required' => 'Kecamatan wajib diisi',
            'desa.required' => 'Desa wajib diisi',
            'nomor_hp.required' => 'Nomor HP wajib diisi',
            'nomor_hp.numeric' => 'Nomor HP hanya boleh berisi angka',
            'nomor_hp.digits_between' => 'Nomor HP harus antara 10-15 digit',
        ];
    }
}
