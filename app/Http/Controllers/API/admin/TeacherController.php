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
use App\Models\Institute_board_sub;
use App\Models\Institute_detail;
use App\Models\Medium_model;
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
use DateTime;
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
            // $requestnstitute = Teacher_model::join('institute_detail', 'institute_detail.id', '=', 'teacher_detail.institute_id')
            //     ->where('teacher_detail.status', '!=', '1')
            //     ->where('teacher_detail.teacher_id', $teacher_id)
            //     ->select('institute_detail.*', 'teacher_detail.status as sstatus', 'teacher_detail.id')
            //     ->groupBy('teacher_detail.institute_id', 'teacher_detail.teacher_id')
            //     ->paginate($perPage);

                $subQuery = DB::table('teacher_detail')
                ->select('institute_id', 'teacher_id', DB::raw('MAX(id) as max_id'))
                ->where('status', '!=', '1')
                ->where('teacher_id', $teacher_id)
                ->groupBy('institute_id', 'teacher_id');
            
            $requestInstitute = Teacher_model::joinSub($subQuery, 'sub', function($join) {
                    $join->on('teacher_detail.id', '=', 'sub.max_id');
                })
                ->join('institute_detail', 'institute_detail.id', '=', 'teacher_detail.institute_id')
                ->select('institute_detail.*', 'teacher_detail.status as sstatus', 'teacher_detail.id')
                ->paginate($perPage);


            $requested_institute = [];
            foreach ($requestInstitute as $value) {
                $requested_institute[] = array(
                    'id' => $value->id,
                    'institute_name' => $value->institute_name,
                    'address' => $value->address,
                    'logo' => asset($value->logo),
                    'status' => $value->sstatus,
                );
            }

            //join with
            $date = Carbon::now()->format('Y-m-d');

            $teachrdt = Teacher_model::where('teacher_id', $teacher_id)
            ->where('status', '1')
            ->whereNull('deleted_at')->pluck('institute_id');
            
            $joininstitute = Institute_detail::where('status', 'active')
                //->where('end_academic_year', '>=', $date)
                ->whereDate('end_academic_year', '>=', $date)
                ->whereNull('deleted_at')
                ->whereIN('id',$teachrdt)
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

            return $this->response($final_repsonse, "Successfully fetch.");
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token .", false, 400);
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
            'country_code_name'=>'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $subids = explode(",",$request->subject_id);
            $valueCounts = array_count_values($subids);
            $repeatedValues = array_filter($valueCounts, function($count) {
                return $count > 1;
            });
            
            // Extract the keys (the repeated values) from the filtered array
            $repeatedValues = array_keys($repeatedValues);
            
            if(!empty($repeatedValues)){
                return $this->response([], "Subject Is Repeated.", false, 400);
            }
            $subject = Subject_model::whereIn('id', explode(',', $request->subject_id))->pluck('base_table_id');

            //foreach ($subject as $value) {
                // $batch_list = Batches_model::whereRaw("FIND_IN_SET($value->id, subjects)")
                //     ->select('*')->get()->toarray();

                // foreach ($batch_list as $values_batch) {

                //     Batch_assign_teacher_model::create([
                //         'teacher_id' => $request->teacher_id,
                //         'batch_id' => $values_batch['id'],
                //     ]);
                // }
                // $base_table_response = Base_table::where('id', $value->base_table_id)->get()->toarray();
                // foreach ($base_table_response as $value2) {
                //     $subject=Subject_model::where('base_table_id',$value2['id'])->get();
                //     $subject_implode=[];
                //     foreach($subject as $value){
                //         $subject_implode[]=$value->id;  
                //     }
                //     $subject_id = implode(',',$subject_implode);
                //     // echo "<pre>";print_r($subject_name);exit;
                //     Teacher_model::create([
                //         'institute_id' => $request->institute_id,
                //         'teacher_id' => $request->teacher_id,
                //         'institute_for_id' => $value2['institute_for'],
                //         'board_id' => $value2['board'],
                //         'medium_id' => $value2['medium'],
                //         'class_id' => $value2['institute_for_class'],
                //         'standard_id' => $value2['standard'],
                //         'stream_id' => $value2['stream'],
                //         'subject_id' => $subject_id,
                //         'status' => '0',
                //     ]);
                // }
                
                $base_table_response = Base_table::whereIN('id', $subject)->get()->toArray();
                    foreach ($base_table_response as $value2) {
                        $subjects = Subject_model::where('base_table_id', $value2['id'])->get();
                        
                        foreach ($subjects as $subject) {
                            if(in_array($subject->id, explode(',', $request->subject_id))){
                            Teacher_model::create([
                                'institute_id' => $request->institute_id,
                                'teacher_id' => $request->teacher_id,
                                'institute_for_id' => $value2['institute_for'],
                                'board_id' => $value2['board'],
                                'medium_id' => $value2['medium'],
                                'class_id' => $value2['institute_for_class'],
                                'standard_id' => $value2['standard'],
                                'stream_id' => $value2['stream'],
                                'subject_id' => $subject->id,
                                'status' => '0',
                            ]);
                        }
                        }
                    }
            //}
            User::where('id', $request->teacher_id)->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'address' => $request->address,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'country_code_name'=>$request->country_code_name,
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
            'institute_id' => 'required|exists:institute_detail,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_data = [];
            $boards = [];

            $institutedeta = Institute_detail::where('id', $request->institute_id)
                ->select('id', 'institute_name', 'address', 'about_us','contact_no','email',
                'website_link','instagram_link','facebook_link','whatsaap_link','youtube_link')
                ->first();

            // $boards = board::join('board_sub', 'board_sub.board_id', '=', 'board.id')
            //     ->where('board_sub.institute_id', $request->institute_id)
            //     ->select('board.name')->get();
            $institute_id = $request->institute_id;
            $uniqueBoardIds = Institute_board_sub::where('institute_id', $institute_id)
                ->distinct()
                ->pluck('board_id')
                ->toArray();

            // Fetch board details
            $board_list = Board::whereIn('id', $uniqueBoardIds)->get(['id', 'name', 'icon']);

            $board_array = [];
            foreach ($board_list as $board) {
                $medium_list = Medium_model::whereIn('id', function ($query) use ($institute_id, $board) {
                    $query->select('medium_id')
                        ->from('medium_sub')
                        ->where('board_id', $board->id)
                        ->where('institute_id', $institute_id);
                })->get(['id', 'name', 'icon']);

                $medium_array = $medium_list->map(function ($medium) {
                    return [
                        'id' => $medium->id,
                        'medium_name' => $medium->name,
                        'medium_icon' => asset($medium->icon)
                    ];
                })->toArray();

                $boards[] = [
                    'id' => $board->id,
                    'board_name' => $board->name,
                    'board_icon' => asset($board->icon),
                    'medium' => $medium_array,
                    // Include banner_array inside board_array
                ];
            }

            //feedbacks
            // $feedback_list = FeedbackModel::select(
            //     'feedbacks.id as feedback_id',
            //     'feedbacks.feedback',
            //     'feedbacks.feedback_to_id',
            //     'feedbacks.institute_id',
            //     'feedbacks.rating',
            //     'feedbacks.role_type',
            //     'feedbacks.created_at',
            //     'users.firstname',
            //     'users.lastname',
            //     'users.image',
            //     'roles.role_name',
            // )
            // ->Join('users', 'users.id', '=', 'feedbacks.feedback_to_id')
            // ->Join('roles', 'roles.id', '=', 'users.role_type')
            // ->Join('institute_detail', 'institute_detail.id', '=', 'feedbacks.institute_id')
            // ->where('feedbacks.institute_id', $institute_id)
            // ->where('feedbacks.role_type', '1')
            // ->orderByDesc('feedbacks.created_at')->get()->toArray();
            

            $stdcount = Student_detail::where('institute_id', $request->institute_id)->where('status','1')->count();
            $subcount = Subject_sub::where('institute_id', $request->institute_id)->count();
            $teacherdt = Teacher_model::where('institute_id', $request->institute_id)->where('status','1')->distinct('teacher_id')->count(); //by priyanka

            $institutedetaa = array(
                'id' => $institutedeta->id,
                'institute_name' => $institutedeta->institute_name,
                'address' => $institutedeta->address,
                'contact_no' => $institutedeta->contact_no,
                'email' => $institutedeta->email,
                'about_us' => $institutedeta->about_us,
                'website_link'=>$institutedeta->website_link,
                'instagram_link'=>$institutedeta->instagram_link,
                'facebook_link'=>$institutedeta->facebook_link,
                'whatsaap_link'=>$institutedeta->whatsaap_link,
                'youtube_link'=>$institutedeta->youtube_link,
                'logo' => (!empty($institutedeta->logo))?asset($institutedeta->logo):asset('no-image.png'),
                'cover_photo' => (!empty($institutedeta->cover_photo))?asset($institutedeta->cover_photo):asset('cover_photo/cover_image.png'),
                'boards' => $boards,
                'students' => $stdcount,
                'subject' => $subcount,
                'teacher' => $teacherdt,
                //'feedback'=>$feedback_list
            );
            return $this->response($institutedetaa, "Successfully fetch data.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }

    private  function convertTo12HourFormat($time24)
    {
        $time = Carbon::createFromFormat('H:i:s', $time24);
        return $time->format('g:i:s A');
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
                    'time_table.lecture_date',
                    'users.image',
                )
                ->orderBy('time_table.start_time', 'asc')
                //->paginate(2);
                ->get();

            foreach ($todayslect as $todayslecDT) {
                $todays_lecture[] = array(
                    'profile' => (!empty($todayslecDT->image)) ? asset($todayslecDT->image) : asset('no-image.png'),
                    'subject' => $todayslecDT->subject,
                    'standard' => $todayslecDT->standard,
                    'lecture_date' => date('d-m-Y',strtotime($todayslecDT->lecture_date)),
                    'lecture_type' => $todayslecDT->lecture_type_name,
                    'start_time' => $this->convertTo12HourFormat($todayslecDT->start_time),
                    'end_time' => $this->convertTo12HourFormat($todayslecDT->end_time),
                );
            }

            $announcQY = announcements_model::where('institute_id', $institute_id)->Where('role_type', 4)->get();
            // ->whereRaw("FIND_IN_SET('4', role_type)")

            foreach ($announcQY as $announcDT) {
                $announcement[] = array(
                    'title' => $announcDT->title,
                    'desc' => $announcDT->detail,
                    'time' => $announcDT->created_at
                );
            }
            $teacher_data = Teacher_model::join('batches', 'batches.id', '=', 'teacher_detail.batch_id')
                ->Join('board', 'board.id', '=', 'teacher_detail.board_id')
                ->Join('medium', 'medium.id', '=', 'teacher_detail.medium_id')
                ->Join('standard', 'standard.id', '=', 'teacher_detail.standard_id')
                ->where('teacher_detail.teacher_id', $teacher_id)
                ->where('teacher_detail.institute_id', $institute_id)
                //->whereNull('teacher_detail.deleted_at')
                // ->groupBy('standard.id')
                ->groupBy(
                    'teacher_detail.standard_id',
                    'board.name',
                    'standard.id',
                    'standard.name',
                    'medium.name',
                    'batches.id',
                    'batches.batch_name'
                )

                ->select(
                    // 'board.name as board_name',
                    'standard.id as standard_id',
                    'batches.id as batch_id',
                    'standard.name as standard_name',
                    DB::raw('MAX(board.name) as board_name'),
                    DB::raw('MAX(medium.name) as medium_name'),
                    DB::raw('MAX(batches.batch_name) as batch_name')
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
                    'batch_id' =>$value['batch_id'],
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
            'standard_id' => 'required',
            'batch_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $teacher_details = DB::table('teacher_detail')
                ->join('batches', 'batches.id', '=', 'teacher_detail.batch_id')
                ->join('board', 'board.id', '=', 'teacher_detail.board_id')
                ->join('medium', 'medium.id', '=', 'teacher_detail.medium_id')
                ->join('standard', 'standard.id', '=', 'teacher_detail.standard_id')
                ->join('subject', 'subject.id', '=', 'teacher_detail.subject_id')
                ->where('teacher_detail.teacher_id', $request->teacher_id)
                ->where('teacher_detail.standard_id', $request->standard_id)
                ->where('teacher_detail.batch_id', $request->batch_id)
                ->select('board.id as board_id',
                    'board.name as board_name',
                    'standard.id as standard_id',
                    'standard.name as standard_name',
                    'medium.id as medium_id',
                    'medium.name as medium_name',
                    'teacher_detail.batch_id',
                    'batches.batch_name',
                    'subject.id as subject_id',
                    'subject.name as subject_name',
                    'subject.image as subject_image')
                ->get();

            $teacher_data = [];
            $result = [
                'board_id' => null,
                'board_name' => null,
                'standard_id' => null,
                'standard_name' => null,
                'medium_id' => null,
                'medium_name' => null,
                'batch_id' => null,
                'batch_name' => null,
                'subject_list' => []
            ];
            foreach ($teacher_details as $detail) {
                $result['board_id'] = $detail->board_id;
                $result['board_name'] = $detail->board_name;
                $result['standard_id'] = $detail->standard_id;
                $result['standard_name'] = $detail->standard_name;
                $result['medium_id'] = $detail->medium_id;
                $result['medium_name'] = $detail->medium_name;
                $result['batch_id'] = $detail->batch_id;
                $result['batch_name'] = $detail->batch_name;

                $result['subject_list'][] = [
                    'id' => $detail->subject_id,
                    'subject_name' => $detail->subject_name,
                    'image' => !empty($detail->subject_image) ? url($detail->subject_image) : ''
                ];
            }

            $result['subject_list'] = array_map(
                "unserialize",
                array_unique(array_map("serialize", $result['subject_list']))
            );
            
            return $this->response($result, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    //timetable list
    public function timetable_list_teacher(Request $request)
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
                ->orderBy('time_table.start_time', 'asc')
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

                if ($request_list->isNotEmpty()) {
                $response = $request_list->map(function ($value) {
                    $user_data = User::find($value->teacher_id);
                    if ($user_data) {
                        return [
                            'teacher_id' => $user_data->id,
                            'name' => $user_data->firstname . ' ' . $user_data->lastname,
                            'photo' => $user_data->image,
                        ];
                    }
                })->filter()->unique('teacher_id')->values()->toArray();

                return $this->response($response, "Fetch teacher request list.");
                } else {
                return $this->response([], "Teacher not found.", false, 400);
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
                    // 'users.qualification',
                    // 'users.lastname',
                    // 'users.dob',
                    // 'users.address',
                    // 'users.email',
                    // 'users.mobile',
                    // 'users.employee_type'
                    
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
                    $batches=Batches_model::where('institute_id',$request->institute_id)
                                   ->where('board_id',$values->board_id)
                                  ->where('medium_id',$values->medium_id)
                                  ->where('standard_id',$values->standard_id)
                                  ->WhereRaw("FIND_IN_SET(?, subjects)", [$values->subject_id])
                                  ->get()->toarray();
                     $batch_detail = [];
                     foreach($batches as $value2){
                        $batch_detail[] = [
                            'id'=>$value2['id'],
                            'batch_name'=>$value2['batch_name']
                        ];
                     }          

                    $response_data_two[]=[
                            'teacher_detail_id' => $values->id,
                            'board' => $values->board,
                            'board_id' => $values->board_id,
                            'medium' => $values->medium,
                            'medium_id' => $values->medium_id,
                            'standard' => $values->standard,
                            'standard_id' => $values->standard_id,
                            'stream' => $values->stream,
                            'stream_id' => $values->stream_id,
                            'subject'=>$subjectslist,
                            'batches'=>$batch_detail
                         ];
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
                    'country_code_name'=>$user_list->country_code_name,
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
            //'area' => 'required|string|max:255',
            //'about_us' => 'nullable|string',
            'qualification' => 'required|string|max:255',
            //'institute_name' => 'required|string|max:255',
            //'startdate' => 'required',
            //'enddate' => 'nullable',
            //'name' => 'required|string|max:255',
            //'relation_with' => 'required|string|max:255',
            //'mobile_no' => 'required|string|max:255',
            'country_code' =>'required',
            'country_code_name'=>'required',
            //'emergency_country_code' =>'required',
        ]);
        
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        $user = User::findOrFail($teacher_id);
        
        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->email = $request->input('email');
        $user->country_code = $request->input('country_code');
        $user->country_code_name = $request->input('country_code_name');
        $user->dob = (!empty($request['dob'])) ? date('d-m-Y', strtotime($request['dob'])) : '';
        $user->address = $request['address'];
        $user->pincode = $request['pincode'];
        //$user->area = $request['area'];
        
        if ($request->file('image')) {
            $iconFile = $request->file('image');
            $imagePath = $iconFile->store('profile', 'public');
            $user->image = $imagePath;
        }
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
                    $startdates = (!empty($startdate[$i])) ? DateTime::createFromFormat('d/m/Y', $startdate[$i])->format('Y-m-d') : '';
                    $enddates = (!empty($enddate[$i])) ? DateTime::createFromFormat('d/m/Y', $enddate[$i])->format('Y-m-d') : '';
                    Users_sub_experience::create([
                        'user_id' => $teacher_id,
                        'institute_name' => $experience[$i],
                        'startdate' => $startdates,
                        'enddate' => $enddates,
                    ]);
                }
            } else {
                if(!empty($request['institute_name'])){
                    $experience = explode(',', $request['institute_name']);
                    $startdate = explode(',', $request['startdate']);
                    $enddate = explode(',', $request['enddate']);
                    for ($i = 0; $i < count($experience); $i++) {
                        $startdates = (!empty($startdate[$i])) ? DateTime::createFromFormat('d/m/Y', $startdate[$i])->format('Y-m-d') : '';
                        $enddates = (!empty($enddate[$i])) ? DateTime::createFromFormat('d/m/Y', $enddate[$i])->format('Y-m-d') : '';
                        Users_sub_experience::create([
                            'user_id' => $teacher_id,
                            'institute_name' => $experience[$i],
                            'startdate' => $startdates,
                            'enddate' => $enddates,
                        ]);
                }
                }
                
            }
            
            $userSub2 = Users_sub_emergency::where('user_id', $teacher_id)->first();
            // echo "<pre>";print_r($userSub2);exit;
            if (!empty($userSub2)) {
                if(!empty($request['name'])){
                $delete_qualification = Users_sub_emergency::where('user_id', $request->teacher_id);
                $delete_qualification->delete();
                $name = explode(',', $request['name']);
                $relation_with = explode(',', $request['relation_with']);
                $mobile_no = explode(',', $request['mobile_no']);
                $emergency_country_code = explode(',', $request['emergency_country_code']);
                $emergency_country_code_name = explode(',', $request['emergency_country_code_name']);
                
                //print_r(count($qualification));exit;
                // print_r($emergency_country_code);exit;
                for ($i = 0; $i < count($name); $i++) {
                    Users_sub_emergency::create([
                        'user_id' => $teacher_id,
                        'name' => $name[$i],
                        'relation_with' => $relation_with[$i],
                        'mobile_no' => $mobile_no[$i],
                        'country_code'=>$emergency_country_code[$i],
                        'country_code_name'=>$emergency_country_code_name[$i],
                    ]);
                }
                } 
            } else {
                if(!empty($request['name'])){
                    $name = explode(',', $request['name']);
                    $relation_with = explode(',', $request['relation_with']);
                    $mobile_no = explode(',', $request['mobile_no']);
                    $emergency_country_code = explode(',', $request['emergency_country_code']);
                    $emergency_country_code_name = explode(',', $request['emergency_country_code_name']);
                    //print_r($emergency_country_code);exit;
                    for ($i = 0; $i < count($name); $i++) {
                        Users_sub_emergency::create([
                            'user_id' => $teacher_id,
                            'name' => $name[$i],
                            'relation_with' => $relation_with[$i],
                            'mobile_no' => $mobile_no[$i],
                            'country_code'=>$emergency_country_code[$i],
                            'country_code_name'=>$emergency_country_code_name[$i],
                        ]);
                    } 
                }
                
            }
            return $this->response([], "Successfully Update data.");
        } catch (Exception $e) {
            
            return $this->response([], "Somthing went wrong.", false, 400);
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
            $user_sub=Users_sub_model::where('user_id', $teacher_id)->first();

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
                ->where('status', '1')
                ->whereNull('deleted_at')->pluck('institute_id');

            $institds = institute_detail::whereIN('id', $instrdids)->get();
            $workwith = [];
            foreach ($institds as $instdata) {
                //start
                $institute_id = $instdata->id;
                $uniqueBoardIds = Institute_board_sub::where('institute_id', $institute_id)
                ->distinct()
                ->pluck('board_id')
                ->toArray();
                $board_list = Board::whereIn('id', $uniqueBoardIds)->get(['id', 'name']);

                    $boards = [];
                    foreach ($board_list as $board) {

                        $boards[] = [
                            'id' => $board->id,
                            'board_name' => $board->name,
                        ];
                    }

                $workwith[] = ['id' => $instdata->id, 
                'institute_name' => $instdata->institute_name,
                'logo'=>(!empty($instdata->logo))?url($instdata->logo):asset('no-image.png'),
                'address'=>$instdata->address,
                'board'=>$boards];
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
          //print_r($teacher_id);exit;
          $emergency = Users_sub_emergency::where('user_id',$teacher_id)->get();
          $emergency_contacts = [];
          //print_r($emergency);exit;
          foreach($emergency as $emergencydata){
            $emergency_contacts[] = ['id'=>$emergencydata->id,
            'name'=>$emergencydata->name,
            'relation_with'=>$emergencydata->relation_with,
            'mobile_no'=>$emergencydata->mobile_no,
            'country_code'=>$emergencydata->country_code,
            'country_code_name'=>$emergencydata->country_code_name];
          }

           $userdetail = array(
            'id' => $userdetl->id,
            'unique_id' => $userdetl->unique_id . '',
            'firstname' => $userdetl->firstname . '',
            'lastname' => $userdetl->lastname.'',
            'email' => $userdetl->email,
            'country_code' => $userdetl->country_code,
            'country_code_name'=>$userdetl->country_code_name,
            'mobile' => $userdetl->mobile . '',
            'image' => $img . '',
            'dob' => $userdetl->dob,
            'address' => $userdetl->address,
            'country' => $userdetl ? $userdetl->country . '' : '',
            'state' => $userdetl ? $userdetl->state . '' : '',
            'city' => $userdetl ? $userdetl->city . '' : '',
            'pincode' => $userdetl ? $userdetl->pincode . '' : '',
            //'area'=>$userdetl ? $userdetl->area . '' : '',
            'about_us' => $user_sub ? $user_sub->about_us .'' : '',
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

    public function remove_institute_teacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_id = $request->institute_id;
            $teacher_id = Auth::id();
            $gette = Teacher_model::where('teacher_id', $teacher_id)
                ->where('institute_id', $institute_id)
                ->first();
            if (!empty($gette)) {
                $remove = Teacher_model::where('teacher_id', $teacher_id)
                    ->where('institute_id', $institute_id)
                    ->delete();
                return $this->response([], "Institute Removed");
            } else {
                return $this->response([], "Data not found", false, 400);
            }
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    // function role_change(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'institute_id' => 'required',
    //         'role_type_id' => 'required',
    //         'teacher_id' => 'required',

    //     ]);

    //     if ($validator->fails()) {
    //         return $this->response([], $validator->errors()->first(), false, 400);
    //     }
    //     try {
    //         $teacherDetail = Teacher_model::where('institute_id', $request->institute_id)->where('teacher_id', $request->teacher_id);

    //         if ($teacherDetail) {
    //             $teacherDetail->update([
    //                 'role_type' => $request->role_type_id
    //             ]);
    //         }
    //         return $this->response([], "Roletype change successfully");
    //     } catch (Exception $e) {
    //         return $this->response($e, "Invalid token.", false, 400);
    //     }
    // }
}
