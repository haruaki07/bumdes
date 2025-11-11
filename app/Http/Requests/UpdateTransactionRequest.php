<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => normalize_currency($this->amount),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $transactionId = $this->route('transaction')->id ?? $this->route('transaction');

        return [
            'transaction_category_id' => ['required', 'exists:transaction_categories,id'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'reference_number' => ['nullable', 'string', 'max:100', 'unique:transactions,reference_number,'.$transactionId],
            'funding_request_id' => ['nullable', 'exists:funding_requests,id'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'transaction_category_id' => 'kategori transaksi',
            'type' => 'tipe transaksi',
            'amount' => 'jumlah',
            'transaction_date' => 'tanggal transaksi',
            'description' => 'deskripsi',
            'reference_number' => 'nomor referensi',
            'funding_request_id' => 'pendanaan terkait',
            'document' => 'dokumen',
            'notes' => 'catatan',
        ];
    }
}
