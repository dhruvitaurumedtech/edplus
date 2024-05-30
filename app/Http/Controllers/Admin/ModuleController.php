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
            'module_name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        Module::create([
            'module_name' => $request->module_name,
        ]);
        $modules = Module::orderBy('id', 'desc')->paginate(10);
        return redirect()->route('module.list')->with('success', 'Module created successfully')->with(compact('modules'));
    }

    public function module_edit(Request $request)
    {
        $id = $request->module_id;
        $modules = Module::find($id);
        return response()->json(['modules' => $modules]);
    }

    public function module_update(Request $request)
    {
        $id = $request->id;
        $modules = Module::find($id);
        $validator = $request->validate([
            'module_name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $modules->update([
            'module_name' => $request->module_name,
        ]);
        return redirect()->route('module.list')->with('success', 'Module Updated successfully');
    }

    public function module_delete(Request $request)
    {
        $id = $request->input('module_id');
        $modules = Module::find($id);

        if (!$modules) {
            return redirect()->route('module.list')->with('error', 'Module not found');
        }

        $modules->delete();

        return redirect()->route('module.list')->with('success', 'Module deleted successfully');
    }
}
