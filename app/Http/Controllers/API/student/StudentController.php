<?php

namespace App\Http\Controllers\API\student;
use App\Http\Controllers\Controller;
use App\Models\Banner_model;
use App\Models\board;
use App\Models\Chapter;
use App\Models\Dobusinesswith_Model;
use App\Models\Subject_sub;
use App\Models\Institute_detail;
use App\Models\Student_detail;
use App\Models\Search_history;
use App\Models\Subject_model;
use App\Models\Topic_model;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VideoCategory;
use Illuminate\Broadcasting\Channel;

class StudentController extends Controller
{
    public function homescreen_student(Request $request){
        $token = $request->header('Authorization');
        
        
        if (strpos($token, 'Bearer') === 0) {
            $token = substr($token, 7);
        }
        
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'per_page'=>'required|integer',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try{
        
        $user_id = $request->user_id;
        $search_keyword = $request->search;
        $perPage = $request->input('per_page', 10);
        $existingUser = User::where('token', $token)->first();
        if ($existingUser) {

            //banner
            
            $banners = Banner_model::where('status', 'active')
            ->whereIn('user_id', explode(',','1'))
            ->paginate($perPage);
            $banners_data = [];
            
            foreach ($banners as $value) {
                $imgpath = asset($value->banner_image);
                $banners_data[] = array(
                    'id' => $value->id,
                    'banner_image' => $imgpath,
                );
            }
            $perPage = 10;
            //student searched response 
            $allinstitute = Institute_detail::where('status', 'active')
            ->where(function ($query) use ($search_keyword) {
                $query->where('unique_id', 'like', '%' . $search_keyword . '%')
                    ->orWhere('institute_name', 'like', '%' . $search_keyword . '%');
            })->paginate($perPage);
            
            $search_list = [];
            foreach ($allinstitute as $value) {
                $search_list[] = array(
                    'id' => $value->id, 
                    'institute_name' => $value->institute_name,
                    'address'=>$value->address,
                );
            }

            //student search history
            $searchhistory = Search_history::where('user_id',$user_id)->paginate($perPage);
            $searchhistory_list = [];
            foreach ($searchhistory as $value) {
                $searchhistory_list[] = array(
                    'id' => $value->id,
                    'user_id' => $value->user_id,
                    'title'=>$value->title,
                );
            }
            
            //requested institute
            $requestnstitute =Student_detail::join('institute_detail','institute_detail.id','=','students_details.institute_id')->
            where('students_details.status','!=','approved')
            ->where('students_details.student_id',$user_id)
            ->select('institute_detail.*','students_details.status as sstatus','students_details.student_id')->paginate($perPage);
           
            $requested_institute = [];
            foreach ($requestnstitute as $value) {
                $requested_institute[] = array(
                    'id' => $value->id,
                    'institute_name' => $value->institute_name,
                    'address'=>$value->address,
                    'logo'=>asset($value->logo),
                    'status'=>$value->sstatus,
                );
            }

            //join with
            $joininstitute =Institute_detail::where('status','active') ->whereIn('id', function($query) use ($user_id) {
                $query->select('institute_id')
              ->where('student_id', $user_id)
              ->where('status','=', 'approved')
              ->from('students_details');
            })->paginate($perPage);
            $join_with = [];
            foreach ($joininstitute as $value) {
                $join_with[] = array(
                    'id' => $value->id,
                    'institute_name' => $value->institute_name.'('.$value->unique_id.')',
                    'address'=>$value->address,
                    'logo'=>asset($value->logo),
                );
            }

            return response()->json([
                'status' => 200,
                'message' => 'Successfully fetch data.',
                'banner' => $banners_data,
                'search_list' => $search_list,
                'searchhistory_list'=>$searchhistory_list,
                'requested_institute'=>$requested_institute,
                'join_with' => $join_with,
            ], 200, [], JSON_NUMERIC_CHECK);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }
        }catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function student_searchhistory_add(Request $request){

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try {
        $search_add = Search_history::create([
            'user_id' => $request->input('user_id'),
            'title' => $request->input('title'),
        ]);

        
        return response()->json([
            'success' => 200,
            'message' => 'Serach History Added',
        ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function student_add_institute_request(Request $request){
        
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'institute_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }
        
        try {
        $instituteid = $request->institute_id;
        $getsid = Student_detail::where('student_id',$request->user_id)
        ->where('institute_id',$instituteid)->first();
        if($getsid){
            
        }else{
            $getuid = Institute_detail::where('id',$instituteid)->select('user_id')->first();
        
        $search_add = Student_detail::create([
            'user_id' => $getuid->user_id,
            'institute_id' => $request->input('institute_id'),
            'student_id' => $request->input('user_id'),
            'status' => 'pending',
        ]);

        }
        
        return response()->json([
            'success' => 200,
            'message' => 'Request added successfully',
        ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //institute detail
    public function institute_detail(Request $request){

        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try{
            $institute_id = $request->institute_id;
            $institute_data = [];
            $boards = [];

            $institutedeta = Institute_detail::where('id',$institute_id)->select('id','institute_name','address')->first();
            $boards = board::join('board_sub','board_sub.board_id','=','board.id')
           ->where('board_sub.institute_id',$institute_id)->select('board.name')->get();

            $stdcount = Student_detail::where('institute_id',$institute_id)->count();
            $subcount = Subject_sub::where('institute_id',$institute_id)->count();
            
            $institutedetaa = array('id'=>$institutedeta->id,
            'institute_name'=>$institutedeta->institute_name,
            'address'=>$institutedeta->address,
            'logo'=>asset($institutedeta->logo),
            'boards'=>$boards,
            'students'=>$stdcount,
            'subject'=>$subcount,
            'total_board'=>count($boards),
            'teacher'=>0);

            
            return response()->json([
                'status' => 200,
                'message' => 'Successfully fetch data.',
                'institute_data'=>$institutedetaa,
            ], 200, [], JSON_NUMERIC_CHECK);
        }catch(\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
        
        
    }

    //student added detail
    public function student_added_detail(Request $request){
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try{
            $user_id = $request->user_id;
            $institute_id = $request->institute_id;
            
            
            //banner
            
                $banners = Banner_model::where('status', 'active')
                ->Where('institute_id', $institute_id)
                ->paginate(10);
            $banners_data = [];
            
            foreach ($banners as $value) {
                $imgpath = asset($value->banner_image);
                $banners_data[] = array(
                    'id' => $value->id,
                    'banner_image' => $imgpath,
                );
            }
            
            $todays_lecture = [];
            $announcement = [];
            $subjects = [];
            
            $subdta = Student_detail::where('student_id',$user_id)
            ->where('institute_id',$institute_id)->select('students_details.*')->first();
            
            $subjecqy = Subject_model::whereIN('id',explode(",",$subdta->subject_id))->get();
            foreach($subjecqy as $subjcdt){
                $subjects[] = array('id'=>$subjcdt->id,'name'=>$subjcdt->name);
            }
            $studentdata = array(
            'banners_data'=> $banners_data,
            'todays_lecture'=>"",
            'announcement'=>"",
            'subjects'=>$subjects,
            'result'=>"");

            
            return response()->json([
                'status' => 200,
                'message' => 'Successfully fetch data.',
                'data'=>$studentdata,
            ], 200, [], JSON_NUMERIC_CHECK);
        }catch(\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
        
        

    }

    //subject wise chapert list
    public function subject_chapers(Request $request){
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'subject_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try{
            $user_id = $request->user_id;
            $subject_id = $request->subject_id;
            
            $chapers = [];
            $cptquy = Chapter::where('subject_id',$subject_id)->get();
            foreach($cptquy as $chval){
            $chapers[] = array( "id"=>$chval->id,
            "subject_id"=>$chval->subject_id,
            "chapter_name"=>$chval->chapter_name,
            "chapter_no"=>$chval->chapter_no,
            "chapter_image"=>asset($chval->chapter_image));
            }
            
            return response()->json([
                'status' => 200,
                'message' => 'Successfully fetch data.',
                'chapter_data'=>$chapers,
            ], 200, [], JSON_NUMERIC_CHECK);
        }catch(\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
        
        

    }

    //topic videos
    public function topic_videos(Request $request){
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'subject_id' => 'required',
            'chapter_id' => 'required',
            'institute_id' => 'required',
            //'video_cayegory'=>'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try{
            $user_id = $request->user_id;
            $subject_id = $request->subject_id;
            $chapter_id = $request->chapter_id;
            $institute_id = $request->institute_id;
            //$video_cayegory = $request->video_cayegory;

            $topics = [];
            $category = []; 
            $catgry = Dobusinesswith_Model::join('topic','topic.video_category_id','=','do_business_with.id')
            ->join('video_categories','video_categories.id','=','do_business_with.category_id')->
            select('do_business_with.*','video_categories.id as vid','video_categories.name as vname')->get();
            foreach($catgry as $catvd){
                $topicqry = Topic_model::
                join('subject','subject.id','=','topic.subject_id')
                ->join('chapters','chapters.id','=','topic.chapter_id')
                ->where('topic.subject_id',$subject_id)
                ->where('topic.chapter_id',$chapter_id)
                ->where('topic.institute_id',$institute_id)
                ->where('topic.video_category_id',$catvd->id)
                ->select('topic.*','subject.name as sname','chapters.chapter_name as chname')->get();
                foreach($topicqry as $topval){
                    $topics[] = array( "id"=>$topval->id,
                    "topic_no"=>$topval->topic_no,
                    "topic_name"=>$topval->topic_name,
                    "topic_video"=>asset($topval->topic_video),
                    "subject_id"=>$topval->subject_id,
                    "subject_name"=>$topval->sname,
                    "chapter_id"=>$topval->chapter_id,
                    "chapter_name"=>$topval->chname);
                    }
                $category[] = array('id'=>$catvd->id,'category_name'=>$catvd->name,'parent_category_id'=>$catvd->vid,'parent_category_name'=>$catvd->vname,'topics'=>$topics);
            }
            
            
            
            
            return response()->json([
                'status' => 200,
                'message' => 'Successfully fetch data.',
                'topic_data'=>$category,
            ], 200, [], JSON_NUMERIC_CHECK);
        }catch(\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
        
        

    }
}
