<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner_model;
use App\Models\User;
use Illuminate\Http\Request;

class BannerApiController extends Controller
{
    public function banner_add(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'user_id' => 'required',
            'banner_image' => 'required',
            //'url' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try {
            $token = $request->header('Authorization');

            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser) {
                $user_id = $request->user_id;
                $institute_id = $request->institute_id;
                $url = $request->url;

                if ($request->hasFile('banner_image')) {
                    $banner_image = $request->file('banner_image');
                    $imagePath = $banner_image->store('banner_image', 'public');
                }

                $bannerad = Banner_model::create([
                    'user_id' => $user_id,
                    'institute_id' => $institute_id,
                    'banner_image' => $imagePath,
                    'url' => $url,
                    'status' => 'active'
                ]);

                if ($bannerad) {
                    return response()->json([
                        'success' => 200,
                        'message' => 'Banner create Successfully',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'success' => 500,
                        'message' => 'Banner Not create Successfully',
                        'data' => []
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'data' => array('error' => $e->getMessage()),
            ], 500);
        }
    }

    //update status
    public function update_status(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'user_id' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try {
            $token = $request->header('Authorization');

            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser) {
                $user_id = $request->user_id;
                $institute_id = $request->institute_id;
                $banner_image = $request->banner_image;
                $url = $request->url;
                $status = $request->status;

                $bannerad = Banner_model::where('user_id', $user_id)->where('institute_id', $institute_id)->update([
                    'status' => $status
                ]);

                if ($bannerad) {
                    return response()->json([
                        'success' => 200,
                        'message' => 'Status Update Successfully',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'success' => 500,
                        'message' => 'Status Update create Successfully',
                        'data' => []
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'data' => array('error' => $e->getMessage()),
            ], 500);
        }
    }

    //update banner record
    public function banner_detail_update(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'user_id' => 'required',
            'url' => 'required',
            'banner_image' => 'required',
            'banner_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try {
            $token = $request->header('Authorization');

            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser) {
                $user_id = $request->user_id;
                $institute_id = $request->institute_id;
                $url = $request->url;
                $banner_id = $request->banner_id;

                if ($request->hasFile('banner_image')) {
                    $banner_image = $request->file('banner_image');
                    $imagePath = $banner_image->store('banner_image', 'public');
                }

                $bannerad = Banner_model::where('id', $banner_id)
                    ->update([
                        'banner_image' => $imagePath,
                        'url' => $url
                    ]);

                if ($bannerad) {
                    return response()->json([
                        'success' => 200,
                        'message' => 'Status Update Successfully',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'success' => 500,
                        'message' => 'Status Not Update Successfully',
                        'data' => []
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'data' => array('error' => $e->getMessage()),
            ], 500);
        }
    }

    //banner list
    public function banner_list(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try {
            $token = $request->header('Authorization');

            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser) {
                $user_id = $request->user_id;
                $institute_id = $request->institute_id;
                $url = $request->url;

                if ($request->hasFile('banner_image')) {
                    $banner_image = $request->file('banner_image');
                    $imagePath = $banner_image->store('banner_image', 'public');
                }

                $bannerad = Banner_model::where('user_id', $user_id)->where('institute_id', $institute_id)
                    ->get();
                $banners = [];
                foreach ($bannerad as $bnDT) {
                    $banners[] = array(
                        'id' => $bnDT->id,
                        'user_id' => $bnDT->user_id,
                        'institute_id' => $bnDT->institute_id,
                        'banner_image' => url($bnDT->banner_image),

                        'url' => $bnDT->url,
                        'status' => $bnDT->status
                    );
                }

                if (!empty($banners)) {
                    return response()->json([
                        'success' => 200,
                        'message' => 'Data Fetch Successfully',
                        'data' => $banners
                    ], 200);
                } else {
                    return response()->json([
                        'success' => 500,
                        'message' => 'Data not Found',
                        'data' => []
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'data' => array('error' => $e->getMessage()),
            ], 500);
        }
    }
}
