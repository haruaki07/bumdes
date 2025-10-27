<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Modules\EBilling\Enums\PaymentMethodType;
use Modules\EBilling\Models\PaymentMethod;

class PaymentMethodController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read-payment-methods,ebil', only: ['index', 'show']),
            new Middleware('permission:update-payment-methods,ebil', only: ['update', 'updateStatus']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::datatable();

        return view('e-billing::payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return view('e-billing::payment-methods.show', compact('paymentMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => ['required', 'string', Rule::enum(PaymentMethodType::class)],
            'account_number' => 'required_if:type,'.PaymentMethodType::BANK_TRANSFER->value,
            'brand_logo' => 'file|mimes:jpg,png,svg|max:2048',
            'fee_type' => 'nullable|in:NONE,PERCENT,FIXED',
            'fee_amount' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('brand_logo')) {
            // need to save the full url path to storage disk, so the /storage prefix is saved to database
            // it's because asset() is used to show the image in the frontend
            // this will return /storage/payment_method_images/name.jpg
            $brand_logo_url = Storage::url(Storage::disk('public')->putFileAs(
                'payment_method_images',
                $request->file('brand_logo'),
                time().'_'.strtolower($request->file('brand_logo')->getClientOriginalName())
            ));
        }

        $feeType = $request->input('fee_type', 'NONE');
        $feeAmount = 0;
        if ($feeType === 'PERCENT' || $feeType === 'FIXED') {
            $feeAmount = (float) $request->input('fee_amount', 0);
        }

        $paymentMethod->update([
            ...$request->except(['type', 'brand_logo', 'is_active', 'fee_amount', 'fee_type']),
            'is_active' => $request->boolean('is_active'),
            'brand_logo' => $request->hasFile('brand_logo') ? $brand_logo_url : $paymentMethod->brand_logo,
            'fee_type' => $feeType,
            'fee_amount' => $feeAmount,
        ]);

        return redirect()->route('e-billing.settings.payment-methods.index')->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function updateStatus(Request $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('e-billing.settings.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil '.($paymentMethod->is_active ? 'diaktifkan' : 'dinonaktifkan').'.');
    }
}
