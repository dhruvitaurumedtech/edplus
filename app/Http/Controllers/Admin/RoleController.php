<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function create_role(){
       return view('role.create');
    }
    public function save_role(Request $request){
        $request->validate([
            'role_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'role_name'), 
            ],
        ]);

        Roles::create([
            'role_name' => $request->input('role_name'),
        ]);

        return redirect()->route('roles.create')->with('success', 'Role created successfully');
   
    }
    public function list_role(){
        $roles = Roles::paginate(10); 
        return view('role.list', compact('roles'));
    }
    public function edit_role(Request $request){
        $id = $request->input('roleId');
        $roles = Roles::find($id);
        return response()->json(['roles'=>$roles]);
        
    }
    public function update_role(Request $request){
        $id=$request->input('id');
        $role = Roles::find($id);
        $validator = $request->validate([
            'role_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'role_name')->ignore($id),
            ],
        ]);
      
        $role->update([
            'role_name' => $request->input('role_name'),
        ]);
        return redirect()->route('roles.list')->with('success', 'Role Updated successfully');
    }
    public function delete_role(Request $request){
        $id=$request->input('roleId');
        $role = Roles::find($id);

        if (!$role) {
            return redirect()->route('roles.list')->with('error', 'Role not found');
        }

        $role->delete();

        return redirect()->route('roles.list')->with('success', 'Role deleted successfully');
        }
}
