<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic_model;
use App\Models\VideoCategory;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function upload_video(Request $request){
       
        $validator = \Validator::make($request->all(), [
            'base_table_id'=>'required',
            'standard_id'=> 'required',
            'subject_id'=> 'required',
            'chapter_id' => 'required',
            'topic_no' => 'required',
            'topic_name' => 'required',
            'video_category_id'=>'required',
            'topic_video' => 'required|mimes:mp4,mov,avi|max:10240', // assuming you want to limit to specific video types and a max size of 10MB
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
       
        if ($request->hasFile('topic_video')) {
            $videoPath = $request->file('topic_video')->store('videos', 'public');
            //$topic->update(['topic_video' => $videoPath]);
        }
        
        $topic = Topic_model::create([
            'base_table_id'=>$request->input('base_table_id'),
            'standard_id'=> $request->input('standard_id'),
            'subject_id'=> $request->input('subject_id'),
            'chapter_id' => $request->input('chapter_id'),
            'topic_no' => $request->input('topic_no'),
            'topic_name' => $request->input('topic_name'),
            'video_category_id'=>$request->input('video_category_id'),
            'topic_video'=>$videoPath
        ]);
        
        

        return response()->json(['message' => 'Topic and video uploaded successfully', 'topic' => $topic]);
  
    }

    //if need video category type
    public function video_category(Request $request){
        $categories = VideoCategory::where('status','active')->get();
        foreach($categories as $catvalu){
            $videocat = array('id'=>$catvalu->id,'name'=>$catvalu->name,'status'=>$catvalu->status);
        }
        return response()->json(['message' => 'Video Category List', 'Category' => $videocat]);
    }
}
