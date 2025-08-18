<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Modules\EBilling\Models\Package;
use TakiElias\TablarKit\Builder\FormBuilder;

class PackageController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::datatable();

        return view('e-billing::packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $form = FormBuilder::create()
            ->action(route('e-billing.master-data.packages.store'))
            ->method('POST')

            ->input('name', 'Nama Paket', ['topGap' => ''])
            ->required()
            ->placeholder('Masukkan nama paket')

            ->input('description', 'Deskripsi', ['topGap' => ''])
            ->placeholder('Masukkan deskripsi paket (opsional)')

            ->number('bandwidth', 'Bandwidth (Mbps)', ['topGap' => ''])
            ->required()
            ->placeholder('Masukkan bandwidth paket')

            ->number('price', 'Harga (IDR)', ['topGap' => ''])
            ->required()
            ->placeholder('Masukkan harga paket')
            ->step('any')

            ->number('due', 'Tanggal jatuh tempo', ['topGap' => ''])
            ->required()
            ->placeholder('Masukkan jatuh tempo paket')

            ->button('Simpan', '', ['topGap' => 'd-inline-flex'])
            ->addClass('btn btn-primary');

        return view('e-billing::packages.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'bandwidth' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'due' => 'required|integer|min:1',
        ]);

        Package::create($data);

        return redirect()->route('e-billing.master-data.packages.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        return view('e-billing::packages.show', compact('package'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        $form = FormBuilder::create()
            ->action(route('e-billing.master-data.packages.update', $package))
            ->method('PUT')

            ->input('name', 'Nama Paket', ['topGap' => ''])
            ->required()
            ->placeholder('Masukkan nama paket')
            ->value($package->name)

            ->input('description', 'Deskripsi', ['topGap' => ''])
            ->placeholder('Masukkan deskripsi paket (opsional)')
            ->value($package->description)

            ->number('bandwidth', 'Bandwidth (Mbps)', ['topGap' => ''])
            ->required()
            ->placeholder('Masukkan bandwidth paket')
            ->value($package->bandwidth)

            ->number('price', 'Harga (IDR)', ['topGap' => ''])
            ->required()
            ->placeholder('Masukkan harga paket')
            ->value($package->price)
            ->step('any')

            ->number('due', 'Tanggal jatuh tempo', ['topGap' => ''])
            ->required()
            ->placeholder('Masukkan jatuh tempo paket')
            ->value($package->due)

            ->button('Simpan', '', ['topGap' => 'd-inline-flex'])
            ->addClass('btn btn-primary');

        return view('e-billing::packages.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'bandwidth' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'due' => 'required|integer|min:1',
        ]);

        $package->update($data);

        return redirect()->route('e-billing.master-data.packages.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('e-billing.master-data.packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
