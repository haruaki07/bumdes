<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $profileId = $this->user()->wargaProfile?->id;

        return [
            // Essential fields (required)
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
                Rule::unique('warga_profiles', 'nik')->ignore($profileId),
            ],
            'phone' => ['required', 'phone:mobile,ID'],
            'address' => ['required', 'string'],

            // Optional fields
            'kk' => ['nullable', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:L,P'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kabupaten' => ['nullable', 'string', 'max:255'],
            'provinsi' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'size:5', 'regex:/^[0-9]{5}$/'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', 'in:belum_kawin,kawin,cerai_hidup,cerai_mati'],
            'religion' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nik' => 'NIK',
            'kk' => 'Nomor KK',
            'phone' => 'Nomor WhatsApp',
            'place_of_birth' => 'Tempat Lahir',
            'date_of_birth' => 'Tanggal Lahir',
            'gender' => 'Jenis Kelamin',
            'address' => 'Alamat',
            'rt' => 'RT',
            'rw' => 'RW',
            'kelurahan' => 'Kelurahan/Desa',
            'kecamatan' => 'Kecamatan',
            'kabupaten' => 'Kabupaten/Kota',
            'provinsi' => 'Provinsi',
            'postal_code' => 'Kode Pos',
            'occupation' => 'Pekerjaan',
            'marital_status' => 'Status Perkawinan',
            'religion' => 'Agama',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nik.regex' => 'Format NIK tidak valid. Harus berupa 16 digit angka.',
            'kk.size' => 'Nomor KK harus terdiri dari 16 digit.',
            'kk.regex' => 'Format Nomor KK tidak valid. Harus berupa 16 digit angka.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.phone' => 'Format nomor WhatsApp tidak valid. Contoh: 081234567890',
            'address.required' => 'Alamat wajib diisi.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
            'postal_code.size' => 'Kode pos harus terdiri dari 5 digit.',
            'postal_code.regex' => 'Kode pos harus terdiri dari 5 digit angka.',
        ];
    }
}
