<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner_model;
use App\Models\Institute_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public function list_banner(){
        $banner_list = DB::table('banner')
        ->leftjoin('institute_detail', 'banner.institute_id', '=', 'institute_detail.id')
        ->select('banner.*','institute_detail.institute_name')
        ->whereNull('banner.deleted_at')
        ->where('banner.user_id', Auth::user()->id)
        ->paginate(10);
        // $institute_list = Institute_detail::get();
        // $banner_list = Banner_model::where('user_id', Auth::user()->id)->paginate(10);
        return view('banner/list',compact('banner_list'));
    }
    public function create_banner(){
        $institute_list = Institute_detail::paginate(10);
        return view('banner/create',compact('institute_list'));
    }
    public function save_banner(Request $request){
        $request->validate([
            'banner_image' => 'required|array',
            'banner_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required',
        ]);
        $bannerImages = [];
        if ($request->hasFile('banner_image')) {
            foreach ($request->file('banner_image') as $file) {
              
                $imagePath = $file->store('banner_image', 'public');
                $bannerImages[] = $imagePath;
            }
        }
       
            if(!empty($request->input('institute_id'))){
                $institute_id = $request->input('institute_id');
            }else{
                $institute_id = '';
            
            } 
        
            if(auth::user()->role_type=='3'){
            foreach ($bannerImages as $imagePath) {
                $banner_id=Banner_model::create([
                    'user_id' => Auth::user()->id,
                    'institute_id'=>$institute_id,
                    'url'=>$request->input('url'),
                    'banner_image' => $imagePath,
                    'status' => $request->input('status'),
                    
                ]);
            }
        }
            
            if(auth::user()->role_type=='1'){
                foreach ($bannerImages as $imagePath) {
                   
                $banner_id=Banner_model::create([
                    'user_id' => Auth::user()->id,
                    'banner_image' => $imagePath,
                    'status' => $request->input('status'),
                ]);
              }
            
        }

    return redirect()->route('banner.list')->with('success', 'Banner Created Successfully');

    }
    function edit_banner(Request $request){
        $id = $request->input('banner_id');
        $banner_list = Banner_model::find($id);
        
        return response()->json(['banner_list'=>$banner_list]);
    }
    function update_banner(Request $request){
        // dd($request->all());exit;
        $id=$request->input('banner_id');
        $role = Banner_model::find($id);
        $request->validate([
            'banner_image' =>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status'=>'required',
       ]);
      
        $iconFile = $request->file('banner_image');
        if(!empty($iconFile)){
            $imagePath = $iconFile->store('banner_image', 'public');
        }else{
            $imagePath=$request->input('old_banner_image');
        }
        $role->update([
            'user_id'=>Auth::user()->id,
            'banner_image'=>$imagePath,
            'status'=>$request->input('status'),
        ]);
        return redirect()->route('banner.list')->with('success', 'Banner Updated successfully');
   
    }
    function banner_delete(Request $request){
        $banner_id=$request->input('banner_id');
        $banner_list = Banner_model::find($banner_id);

        if (!$banner_list) {
            return redirect()->route('banner.list')->with('error', 'banner not found');
        }

        $banner_list->delete();

        return redirect()->route('banner.list')->with('success', 'Banner deleted successfully');
  
      
    }
}
