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
            if ($request->hasFile('topic_video_pdf') && $request->file('topic_video_pdf')->isValid()) {
                $path = '';
                // if ($request->parent_category_id == '1') {
                //     $fileName = $request->file('topic_video_pdf')->getClientOriginalName();
                //     $fileNames = str_replace(' ', '_', $fileName);
                //     $path = $request->file('topic_video_pdf')->storeAs("public/$dynamicPath/videos", $fileNames);
                //     //s3 bucket
                //      // $filename = $request->file('topic_video_pdf')->getClientOriginalName();
                //      // $filePath = Storage::disk('s3')->putFileAs("$dynamicPath/videos", $request->file('topic_video_pdf'), $filename);
                //   } elseif ($request->parent_category_id == '3') {
                //     if (implode(',', $extensions) == 'pdf') {
                //         $fileName = $request->file('topic_video_pdf')->getClientOriginalName();
                //         $fileNames = str_replace(' ', '_', $fileName);
                //         $path = $request->file('topic_video_pdf')->storeAs("public/$dynamicPath/pdfs", $fileNames);
                          
                //         //s3 bucket
                //         // $filename = $request->file('topic_video_pdf')->getClientOriginalName();
                //         // $filePath = Storage::disk('s3')->putFileAs("$dynamicPath/pdfs", $request->file('topic_video_pdf'), $filename);
                //      }
                // }
                if ($request->parent_category_id == '1') {
                    $file = $request->file('topic_video_pdf');
                    $fileName = $file->getClientOriginalName();
                    $fileNames = str_replace(' ', '_', $fileName);
                    $destinationPath = public_path("$dynamicPath/videos");
                
                    // Ensure the directory exists
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                
                    // Move the file
                    $file->move($destinationPath, $fileNames);
                
                    // Optionally, you can store the file path
                    $path = "$dynamicPath/videos/$fileNames";
                    
                } elseif ($request->parent_category_id == '3') {
                    if (in_array($request->file('topic_video_pdf')->extension(), ['pdf'])) {
                        $file = $request->file('topic_video_pdf');
                        $fileName = $file->getClientOriginalName();
                        $fileNames = str_replace(' ', '_', $fileName);
                        $destinationPath = public_path("$dynamicPath/pdfs");
                
                        // Ensure the directory exists
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }
                
                        // Move the file
                        $file->move($destinationPath, $fileNames);
                
                        // Optionally, you can store the file path
                        $path = "$dynamicPath/pdfs/$fileNames";
                    }
                }
                
                $fullPath =  url($path);
            }
            
            if ($request->video_id) {
                $msg = 'updated';
                $videoupld = Topic_model::where('id',$request->video_id)->first();
                if (!$videoupld) {
                    return $this->response([], 'Record not found', false, 400);
                }
            }else{
                $videoupld = new Topic_model();
                $msg = 'uploaded';
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
            $videoupld->topic_video = isset($fullPath) ? $fullPath : null;//Storage::disk('s3')->url($path)
            $videoupld->created_by = ($request->user_id)? $request->user_id:'';
            $videoupld->save();   
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
            if ($request->video_id) {
                $msg = 'updated';
                $videoupld = Topic_model::where('id',$request->video_id)->first();
                if (!$videoupld) {
                    return $this->response([], 'Record not found', false, 400);
                }
            }else{
                $videoupld = new Topic_model();
                $msg = 'uploaded';
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
            $videoupld->topic_video = $request->youtube_url;//Storage::disk('s3')->url($path)
            $videoupld->created_by = ($request->user_id)? $request->user_id:'';
            $videoupld->save(); 

            return $this->response($videoupld, "Topic and file $msg successfully");
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
            return $this->response($e, "Something went wrong.", false, 400);
        }

    }

    public function video_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $categories = Dobusinesswith_sub::where('institute_id', $request->institute_id)
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
            'standard_id' => 'required',
            'chapter_id' => 'required',
            'subject_id' => 'required',
            'user_id'  => 'required',
            'assign_status' => 'required',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $batch_ids=[];

            if($request->batch_id)
            $batch_ids= explode(",", $request->batch_id);
            foreach ($batch_ids as $batch_id_value) { 
                $existingRecordsCount = VideoAssignToBatch::where('batch_id', $batch_id_value)
                    ->where('subject_id', $request->subject_id)
                    ->count();
                if ($existingRecordsCount >= 4) {
                    return $this->response([], 'Four records with the same Batch and Subject already exist', false, 400);
                }
            }

            VideoAssignToBatch::where('standard_id', $request->standard_id)
            ->where('subject_id', $request->subject_id)
            ->where('chapter_id', $request->chapter_id) 
            ->where('video_id', $request->video_id)
            ->where('assign_status', 1)->forcedelete(); 

            foreach ($batch_ids as $batch_id_value) {
            $VideoAssignToBatch = VideoAssignToBatch::create([
                'video_id' => $request->video_id,
                'batch_id' => $batch_id_value,
                'standard_id' => $request->standard_id,
                'chapter_id' => $request->chapter_id,
                'subject_id' => $request->subject_id,
                'assign_status' => $request->assign_status,
            ]);
        }
            return $this->response([], "Video Assign Batch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function video_batchlist(Request $request){
        $validator = Validator::make($request->all(), [
            'video_id'=>'required',
            'standard_id' => 'required',
            'chapter_id' => 'required',
            'subject_id' => 'required',
             ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400); 
        try {
        $batch_list = VideoAssignToBatch::where('standard_id', $request->standard_id)
                    ->where('subject_id', $request->subject_id)
                    ->where('chapter_id', $request->chapter_id)
                    ->where('video_id', $request->video_id)
                    ->whereNull('deleted_at')
                    ->where('assign_status','1')
                    ->pluck('batch_id')
                    ->toArray();
                    return $this->response($batch_list, "Successfully display Batchlist");            
        }
        catch (Exception $e) {
            return $this->response($e, "Something want wrong.", false, 400);
        }
                
    }


}
