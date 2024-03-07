<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;




class ForgotPasswordController extends Controller
{

  public function submitForgetPasswordForm(Request $request): JsonResponse
  {
    if ($request->validate([
      'email' => 'required|email|exists:users',
    ])) {
      // $token = $request->header('Authorization');
      // print_r($token);
      $existingUser = User::where('email', $request->email)->first();
      DB::table('password_resets')->insert([
        'email' => $request->email,
        'token' => $existingUser->token,
        'created_at' => Carbon::now()
      ]);
      Mail::send('emails.forgot', ['token' => $existingUser->token], function ($message) use ($request) {
        $message->to($request->email);
        $message->subject('Reset Password');
      });

      return response()->json([
        'status' => 200,
        'message' => 'We have e-mailed your password reset link!'
      ], 200);
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
