<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Models\Roles;
use App\Models\UserHasRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $roles = new Roles();
        $roles->role_name = $request->role_name;
        $roles->created_by = auth()->id();
        if ($roles->save()) {
            $user_has_role = new UserHasRole();
            $user_has_role->role_id = $roles->id;
            $user_has_role->user_id = auth()->id();
            $user_has_role->save();
        }

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
        $user_has_role = UserHasRole::where('user_id', 1)->where('role_id', $id)->first();

        if (!$user_has_role) {
            return redirect()->back()->with('error', 'User role association not found.');
        }

        $modules = Module::with(['features'])->get();
        $actions = Action::select('id', 'name')->get();

        foreach ($modules as $module) {
            foreach ($module->features as $feature) {
                $featureActions = [];

                foreach ($actions as $action) {
                    $hasPermission = DB::table('role_has_permissions')
                        ->where('user_has_role_id', $user_has_role->id)
                        ->where('feature_id', $feature->id)
                        ->where('action_id', $action->id)
                        ->exists();

                    $featureActions[] = [
                        'id' => $action->id,
                        'name' => $action->name,
                        'has_permission' => $hasPermission
                    ];
                }

                $feature->actions = $featureActions;
            }
        }

        return view('approle.permissions', compact('role', 'modules'));
    }

    public function update_permissions(Request $request, $id)
    {
        $user_has_role = UserHasRole::where('user_id', 1)->where('role_id', $id)->first();

        if (!$user_has_role) {
            return redirect()->route('app_role.permissions', $id)->with('error', 'User role not found.');
        }

        // Validate the request
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.feature_id' => 'required|integer',
            'permissions.*.actions' => 'required|array',
            'permissions.*.actions.*' => 'required|integer',
        ]);

        // Clear existing permissions for the user_has_role_id
        DB::table('role_has_permissions')->where('user_has_role_id', $user_has_role->id)->delete();

        // Insert new permissions
        $permissions = $request->input('permissions');
        // print_r($permissions);exit;
        $data = [];

        foreach ($permissions as $permission) {
            $feature_id = $permission['feature_id'];
            $actions = $permission['actions'];

            foreach ($actions as $action_id) {
                $data[] = [
                    'user_has_role_id' => $user_has_role->id,
                    'feature_id' => $feature_id,
                    'action_id' => $action_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert the new permissions
        DB::table('role_has_permissions')->insert($data);

        return redirect()->route('app_role.permissions', $id)->with('success', 'Permissions updated successfully');
    }
}
