<?php

namespace App\Http\Requests;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFundingRequestRequest extends FormRequest
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
            'amount' => normalize_currency($this->amount),
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
            'business_id' => [
                'required',
                'integer',
                'exists:businesses,id',
                function ($attribute, $value, $fail) {
                    $business = Business::find($value);
                    if ($business && $business->owner_id !== Auth::id()) {
                        $fail('Anda tidak memiliki akses ke usaha ini.');
                    }
                },
            ],
            'amount' => [
                'required',
                'numeric',
                'min:1000000',
                'max:500000000',
            ],
            'purpose' => [
                'required',
                'string',
                'min:50',
                'max:2000',
            ],
            'repayment_duration_months' => [
                'nullable',
                'integer',
                'min:1',
                'max:60',
            ],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'business_id' => 'usaha',
            'amount' => 'jumlah pendanaan',
            'purpose' => 'tujuan penggunaan dana',
            'repayment_duration_months' => 'durasi pengembalian',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Jumlah pendanaan minimal Rp 1.000.000.',
            'amount.max' => 'Jumlah pendanaan maksimal Rp 500.000.000.',
            'purpose.min' => 'Tujuan penggunaan dana harus minimal 50 karakter.',
            'repayment_duration_months.min' => 'Durasi pengembalian minimal 1 bulan.',
            'repayment_duration_months.max' => 'Durasi pengembalian maksimal 60 bulan.',
        ];
    }
}
