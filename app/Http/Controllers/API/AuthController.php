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

    //    Google Login

    public function handleGoogle(Request $request)
    {
        try {
            $user = Socialite::with('google')->stateless()->userFromToken($request->token);
            if (!$user) {
                return $this->response([], "UnAuthorized User", false, 400);
            }
            return $this->handleUser($user, "google", $request);
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
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
                $user = new User();
                $user->firstname = $ssoUser->user['name'];
                $user->lastname = $ssoUser->user['given_name'];
                $user->email = $ssoUser->user['email'];
                $user->email_verified_at = Carbon::now();
                $user->mobile = $request->mobile;
                $user->role_type = $request->role_type;
                $user->device_key = $request->device_key;
                $user->social_id = $ssoUser->user['id'];
                $user->save();
            }
            $user = User::find($user->id);
            return $this->login_res($user);
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'mobile' => 'required|min:10',
            'role_type' => 'required|integer|exists:roles,id',
            'confirm_password' => 'required|string|same:password',
            'device_key' => 'nullable',
            'country_code' => 'required',
            'country_code_name'=>'required',
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
            $user->country_code_name = $request->country_code_name;
            $user->mobile = $request->mobile;
            $user->role_type = $request->role_type;
            $user->otp_num = rand(100000, 999999);
            $user->device_key = $request->device_key;
            $user->save();
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

            Mail::send('emails.registerotpverifymail', ['token' => $token], function ($message) use ($request) {
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
                //'token' => $token,
            ];
            return $this->response($data, "Registration Successfully !");
        } catch (Exception $e) {
            return $e;
            return $this->response($e, "Something want Wrong!!", false, 400);
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

        $user = User::where('email', $request->email)->where('status', '1')->first();

        if (!$user) {
            return $this->response([], "User not found", false, 400);
        }

        // $validRoles = ($request->login_type == 1) ? [5, 6] : [3, 4];

        // if (empty($user->password) && !empty($user->social_id)) {
        //     return $this->response([], "Please use Social Login", false, 400);
        // }
        // if (!in_array($user->role_type, $validRoles)) {
        //     $errorMessage = ($request->login_type == 1) ? "Please use Institute Application" : "Please use Student Application";
        //     return $this->response([], $errorMessage, false, 400);
        // }

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
                    // $ins = Staff_detail_Model::where('user_id', $user->id)->first();
                    // $institute_id = $ins->institute_id;
                    // $institute_name = null;
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
            return $this->response([], "Invalid Username Or Password!", false, 400);
        }
    }



    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            $user->token = '';
            $user->save();
            Auth::logout();
            return $this->response([], "Successfully logged out");
        } catch (Exception $e) {
            return $this->response([], "Something went wrong!", false, 400);
        }
    }



    // public function register(Request $request)
    // {
    //     $validator = \Validator::make(
    //         $request->all(),
    //         [
    //             'firstname' => 'required',
    //             'lastname' => 'required',
    //             'email' => 'required|email|unique:users,email',
    //             'password' => 'required|string|min:6',
    //             'mobile' => 'required|min:10',
    //             'role_type' => 'required|integer',
    //         ],
    //         [
    //             'mobile' => 'The mobile field must be at least 10 characters',
    //             'role_type' => 'select roleid'
    //         ]
    //     );
    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());

    //         return response()->json([
    //             'status' => 400,
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $existingUser = User::where('email', $request->email)->first();
    //     if ($existingUser) {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'This email already exists.',

    //         ]);
    //     }
    //     if ($request->password != $request->confirm_password) {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Password and confirm_password does not match!',
    //         ]);
    //     }
    //     $user = new User();
    //     $user->firstname = $request->firstname;
    //     $user->lastname = $request->lastname;
    //     $user->email = $request->email;
    //     $user->password = Hash::make($request->password);
    //     $user->mobile = $request->mobile;
    //     $user->role_type = $request->role_type;
    //     $user->otp_num = rand(100000, 999999);
    //     $user->save();
    //     $token = JWTAuth::fromUser($user);
    //     $user->token = $token;
    //     $user->save();
    //     $responseData = [
    //         'success' => 200,
    //         'message' => 'OTP Sent Successfully !',
    //         'data' => array('OTP' => $user->otp_num),
    //     ];
    //     return response()->json($responseData);
    // }

    // public function verify_otp12(Request $request)
    // {
    //     $phone = $request->input('mobile');
    //     $otp_num = $request->input('otp_num');

    //     $user = DB::table('users')->where('mobile', $phone)->get();

    //     if ($user->count() > 0) {

    //         $otp_check = DB::table('users')->where('otp_num', $otp_num)->get();

    //         if ($otp_check->isNotEmpty()) {
    //             if (!empty($user->photo)) {
    //                 $photo = asset('profile/' . $user->image);
    //             } else {
    //                 $photo = asset('profile/image.jpg');
    //             }
    //             foreach ($user as $value) {
    //                 $data = array(
    //                     'user_id' => $value->id,
    //                     'user_name' => $value->firstname . ' ' . $value->lastname,
    //                     'mobile_no' => $value->mobile,
    //                     'user_email' => $value->email,
    //                     'user_image' => $photo,
    //                     'role_type' => $value->role_type,
    //                     'token' => $value->token
    //                 );
    //             }
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Registered successfully',
    //                 'data' => $data
    //             ], 200, [], JSON_NUMERIC_CHECK);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid OTP.',
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'This mobile_no does not already exists.',

    //         ]);
    //     }
    // }


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

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email|exists:users,email',
    //         'password' => 'required',
    //     ]);
    //     $userexists = User::where('email', $request->email)->first();
    //     if ($userexists) {
    //         if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    //             $user = Auth::user();
    //             $token = JWTAuth::fromUser($user);
    //             $user->token = $token;
    //             $user->save();
    //             $institute_id = ($user->role_type == 3) ? (Institute_detail::where('user_id', $user->id)->first()?->id ?? null) : null;
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Login successful',
    //                 'data' => [
    //                     'user_id' => $user->id,
    //                     'user_name' => $user->firstname . ' ' . $user->lastname,
    //                     'mobile_no' => $user->mobile,
    //                     'user_email' => $user->email,
    //                     'user_image' => $user->image,
    //                     'role_type' => $user->role_type,
    //                     'institute_id' => $institute_id,
    //                     'token' => $token,
    //                 ]
    //             ], 200, [], JSON_NUMERIC_CHECK);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid credentials'
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => '404',
    //             'message' => 'User not found',
    //         ], 404);
    //     }
    // }


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
    // public function logout(Request $request)
    // {
    //     $userId = $request->user_id;
    //     $token = $request->header('Authorization');
    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $userId)->first();
    //     if ($existingUser) {

    //         Auth::logout($userId);
    //         user::where('id', $userId)->update(['token' => '']);
    //         return response()->json([
    //             'status' => '200',
    //             'message' => 'Successfully logged out',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ], 400);
    //     }
    // }
}
