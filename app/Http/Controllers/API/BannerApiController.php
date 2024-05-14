<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner_model;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Auth;

class BannerApiController extends Controller
{
    use ApiTrait;
    // public function banner_add(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'institute_id' => 'required',
    //         'user_id' => 'required',
    //         'banner_image' => 'required',
    //         //'url' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     try {
    //         $token = $request->header('Authorization');

    //         if (strpos($token, 'Bearer ') === 0) {
    //             $token = substr($token, 7);
    //         }

    //         $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //         if ($existingUser) {
    //             $user_id = $request->user_id;
    //             $institute_id = $request->institute_id;
    //             $url = $request->url;

    //             if ($request->hasFile('banner_image')) {
    //                 $banner_image = $request->file('banner_image');
    //                 $imagePath = $banner_image->store('banner_image', 'public');
    //             }

    //             $bannerad = Banner_model::create([
    //                 'user_id' => $user_id,
    //                 'institute_id' => $institute_id,
    //                 'banner_image' => $imagePath,
    //                 'url' => $url,
    //                 'status' => 'active'
    //             ]);

    //             if ($bannerad) {
    //                 return response()->json([
    //                     'success' => 200,
    //                     'message' => 'Banner create Successfully',
    //                     'data' => []
    //                 ], 200);
    //             } else {
    //                 return response()->json([
    //                     'success' => 500,
    //                     'message' => 'Banner Not create Successfully',
    //                     'data' => []
    //                 ], 200);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid token.',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'Something went wrong',
    //             'data' => array('error' => $e->getMessage()),
    //         ], 500);
    //     }
    // }


    public function banner_add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'banner_image' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $bannerad = new Banner_model();
            $bannerad->user_id = Auth::id();
            $bannerad->institute_id = $request->institute_id;
            if ($request->hasFile('banner_image')) {
                $banner_image = $request->file('banner_image');
                $imagePath = $banner_image->store('banner_image', 'public');
                $bannerad->banner_image = $imagePath;
            }
            $bannerad->url = $request->url;
            $bannerad->status = 'inactive';
            $bannerad->save();
            return $this->response([], "Banner create Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    public function update_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'status' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $banner_count=Banner_model::where('user_id', Auth::id())
                 ->where('institute_id', $request->institute_id)
                 ->where('status', 'active')->count();
                //  echo $banner_count;exit;
            if($banner_count != 5 ){
                Banner_model::where('user_id', Auth::id())
                            ->where('institute_id', $request->institute_id)
                            ->where('id', $request->id)->update(['status' => $request->status]);
            }else{
                return $this->response([], "Maxium banner active limit 5!");
            }  
             
            return $this->response([], "Status Update create Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }




    //update status
    // public function update_statusss(Request $request)
    // {

    //     $validator = \Validator::make($request->all(), [
    //         'institute_id' => 'required',
    //         'user_id' => 'required',
    //         'status' => 'required',
    //         'id' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     try {
    //         $token = $request->header('Authorization');

    //         if (strpos($token, 'Bearer ') === 0) {
    //             $token = substr($token, 7);
    //         }

    //         $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //         if ($existingUser) {
    //             $user_id = $request->user_id;
    //             $institute_id = $request->institute_id;
    //             $status = $request->status;
    //             $id = $request->id;
    //             $bannerad = Banner_model::where('user_id', $user_id)
    //                 ->where('institute_id', $institute_id)
    //                 ->where('id', $id)->update([
    //                     'status' => $status
    //                 ]);

    //             if ($bannerad) {
    //                 return response()->json([
    //                     'success' => 200,
    //                     'message' => 'Status Update Successfully',
    //                     'data' => []
    //                 ], 200);
    //             } else {
    //                 return response()->json([
    //                     'success' => 500,
    //                     'message' => 'Status Update create Successfully',
    //                     'data' => []
    //                 ], 200);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid token.',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'Something went wrong',
    //             'data' => array('error' => $e->getMessage()),
    //         ], 500);
    //     }
    // }

    //update banner record
    // public function banner_detail_update(Request $request)
    // {

    //     $validator = \Validator::make($request->all(), [
    //         'institute_id' => 'required',
    //         'user_id' => 'required',
    //         //'banner_image' => 'required',
    //         'id' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     try {
    //         $token = $request->header('Authorization');

    //         if (strpos($token, 'Bearer ') === 0) {
    //             $token = substr($token, 7);
    //         }

    //         $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //         if ($existingUser) {
    //             $user_id = $request->user_id;
    //             $institute_id = $request->institute_id;
    //             $url = $request->url;
    //             $id = $request->id;

    //             $imagePath = null;
    //             if ($request->hasFile('banner_image')) {
    //                 $banner_image = $request->file('banner_image');
    //                 $imagePath = $banner_image->store('banner_image', 'public');
    //             }
    //             $updateData = ['url' => $url];
    //             if ($imagePath !== null) {
    //                 $updateData['banner_image'] = $imagePath;
    //             }

    //             $bannerad = Banner_model::where('id', $id)
    //                 ->update($updateData);

    //             if ($bannerad) {
    //                 return response()->json([
    //                     'success' => 200,
    //                     'message' => 'Update Successfully',
    //                     'data' => []
    //                 ], 200);
    //             } else {
    //                 return response()->json([
    //                     'success' => 500,
    //                     'message' => 'Not Update Successfully',
    //                     'data' => []
    //                 ], 200);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid token.',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'Something went wrong',
    //             'data' => array('error' => $e->getMessage()),
    //         ], 500);
    //     }
    // }


    public function banner_detail_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $bannerad = Banner_model::find($request->id);
            $bannerad->user_id = Auth::id();
            $bannerad->institute_id = $request->institute_id;
            if ($request->hasFile('banner_image')) {
                $banner_image = $request->file('banner_image');
                $imagePath = $banner_image->store('banner_image', 'public');
                $bannerad->banner_image = $imagePath;
            }
            $bannerad->url = $request->url;
            $bannerad->status = 'active';
            $bannerad->save();
            return $this->response([], "Banner Updated Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    public function  banner_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $banners = Banner_model::where('user_id', Auth::id())
                ->where('institute_id', $request->institute_id)
                ->orderByDesc('created_at')
                ->get(['id', 'user_id', 'institute_id', 'banner_image', 'url', 'status'])
                ->map(function ($banner) {
                    return [
                        'id' => $banner->id,
                        'user_id' => $banner->user_id,
                        'institute_id' => $banner->institute_id,
                        'banner_image' => url($banner->banner_image),
                        'url' => $banner->url,
                        'status' => $banner->status
                    ];
                })
                ->toArray();
            return $this->response($banners, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }



    //banner list
    // public function banner_list(Request $request)
    // {

    //     $validator = \Validator::make($request->all(), [
    //         'institute_id' => 'required',
    //         'user_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     try {
    //         $token = $request->header('Authorization');

    //         if (strpos($token, 'Bearer ') === 0) {
    //             $token = substr($token, 7);
    //         }

    //         $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //         if ($existingUser) {
    //             $user_id = $request->user_id;
    //             $institute_id = $request->institute_id;
    //             $url = $request->url;

    //             if ($request->hasFile('banner_image')) {
    //                 $banner_image = $request->file('banner_image');
    //                 $imagePath = $banner_image->store('banner_image', 'public');
    //             }

    //             $bannerad = Banner_model::where('user_id', $user_id)
    //                 ->where('institute_id', $institute_id)
    //                 ->orderByDesc('created_at')
    //                 ->get();
    //             $banners = [];
    //             foreach ($bannerad as $bnDT) {
    //                 $banners[] = array(
    //                     'id' => $bnDT->id,
    //                     'user_id' => $bnDT->user_id,
    //                     'institute_id' => $bnDT->institute_id,
    //                     'banner_image' => url($bnDT->banner_image),

    //                     'url' => $bnDT->url,
    //                     'status' => $bnDT->status
    //                 );
    //             }

    //             if (!empty($banners)) {
    //                 return response()->json([
    //                     'success' => 200,
    //                     'message' => 'Data Fetch Successfully',
    //                     'data' => $banners
    //                 ], 200);
    //             } else {
    //                 return response()->json([
    //                     'success' => 500,
    //                     'message' => 'Data not Found',
    //                     'data' => []
    //                 ], 200);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid token.',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'Something went wrong',
    //             'data' => array('error' => $e->getMessage()),
    //         ], 500);
    //     }
    // }

    // public function banner_delete(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     try {
    //         $token = $request->header('Authorization');

    //         if (strpos($token, 'Bearer ') === 0) {
    //             $token = substr($token, 7);
    //         }

    //         $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //         if ($existingUser) {

    //             $remove = Banner_model::where('id', $request->id)->delete();

    //             return response()->json([
    //                 'success' => 200,
    //                 'message' => 'Delete Successfully',
    //                 'data' => []
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid token.',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'Something went wrong',
    //             'data' => array('error' => $e->getMessage()),
    //         ], 500);
    //     }
    // }


    public function  banner_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $remove = Banner_model::where('id', $request->id)->delete();
            return $this->response([], "Delete Successfullyu");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
}
