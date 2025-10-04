<?php

namespace Modules\EBilling\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\EBilling\Models\Customer;

/**
 * @property Customer $customer
 */
class UpdateCustomerRequest extends FormRequest
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
        return [
            'customer_id' => ['required', 'string', 'max:50', Rule::unique('ebil_customers', 'customer_id')->ignore($this->customer->id)],
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|phone:mobile,ID',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'site_id' => 'required|exists:ebil_sites,id',
            'package_id' => 'required|exists:ebil_packages,id',
            'device_id' => 'required|exists:ebil_devices,id',
            'serial_number' => 'nullable|string|max:100',
            'mac_address' => 'nullable|string|max:100',
            'due' => 'required|integer|min:1|max:28',
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'ID Pelanggan',
            'name' => 'Nama Pelanggan',
            'email' => 'Email',
            'phone' => 'Nomor HP',
            'address' => 'Alamat',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'site_id' => 'Site',
            'package_id' => 'Paket',
            'device_id' => 'Perangkat',
            'serial_number' => 'Serial Number',
            'mac_address' => 'MAC Address',
            'due' => 'Tanggal Jatuh Tempo',
        ];
    }
}
