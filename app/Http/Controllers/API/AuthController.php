<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Institute_detail;
use App\Models\Roles;
use App\Models\Staff_detail_Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiTrait;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{
    use ApiTrait;
    public function handleGoogle(Request $request)
    {
        try {
            $user = Socialite::with('google')->stateless()->userFromToken($request->token);
            if (!$user) {
                return $this->response([], "UnAuthorized User", false, 400);
            }
            return $this->handleUser($user, "google", $request);
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
    private function handleUser($ssoUser, $ssoPlatform, $request)
    {
        try {
            $emaillogincheck = User::where('email', $ssoUser->email)->where('social_id', null)->first();
            if ($emaillogincheck) {
                $emaillogincheck->social_id = $ssoUser->id;
                $emaillogincheck->save();
            }
            $user = User::where('social_id', $ssoUser->id)->first();
            $validRoles = ($request->login_type == 1) ? [5, 6] : [3, 4];
            if (!empty($user)) {
                if ($user->role_type != $request->role_type) {
                    return $this->response([], "Please Select Correct Role", false, 400);
                }
                if (!in_array($user->role_type, $validRoles)) {
                    $errorMessage = ($request->login_type == 1) ? "Please use Institute Application" : "Please use Student Application";
                    return $this->response([], $errorMessage, false, 400);
                }
            }
            if (!$user) {
               $name = $ssoUser->user['name'];
               $nameParts = explode(' ', $name);
                $firstname = $nameParts[0]; // First element is the first name
                $lastname = isset($nameParts[1]) ? $nameParts[1] : '';
                $user = new User();
                $user->firstname = $firstname;
                $user->lastname = $lastname;
                $user->email = $ssoUser->user['email'];
                $user->email_verified_at = Carbon::now();
                $user->mobile = $request->mobile;
                $user->role_type = $request->role_type;
                $user->device_key = $request->device_key;
                $user->social_id = $ssoUser->user['id'];
                $user->save();
            }
            $user = User::find($user->id);
            $tomail = $ssoUser->user['email'];
            Mail::send('emails.welcomemailtogooglelogin', ['name'=>$firstname], function ($message) use ($tomail) {
                $message->to($tomail);
                $message->subject('Welcome to Edwide');
              });
            return $this->login_res($user);
        } catch (Exception $e) {
            return $this->response($e, "Something went Wrong!!", false, 400);
        }
    }
    private function login_res(User $user)
    {
        $token = JWTAuth::fromUser($user);
        $user->token = $token;
        $user->save();
        $userdata = user::join('institute_detail', 'institute_detail.user_id', '=', 'users.id')
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
            'country_code_name'=>$user->country_code_name,
            'mobile_no' => $user->mobile,
            'user_email' => $user->email,
            'user_image' => $user->image,
            'role_type' => (int)$user->role_type,
            'institute_id' => $institute_id,
            'token' => $token,
        ];
        return $this->response($data, "Login SuccessFully!!");
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'password' => 'required|string|min:6',
            'mobile' => 'required|min:10',
            'role_type' => 'required|integer|exists:roles,id',
            'confirm_password' => 'required|string|same:password',
            'device_key' => 'nullable',
            'country_code' => 'required',
            'country_code_name'=>'required',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        $user = User::where('email', $request->email)
        ->where('status', '1')->first();
        if (isset($user)) {
            return $this->response([], 'This email already exists.', false, 400);
        }
        $data= User::withTrashed()->where('email', $request->email)->whereNotNull('deleted_at')->first();
        if(!empty($data)){
            return $this->response([],'Enter Your Mobile Number!' , false, 400);
        }
        try {
            $user = User::where('email', $request->email)->first();
            if(!isset($user)){
                $user = new User();
                $user->firstname = $request->firstname;
                $user->lastname = $request->lastname;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->country_code = $request->country_code;
                $user->country_code_name = $request->country_code_name;
                $user->mobile = $request->mobile;
                $user->role_type = $request->role_type;
                $user->otp_num = rand(100000, 999999);
                $user->device_key = $request->device_key;
                $user->save();
            }
            $token = JWTAuth::fromUser($user);
            $user->token = $token;
            $user->save();
            $userdata = user::join('institute_detail', 'institute_detail.user_id', '=', 'users.id')
                ->where('users.email', $user->email)
                ->select('institute_detail.id')
                ->first();
            if (!empty($userdata->id)) {
                $institute_id = $userdata->id;
            } else {
                $institute_id = null;
            }
            $token = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            User::where('email',$request->email)->update(['otp_num'=>$token]);
            Mail::send('emails.registerotpverifymail', ['token' => $token,'name'=>$request->firstname], function ($message) use ($request) {
              $message->to($request->email);
              $message->subject('Verification Code');
            });
            $data = [
                'user_id' => $user->id,
                'user_name' => $user->firstname . ' ' . $user->lastname,
                'country_code' => $user->country_code,
                'country_code_name' => $user->country_code_name,
                'mobile_no' => (int)$user->mobile,
                'user_email' => $user->email,
                'user_image' => $user->image,
                'role_type' => (int)$user->role_type,
                'institute_id' => $institute_id,
            ];
            return $this->response($data, "OTP is sent to you mail!");
        } catch (Exception $e) {
            dd($e);
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
    public function login(Request $request)
    {
      $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
            'login_type' => 'required|in:1,2',
            'device_key' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        $user = User::where('email', $request->email)
                    ->where('status', '0')
                    ->first();
        if ($user) {
            return $this->response(['status'=>0], "Please verify OTP", false, 400);
        }
         $user = User::where('email', $request->email)
                    ->where('status', '1')
                    ->first();
        if (!$user) {
            return $this->response([], "User not found", false, 400);
        }
        if ($request->login_type == 1) {
            $validRoles = [5, 6];
            $errorMessage = "Please use Institute Application";
        } elseif ($request->login_type == 2) {
            $allRoles = Roles::pluck('id')->toArray();
            $validRoles = array_diff($allRoles, [5, 6]);
            $errorMessage = "Please use Student Application";
        } else {
            return $this->response([], "Invalid login type", false, 400);
        }
        if (!in_array($user->role_type, $validRoles)) {
            return $this->response([], $errorMessage, false, 400);
        }
        if($user->device_key != null || $user->device_key!=''){
        // if($request->device_key != $user->device_key){
        //     return $this->response([], "Already logged in another device.", false, 400);
        // }
        }
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);
            $user->token = $token;
            $user->device_key = $request->device_key;
            $user->save();
            $userdata = user::join('institute_detail', 'institute_detail.user_id', '=', 'users.id')
                ->where('users.email', $request->email)
                ->select('institute_detail.id', 'institute_detail.institute_name')
                ->first();
                if (!empty($userdata->id)) {
                    $institute_id = $userdata->id;
                    $institute_name = $userdata->institute_name;
                } elseif ($user->role_type != 3 && $user->role_type != 4 && $user->role_type != 5 && $user->role_type != 6) {
                    $ins = Staff_detail_Model::where('user_id', $user->id)->first();
                    $institute_id = $ins->institute_id;
                    $institutename = Institute_detail::where('id', $institute_id)->first();
                    $institute_name = $institutename->institute_name;
                } else {
                    $institute_id = null;
                    $institute_name = null;
                }
            $data = [
                'user_id' => $user->id,
                'user_name' => $user->firstname . ' ' . $user->lastname,
                'country_code' => $user->country_code,
                'country_code_name'=>$user->country_code_name,
                'mobile_no' => $user->mobile,
                'user_email' => $user->email,
                'user_image' => $user->image,
                'role_type' => (int)$user->role_type,
                'institute_id' => (int)$institute_id,
                'institute_name' => $institute_name,
                'token' => $token,
            ];
            return $this->response($data, "Login successful");
        } else {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->response([], "Invalid Email", false, 400);
            } else {
                return $this->response([], "Invalid Password", false, 400);
            }
        }
    }
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            $user->token = '';
            $user->device_key = '';
            $user->save();
            Auth::logout();
            return $this->response([], "Successfully logged out");
        } catch (Exception $e) {
            return $this->response([], "Something went wrong!", false, 400);
        }
    }
    public function setnew_password(Request $request){
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'password' => 'required|string|min:6',
                'confirm_password' => 'required|string|same:password',
            ]);
            if ($validator->fails()) {
                return $this->response([], $validator->errors()->first(), false, 400);
            }
            try{
                $user = User::findOrFail(Auth::id());
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->response([], "The current password is incorrect.", false, 400);
            }
            $user->password = Hash::make($request->password);
            $user->save();
            return $this->response([], "Password updated successfully.");
        }catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
    public function verify_otp(Request $request)
    {
        try {
            $user = User::where('email', $request->email)
                ->where('otp_num', $request->otp_num)
                ->first();
            if ($user) {
                $data = [
                    'user_id' => $user->id,
                    'user_name' => $user->firstname . ' ' . $user->lastname,
                    'country_code' => $user->country_code,
                    'country_code_name'=>$user->country_code_name,
                    'mobile_no' => $user->mobile,
                    'user_email' => $user->email,
                    'user_image' => $user->image,
                    'role_type' => (int)$user->role_type,
                    'token' => $user->token
                ];
                if ($user->otp_num == $request->otp_num) {
                    User::where('email',$request->email)->update(['status'=>'1']);
                    return $this->response($data, "OTP verification successful");
                } else {
                    return $this->response([], "OTP do not match", false, 400);
                }
            } else {
                return $this->response([], "OTP do not match", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Something went wrong!", false, 400);
        }
    }
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 * 24 * 7,
            'user' => auth()->user()
        ]);
    }
    protected function validateToken($user, $providedToken)
    {
        return $user->token === $providedToken;
    }
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
