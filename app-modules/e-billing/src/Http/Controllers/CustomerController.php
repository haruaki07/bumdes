<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Modules\EBilling\Http\Requests\Customer\CreateCustomerRequest;
use Modules\EBilling\Http\Requests\Customer\UpdateCustomerRequest;
use Modules\EBilling\Imports\CustomersImport;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Device;
use Modules\EBilling\Models\Package;
use Modules\EBilling\Models\Site;

class CustomerController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::datatable();

        return view('e-billing::customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sites = Site::all();
        $devices = Device::all();
        $packages = Package::all();

        return view('e-billing::customers.create', compact('sites', 'devices', 'packages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCustomerRequest $request)
    {
        $data = $request->validated();

        Customer::create([
            ...$data,
            'registration_date' => now(),
        ]);

        return redirect()->route('e-billing.master-data.customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('e-billing::customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $sites = Site::all();
        $devices = Device::all();
        $packages = Package::all();

        return view('e-billing::customers.edit', compact('customer', 'sites', 'devices', 'packages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        $customer->update($data);

        return redirect()->route('e-billing.master-data.customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('e-billing.master-data.customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    /**
     * Handle customer import upload.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt|max:5120',
        ]);

        try {
            (new CustomersImport)->import($request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $messages = collect($failures)->map(function ($failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());

                return "<li>Baris {$row}: Error pada kolom \"{$attribute}\" — {$errors}</li>";
            });

            return back()->with('import_error', $messages->join("\n"));
        } catch (\Throwable $e) {
            logger()->error('Error during customer import: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data pelanggan.');
        }

        return redirect()->back()->with('success', 'Data pelanggan berhasil ditambahkan.');
    }
}
