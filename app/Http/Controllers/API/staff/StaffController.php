<?php

namespace App\Http\Controllers\API\staff;

use App\Http\Controllers\Controller;
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
}
