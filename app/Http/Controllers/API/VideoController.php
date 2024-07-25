<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Base_table;
use App\Models\board;
use App\Models\Class_model;
use App\Models\Dobusinesswith_Model;
use App\Models\Dobusinesswith_sub;
use App\Models\Institute_detail;
use App\Models\Medium_model;
use App\Models\Standard_model;
use App\Models\Subject_model;
use App\Models\Topic_model;
use App\Models\User;
use App\Models\VideoAssignToBatch;
use App\Models\VideoAssignToBatch_Sub;
use App\Models\VideoCategory;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    use ApiTrait;

    public function upload_video(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
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
            //'created_by' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        $institute_name=Institute_detail::where('id',$request->input('institute_id'))->first();
        $base_table = Base_table::where('id',$request->base_table_id)->first();
        $ins_name=$institute_name->institute_name;
        $ins_name_without_spaces = str_replace(' ', '', $ins_name);
        $board = board::where('id',$base_table->board)->first();
        $board_name = $board->name;
        $board_name_without_spaces = str_replace(' ', '', $board_name);
        $medium = Medium_model::where('id',$base_table->medium)->first();
        $medium_name = $medium->name;
        $medium_name_without_spaces = str_replace(' ', '', $medium_name);
        $class = Class_model::where('id',$base_table->institute_for_class)->first();
        $class_name = $class->name;
        $class_name_without_spaces = str_replace(' ', '', $class_name);
        $standard = Standard_model::where('id',$base_table->standard)->first();
        $standard_name = $standard->name;
        $standard_name_without_spaces = str_replace(' ', '', $standard_name);
        $subject = Subject_model::where('id',$request->subject_id)->first();
        $subject_name = $subject->name;
        $subject_name_without_spaces = str_replace(' ', '', $subject_name);
         
        $dynamicPath = "$ins_name_without_spaces/$board_name_without_spaces/$medium_name_without_spaces/$class_name_without_spaces/$standard_name_without_spaces/$subject_name_without_spaces";
        if (!Storage::exists("public/$dynamicPath")) {
            Storage::makeDirectory("public/$dynamicPath", 0775, true);
        }
        $check = Dobusinesswith_Model::where('id', $request->category_id)->select('category_id')->first();
        
        if (!$check || $check->category_id != $request->parent_category_id) {
            return $this->response([], 'Please Select Correct Category', false, 400);
        }
        $extensions = [];
        if ($request->parent_category_id == '1') {
            $extensions = ['mp4', 'mov', 'avi'];
        } elseif ($request->parent_category_id == '3') {
            $extensions = ['pdf'];
        }
        $validator = Validator::make($request->all(), []);
        $validator->sometimes('topic_video_pdf', 'required|mimes:' . implode(',', $extensions) . '|max:204800', function ($input) use ($request) {
            return $request->parent_category_id == '1' || $request->parent_category_id == '3';
        });
        
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return $this->response([], $errorMessage, false, 400);
        }
        
        try {
            // print_r($request->file('topic_video_pdf'));exit;
            // Handle file upload
            if ($request->hasFile('topic_video_pdf') && $request->file('topic_video_pdf')->isValid()) {
                $path = '';
                // echo "$dynamicPath/video";exit;
                if ($request->parent_category_id == '1') {
                    // $path = $request->file('topic_video_pdf')->store("public/$dynamicPath", 'public');
                    $fileName = $request->file('topic_video_pdf')->getClientOriginalName();
                    $path = $request->file('topic_video_pdf')->storeAs("$dynamicPath/videos", $fileName);
                    //s3 bucket
                     // $filename = $request->file('topic_video_pdf')->getClientOriginalName();
                     // $filePath = Storage::disk('s3')->putFileAs("$dynamicPath/videos", $request->file('topic_video_pdf'), $filename);
                    


                } elseif ($request->parent_category_id == '3') {
                    if (implode(',', $extensions) == 'pdf') {
                        // $path = $request->file('topic_video_pdf')->store("public/$dynamicPath", 'public');
                        $fileName = $request->file('topic_video_pdf')->getClientOriginalName();
                        $path = $request->file('topic_video_pdf')->storeAs("$dynamicPath/pdfs", $fileName);
                          
                        //s3 bucket
                        // $filename = $request->file('topic_video_pdf')->getClientOriginalName();
                        // $filePath = Storage::disk('s3')->putFileAs("$dynamicPath/pdfs", $request->file('topic_video_pdf'), $filename);
                     }
                }
            }
            $videoupld = new Topic_model();
            $msg = 'uploaded';
            if ($request->video_id) {
                $msg = 'updated';
                $videoupld = $videoupld->find($request->video_id);
                if (!$videoupld) {
                    return $this->response([], 'Record not found', false, 400);
                }
            }

            $videoupld->user_id = $request->input('user_id');
            $videoupld->institute_id = $request->input('institute_id');
            $videoupld->base_table_id = $request->input('base_table_id');
            $videoupld->standard_id = $request->input('standard_id');
            $videoupld->subject_id = $request->input('subject_id');
            $videoupld->chapter_id = $request->input('chapter_id');
            $videoupld->topic_no = $request->input('topic_no');
            $videoupld->topic_description = $request->input('topic_description');
            $videoupld->topic_name = $request->input('topic_name');
            $videoupld->video_category_id = $request->input('category_id');
            $videoupld->topic_video = isset($path) ? asset($path) : null;//Storage::disk('s3')->url($path)
            $videoupld->created_by = ($request->user_id)? $request->user_id:'';
            $videoupld->save();   
            // $topic = Topic_model::create([
            //     $videoupld->user_id => $request->input('user_id'),
            //     $videoupld->institute_id => $request->input('institute_id'),
            //     'base_table_id' => $request->input('base_table_id'),
            //     'standard_id' => $request->input('standard_id'),
            //     'subject_id' => $request->input('subject_id'),
            //     'chapter_id' => $request->input('chapter_id'),
            //     'topic_no' => $request->input('topic_no'),
            //     'topic_description' => $request->input('topic_description'),
            //     'topic_name' => $request->input('topic_name'),
            //     'video_category_id' => $request->input('category_id'),
            //     'topic_video' => isset($path) ? asset($path) : null,
            //     'created_by' => ($request->user_id)? $request->user_id:'',
            // ]);
            return $this->response($videoupld, "Topic and file $msg successfully");
        } catch (Exception $e) {
            return $this->response($e, 'Something went Wrong!!', false, 400);
        }
    }

    public function upload_youtube_video(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            'youtube_url' => 'required|url',

        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        $check = Dobusinesswith_Model::where('id', $request->category_id)->select('category_id')->first();
        if (!$check || $check->category_id != $request->parent_category_id) {
            return $this->response([], 'Please Select Correct Category', false, 400);
        }

        try {
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
                'topic_video' =>  $request->youtube_url,
                'created_by' => ($request->user_id) ? $request->user_id:'',
            ]);
            return $this->response($topic, 'Topic and file uploaded successfully');
        } catch (Exception $e) {
            return $this->response($e, 'Something went Wrong!!', false, 400);
        }
    }

    public function delete_video(Request $request){
        $validator = Validator::make($request->all(), [
            'video_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        
        try{
            $videodel = Topic_model::where('id',$request->video_id);
            if(!empty($videodel)){
                $videodel->delete();
                return $this->response([], "Video Delete Successfully.");
            }else{
                return $this->response([], "Record Not Found.");
            }
        }catch (Exception $e) {
            return $this->response($e, "Somthing went wrong.", false, 400);
        }

    }

    // public function upload_video1(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'base_table_id' => 'required',
    //         'user_id' => 'required',
    //         'institute_id' => 'required',
    //         'standard_id' => 'required',
    //         'subject_id' => 'required',
    //         'chapter_id' => 'required',
    //         'topic_no' => 'required',
    //         'topic_name' => 'required',
    //         'category_id' => 'required',
    //         'parent_category_id' => 'required',
    //     ]);

    //     $check = Dobusinesswith_Model::where('id', $request->category_id)->select('category_id')->first();

    //     if (!$check || $check->category_id != $request->parent_category_id) {
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Please Select Correct Category',
    //             'data' => []
    //         ], 400);
    //     }

    //     // Validate the file based on its type
    //     $extensions = [];
    //     if ($request->parent_category_id == '1') {
    //         $extensions = ['mp4', 'mov', 'avi'];
    //     } elseif ($request->parent_category_id == '3') {
    //         $extensions = ['pdf'];
    //     }

    //     $validator->sometimes('topic_video_pdf', 'required|mimes:' . implode(',', $extensions) . '|max:5242880', function ($input) use ($request) {
    //         return $request->parent_category_id == '1' || $request->parent_category_id == '3';
    //     });

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Upload Fail',
    //             'error' => $validator->errors()
    //         ], 400);
    //     }

    //     // Handle file upload
    //     if ($request->hasFile('topic_video_pdf') && $request->file('topic_video_pdf')->isValid()) {
    //         $path = '';
    //         if ($request->parent_category_id == '1') {
    //             $path = $request->file('topic_video_pdf')->store('videos', 'public');
    //         } elseif ($request->parent_category_id == '3') {
    //             if (implode(',', $extensions) == 'pdf') {
    //                 $path = $request->file('topic_video_pdf')->store('pdfs', 'public');
    //             }
    //         }
    //     }

    //     // Create topic record in the database
    //     $topic = Topic_model::create([
    //         'user_id' => $request->input('user_id'),
    //         'institute_id' => $request->input('institute_id'),
    //         'base_table_id' => $request->input('base_table_id'),
    //         'standard_id' => $request->input('standard_id'),
    //         'subject_id' => $request->input('subject_id'),
    //         'chapter_id' => $request->input('chapter_id'),
    //         'topic_no' => $request->input('topic_no'),
    //         'topic_description' => $request->input('topic_description'),
    //         'topic_name' => $request->input('topic_name'),
    //         'video_category_id' => $request->input('category_id'),
    //         'topic_video' => isset($path) ? asset($path) : null
    //     ]);

    //     return response()->json([
    //         'success' => 200,
    //         'message' => 'Topic and file uploaded successfully',
    //         'topic' => $topic
    //     ]);
    // }

    //if need video category type
    // public function video_category(Request $request)
    // {
    //     $user_id = $request->user_id;
    //     $instituteid = $request->institute_id;
    //     $categories = Dobusinesswith_sub::join('do_business_with', 'do_business_with.id', '=', 'do_business_with_sub.do_business_with_id')
    //         ->join('video_categories', 'video_categories.id', '=', 'do_business_with.category_id')
    //         ->where('do_business_with_sub.user_id', $user_id)
    //         ->where('do_business_with_sub.institute_id', $instituteid)
    //         ->where('do_business_with.status', 'active')
    //         ->whereNull('do_business_with.deleted_at')
    //         ->select('do_business_with.name', 'do_business_with.id as did', 'do_business_with.status', 'video_categories.name as cname', 'video_categories.id as cid')
    //         ->get();

    //     $videocat = [];
    //     foreach ($categories as $catvalu) {
    //         $videocat[] = array(
    //             'id' => $catvalu->did,
    //             'name' => $catvalu->name,
    //             'parent_category_id' => $catvalu->cid,
    //             'parent_category_name' => $catvalu->cname,
    //             'status' => $catvalu->status
    //         );
    //     }
    //     return response()->json(['success' => 200, 'message' => 'Video Category List', 'Category' => $videocat]);
    // }

    public function video_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $categories = Dobusinesswith_sub::where('user_id', Auth::id())
                ->where('institute_id', $request->institute_id)
                ->whereHas('business', function ($query) {
                    $query->where('status', 'active');
                })
                ->with(['business', 'business.category'])
                ->get();
            $video_categories = $categories->map(function ($category) {
                return [
                    'id' => $category->business->id,
                    'name' => $category->business->name,
                    'parent_category_id' => $category->business->category->id,
                    'parent_category_name' => $category->business->category->name,
                    'status' => $category->business->status
                ];
            });
            return $this->response($video_categories, "Video Category List");
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    public function videoassign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'batch_id' => 'exists:batches,id',
            'standard_id' => 'required',
            'chapter_id' => 'required',
            'subject_id' => 'required',
            'user_id'  => 'required',
            'assign_status' => 'required',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $batch_ids = explode(",", $request->batch_id);
            foreach ($batch_ids as $batch_id_value) { 
                $existingRecordsCount = VideoAssignToBatch::where('batch_id', $batch_id_value)
                    ->where('subject_id', $request->subject_id)
                    ->count();
                if ($existingRecordsCount >= 4) {
                    return $this->response([], 'Four records with the same Batch and Subject already exist', false, 400);
                }
            }

            $existingBatches = VideoAssignToBatch::where('standard_id', $request->standard_id)
                    ->where('subject_id', $request->subject_id)
                    ->where('chapter_id', $request->chapter_id) 
                    // ->where('user_id', $request->user_id)
                    ->where('assign_status', 1)
                    ->pluck('batch_id')->toArray(); 
 
           $removed_id= array_diff($existingBatches,$batch_ids);
           $add_id= array_diff($batch_ids,$existingBatches); 
           if(!empty($removed_id))
           {
           
                foreach ($add_id as $value) {
                    $VideoAssignToBatch = VideoAssignToBatch::create([
                        'video_id' => $request->video_id,
                        'batch_id' => $value,
                        'standard_id' => $request->standard_id,
                        'chapter_id' => $request->chapter_id,
                        'subject_id' => $request->subject_id,
                        'assign_status' => $request->assign_status,
                    ]);
                }
            }
            if(!empty($removed_id))
            { 
            VideoAssignToBatch::where('standard_id', $request->standard_id)
                    ->where('subject_id', $request->subject_id)
                    ->where('chapter_id', $request->chapter_id) 
                    //->where('user_id', $request->user_id)
                    ->where('assign_status', 1)
                    ->wherein('batch_id', $removed_id)->delete(); 
            }
            return $this->response([], "Video Assign Batch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function video_batchlist(Request $request){
        $validator = Validator::make($request->all(), [
            'standard_id' => 'required',
            'chapter_id' => 'required',
            'subject_id' => 'required',
             ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400); 
        try {
        $batch_list = VideoAssignToBatch::where('standard_id', $request->standard_id)
                    ->where('subject_id', $request->subject_id)
                    ->where('chapter_id', $request->chapter_id)
                    ->whereNull('deleted_at')
                    ->pluck('batch_id')
                    ->toArray();
                    return $this->response($batch_list, "Successfully display Batchlist");            
        }
        catch (Exception $e) {
            return $this->response($e, "Something want wrong.", false, 400);
        }
                
    }


    // public function videoassign1(Request $request)
    // {
    //     $batch_id = $request->batch_id;
    //     $video_id = $request->video_id;
    //     $user_id = $request->user_id;
    //     $standard_id = $request->standard_id;
    //     $chapter_id = $request->chapter_id;
    //     $subject_id = $request->subject_id;

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $user_id)->first();
    //     if ($existingUser) {
    //         $validator = \Validator::make($request->all(), [
    //             'batch_id' => 'required|exists:batches,id',
    //             'standard_id' => 'required',
    //             'chapter_id' => 'required',
    //             'subject_id' => 'required',
    //             'user_id'  => 'required',
    //         ]);

    //         // Check if validation fails
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'success' => 400,
    //                 'message' => 'Validation failed',
    //                 'errors' => $validator->errors(),
    //             ], 400);
    //         }
    //         $batch_ids = explode(",", $batch_id);
    //         foreach ($batch_ids as $batch_id_value) {
    //             $record = VideoAssignToBatch::where('batch_id', $batch_id_value)
    //                 ->where('subject_id', $subject_id)
    //                 ->where('video_id', $video_id)
    //                 ->count();
    //             if ($record == 1) {
    //                 return response()->json([
    //                     'success' => 400,
    //                     'message' => 'Already Assign Video This Batch!',
    //                     'data' => []
    //                 ], 400);
    //             }
    //             $existingRecordsCount = VideoAssignToBatch::where('batch_id', $batch_id_value)
    //                 ->where('subject_id', $subject_id)
    //                 ->count();
    //             if ($existingRecordsCount >= 4) {
    //                 return response()->json([
    //                     'success' => 400,
    //                     'message' => 'Four records with the same Batch and Subject already exist',
    //                     'data' => []
    //                 ], 400);
    //             }
    //         }
    //         // video_assignbatch::whereIn('b')
    //         foreach ($batch_ids as $value) {
    //             $VideoAssignToBatch = VideoAssignToBatch::create([
    //                 'video_id' => $video_id,
    //                 'batch_id' => $value,
    //                 'standard_id' => $standard_id,
    //                 'chapter_id' => $chapter_id,
    //                 'subject_id' => $subject_id
    //             ]);
    //         }
    //         // foreach ($batch_ids as $value) {
    //         //     $assign_video_sub = VideoAssignToBatch_Sub::create([
    //         //         'video_assign_id' => $VideoAssignToBatch->id,
    //         //         'batch_id' => $value,
    //         //         'subject_id' => $subject_id,
    //         //     ]);
    //         // }

    //         return response()->json([
    //             'success' => 200,
    //             'message' => 'Video Assign Batch Successfully',
    //             'data' => []
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //             'data' => []
    //         ]);
    //     }
    // }
}
