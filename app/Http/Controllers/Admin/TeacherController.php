<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\announcements_model;
use App\Models\Banner_model;
use App\Models\Base_table;
use App\Models\Batch_assign_teacher_model;
use App\Models\Batches_model;
use App\Models\board;
use App\Models\Common_announcement;
use App\Models\Institute_detail;
use App\Models\Search_history;
use App\Models\Student_detail;
use App\Models\Subject_model;
use App\Models\Subject_sub;
use App\Models\Teacher_model;
use App\Models\TeacherAssignBatch;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use Dotenv\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    use ApiTrait;
    public function homescreen_teacher(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'teacher_id' => 'required|integer',
            'per_page' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $teacher_id = $request->teacher_id;
            $search_keyword = $request->search;
            $perPage = $request->input('per_page', 10);
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
            $announcement = Common_announcement::whereRaw("FIND_IN_SET($request->teacher_id, teacher_id)")
                ->select('*')->get()->toarray();
            $announcement_response = [];
            foreach ($announcement as $value) {
                $announcement_response[] = [
                    'date' => !empty($value['created_at']) ? $value['created_at'] : '',
                    'title' => !empty($value['title']) ? $value['title'] : '',
                    'announcement' => !empty($value['announcement']) ? $value['announcement'] : '',
                ];
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
            $final_repsonse = [
                'banner' => $banners_data,
                'search_list' => $search_list,
                'searchhistory_list' => $searchhistory_list,
                'requested_institute' => $requested_institute,
                'join_with' => $join_with,
                'announcement' => $announcement_response

            ];

            return $this->response($final_repsonse, "Successfully fetch data.");
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    // public function teacher_add_institute_request(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'teacher_id' => 'required|integer',
    //         'institute_id' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'data' => array('errors' => $errorMessages),
    //         ], 400);
    //     }

    //     try {
    //         $token = $request->header('Authorization');

    //         if (strpos($token, 'Bearer ') === 0) {
    //             $token = substr($token, 7);
    //         }

    //         $user_id = $request->input('user_id');
    //         $existingUser = User::where('token', $token)->where('id', $user_id)->first();
    //         if ($existingUser) {
    //             $instituteid = $request->institute_id;
    //             $getsid = Student_detail::where('student_id', $request->user_id)
    //                 ->where('institute_id', $instituteid)->first();
    //             if ($getsid) {
    //             } else {
    //                 $getuid = Institute_detail::where('id', $instituteid)->select('user_id')->first();

    //                 $search_add = Student_detail::create([
    //                     'user_id' => $getuid->user_id,
    //                     'institute_id' => $request->input('institute_id'),
    //                     'student_id' => $request->input('user_id'),
    //                     'status' => '0',
    //                 ]);
    //             }

    //             return response()->json([
    //                 'success' => 200,
    //                 'message' => 'Request added successfully',
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid token.',
    //             ], 400);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'Something went wrong',
    //             'data' => array('error' => $e->getMessage()),
    //         ], 500);
    //     }
    // }


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
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $subject = Subject_model::whereIn('id', explode(',', $request->subject_id))->get();

            foreach ($subject as $value) {
                $batch_list = Batches_model::whereRaw("FIND_IN_SET($value->id, subjects)")
                    ->select('*')->get()->toarray();

                foreach ($batch_list as $values_batch) {
                    Batch_assign_teacher_model::create([
                        'teacher_id' => $request->teacher_id,
                        'batch_id' => $values_batch['id'],
                    ]);
                }
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
            User::where('id', $request->teacher_id)->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'address' => $request->address,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'employee_type' => $request->employee_type,
                'qualification' => $request->qualification,
            ]);
            return $this->response([], "Teacher added successfully");
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function institute_detail(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'teacher_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $institute_id = $request->institute_id;
            $boards = [];
            $institutedeta = Institute_detail::where('id', $institute_id)
                ->select('id', 'institute_name', 'address', 'about_us')->first();
            $boards = board::join('board_sub', 'board_sub.board_id', '=', 'board.id')
                ->where('board_sub.institute_id', $institute_id)->select('board.name')->get();
            $stdcount = Teacher_model::where('institute_id', $institute_id)->count();
            $subcount = Subject_sub::where('institute_id', $institute_id)->count();
            $institutedetaa = array(
                'id' => $institutedeta->id,
                'institute_name' => $institutedeta->institute_name,
                'address' => $institutedeta->address,
                'about_us' => $institutedeta->about_us,
                'logo' => asset($institutedeta->logo),
                'boards' => $boards,
                'students' => $stdcount,
                'subject' => $subcount,
                'total_board' => count($boards),
                'teacher' => 0
            );
            return $this->response($institutedetaa, "Successfully fetch data.");
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function teacher_added_detail(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'teacher_id' => 'required',
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $teacher_id = $request->teacher_id;
            $institute_id = $request->institute_id;
            //banner
            $bannerss = Banner_model::where('status', 'active')
                ->Where('institute_id', $institute_id)
                ->Where('user_id', $teacher_id)
                ->paginate(10);

            if ($bannerss->isEmpty()) {
                $banners = Banner_model::where('status', 'active')
                    ->Where('user_id', '1')
                    ->paginate(10);
            } else {
                $banners = $bannerss;
            }
            $banners_data = [];
            foreach ($banners as $value) {
                $imgpath = asset($value->banner_image);
                $banners_data[] = array(
                    'id' => $value->id,
                    'banner_image' => $imgpath,
                );
            }
            $todays_lecture = [];
            $subjects = [];
            $result = [];
            $announcement = [];
            $examlist = [];

            $user_id = Auth::id();
            $today = date('Y-m-d');
            $todayslect = Timetable::join('subject', 'subject.id', '=', 'time_table.subject_id')
                ->join('users', 'users.id', '=', 'time_table.teacher_id')
                ->join('lecture_type', 'lecture_type.id', '=', 'time_table.lecture_type')
                ->join('batches', 'batches.id', '=', 'time_table.batch_id')
                ->join('standard', 'standard.id', '=', 'batches.standard_id')
                ->where('time_table.teacher_id', $user_id)
                ->where('time_table.lecture_date', $today)
                ->select(
                    'subject.name as subject',
                    'standard.name as standard',
                    'lecture_type.name as lecture_type_name',
                    'time_table.start_time',
                    'time_table.end_time',
                    'time_table.lecture_date'
                )
                ->paginate(2);

            foreach ($todayslect as $todayslecDT) {
                $todays_lecture[] = array(
                    'subject' => $todayslecDT->subject,
                    'standard' => $todayslecDT->standard,
                    'lecture_date' => $todayslecDT->lecture_date,
                    'lecture_type' => $todayslecDT->lecture_type_name,
                    'start_time' => $todayslecDT->start_time,
                    'end_time' => $todayslecDT->end_time,
                );
            }

            $announcQY = Common_announcement::where('institute_id', $institute_id)->Where('teacher_id', $teacher_id)->get();
            // ->whereRaw("FIND_IN_SET('4', role_type)")

            foreach ($announcQY as $announcDT) {
                $announcement[] = array(
                    'title' => $announcDT->title,
                    'desc' => $announcDT->announcement,
                    'time' => $announcDT->created_at
                );
            }
            $teacher_data = Teacher_model::leftJoin('board', 'board.id', '=', 'teacher_detail.board_id')
                ->leftJoin('medium', 'medium.id', '=', 'teacher_detail.medium_id')
                ->leftJoin('standard', 'standard.id', '=', 'teacher_detail.standard_id')
                ->leftJoin('teacher_assign_batch', 'teacher_assign_batch.teacher_id', '=', 'teacher_detail.teacher_id')
                ->Rightjoin('batches', 'batches.id', '=', 'teacher_assign_batch.batch_id')

                ->where('teacher_detail.teacher_id', $teacher_id)
                ->where('teacher_detail.institute_id', $institute_id)
                ->whereNull('teacher_detail.deleted_at')
                ->select(
                    'board.name as board_name',
                    'standard.name as standard_name',
                    'medium.name as medium_name',
                    'teacher_assign_batch.batch_id',
                    'batches.batch_name'
                )
                ->get()
                ->toArray();
            $teacher_response = [];
            foreach ($teacher_data as $value) {
                $teacher_response = [
                    'board' => $value['board_name'],
                    'standard' => $value['standard_name'],
                    'medium' => $value['medium_name'],
                    'batch' => $value['batch_name']

                ];
            }
            $studentdata = array(
                'banners_data' => $banners_data,
                'todays_lecture' => $todays_lecture,
                'announcement' => $announcement,
                'class_detail' => $teacher_response,
            );
            return $this->response($studentdata, "Successfully fetch data.");
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function second_homescreen(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'teacher_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $teacher_Data = Teacher_model::join('board', 'board.id', '=', 'teacher_detail.board_id')
                ->join('medium', 'medium.id', '=', 'teacher_detail.medium_id')
                ->join('standard', 'standard.id', '=', 'teacher_detail.standard_id')
                ->join('teacher_assign_batch', 'teacher_assign_batch.teacher_id', '=', 'teacher_detail.teacher_id')
                ->join('batches', 'batches.id', '=', 'teacher_assign_batch.batch_id')
                ->where('teacher_detail.teacher_id', $request->teacher_id)
                ->select('teacher_detail.*', 'board.name as board_name', 'medium.name as medium_name', 'standard.name as standard_name', 'batches.batch_name')
                ->get();
            $teacher_response = [];

            foreach ($teacher_Data as $value) {
                $subject_data = Subject_model::whereIn('id', explode(',', $value->subject_id))->get()->toarray();
                $subject_response = [];

                foreach ($subject_data as $subject_value) {
                    $subject_response[] = [
                        'subject_name' => $subject_value['name']
                    ];
                }
                // exit;
                $teacher_response = [
                    'teacher_id' => $value->teacher_id,
                    'board' => $value->board_name,
                    'medium' => $value->medium_name,
                    'standard' => $value->standard_name,
                    'batch' => $value->batch_name,
                    'subject_list' => $subject_response,
                ];
            }
            return $this->response($teacher_response, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    //timetable list
    public function timetable_list_teache(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'date' => 'required',
            'batch_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {

            $teacher_id = Auth::id();
            $lectures = [];
            $todaysletech = Timetable::join('subject', 'subject.id', '=', 'time_table.subject_id')
                ->join('users', 'users.id', '=', 'time_table.teacher_id')
                ->join('lecture_type', 'lecture_type.id', '=', 'time_table.lecture_type')
                ->join('batches', 'batches.id', '=', 'time_table.batch_id')
                ->join('standard', 'standard.id', '=', 'batches.standard_id')
                ->where('time_table.batch_id', $request->batch_id)
                ->where('time_table.lecture_date', $request->date)
                ->where('time_table.teacher_id', $teacher_id)
                ->select(
                    'subject.name as subject',
                    'standard.name as standard',
                    'lecture_type.name as lecture_type_name',
                    'time_table.start_time',
                    'time_table.end_time',
                    'time_table.lecture_date'
                )
                ->get();

            foreach ($todaysletech as $todaysDT) {
                $lectures[] = array(
                    'subject' => $todaysDT->subject,
                    'standard' => $todaysDT->standard,
                    'lecture_date' => $todaysDT->lecture_date,
                    'lecture_type' => $todaysDT->lecture_type_name,
                    'start_time' => $todaysDT->start_time,
                    'end_time' => $todaysDT->end_time,
                );
            }

            return $this->response($lectures, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }
}
