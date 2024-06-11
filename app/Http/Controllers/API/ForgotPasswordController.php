<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\User;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Mail;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;




class ForgotPasswordController extends Controller
{
  use ApiTrait;
  public function submitForgetPasswordForm(Request $request): JsonResponse
  {
   
    if ($request->validate([
      'email' => 'required|email|exists:users',
    ])) {
      // $token = $request->header('Authorization');
       //print_r($token);
      $existingUser = User::where('email', $request->email)->first();
      if (empty($existingUser)) {
        return $this->sendError("This email is not registered", 401);
      }
    try {
      $token = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

      $abc = DB::table('password_resets')->insert([
        'email' => $request->email,
        'token' => $token,
        'created_at' => Carbon::now()
      ]);
      
      Mail::send('emails.forgot', ['token' => $token,'name' => $existingUser->firstname], function ($message) use ($request) {
        $message->to($request->email);
        $message->subject('Reset Your Password');
      });
      
      return response()->json([
        'status' => 200,
        'message' => 'We have e-mailed your verification code!'
      ], 200);
    } catch (Exception $e) {
      return $this->sendError($e->getMessage(), 422);
    }
      // return response()->json(['message' => 'Invalid credentials'], 401);
    } else {
      return response()->json([
        'status' => 400,
        'message' => 'Invalid email address'
      ], 400);
    }
  }
  /**
   * Write code on Method
   *
   * @return response()
   */
  public function showResetPasswordForm($token): View
  {
    return view('auth.forgetPasswordLink', ['token' => $token]);
  }
  public function showForgetPasswordForm(): View
  {
    return view('auth.forgetPasswordLink');
  }

  public function verify_code(Request $request){
    //email code verify
    $validator = Validator::make($request->all(), [
      'email' => 'required',
      'code' => 'required',
    ]);
    if ($validator->fails()) {
      return $this->response([], $validator->errors()->first(), false, 400);
    }
    try{
      $updatePassword = DB::table('password_resets')
      ->where([
        'email' => $request->email,
        'token' => $request->code
      ])->first();

    if (!$updatePassword) {
      return $this->response([], "Code Not Match!!", false, 400);
    }
    return $this->response([], "Code Match Successfully");

    } catch (Exception $e) {
      return $this->response($e, "Something want Wrong!!", false, 400);
    }
  
  }

  public function update_password(Request $request){
    $validator = Validator::make($request->all(), [
      'email' => 'required|email|exists:users,email',
      'password' => 'required|string|min:6',
      'confirm_password' => 'required|string|same:password',
    ]);
    if ($validator->fails()) {
      return $this->response([], $validator->errors()->first(), false, 400);
    }
    try{
      $user = User::where('email', $request->email)
      ->update(['password' => Hash::make($request->password)]);

      DB::table('password_resets')->where(['email' => $request->email])->delete();

    return response()->json([
      'status' => 200,
      'message' => 'Your password has been changed!'
    ], 200);

    }catch (Exception $e) {
      return $this->response($e, "Something want Wrong!!", false, 400);
    }
  }

  /**
   * Write code on Method
   *
   * @return response()
   */
  public function submitResetPasswordForm(Request $request): JsonResponse
  {
    $request->validate([
      'email' => 'required|email|exists:users',
      'password' => 'required|string|min:6|confirmed',
      'password_confirmation' => 'required'
    ]);
    // print_r($request->all());exit;
    $updatePassword = DB::table('password_resets')
      ->where([
        'email' => $request->email,
        'token' => $request->token
      ])
      ->first();

    if (!$updatePassword) {
      return back()->withInput()->with('error', 'Invalid token!');
    }

    $user = User::where('email', $request->email)
      ->update(['password' => Hash::make($request->password)]);

    DB::table('password_resets')->where(['email' => $request->email])->delete();

    return response()->json([
      'status' => 200,
      'message' => 'Your password has been changed!'
    ], 200);
  }
}
