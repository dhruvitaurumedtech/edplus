<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dobusinesswith_Model;
use App\Models\Dobusinesswith_sub;
use App\Models\Topic_model;
use App\Models\VideoCategory;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function upload_video(Request $request){
    
        $validator = \Validator::make($request->all(), [
            'base_table_id'=>'required',
            'user_id'=>'required',
            'institute_id'=>'required',
            'standard_id'=> 'required',
            'subject_id'=> 'required',
            'chapter_id' => 'required',
            'topic_no' => 'required',
            'topic_name' => 'required',
            'category_id'=>'required',
            // 'topic_video' => 'required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,application/pdf|max:5242880',

            'topic_video_pdf' => 'required|mimes:mp4,mov,avi,pdf|max:5242880', // assuming you want to limit to specific video types and a max size of 10MB
        ]);
        if ($request->hasFile('topic_video')) {
            // Validate the uploaded file
            $request->validate([
                'topic_video' => 'required|mimes:mp4,mov,avi|max:5242880', // Adjust the validation rules as needed
            ]);
        }
        if ($validator->fails()) {
            return response()->json(['success' => 400,
            'message' => 'Upload Fail','error' => $validator->errors()], 400);
        }
       
        // if ($request->hasFile('topic_video')) {
        //     $videoPath = $request->file('topic_video')->store('videos', 'public');
        if ($request->hasFile('topic_video_pdf') && $request->file('topic_video_pdf')->isValid()) {
            $videoPath = $request->file('topic_video_pdf')->store('videos', 'public');
            //$topic->update(['topic_video' => $videoPath]);
        }
        $topic = Topic_model::create([
            'user_id'=>$request->input('user_id'),
            'institute_id'=>$request->input('institute_id'),
            'base_table_id'=>$request->input('base_table_id'),
            'standard_id'=> $request->input('standard_id'),
            'subject_id'=> $request->input('subject_id'),
            'chapter_id' => $request->input('chapter_id'),
            'topic_no' => $request->input('topic_no'),
            'topic_description' => $request->input('topic_description'),
            'topic_name' => $request->input('topic_name'),
            'video_category_id'=>$request->input('category_id'),
            'topic_video'=>asset($videoPath) //here add both video and pdf
        ]);
        
        

        return response()->json(['success' => 200,'message' => 'Topic and video uploaded successfully', 'topic' => $topic]);
  
    }

    //if need video category type
    public function video_category(Request $request){
        $user_id = $request->user_id;
        $instituteid = $request->institute_id;
        $categories = Dobusinesswith_sub::join('do_business_with','do_business_with.id','=','do_business_with_sub.do_business_with_id')
        ->where('do_business_with_sub.user_id',$user_id)
        ->where('do_business_with_sub.institute_id',$instituteid)
        ->where('do_business_with.status','active')
        ->whereNull('do_business_with.deleted_at')
        ->select('do_business_with.name','do_business_with.id as did','do_business_with.status')
        ->get();
        
        $videocat = [];
        foreach($categories as $catvalu){
            $videocat[] = array('id'=>$catvalu->did,'name'=>$catvalu->name,'status'=>$catvalu->status);
        }
        return response()->json(['success' => 200,'message' => 'Video Category List', 'Category' => $videocat]);
    }
}
