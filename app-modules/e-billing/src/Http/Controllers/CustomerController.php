<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|string|max:50|unique:ebil_customers,customer_id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'site_id' => 'required|exists:ebil_sites,id',
            'package_id' => 'required|exists:ebil_packages,id',
            'device_id' => 'required|exists:ebil_devices,id',
            'serial_number' => 'nullable|string|max:100',
            'mac_address' => 'nullable|string|max:100',
            'due' => 'required|integer',
        ]);

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
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'string', 'max:50', Rule::unique('ebil_customers', 'customer_id')->ignore($customer->id)],
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'site_id' => 'required|exists:ebil_sites,id',
            'package_id' => 'required|exists:ebil_packages,id',
            'device_id' => 'required|exists:ebil_devices,id',
            'serial_number' => 'nullable|string|max:100',
            'mac_address' => 'nullable|string|max:100',
            'due' => 'required|integer',
        ]);

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
}
