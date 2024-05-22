<?php

namespace App\Http\Controllers\API\staff;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\Staff_detail_Model;
use App\Models\User;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class StaffController extends Controller
{
    use ApiTrait;
    public function add_staff(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'institute_id'=>'required|integer',
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|min:10',
            'password' => 'required|string|min:6',
            'role_type' => 'required|integer',
            'confirm_password' => 'required|string|same:password',
            'device_key' => 'nullable',
            'country_code'=>'required',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        $user = User::where('email', $request->email)->first();
        if (isset($user)) {
            return $this->response([], 'This email already exists.', false, 400);
        }
        try {
            $user = new User();
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->country_code = $request->country_code;
            $user->mobile = $request->mobile;
            $user->role_type = $request->role_type;
            $user->otp_num = rand(100000, 999999);
            $user->device_key = $request->device_key;
            $user->save();

            $staff = new Staff_detail_Model();
            $staff->institute_id = $request->institute_id;
            $staff->user_id = $user->id;
            $staff->save();

            $token = JWTAuth::fromUser($user);
            $user->token = $token;
            $user->save();
            $userdata = user::join('staff_detail', 'staff_detail.user_id', '=', 'users.id')
                            ->join('institute_detail', 'institute_detail.id', '=', 'staff_detail.institute_id')
                            ->where('users.email', $user->email)
                            ->select('institute_detail.id')
                            ->first();
            if (!empty($userdata->id)) {
                $institute_id = $userdata->id;
            } else {
                $institute_id = null;
            }
            $data = [
                'user_id' => $user->id,
                'user_name' => $user->firstname . ' ' . $user->lastname,
                'country_code' => $user->country_code,
                'mobile_no' => (int)$user->mobile,
                'user_email' => $user->email,
                'user_image' => $user->image,
                'role_type' => (int)$user->role_type,
                'institute_id' => $institute_id,
                'token' => $token,
            ];
            return $this->response($data, "Staff Added Successfully !");
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
 
    }
    public function view_staff(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id'=>'required|integer',
        ]);
        $userdata = user::join('staff_detail', 'staff_detail.user_id', '=', 'users.id')
                            ->join('roles', 'roles.id', '=', 'users.role_type')
                            ->where('staff_detail.institute_id', $request->institute_id)
                            ->select('users.*','roles.role_name')
                            ->get();
        try{
            $data=[];
            foreach($userdata as $value){
                 $data[]=['user_id'=>$value->id,
                          'fullname'=>$value->firstname.' '.$value->lastname,
                          'role_name'=>$value->role_name,
                         ];
            }
            return $this->response($data, "Staff Fetch Successfully !");

        }catch(Exception $e){

            return $this->response($e, "Something want Wrong!!", false, 400);
        }
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
    }
    public function delete_staff(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            user::where('id', $request->staff_id)->delete();
            return $this->response([], "Successfully Deleted Staff.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
    public function view_roles(Request $request){
        try {
            $roles = Roles::whereNull('created_by')->get();
            $data=[];
            foreach($roles as $value){
               $data[] = ['role_id'=>$value->id,'role_name'=>$value->role_name,'photo' => asset('profile/no-image.png')];
            }
            return $this->response($data, "Successfully Display Roles.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
}
