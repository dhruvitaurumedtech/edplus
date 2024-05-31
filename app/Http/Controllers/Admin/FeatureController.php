<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Module;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function feature_list()
    {
        $features = Feature::orderBy('id', 'desc')->paginate(10);
        $modules = Module::all();
        return view('feature.list', compact('features', 'modules'));
    }

    public function feature_create(Request $request)
    {
        $request->validate([
            'feature_name' => 'required|string|max:255',
            'module_id' => 'required|exists:modules,id',
        ]);

        Feature::create([
            'feature_name' => $request->feature_name,
            'module_id' => $request->module_id,
        ]);

        return response()->json(['success' => 'Feature created successfully']);
    }

    public function feature_edit(Request $request)
    {
        $feature = Feature::findOrFail($request->feature_id);
        return response()->json(['feature' => $feature]);
    }

    public function feature_update(Request $request)
    {
        $request->validate([
            'feature_name' => 'required|string|max:255',
            'module_id' => 'required|exists:modules,id',
        ]);

        $feature = Feature::findOrFail($request->id);
        $feature->update([
            'feature_name' => $request->feature_name,
            'module_id' => $request->module_id,
        ]);

        return response()->json(['success' => 'Feature updated successfully']);
    }

    public function feature_delete(Request $request)
    {
        $feature = Feature::findOrFail($request->feature_id);
        $feature->delete();

        return response()->json(['success' => 'Feature deleted successfully']);
    }
}
