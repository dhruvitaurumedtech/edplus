<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner_model;
use App\Models\Base_table;
use App\Models\Institute_detail;
use App\Models\Search_history;
use App\Models\Subject_model;
use App\Models\Teacher_model;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{

    public function homescreen_teacher(Request $request)
    {
        $token = $request->header('Authorization');


        if (strpos($token, 'Bearer') === 0) {
            $token = substr($token, 7);
        }

        $validator = \Validator::make($request->all(), [
            'teacher_id' => 'required|integer',
            'per_page' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'data' => array('errors' => $errorMessages),
            ], 400);
        }

        try {

            $teacher_id = $request->teacher_id;
            $search_keyword = $request->search;
            $perPage = $request->input('per_page', 10);
            $existingUser = User::where('token', $token)->where('id', $teacher_id)->first();
            if ($existingUser) {

                $banners = Banner_model::where('status', 'active')
                    ->whereIn('user_id', explode(',', '1'))
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
                // echo "<pre>";
                // print_r($allinstitute);
                // exit;

                $search_list = [];
                foreach ($allinstitute as $value) {
                    $search_list[] = array(
                        'id' => $value->id,
                        'institute_name' => $value->institute_name,
                        'address' => $value->address,
                        'logo' => asset($value->logo),
                    );
                }

                //student search history
                $searchhistory = Search_history::where('user_id', $teacher_id)->paginate($perPage);
                $searchhistory_list = [];
                foreach ($searchhistory as $value) {
                    // Check if the title already exists in the $searchhistory_list array
                    $existingTitles = array_column($searchhistory_list, 'title');
                    if (!in_array($value->title, $existingTitles)) {

                        $searchhistory_list[] = [
                            'id' => $value->id,
                            'institute_id' => $value->institute_id,
                            'user_id' => $value->user_id,
                            'title' => $value->title,
                        ];
                    }
                }

                //requested institute
                $requestnstitute = Teacher_model::join('institute_detail', 'institute_detail.id', '=', 'teacher_detail.institute_id')
                    ->where('teacher_detail.status', '!=', '1')
                    ->where('teacher_detail.teacher_id', $teacher_id)
                    ->select('institute_detail.*', 'teacher_detail.status as sstatus', 'teacher_detail.id')->paginate($perPage);

                $requested_institute = [];
                foreach ($requestnstitute as $value) {
                    $requested_institute[] = array(
                        'id' => $value->id,
                        'institute_name' => $value->institute_name,
                        'address' => $value->address,
                        'logo' => asset($value->logo),
                        'status' => $value->sstatus,
                    );
                }

                //join with

                $joininstitute = Institute_detail::where('institute_detail.status', 'active')
                    ->join('teacher_detail', 'teacher_detail.institute_id', '=', 'institute_detail.id')
                    ->where('teacher_detail.teacher_id', $teacher_id)
                    ->where('teacher_detail.status', '1')
                    ->whereNull('teacher_detail.deleted_at')
                    ->where('institute_detail.end_academic_year', '>=', now())
                    ->whereNull('institute_detail.deleted_at')
                    ->select('institute_detail.*')
                    ->paginate($perPage);


                // echo "<pre>";
                // print_r($joininstitute);
                // exit; // ->where('end_academic_year', '>=', now())
                $join_with = [];
                foreach ($joininstitute as $value) {
                    $join_with[] = array(
                        'id' => $value->id,
                        'institute_name' => $value->institute_name . '(' . $value->unique_id . ')',
                        'address' => $value->address,
                        'logo' => asset($value->logo),
                    );
                }

                // $parentsdt = Parents::where('student_id', $user_id)->get();

                // $veryfy = [];
                // foreach ($parentsdt as $checkvery) {
                //     $veryfy[] = array('relation' => $checkvery->relation, 'verify' => $checkvery->verify);
                // }
                // if ($parentsdt->isEmpty()) {

                //     $studentparents = '0';
                // } else {
                //     $studentparents = '1';
                // }
                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch data.',
                    'data' => array(
                        'banner' => $banners_data,
                        'search_list' => $search_list,
                        'searchhistory_list' => $searchhistory_list,
                        'requested_institute' => $requested_institute,
                        'join_with' => $join_with,
                        // 'parents_detail' => $studentparents,
                        // 'parents_verification' => $veryfy
                    ),
                ], 200, [], JSON_NUMERIC_CHECK);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token.',
                    'data' => []
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'data' => array('error' => $e->getMessage()),
            ], 500);
        }
    }


    public function teacher_add_institute_request(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'teacher_id' => 'required|integer',
            'institute_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'data' => array('errors' => $errorMessages),
            ], 400);
        }

        try {
            $token = $request->header('Authorization');

            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $user_id = $request->input('user_id');
            $existingUser = User::where('token', $token)->where('id', $user_id)->first();
            if ($existingUser) {
                $instituteid = $request->institute_id;
                $getsid = Student_detail::where('student_id', $request->user_id)
                    ->where('institute_id', $instituteid)->first();
                if ($getsid) {
                } else {
                    $getuid = Institute_detail::where('id', $instituteid)->select('user_id')->first();

                    $search_add = Student_detail::create([
                        'user_id' => $getuid->user_id,
                        'institute_id' => $request->input('institute_id'),
                        'student_id' => $request->input('user_id'),
                        'status' => '0',
                    ]);
                }

                return response()->json([
                    'success' => 200,
                    'message' => 'Request added successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'data' => array('error' => $e->getMessage()),
            ], 500);
        }
    }


    public function add_teacher(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'mobile' => 'required',
            'email' => 'required',
            'qualification' => 'required',
            'employee_type' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'standard_id' => 'required',
            'stream_id' => 'required',
            'subject_id' => 'required',
            'teacher_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        try {
            $token = $request->header('Authorization');

            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $teacher_id = $request->input('teacher_id');
            $existingUser = User::where('token', $token)->where('id', $teacher_id)->first();
            if ($existingUser) {

                $subject = Subject_model::whereIn('id', explode(',', $request->subject_id))->get();
                foreach ($subject as $value) {
                    $base_table_response = Base_table::where('id', $value->base_table_id)->get()->toarray();
                    foreach ($base_table_response as $value2) {
                        Teacher_model::create([
                            'institute_id' => $request->institute_id,
                            'teacher_id' => $request->teacher_id,
                            'institute_for_id' => $value2['institute_for'],
                            'board_id' => $value2['board'],
                            'medium_id' => $value2['medium'],
                            'class_id' => $value2['institute_for_class'],
                            'standard_id' => $value2['standard'],
                            'stream_id' => $value2['stream'],
                            'subject_id' => $value['id'],
                            'status' => '1',
                        ]);
                    }
                }
                $student_details = User::where('id', $request->teacher_id)->update([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'address' => $request->address,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'employee_type' => $request->employee_type,
                    'qualification' => $request->qualification,
                ]);


                if (!$student_details) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found',
                    ], 404);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Teacher added successfully',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
