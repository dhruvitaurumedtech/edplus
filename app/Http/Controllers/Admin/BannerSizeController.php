<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerSize;
use Illuminate\Http\Request;

class BannerSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bannerSizes = BannerSize::all();
        return view('banner-sizes.index', compact('bannerSizes'));
    }

    public function create()
    {
        return view('banner-sizes.create');
    }

    public function store(Request $request)
    {
        BannerSize::create($request->all());
        return redirect()->route('banner-sizes.index')->with('success', 'Banner size created successfully.');
    }

    public function show($id)
    {
        $bannerSize = BannerSize::findOrFail($id);
        return view('banner-sizes.show', compact('bannerSize'));
    }

    public function edit($id)
    {
        $bannerSize = BannerSize::findOrFail($id);
        return view('banner-sizes.edit', compact('bannerSize'));
    }

    public function update(Request $request, $id)
    {
        $bannerSize = BannerSize::findOrFail($id);
        $bannerSize->update($request->all());
        return redirect()->route('banner-sizes.index')->with('success', 'Banner size updated successfully.');
    }

    public function destroy($id)
    {
        BannerSize::findOrFail($id)->delete();
        return redirect()->route('banner-sizes.index')->with('success', 'Banner size deleted successfully.');
    }
}
