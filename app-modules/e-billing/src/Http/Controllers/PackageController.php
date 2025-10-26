<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\EBilling\Http\Requests\Package\CreatePackageRequest;
use Modules\EBilling\Http\Requests\Package\UpdatePackageRequest;
use Modules\EBilling\Models\Package;

class PackageController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read-packages,ebil', only: ['index', 'show']),
            new Middleware('permission:create-packages,ebil', only: ['create', 'store']),
            new Middleware('permission:update-packages,ebil', only: ['edit', 'update']),
            new Middleware('permission:delete-packages,ebil', only: ['destroy']),
        ];
    }

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
        return view('e-billing::packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePackageRequest $request)
    {
        $data = $request->validated();

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
        return view('e-billing::packages.edit', compact('package'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageRequest $request, Package $package)
    {
        $data = $request->validated();

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
