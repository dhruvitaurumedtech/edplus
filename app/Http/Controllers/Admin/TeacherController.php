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
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    use ApiTrait;
    public function homescreen_teacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
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


    public function join_with_teacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'teacher_id' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $teacher = Teacher_model::where('teacher_id', $request->teacher_id)->where('institute_id', $request->institute_id)->where('status', '0')->get()->toarray();
            if (empty($teacher)) {
                Teacher_model::create([
                    'teacher_id' => $request->teacher_id,
                    'institute_id' => $request->institute_id,
                    'status' => '0',
                ]);
            } else {
                return $this->response([], "Already Sended Request!");
            }
            return $this->response([], "Request added successfully");
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    public function add_teacher(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'mobile' => 'required',
            'email' => 'required',
            'qualification' => 'required',
            'employee_type' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'standard_id' => 'required',
            // 'stream_id' => 'required',
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
        $validator = Validator::make($request->all(), [
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
        $validator = Validator::make($request->all(), [
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
            $teacher_data = TeacherAssignBatch::join('batches', 'batches.id', '=', 'teacher_assign_batch.batch_id')
                ->Join('board', 'board.id', '=', 'batches.board_id')
                ->Join('medium', 'medium.id', '=', 'batches.medium_id')
                ->Join('standard', 'standard.id', '=', 'batches.standard_id')
                ->where('teacher_assign_batch.teacher_id', $teacher_id)
                ->where('batches.institute_id', $institute_id)
                //->whereNull('teacher_detail.deleted_at')
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
                $teacher_response[] = [
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
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'teacher_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $teacher_data = TeacherAssignBatch::join('batches', 'batches.id', '=', 'teacher_assign_batch.batch_id')
            ->Join('board', 'board.id', '=', 'batches.board_id')
            ->Join('medium', 'medium.id', '=', 'batches.medium_id')
            ->Join('standard', 'standard.id', '=', 'batches.standard_id')
            ->where('teacher_assign_batch.teacher_id', $request->teacher_id)
            ->where('batches.standard_id', $request->standard_id)
            ->select(
                'board.name as board_name',
                'standard.name as standard_name',
                'medium.name as medium_name',
                'teacher_assign_batch.batch_id',
                'batches.batch_name',
                'batches.subjects'
            )
            ->get();
            $teacher_response = [];

            foreach ($teacher_data as $value) {
                $subject_data = Subject_model::whereIn('id', explode(',', $value->subjects))->get();
                $subject_response = [];
                foreach ($subject_data as $subject_value) {
                    $subject_response[] = array(
                        'id' => $subject_value->id, 
                        'subject_name' => $subject_value->name,
                        'image'=>!empty($subject_value->image)?url($subject_value->image):'');
                }

                $teacher_response[] = [
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

        $validator = Validator::make($request->all(), [
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
    public function get_teacher_request_list(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $user = Auth::user();
            $request_list = Teacher_model::where('institute_id', $request->institute_id)
                ->where('status', '0')
                ->get();
            if (!empty($request_list)) {
                $response = $request_list->filter(function ($value) {
                    return $user_data = User::find($value->teacher_id);
                })->map(function ($value) {
                    $user_data = User::find($value->teacher_id);
                    return [
                        'teacher_id' => $user_data->id,
                        'name' => $user_data->firstname . ' ' . $user_data->lastname,
                        'photo' => $user_data->image,
                    ];
                })->toArray();
                return $this->response($response, "Fetch teacher request list.");
            } else {
                return $this->response([], "teacher not found.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
     }
     public function teacher_reject_request(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $response = Teacher_model::where('institute_id', $request->institute_id)->where('teacher_id', $request->user_id)->update(['status' => '2']);
            return $this->response([], "Successfully Reject Request.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
     }
     public function get_teacher_reject_request_list(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $teacher_id = Teacher_model::where('institute_id', $request->institute_id)
                ->where('status', '2')
                ->where('created_at', '>=', Carbon::now()->subDays(15))
                ->pluck('teacher_id');
            if (!empty($teacher_id)) {
                $response = User::whereIn('id', $teacher_id)
                    ->get(['id', 'firstname', 'lastname', 'image'])
                    ->map(function ($user) {
                        return [
                            'teacher_id' => $user->id,
                            'name' => $user->firstname . ' ' . $user->lastname,
                            'photo' => $user->image,
                        ];
                    })->toArray();
                return $this->response($response, "Fetch teacher Reject list.");
            }
            return $this->response([], "Successfully Reject Request.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
     }
     public function fetch_teacher_detail(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'institute_id' => 'required|integer',
        ]);

        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $user_list = Teacher_model::join('users', 'users.id', '=', 'teacher_detail.teacher_id')
                ->join('board', 'board.id', '=', 'teacher_detail.board_id')
                ->join('medium', 'medium.id', '=', 'teacher_detail.medium_id')
                ->join('standard', 'standard.id', '=', 'teacher_detail.standard_id')
                ->leftjoin('stream', 'stream.id', '=', 'teacher_detail.stream_id')
                ->where('teacher_detail.teacher_id', $request->user_id)
                ->where('teacher_detail.institute_id', $request->institute_id)
                ->select(
                    'teacher_detail.*',
                    'users.firstname',
                    'users.lastname',
                    'users.dob',
                    'users.address',
                    'users.email',
                    'users.mobile',
                    'board.name as board',
                    'medium.name as medium',
                    'standard.name as standard',
                    'stream.name as stream'
                )
                ->first();
            if ($user_list) {
                $subjids = explode(',', $user_list->subject_id);
                $subjcts = Subject_model::whereIN('id', $subjids)->get();
                $subjectslist = [];
                foreach ($subjcts as $subDT) {
                    $subjectslist[] = array(
                        'id' => $subDT->id,
                        'name' => $subDT->name,
                        'image' => asset($subDT->image)
                    );
                }
                $response_data = [
                    'user_id' => $user_list->teacher_id,
                    'institute_id' => $user_list->institute_id,
                    'first_name' => $user_list->firstname,
                    'last_name' => $user_list->lastname,
                    'date_of_birth' => date('d-m-Y', strtotime($user_list->dob)),
                    'address' => $user_list->address,
                    'email' => $user_list->email,
                    'mobile_no' => $user_list->mobile,
                    //'institute_for' => $institute_for_list,
                    'board' => $user_list->board,
                    'board_id' => $user_list->board_id,
                    'medium' => $user_list->medium,
                    'medium_id' => $user_list->medium_id,
                    //'class_list' => $class_list,
                    'standard' => $user_list->standard,
                    'standard_id' => $user_list->standard_id,
                    'stream' => $user_list->stream,
                    'stream_id' => $user_list->stream_id,
                    'subject_list' => $subjectslist,
                ];
                return $this->response($response_data, "Successfully Fetch data.");
            } else {
                return $this->response([], "Successfully Fetch data.");
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
     }
}
