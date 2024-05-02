<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Institute_detail;
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


class AuthController extends Controller
{
    use ApiTrait;
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
            $user->mobile = $request->mobile;
            $user->role_type = $request->role_type;
            $user->otp_num = rand(100000, 999999);
            $user->save();
            $token = JWTAuth::fromUser($user);
            $user->token = $token;
            $user->save();
            return $this->response($user->otp_num, "OTP Sent Successfully !");
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
            'login_type' => 'required|in:1,2',
        ]);


        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->response([], "User not found", false, 400);
        }

        $validRoles = ($request->login_type == 1) ? [5, 6] : [3, 4];

        if (!in_array($user->role_type, $validRoles)) {
            $errorMessage = ($request->login_type == 1) ? "Please use Institute Application" : "Please use Student Application";
            return $this->response([], $errorMessage, false, 400);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);
            $user->update(['token' => $token]);

            $institute_id = ($user->role_type == 3) ? (optional($user->instituteDetail)->id) : null;

            $data = [
                'user_id' => $user->id,
                'user_name' => $user->firstname . ' ' . $user->lastname,
                'mobile_no' => $user->mobile,
                'user_email' => $user->email,
                'user_image' => $user->image,
                'role_type' => (int)$user->role_type,
                'institute_id' => $institute_id,
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
            $user = User::where('mobile', $request->mobile)
                ->where('otp_num', $request->otp_num)
                ->first();

            if ($user) {
                $data = [
                    'user_id' => $user->id,
                    'user_name' => $user->firstname . ' ' . $user->lastname,
                    'mobile_no' => $user->mobile,
                    'user_email' => $user->email,
                    'user_image' => $user->image,
                    'role_type' => (int)$user->role_type,
                    'token' => $user->token
                ];
                if ($user->mobile == $request->mobile && $user->otp_num == $request->otp_num) {
                    return $this->response($data, "OTP verification successful");
                } else {
                    return $this->response([], "Mobile number and OTP do not match", false, 400);
                }
            } else {
                return $this->response([], "User not found or OTP incorrect", false, 400);
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
            'expires_in' => auth()->factory()->getTTL() * 60,
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
