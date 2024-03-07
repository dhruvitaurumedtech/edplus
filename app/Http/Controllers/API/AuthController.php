<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;



class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'verify_otp', 'register', 'refresh', 'logout']]);
    }
    public function register(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'firstname' => 'required',
                'lastname' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'mobile' => 'required|min:10',
                'role_type' => 'required|integer',
            ],
            [
                'mobile' => 'The mobile field must be at least 10 characters',
                'role_type' => 'select roleid'
            ]
        );
        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());

            return response()->json([
                'status' => 400,
                'errors' => $errorMessages,
            ], 400);
        }

        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'status' => 400,
                'message' => 'This email already exists.',

            ]);
        }
        // $existingUser1 = User::where('mobile', $request->mobile)->first();
        // if ($existingUser1) {
        //     return response()->json([
        //         'status' => 400,
        //         'message' => 'This mobile_no already exists.',

        //     ]);
        // }
        if ($request->password != $request->confirm_password) {
            return response()->json([
                'status' => 400,
                'message' => 'Password and confirm_password does not match!',

            ]);
        }
        $phone = $request->input('mobile');
        $mobileNumber =  $phone;
        $rndno = rand(100000, 999999);
        $user = User::create([

            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'role_type' => $request->role_type,
            'mobile' => $mobileNumber,
            'otp_num' => $rndno

        ]);
        $token = JWTAuth::fromUser($user);

        User::where('email', $request->email)
            ->update([
                'token' => $token
            ]);
        $otp_reponse = array('OTP' => $rndno);
        $responseData = [
            'success' => '200',
            'message' => 'OTP Sent Successfully !',
            'data' => $otp_reponse,
        ];

        return response()->json($responseData);
    }
    public function verify_otp(Request $request)
    {
        $phone = $request->input('mobile');
        $otp_num = $request->input('otp_num');

        $user = DB::table('users')->where('mobile', $phone)->get();

        if ($user->count() > 0) {

            $otp_check = DB::table('users')->where('otp_num', $otp_num)->get();

            if ($otp_check->isNotEmpty()) {
                if (!empty($user->photo)) {
                    $photo = asset('profile/' . $user->image);
                } else {
                    $photo = asset('profile/image.jpg');
                }
                foreach ($user as $value) {
                    $data = array(
                        'user_id' => $value->id,
                        'user_name' => $value->firstname.' '.$value->lastname,
                        'mobile' => $value->mobile,
                        'user_email' => $value->email,
                        'user_image' => $photo,
                        'role_type' => $value->role_type,
                        'token' => $value->token
                    );
                }
                return response()->json([
                    'status' => 200,
                    'message' => 'Registered successfully',
                    'data' => $data
                ], 200, [], JSON_NUMERIC_CHECK);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid OTP.',

                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'This mobile_no does not already exists.',

            ]);
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
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = Auth::user();
        // $providedToken = $request->header('Authorization');
        $existingUser = User::where('email', $request->email)->first();
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if (!empty($user->photo)) {
                $photo = asset('profile/' . $user->photo);
            } else {
                $photo = asset('profile/image.jpg');
            }

            return response()->json([
                'status' => 200,
                'message' => 'Login successful',
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->firstname.' '.$user->lastname,
                    'mobile_no' => $user->mobile,
                    'user_email' => $user->email,
                    'user_image' => $photo,
                    'role_type' => $user->role_type,
                    'token' => $existingUser->token,
                ]
            ], 200, [], JSON_NUMERIC_CHECK);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid credentials'
            ]);
        }
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
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => '200',
            'message' => 'Successfully logged out',
        ]);
    }
}
