<?php

namespace App\Http\Controllers\API\admin;

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
use App\Models\Standard_model;
use App\Models\Student_detail;
use App\Models\Subject_model;
use App\Models\Subject_sub;
use App\Models\Teacher_model;
use App\Models\TeacherAssignBatch;
use App\Models\Timetable;
use App\Models\User;
use App\Models\Users_sub_emergency;
use App\Models\Users_sub_experience;
use App\Models\Users_sub_model;
use App\Models\Users_sub_qualification;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
                // echo "<pre>";print_r($allinstitute);exit;
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
            'country_code' =>'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $subject = Subject_model::whereIn('id', explode(',', $request->subject_id))->get();

            foreach ($subject as $value) {
                // $batch_list = Batches_model::whereRaw("FIND_IN_SET($value->id, subjects)")
                //     ->select('*')->get()->toarray();

                // foreach ($batch_list as $values_batch) {

                //     Batch_assign_teacher_model::create([
                //         'teacher_id' => $request->teacher_id,
                //         'batch_id' => $values_batch['id'],
                //     ]);
                // }
                $base_table_response = Base_table::where('id', $value->base_table_id)->get()->toarray();
                foreach ($base_table_response as $value2) {
                    $subject=Subject_model::where('base_table_id',$value2['id'])->get();
                    $subject_implode=[];
                    foreach($subject as $value){
                        $subject_implode[]=$value->id;  
                    }
                    $subject_id = implode(',',$subject_implode);
                    // echo "<pre>";print_r($subject_name);exit;
                    Teacher_model::create([
                        'institute_id' => $request->institute_id,
                        'teacher_id' => $request->teacher_id,
                        'institute_for_id' => $value2['institute_for'],
                        'board_id' => $value2['board'],
                        'medium_id' => $value2['medium'],
                        'class_id' => $value2['institute_for_class'],
                        'standard_id' => $value2['standard'],
                        'stream_id' => $value2['stream'],
                        'subject_id' => $subject_id,
                        'status' => '0',
                    ]);
                }
            }
            User::where('id', $request->teacher_id)->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'address' => $request->address,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'mobile' => $request->mobile,
                'employee_type' => $request->employee_type,
                'qualification' => $request->qualification,
            ]);
            $serverKey = env('SERVER_KEY');

            $url = "https://fcm.googleapis.com/fcm/send";
            $inst_owner_id = Institute_detail::where('id', $request->institute_id)->first();
            $users = User::where('id', $inst_owner_id->user_id)->pluck('device_key');

            $notificationTitle = "Teacher Join Request";
            $notificationBody = $request->firstname . " Requestd To Join Your Institute";

            $data = [
                'registration_ids' => $users,
                'notification' => [
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // Adjust this if needed
                ],
            ];

            if ($users->isNotEmpty()) {
                $json = json_encode($data);

                $headers = [
                    'Content-Type: application/json',
                    'Authorization: key=' . $serverKey
                ];

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $json,
                    CURLOPT_HTTPHEADER => $headers,
                ]);

                $result = curl_exec($ch);

                if ($result === FALSE) {
                }

                curl_close($ch);
            }
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

            $announcQY = announcements_model::where('institute_id', $institute_id)->Where('role_type', 4)->get();
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
                    'standard.id as standard_id',
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
                    'standard_id' => $value['standard_id'],
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
                        'image' => !empty($subject_value->image) ? url($subject_value->image) : ''
                    );
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
    public function get_teacher_request_list(Request $request)
    {
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
                    // print_r($user_data->image);exit;
                    return [
                        // 'id'=>$value->id,
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
    public function teacher_reject_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'teacher_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $response = Teacher_model::where('institute_id', $request->institute_id)->where('teacher_id', $request->teacher_id)->update(['status' => '2']);
            $serverKey = env('SERVER_KEY');


            $url = "https://fcm.googleapis.com/fcm/send";
            $users = User::where('id', $request->teacher_id)->pluck('device_key');

            $notificationTitle = "Your Request Rejected";
            $notificationBody = "Your Teacher Request Rejected successfully!!";

            $data = [
                'registration_ids' => $users,
                'notification' => [
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
            ];

            if ($users->isNotEmpty()) {
                $json = json_encode($data);

                $headers = [
                    'Content-Type: application/json',
                    'Authorization: key=' . $serverKey
                ];

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $json,
                    CURLOPT_HTTPHEADER => $headers,
                ]);

                $result = curl_exec($ch);

                if ($result === FALSE) {
                }

                curl_close($ch);
            }
            return $this->response([], "Successfully Reject Request.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
    public function get_teacher_reject_request_list(Request $request)
    {
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
    public function fetch_teacher_detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|integer',
            'institute_id' => 'required|integer',
        ]);

        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $user_list = Teacher_model::join('users', 'users.id', '=', 'teacher_detail.teacher_id')
                ->where('teacher_detail.teacher_id', $request->teacher_id)
                ->where('teacher_detail.institute_id', $request->institute_id)
                ->where('teacher_detail.status', '0')
                ->select(
                    'teacher_detail.*',
                    'users.*',
                    'users.qualification',
                    'users.lastname',
                    'users.dob',
                    'users.address',
                    'users.email',
                    'users.mobile',
                    'users.employee_type'
                    
                )
                ->first();
            if ($user_list) {
                $teacher_detail=Teacher_model::join('users', 'users.id', '=', 'teacher_detail.teacher_id')
                ->join('board', 'board.id', '=', 'teacher_detail.board_id')
                ->join('medium', 'medium.id', '=', 'teacher_detail.medium_id')
                ->join('standard', 'standard.id', '=', 'teacher_detail.standard_id')
                ->leftjoin('stream', 'stream.id', '=', 'teacher_detail.stream_id')
                ->where('teacher_detail.teacher_id', $request->teacher_id)
                ->where('teacher_detail.institute_id', $request->institute_id)
                ->where('teacher_detail.status', '0')
                ->select('teacher_detail.*',
                         'users.qualification',
                         'board.name as board',
                         'medium.name as medium',
                         'standard.name as standard',
                         'stream.name as stream')
               ->get();
             
               $response_data_two= [];
                foreach($teacher_detail as $values){
                    $subjids = explode(',', $values->subject_id);
                    $subjcts = Subject_model::whereIN('id', $subjids)->get();
                    $subjectslist = [];
                    foreach ($subjcts as $subDT) {
                        $subjectslist[] = array(
                            'id' => $subDT->id,
                            'name' => $subDT->name,
                            'image' => asset($subDT->image)
                        );
                    }
                    $response_data_two[]=[ 'board' => $values->board,
                            'board_id' => $values->board_id,
                            'medium' => $values->medium,
                            'medium_id' => $values->medium_id,
                            'standard' => $values->standard,
                            'standard_id' => $values->standard_id,
                            'stream' => $values->stream,
                            'stream_id' => $values->stream_id,
                            'subject'=>$subjectslist ];
                 }
                $response_data = [
                    'id' => $user_list->id,
                    'teacher_id' => $user_list->teacher_id,
                    'institute_id' => $user_list->institute_id,
                    'first_name' => $user_list->firstname,
                    'last_name' => $user_list->lastname,
                    'date_of_birth' => date('d-m-Y', strtotime($user_list->dob)),
                    'address' => $user_list->address,
                    'email' => $user_list->email,
                    'employee_type'=> $user_list->employee_type,
                    'qualification' => $user_list->qualification,
                    'country_code' => $user_list->country_code,
                    'mobile_no' => $user_list->mobile,
                    'education'=>$response_data_two,
                ];
                return $this->response($response_data, "Successfully Fetch data.");
            } else {
                return $this->response([], "Successfully Fetch data.");
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
    public function edit_profile(Request $request)
    {
        $teacher_id = $request->teacher_id;
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($teacher_id),
            ],
            'phone_no' => 'required|string|max:255',
            'dob' => 'required',
            'address' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'area' => 'required|string|max:255',
            'about_us' => 'nullable|string',
            'qualification' => 'required|string|max:255',
            'institute_name' => 'required|string|max:255',
            'startdate' => 'required',
            'enddate' => 'nullable',
            'name' => 'required|string|max:255',
            'relation_with' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:255',
            'country_code' =>'required',
        ]);

        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);


        $user = User::findOrFail($teacher_id);

        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->email = $request->input('email');
        $user->country_code = $request->input('country_code');
        $user->dob = date('Y-m-d', strtotime($request['dob']));
        $user->address = $request['address'];
        $user->pincode = $request['pincode'];
        $user->area = $request['area'];
        
        $user->save();
        try {
            $userSub = Users_sub_model::where('user_id', $teacher_id)->first();
            if (!empty($userSub)) {

                $userSub->update([
                    'phone_no' => $request['phone_no'],
                    'about_us' => $request['about_us'],

                ]);
            } else {


                Users_sub_model::create([
                    'user_id' => $teacher_id,
                    'phone_no' => $request['phone_no'],
                    'about_us' => $request['about_us'],
                ]);
            }
            $userSub2 = Users_sub_qualification::where('user_id', $teacher_id)->first();
            if (!empty($userSub2)) {
                $delete_qualification = Users_sub_qualification::where('user_id', $request->teacher_id);
                $delete_qualification->delete();
                $qualification = explode(',', $request['qualification']);
                for ($i = 0; $i < count($qualification); $i++) {
                    Users_sub_qualification::create([
                        'user_id' => $teacher_id,
                        'qualification' => $qualification[$i],
                    ]);
                }
            } else {
                $qualification = explode(',', $request['qualification']);
                for ($i = 0; $i < count($qualification); $i++) {
                    Users_sub_qualification::create([
                        'user_id' => $teacher_id,
                        'qualification' => $qualification[$i],
                    ]);
                }
            }
            $userSub2 = Users_sub_experience::where('user_id', $teacher_id)->first();
            if (!empty($userSub2)) {
                $delete_experience = Users_sub_experience::where('user_id', $request->teacher_id);
                $delete_experience->delete();
                $experience = explode(',', $request['institute_name']);
                $startdate = explode(',', $request['startdate']);
                $enddate = explode(',', $request['enddate']);
                for ($i = 0; $i < count($experience); $i++) {
                    $startdates = date('Y-m-d', strtotime($startdate[$i]));
                    $enddates = date('Y-m-d', strtotime($enddate[$i]));
                    Users_sub_experience::create([
                        'user_id' => $teacher_id,
                        'institute_name' => $experience[$i],
                        'startdate' => $startdates,
                        'enddate' => $enddates,
                    ]);
                }
            } else {
                $experience = explode(',', $request['institute_name']);
                $startdate = explode(',', $request['startdate']);
                $enddate = explode(',', $request['enddate']);
                for ($i = 0; $i < count($experience); $i++) {
                    $startdates = date('Y-m-d', strtotime($startdate[$i]));
                    $enddates = date('Y-m-d', strtotime($enddate[$i]));
                    Users_sub_experience::create([
                        'user_id' => $teacher_id,
                        'institute_name' => $experience[$i],
                        'startdate' => $startdates,
                        'enddate' => $enddates,
                    ]);
                }
            }
            $userSub2 = Users_sub_emergency::where('user_id', $teacher_id)->first();
            if (!empty($userSub2)) {
                $delete_qualification = Users_sub_emergency::where('user_id', $request->teacher_id);
                $delete_qualification->delete();
                $name = explode(',', $request['name']);
                $relation_with = explode(',', $request['relation_with']);
                $mobile_no = explode(',', $request['mobile_no']);
                for ($i = 0; $i < count($qualification); $i++) {
                    Users_sub_emergency::create([
                        'user_id' => $teacher_id,
                        'name' => $name[$i],
                        'relation_with' => $relation_with[$i],
                        'mobile_no' => $mobile_no[$i]
                    ]);
                }
            } else {
                $name = explode(',', $request['name']);
                $relation_with = explode(',', $request['relation_with']);
                $mobile_no = explode(',', $request['mobile_no']);
                for ($i = 0; $i < count($qualification); $i++) {
                    Users_sub_emergency::create([
                        'user_id' => $teacher_id,
                        'name' => $name[$i],
                        'relation_with' => $relation_with[$i],
                        'mobile_no' => $mobile_no[$i]
                    ]);
                }
            }
            return $this->response([], "Successfully Update data.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
    public function teacher_profile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            //'teacher_id' => 'required|integer',
        ]);

        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);

        try {
            if($request->teacher_id){
                $teacher_id = $request->teacher_id;
            }else{
                $teacher_id = Auth::id();
            }
            
            $userdetl = user::where('id', $teacher_id)->first();

            if ($userdetl->image) {
                $img = $userdetl->image;
            } else {
                $img = asset('no-image.PNG');
            }

            $standardids = Teacher_model::where('teacher_id', $teacher_id)
                ->whereNull('deleted_at')->pluck('standard_id');

            $standards = Standard_model::whereIN('id', $standardids)->get();
            $stds = [];
            foreach ($standards as $stddata) {
                $stds[] = ['id' => $stddata->id, 'name' => $stddata->name];
            }

            $instrdids = Teacher_model::where('teacher_id', $teacher_id)
                ->whereNull('deleted_at')->pluck('institute_id');

            $institds = institute_detail::whereIN('id', $instrdids)->get();
            $workwith = [];
            foreach ($institds as $instdata) {
                $workwith[] = ['id' => $instdata->id, 'institute_name' => $instdata->institute_name];
            }

          $educationds = Users_sub_qualification::where('user_id',$teacher_id)->get();
          $education = [];
          foreach($educationds as $edudata){
            $education[] = ['id'=>$edudata->id,'qualification'=>$edudata->qualification];
          }

          $experiences = Users_sub_experience::where('user_id',$teacher_id)->get();
          $experience = [];
          foreach($experiences as $expdata){
            $experience[] = ['id'=>$expdata->id,
            'institute_name'=>$expdata->institute_name,
            'startdate'=>$expdata->startdate,
            'enddate'=>$expdata->enddate];
          }

          $emergency = Users_sub_emergency::where('user_id',$teacher_id)->get();
          $emergency_contacts = [];
          foreach($emergency as $emergencydata){
            $emergency_contacts[] = ['id'=>$emergencydata->id,
            'name'=>$emergencydata->name,
            'relation_with'=>$emergencydata->relation_with,
            'mobile_no'=>$emergencydata->mobile_no];
          }

           $userdetail = array(
            'id' => $userdetl->id,
            'unique_id' => $userdetl->unique_id . '',
            'firstname' => $userdetl->firstname . '',
            'lastname' => $userdetl->lastname.'',
            'email' => $userdetl->email,
            'country_code' => $userdetl->country_code,
            'mobile' => $userdetl->mobile . '',
            'image' => $img . '',
            'dob' => $userdetl->dob,
            'address' => $userdetl->address,
            'country' => $userdetl ? $userdetl->country . '' : '',
            'state' => $userdetl ? $userdetl->state . '' : '',
            'city' => $userdetl ? $userdetl->city . '' : '',
            'pincode' => $userdetl ? $userdetl->pincode . '' : '',
            'about_us' => $userdetl->about_us,
            'standard' => $stds,
            'institutes' => $workwith, // work with
            'education' => $education,
            'experience'=>$experience,
            'emergency_contacts' => $emergency_contacts
            //'medium' => $sdtls ? $sdtls->medium . '(' . $sdtls->board . ')' : '',
        );

            return $this->response($userdetail, "Successfully fetch data.");
        } catch (Exception $e) {
            return $this->response([], "Somthing went wrong!!", false, 400);
        }
    }
}
