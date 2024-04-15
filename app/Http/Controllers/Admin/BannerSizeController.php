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
        $bannerSizes = BannerSize::paginate(10);
        return view('banner-sizes.create', compact('bannerSizes'));
    }

    public function create()
    {
        $bannerSizes = BannerSize::paginate(10);
        return view('banner-sizes.create', compact('bannerSizes'));
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

    public function edit(Request $request)
    {
        $bannerSize = BannerSize::find($request->banner_id);

        if (!$bannerSize) {
            return response()->json(['error' => 'Banner size not found'], 404);
        }

        return response()->json(['bannerSize' => $bannerSize]);
    }

    public function update(Request $request)
    {
        $bannerSize = BannerSize::findOrFail($request->id);
        $bannerSize->update($request->all());
        return redirect('banner-sizes/create')->with('success', 'Banner size updated successfully.');
    }

    public function destroy(Request $request)
    {

        BannerSize::findOrFail($request->bannerId)->delete();
        return redirect('banner-sizes/create')->with('success', 'Banner size deleted successfully.');
    }
}
