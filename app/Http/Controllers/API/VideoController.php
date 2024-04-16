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
    public function upload_video(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'base_table_id' => 'required',
            'user_id' => 'required',
            'institute_id' => 'required',
            'standard_id' => 'required',
            'subject_id' => 'required',
            'chapter_id' => 'required',
            'topic_no' => 'required',
            'topic_name' => 'required',
            'category_id' => 'required',
            'parent_category_id' => 'required',
        ]);

        $check = Dobusinesswith_Model::where('id', $request->category_id)->select('category_id')->first();


        if ($check->category_id == $request->parent_category_id) {

            if ($request->parent_category_id == '1' || $request->parent_category_id == '2') {
                $extension = 'mp4,mov,avi';
            } else {
                $extension = 'pdf';
            }
        } else {
            return response()->json([
                'success' => 400,
                'message' => 'Please Select Correct Category'
            ], 400);
        }

        // $validator->sometimes('topic_video_pdf', 'required|mimes:' . $extension . '|max:5242880', function ($input) {
        //     return $input->parent_category_id == '1' || $input->parent_category_id == '2';
        // });
        $videoValidator = \Validator::make($request->all(), [
            'topic_video_pdf' => 'sometimes|required|mimes:mp4,mov,avi|max:5242880',
        ]);

        // Validation rules for PDF files
        $pdfValidator = \Validator::make($request->all(), [
            'topic_video_pdf' => 'sometimes|required|mimes:pdf|max:5242880',
        ]);

        // Check if parent_category_id indicates video or PDF upload
        $isVideo = $request->parent_category_id == '1' || $request->parent_category_id == '3';


        if ($validator->fails()) {
            return response()->json([
                'success' => 400,
                'message' => 'Upload Fail',
                'error' => $validator->errors()
            ], 400);
        }

        if ($request->hasFile('topic_video_pdf') && $request->file('topic_video_pdf')->isValid()) {
            $videoPath = $request->file('topic_video_pdf')->store('videos', 'public');
        } elseif (!$isVideo && $request->hasFile('topic_video_pdf') && $request->file('topic_video_pdf')->isValid()) {
            $pdfPath = $request->file('topic_video_pdf')->store('pdfs', 'public');
        }

        $topic = Topic_model::create([
            'user_id' => $request->input('user_id'),
            'institute_id' => $request->input('institute_id'),
            'base_table_id' => $request->input('base_table_id'),
            'standard_id' => $request->input('standard_id'),
            'subject_id' => $request->input('subject_id'),
            'chapter_id' => $request->input('chapter_id'),
            'topic_no' => $request->input('topic_no'),
            'topic_description' => $request->input('topic_description'),
            'topic_name' => $request->input('topic_name'),
            'video_category_id' => $request->input('category_id'),
            'topic_video' => isset($videoPath) ? asset($videoPath) : null
        ]);

        return response()->json([
            'success' => 200,
            'message' => 'Topic and video uploaded successfully',
            'topic' => $topic
        ]);
    }

    //if need video category type
    public function video_category(Request $request)
    {
        $user_id = $request->user_id;
        $instituteid = $request->institute_id;
        $categories = Dobusinesswith_sub::join('do_business_with', 'do_business_with.id', '=', 'do_business_with_sub.do_business_with_id')
            ->join('video_categories', 'video_categories.id', '=', 'do_business_with.category_id')
            ->where('do_business_with_sub.user_id', $user_id)
            ->where('do_business_with_sub.institute_id', $instituteid)
            ->where('do_business_with.status', 'active')
            ->whereNull('do_business_with.deleted_at')
            ->select('do_business_with.name', 'do_business_with.id as did', 'do_business_with.status', 'video_categories.name as cname', 'video_categories.id as cid')
            ->get();

        $videocat = [];
        foreach ($categories as $catvalu) {
            $videocat[] = array(
                'id' => $catvalu->did, 'name' => $catvalu->name,
                'parent_category_id' => $catvalu->cid, 'parent_category_name' => $catvalu->cname, 'status' => $catvalu->status
            );
        }
        return response()->json(['success' => 200, 'message' => 'Video Category List', 'Category' => $videocat]);
    }
}
