<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Modules\EBilling\Models\Site;
use TakiElias\TablarKit\Builder\FormBuilder;

class SiteController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sites = Site::datatable();

        return view('e-billing::sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $form = FormBuilder::create()
            ->action(route('e-billing.master-data.sites.store'))
            ->method('POST')

            ->input('name', 'Nama', ['topGap' => ''])
            ->required()
            ->placeholder('Masukkan nama site')

            ->textarea('description', 'Deskripsi', ['topGap' => ''])
            ->placeholder('Masukkan deskripsi site (opsional)')

            ->button('Simpan', '', ['topGap' => 'd-inline-flex'])
            ->addClass('btn btn-primary');

        return view('e-billing::sites.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $site = Site::create($data);

        return redirect()->route('e-billing.master-data.sites.show', $site)->with('success', "Site '{$site->name}' berhasil dibuat!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site)
    {
        return view('e-billing::sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site)
    {
        $form = FormBuilder::create()
            ->action(route('e-billing.master-data.sites.update', $site))
            ->method('PUT')

            ->input('name', 'Nama', ['topGap' => ''])
            ->required()
            ->value($site->name)
            ->placeholder('Masukkan nama site')

            ->textarea('description', 'Deskripsi', ['topGap' => ''])
            ->value($site->description)
            ->placeholder('Masukkan deskripsi site (opsional)')

            ->button('Simpan', '', ['topGap' => 'd-inline-flex'])
            ->addClass('btn btn-primary');

        return view('e-billing::sites.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Site $site)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $site->update($data);

        return redirect()->route('e-billing.master-data.sites.show', $site)->with('success', "Site '{$site->name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('e-billing.master-data.sites.index')->with('success', "Site '{$site->name}' berhasil dihapus!");
    }
}
