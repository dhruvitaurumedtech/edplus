<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject_detail;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function upload_video(Request $request){
        $validator = \Validator::make($request->all(), [
            'topic_no' => 'required',
            'topic_name' => 'required',
            'topic_video' => 'required|mimes:mp4,mov,avi|max:10240', // assuming you want to limit to specific video types and a max size of 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $topic = Subject_detail::create([
            'subject_chapter_id' => $request->input('subject_chapter_id'),
            'topic_no' => $request->input('topic_no'),
            'topic_name' => $request->input('topic_name'),
        ]);

        if ($request->hasFile('topic_video')) {
            $videoPath = $request->file('topic_video')->store('videos', 'public');
            $topic->update(['topic_video' => $videoPath]);
        }

        return response()->json(['message' => 'Topic and video uploaded successfully', 'topic' => $topic]);
  
    }
}
