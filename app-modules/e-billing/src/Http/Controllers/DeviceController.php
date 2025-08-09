<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Modules\EBilling\Models\Device;
use TakiElias\TablarKit\Builder\FormBuilder;

class DeviceController
{
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
        $form = FormBuilder::create()
            ->action(route('e-billing.master-data.devices.store'))
            ->method('POST')

            ->input('brand', 'Merek', ["topGap" => ""])
            ->required()
            ->placeholder('Masukkan nama perangkat')

            ->input('model', 'Model', ["topGap" => ""])
            ->required()
            ->placeholder('Masukkan model perangkat')

            ->textarea('description', 'Deskripsi', ["topGap" => ""])
            ->placeholder('Masukkan deskripsi perangkat (opsional)')

            ->button('Simpan', "", ["topGap" => "d-inline-flex"])
            ->addClass('btn btn-primary');

        return view('e-billing::devices.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

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
        $form = FormBuilder::create()
            ->action(route('e-billing.master-data.devices.update', $device))
            ->method('PUT')

            ->input('brand', 'Merek', ["topGap" => ""])
            ->required()
            ->value($device->brand)
            ->placeholder('Masukkan nama perangkat')

            ->input('model', 'Model', ["topGap" => ""])
            ->required()
            ->value($device->model)
            ->placeholder('Masukkan model perangkat')

            ->textarea('description', 'Deskripsi', ["topGap" => ""])
            ->value($device->description)
            ->placeholder('Masukkan deskripsi perangkat (opsional)')

            ->button('Simpan', "", ["topGap" => "d-inline-flex"])
            ->addClass('btn btn-primary');

        return view('e-billing::devices.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

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
