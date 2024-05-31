<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;

class AppRoleController extends Controller
{
    public function role_list()
    {
        $roles = Roles::where('created_by', 1)->orderBy('id', 'ASC')->paginate(10);
        return view('approle.list', compact('roles'));
    }

    public function role_create(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        Roles::create([
            'role_name' => $request->role_name,
            'created_by' => auth()->id(),  // Assuming you're using authentication
        ]);

        return response()->json(['success' => 'Role created successfully']);
    }

    public function role_edit(Request $request)
    {
        $id = $request->role_id;
        $role = Roles::find($id);
        return response()->json(['role' => $role]);
    }

    public function role_update(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        $role = Roles::find($request->id);
        $role->update([
            'role_name' => $request->role_name,
        ]);

        return response()->json(['success' => 'Role updated successfully']);
    }

    public function role_delete(Request $request)
    {
        $id = $request->input('role_id');
        $role = Roles::find($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json(['success' => 'Role deleted successfully']);
    }

    public function permissions($id)
    {
        $role = Roles::findOrFail($id);

        return view('approle.permissions', compact('role'));
    }

    public function update_permissions(Request $request, $id)
    {
        $role = Roles::findOrFail($id);
        $role->permissions()->sync($request->permissions);

        return redirect()->route('app_role.permissions', $id)->with('success', 'Permissions updated successfully');
    }
}
