<?php

namespace Modules\EBilling\Http\Controllers;

use Modules\EBilling\Http\Requests\Site\CreateSiteRequest;
use Modules\EBilling\Http\Requests\Site\UpdateSiteRequest;
use Modules\EBilling\Models\Site;

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
        return view('e-billing::sites.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSiteRequest $request)
    {
        $data = $request->validated();

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
        return view('e-billing::sites.edit', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSiteRequest $request, Site $site)
    {
        $data = $request->validated();

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
