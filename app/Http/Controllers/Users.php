<?php

namespace App\Http\Controllers;

use App\Models\Institute_detail;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;



class Users extends Controller
{
    public function list_admin(): View
    {
        $users = User::where('role_type', [2])->orderBy('id', 'desc')->paginate(10);
        return view('admin.list', compact('users'));
    }

    public function list_institute(): view
    {
        $institute = User::where('role_type', [3])->orderBy('id', 'desc')->paginate(10);
        return view('institute.list', compact('institute'));
    }

    public function subadmin_create(Request $request)
    {
        return view('admin.create');
    }

    public function subadmin_store(Request $request)
    {
        if ($request->role_type == '2') {
            $subadminPrefix = 'sa_';
        } else {
            $subadminPrefix = 'ia_';
        }
        $startNumber = 101;

        $lastInsertedId = DB::table('users')->orderBy('id', 'desc')->value('unique_id');
        if (!is_null($lastInsertedId)) {
            $number = substr($lastInsertedId, 3);
            // echo $number;exit; 
            $newID = $number + 1;
        } else {
            $newID = $startNumber;
        }

        $paddedNumber = str_pad($newID, 3, '0', STR_PAD_LEFT);

        $unique_id = $subadminPrefix . $paddedNumber;



        // print_r($request->all());exit;
        $validator = $request->validate([
            'role_type' => 'required',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Create sub-admin

        $subAdmin = User::create([
            'unique_id' => $unique_id,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'mobile' =>  $request->mobile,
            'password' => Hash::make($request->password),
            'role_type' => $request->role_type,
        ]);
        $token = JWTAuth::fromUser($subAdmin);

        User::where('email', $request->email)
            ->update([
                'token' => $token
            ]);
        //return Redirect::route('admin.create')->with('success', 'profile-created');
        if($request->role_type == 2){
            return Redirect::route('admin.list')->with('success', 'Profile created successfully!');
        }else{
            return Redirect::route('institute.list')->with('success', 'Profile created successfully!');
        }
        
    }

    public function subadmin_edit(Request $request)
    {
        $id = $request->input('user_id');
        
        $userDT = User::find($id);
        return response()->json(['userDT' => $userDT]);
    }
    public function institute_subadmin_edit(Request $request){
        $id = $request->input('user_id');
        $userDT = Institute_detail::where('id',$id)->first();
       
        return response()->json(['userDT' => $userDT]);
    }

    public function subadmin_update(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());
        // exit;
        $user_id = $request->user_id;
        $userUP = User::find($user_id);
        $validator = $request->validate([
            'firstname' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'email')->ignore($userUP),
            ],
            'lastname' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $userUP->update([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
        ]);

        // if ($userUP->role_type == 2) {
        //     $routnm = 'admin.list';
        // } else {
        //     $routnm = 'institute.list';
        // }
        return redirect()->route('institute_admin.list')->with('success', 'Role Updated successfully');
    }
    public function institute_subadmin_update(Request $request){
        $user_id = $request->user_id;
        $userUP = Institute_detail::where('id',$user_id);
        
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'mobile' => [
                'required',
                'digits:10', 
            ],
            'email' => [
                'required',
                'string',
                'max:255',
            ],
        ]);
        
        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return redirect()->route('institute.list')->with('error', implode(',',$errorMessages));
        }
        
        $userUP->update([
            'institute_name' => $request->input('name'),
            'email' => $request->input('email'),
            'contact_no' => $request->input('mobile'),
        ]);

      
        return redirect()->route('institute.list')->with('success', 'Role Updated successfully');
    }
    //delete
    public function subadmin_delete(Request $request)
    {
        $institute_id = $request->input('user_id');
        $userd = Institute_detail::find($institute_id);


        if ($userd->role_type == 2) {
            $routnm = 'admin.list';
        } else {
            $routnm = 'institute.list';
        }

        if (!$userd) {
            return redirect()->route($routnm)->with('error', 'Role not found');
        }

        $userd->delete();

        return redirect()->route($routnm)->with('success', 'Role deleted successfully');
    }
}
