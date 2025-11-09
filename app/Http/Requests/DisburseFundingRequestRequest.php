<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisburseFundingRequestRequest extends FormRequest
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
            'disbursement_date' => ['required', 'date', 'before_or_equal:today'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:transfer,tunai'],
            'bank_name' => ['required_if:payment_method,transfer', 'nullable', 'string', 'max:255'],
            'account_number' => ['required_if:payment_method,transfer', 'nullable', 'string', 'max:255'],
            'account_holder_name' => ['required_if:payment_method,transfer', 'nullable', 'string', 'max:255'],
            'proof_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'disbursement_date' => 'tanggal pencairan',
            'amount' => 'jumlah',
            'payment_method' => 'metode pembayaran',
            'bank_name' => 'nama bank',
            'account_number' => 'nomor rekening',
            'account_holder_name' => 'nama pemilik rekening',
            'proof_document' => 'bukti pencairan',
            'notes' => 'catatan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'disbursement_date.before_or_equal' => 'Tanggal pencairan tidak boleh melebihi hari ini.',
            'bank_name.required_if' => 'Nama bank wajib diisi untuk metode transfer.',
            'account_number.required_if' => 'Nomor rekening wajib diisi untuk metode transfer.',
            'account_holder_name.required_if' => 'Nama pemilik rekening wajib diisi untuk metode transfer.',
        ];
    }
}
