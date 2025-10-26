<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\EBilling\Http\Requests\Device\CreateDeviceRequest;
use Modules\EBilling\Http\Requests\Device\UpdateDeviceRequest;
use Modules\EBilling\Models\Device;

class DeviceController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read-devices,ebil', only: ['index', 'show']),
            new Middleware('permission:create-devices,ebil', only: ['create', 'store']),
            new Middleware('permission:update-devices,ebil', only: ['edit', 'update']),
            new Middleware('permission:delete-devices,ebil', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devices = Device::datatable();

        return view('e-billing::devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('e-billing::devices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDeviceRequest $request)
    {
        $data = $request->validated();

        Device::create($data);

        return redirect()->route('e-billing.master-data.devices.index')
            ->with('success', 'Perangkat berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device)
    {
        return view('e-billing::devices.show', compact('device'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        return view('e-billing::devices.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $data = $request->validated();

        $device->update($data);

        return redirect()->route('e-billing.master-data.devices.index')
            ->with('success', 'Perangkat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('e-billing.master-data.devices.index')
            ->with('success', 'Perangkat berhasil dihapus.');
    }
}
