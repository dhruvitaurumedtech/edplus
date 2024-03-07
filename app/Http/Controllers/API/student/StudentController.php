<?php

namespace App\Http\Controllers\API\student;
use App\Http\Controllers\Controller;
use App\Models\Banner_model;
use App\Models\board;
use App\Models\Subject_sub;
use App\Models\Institute_detail;
use App\Models\Student_detail;
use App\Models\Search_history;
use Illuminate\Http\Request;
use App\Models\User;

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
            $studentDT = Student_detail::where('id',$user_id) ->get();
            $instituteids = '';
            $instuser_ids = '';
            foreach($studentDT as $value){
                $instituteids .= $value->institute_id.',';
                $instuser_ids .=$value->user_id.',';
            }
            $instituteids .= '0';
            $instuser_ids .= '0';
            if($instituteids == '0'){
                $instuser_id = '1';
            }else{
                $instuser_id = $instuser_ids;
            }
                $banners = Banner_model::where('status', 'active')
                            ->whereIn('user_id', explode(',',$instuser_id))
                            ->orWhereIn('institute_id', explode(',',$instuser_ids))
                            ->paginate($perPage);
            $banners_data = [];
            
            foreach ($banners as $value) {
                $imgpath = asset($value->banner_image);
                $banners_data[] = array(
                    'id' => $value->id,
                    'banner_image' => $imgpath,
                );
            }

            //student searched response 
            $allinstitute = Institute_detail::where('institute_name','like','%' . $search_keyword . '%')
            ->where('status','active')->paginate($perPage);
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
            ->where('students_details.student_id',$user_id)->paginate($perPage);
           
            $requested_institute = [];
            foreach ($requestnstitute as $value) {
                $requested_institute[] = array(
                    'id' => $value->id,
                    'institute_name' => $value->institute_name,
                    'address'=>$value->address,
                    'status'=>$value->status,
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
                    'institute_name' => $value->institute_name,
                    'address'=>$value->address,
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

            $institutedeta = Institute_detail::where('id',$institute_id)->select('id','institute_name','address')->get();
            $boards = board::join('board_sub','board_sub.board_id','=','board.id')
           ->where('board_sub.institute_id',$institute_id)->select('board.name')->get();

            $stdcount = Student_detail::where('institute_id',$institute_id)->count();
            $subcount = Subject_sub::where('institute_id',$institute_id)->count();
            
            
            return response()->json([
                'status' => 200,
                'message' => 'Successfully fetch data.',
                'institute_data'=>$institutedeta,
                'boards'=>$boards,
                'students'=>$stdcount,
                'subject'=>$subcount,
                'total_board'=>count($boards),
                'teacher'=>0,
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
