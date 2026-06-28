<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request untuk mengupdate data Petani
 */
class UpdatePetaniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $petaniId = $this->route('petani');
        
        return [
            'nama' => 'sometimes|required|string|max:255',
            'nik' => [
                'sometimes',
                'required',
                'numeric',
                'digits_between:16,16',
                Rule::unique('petani', 'nik')->ignore($petaniId),
            ],
            'alamat' => 'sometimes|required|string|max:500',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten' => 'sometimes|required|string|max:255',
            'kecamatan' => 'sometimes|required|string|max:255',
            'desa' => 'sometimes|required|string|max:255',
            'nomor_hp' => 'sometimes|required|numeric|digits_between:10,15',
            'status_aktif' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama petani wajib diisi',
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
