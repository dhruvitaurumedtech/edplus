<?php

namespace App\Http\Controllers;

use App\Models\Institute_detail;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
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
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|exists:users,firstname',
            'lastname'=>'required',
            'email'=>'required|email|unique:users,email',
            'mobile' => 'required|digits:10|numeric'
        ]);
          
        if ($validator->fails()) {
          
         return redirect()->route('institute_admin.list')->with('error', $validator->errors()->first());

        }

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
        // if($request->role_type == 2){
        //     return Redirect::route('admin.list')->with('success', 'Profile created successfully!');
        // }else{
            return Redirect::route('institute_admin.list')->with('success', 'Institute Owner created successfully!');
        // }
        
    }

    public function subadmin_edit(Request $request)
    {
        $id = $request->input('user_id');

        $userDT = Institute_detail::find($id);
        return response()->json(['userDT' => $userDT]);
    }
    public function user_edit(Request $request){
        $id = $request->input('user_id');
        $userDT = User::find($id);
        return response()->json(['userDT' => $userDT]);
    }
    public function user_update(Request $request){
        $user_id = $request->user_id;
        $institute_data = User::find($user_id);
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|exists:users,firstname',
            'lastname'=>'required',
            'email'=>'required|email|unique:users,email',
            'mobile' => 'required|digits:10|numeric'
        ]);
          
        if ($validator->fails()) {
          
         return redirect()->route('institute_admin.list')->with('error', $validator->errors()->first());

        }
        if ($institute_data) {
            $institute_data->update([
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),

            ]);
        } 

         return redirect()->route('institute_admin.list')->with('success', 'Institute Admin Updated successfully');
    }
    public function subadmin_update(Request $request)
    {
        $institute_id = $request->institute_id;
        // echo "hi";exit;
        // echo "<pre>";print_r($request->all());exit;
        $validator = Validator::make($request->all(), [
            'institute_name' => 'required|exists:institute_detail,institute_name',
            'email' => 'required|email|unique:institute_detail,email,' . $institute_id, // Ignore current email
            'contact_no' => 'required|digits:10|numeric',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->route('institute.list')->with('error', $validator->errors()->first());
        }
        $institute_data = Institute_detail::find($institute_id);

        if ($institute_data) {
            $institute_data->update([
                'institute_name' => $request->input('institute_name'),
                'email' => $request->input('email'),
                'contact_no' => $request->input('contact_no'),
            ]);
        } 

        // if ($userUP->role_type == 2) {
        //     $routnm = 'admin.list';
        // } else {
        //     $routnm = 'institute_admin.list';
        // }
        return redirect()->route('institute.list')->with('success', 'Institute Updated successfully');
    }

    //delete
    public function subadmin_delete(Request $request)
    {
        $institute_id = $request->input('user_id');
        $userd = User::find($institute_id);

    //    print_r($userd->role_type);exit;
    //     if ($userd->role_type == 2) {
    //         $routnm = 'admin.list';
    //     } else {
            // $routnm = 'institute.list';
        // }

        if (!$userd) {
            return redirect()->route('institute.list')->with('error', 'Role not found');
        }

        $userd->delete();

        return redirect()->route('institute.list')->with('success', 'Role deleted successfully');
    }
    public function user_delete(Request $request){
// echo "hi";exit;
        $user_id = $request->input('user_id');
        $userd = User::find($user_id);


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
    public function institute_delete(Request $request){
        $user_id = $request->input('user_id');
        $userd = Institute_detail::find($user_id);


        // if ($userd->role_type == 2) {
        //     $routnm = 'admin.list';
        // } else {
        //     $routnm = 'institute.list';
        // }

        if (!$userd) {
            return redirect()->route('institute.list')->with('error', 'Role not found');
        }

        $userd->delete();

        return redirect()->route('institute.list')->with('success', 'Role deleted successfully');
    }
}
