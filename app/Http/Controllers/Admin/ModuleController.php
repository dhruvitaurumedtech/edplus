<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function module_list()
    {
        $modules = Module::orderBy('id', 'desc')->paginate(10);
        return view('module.list', compact('modules'));
    }

    public function module_create(Request $request)
    {
        $request->validate([
            'module_name' => 'required|string|max:255',
            'type' => 'required',
        ]);

        Module::create([
            'module_name' => $request->module_name,
            'type' => $request->type,
        ]);

        return response()->json(['success' => 'Module created successfully']);
    }

    public function module_edit(Request $request)
    {
        $id = $request->module_id;
        $module = Module::find($id);
        return response()->json(['module' => $module]);
    }

    public function module_update(Request $request)
    {
        $request->validate([
            'module_name' => 'required|string|max:255',
            'type' => 'required',
        ]);

        $module = Module::find($request->id);
        $module->module_name = $request->module_name;
        $module->type = $request->type;
        $module->save();
        return response()->json(['success' => 'Module updated successfully']);
    }

    public function module_delete(Request $request)
    {
        $id = $request->input('module_id');
        $module = Module::find($id);

        if (!$module) {
            return response()->json(['error' => 'Module not found'], 404);
        }

        $module->delete();

        return response()->json(['success' => 'Module deleted successfully']);
    }
}
