<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dobusinesswith_Model;
use App\Models\Dobusinesswith_sub;
use App\Models\Topic_model;
use App\Models\User;
use App\Models\VideoAssignToBatch;
use App\Models\VideoAssignToBatch_Sub;
use App\Models\VideoCategory;
use Illuminate\Contracts\Validation\Rule;
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

        if (!$check || $check->category_id != $request->parent_category_id) {
            return response()->json([
                'success' => false,
                'message' => 'Please Select Correct Category'
            ], 400);
        }

        // Validate the file based on its type
        $extensions = [];
        if ($request->parent_category_id == '1') {
            $extensions = ['mp4', 'mov', 'avi'];
        } elseif ($request->parent_category_id == '3') {
            $extensions = ['pdf'];
        }

        $validator->sometimes('topic_video_pdf', 'required|mimes:' . implode(',', $extensions) . '|max:5242880', function ($input) use ($request) {
            return $request->parent_category_id == '1' || $request->parent_category_id == '3';
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Upload Fail',
                'error' => $validator->errors()
            ], 400);
        }

        // Handle file upload
        if ($request->hasFile('topic_video_pdf') && $request->file('topic_video_pdf')->isValid()) {
            $path = '';
            if ($request->parent_category_id == '1') {
                $path = $request->file('topic_video_pdf')->store('videos', 'public');
            } elseif ($request->parent_category_id == '3') {
                if (implode(',', $extensions) == 'pdf') {
                    $path = $request->file('topic_video_pdf')->store('pdfs', 'public');
                }
            }
        }

        // Create topic record in the database
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
            'topic_video' => isset($path) ? asset($path) : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topic and file uploaded successfully',
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
    public function videoassign(Request $request)
    {
        $batch_id = $request->batch_id;
        $video_id = $request->video_id;
        $user_id = $request->user_id;
        $standard_id = $request->standard_id;
        $chapter_id = $request->chapter_id;
        $subject_id = $request->subject_id;

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $user_id)->first();
        if ($existingUser) {
            $validator = \Validator::make($request->all(), [
                'batch_id' => 'required|exists:batches,id',
                'standard_id' => 'required',
                'chapter_id' => 'required',
                'subject_id' => 'required',
                'user_id'  => 'required',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'success' => 400,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }
            $batch_ids = explode(",", $batch_id);
            foreach ($batch_ids as $batch_id_value) {
                $record = VideoAssignToBatch::where('batch_id', $batch_id_value)
                    ->where('subject_id', $subject_id)
                    ->get();
                if ($record) {
                    return response()->json([
                        'success' => 400,
                        'message' => 'Already Assign Video This Batch!',
                    ], 400);
                }
                $existingRecordsCount = VideoAssignToBatch::where('batch_id', $batch_id_value)
                    ->where('subject_id', $subject_id)
                    ->count();
                if ($existingRecordsCount >= 4) {
                    return response()->json([
                        'success' => 400,
                        'message' => 'Four records with the same Batch and Subject already exist',
                    ], 400);
                }
            }
            // video_assignbatch::whereIn('b')
            foreach ($batch_ids as $value) {
                $VideoAssignToBatch = VideoAssignToBatch::create([
                    'video_id' => $video_id,
                    'batch_id' => $value,
                    'standard_id' => $standard_id,
                    'chapter_id' => $chapter_id,
                    'subject_id' => $subject_id
                ]);
            }
            // foreach ($batch_ids as $value) {
            //     $assign_video_sub = VideoAssignToBatch_Sub::create([
            //         'video_assign_id' => $VideoAssignToBatch->id,
            //         'batch_id' => $value,
            //         'subject_id' => $subject_id,
            //     ]);
            // }

            return response()->json([
                'success' => 400,
                'message' => 'Video Assign Batch Successfully',
            ], 400);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
}
