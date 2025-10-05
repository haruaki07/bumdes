<?php

namespace Modules\EBilling\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'nullable|string|max:50|unique:ebil_devices,code,'.$this->device->id,
            'brand' => 'required|string|max:255',
            'model' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ebil_devices', 'model')
                    ->where(fn ($q) => $q->where('brand', $this->brand))
                    ->ignore($this->device->id),
            ],
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'kode',
            'brand' => 'merek',
            'model' => 'model',
            'description' => 'deskripsi',
        ];
    }

    public function messages(): array
    {
        return [
            'model.unique' => 'Model perangkat dengan merek tersebut sudah ada.',
        ];
    }
}
