<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveFundingRequestRequest extends FormRequest
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
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'repayment_duration_months' => ['required', 'integer', 'min:1', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'interest_rate' => 'bunga',
            'repayment_duration_months' => 'durasi pengembalian',
            'notes' => 'catatan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'interest_rate.min' => 'Bunga minimal 0%.',
            'interest_rate.max' => 'Bunga maksimal 100%.',
            'repayment_duration_months.min' => 'Durasi pengembalian minimal 1 bulan.',
            'repayment_duration_months.max' => 'Durasi pengembalian maksimal 60 bulan.',
        ];
    }
}
