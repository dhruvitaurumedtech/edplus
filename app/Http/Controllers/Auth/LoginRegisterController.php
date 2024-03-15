<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginRegisterController extends Controller
{
    //
   
    
    public function register(Request $request){
        
        try{
        $validate = validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
            'roll'=>'required',
            'password'=>'required'
        ]);

        if($validate->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'validation error',
                'errors'=>$validate->errors()
            ],401);
        }

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'roll'=>$request->roll,
            'password'=>Hash::make($request->password)
        ]);

        $token = JWTAuth::fromUser($user);
        
        User::where('email', $request->email)
            ->update([
                'token' => $token
            ]);

        return response()->json([
            'status'=>true,
            'message'=>'User Login Successfully',
            'token'=>$user->createToken("API TOKEN")->plainTextToken
        ],200);
    }catch(\Throwable $th){
        return response()->json([
            'status'=>false,
            'message'=>$th->getMessage()
        ],500);
    }
    }

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
