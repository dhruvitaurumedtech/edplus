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
            $bannerad->url = (!empty($request->url)) ? $request->url : '';
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
            $banner_count = Banner_model::where('user_id', Auth::id())
                ->where('institute_id', $request->institute_id)
                ->where('status', 'active')->count();
            //  echo $banner_count;exit;
            if ($banner_count != 5) {
                Banner_model::where('user_id', Auth::id())
                    ->where('institute_id', $request->institute_id)
                    ->where('id', $request->id)->update(['status' => $request->status]);
            } else {
                return $this->response([], "Maxium banner active limit 5!");
            }

            return $this->response([], "Status Update create Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }

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
            $bannerad->url = (!empty($request->url)) ? $request->url : '';
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
