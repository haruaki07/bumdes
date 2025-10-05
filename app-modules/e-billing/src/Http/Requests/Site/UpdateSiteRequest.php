<?php

namespace Modules\EBilling\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteRequest extends FormRequest
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
            'code' => 'nullable|string|max:50|unique:ebil_sites,code,'.$this->site->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'kode',
            'name' => 'nama',
            'description' => 'deskripsi',
        ];
    }
}
