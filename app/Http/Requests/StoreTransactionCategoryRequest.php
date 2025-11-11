<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionCategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:transaction_categories,code'],
            'type' => ['required', 'in:income,expense'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:transaction_categories,id'],
            'is_active' => ['boolean'],
            'order' => ['integer', 'min:0'],
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama kategori',
            'code' => 'kode',
            'type' => 'tipe',
            'description' => 'deskripsi',
            'parent_id' => 'kategori induk',
            'is_active' => 'status aktif',
            'order' => 'urutan',
        ];
    }
}
