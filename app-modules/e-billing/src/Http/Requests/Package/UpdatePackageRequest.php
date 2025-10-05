<?php

namespace Modules\EBilling\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property \Modules\EBilling\Models\Package $package
 */
class UpdatePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'price' => normalize_currency($this->price),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:ebil_packages,code,'.$this->package->id,
            'description' => 'nullable|string|max:255',
            'bandwidth' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'due' => 'required|integer|min:1',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama paket',
            'code' => 'kode paket',
            'description' => 'deskripsi',
            'bandwidth' => 'bandwidth',
            'price' => 'harga',
            'due' => 'jatuh tempo',
        ];
    }
}
