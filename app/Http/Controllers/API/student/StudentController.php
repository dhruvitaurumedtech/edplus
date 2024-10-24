<?php

namespace App\Http\Controllers\API\student;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\Banner_model;
use App\Models\board;
use App\Models\Institute_for_model;
use App\Mail\DirectMessage;
use App\Models\announcements_model;
use App\Models\Attendance_model;
use App\Models\Base_table;
use App\Models\Batches_model;
use Illuminate\Support\Facades\Mail;
use App\Models\Chapter;
use App\Models\Dobusinesswith_Model;
use App\Models\Subject_sub;
use App\Models\Class_model;
use App\Models\Exam_Model;
use App\Models\FeedbackModel;
use App\Models\Institute_board_sub;
use App\Models\Stream_model;
use App\Models\Standard_model;
use App\Models\Institute_detail;
use App\Models\Marks_model;
use App\Models\Parents;
use App\Models\Student_detail;
use App\Models\Search_history;
use App\Models\Subject_model;
use App\Models\Topic_model;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VideoCategory;
use App\Models\Medium_model;
use App\Models\Student_fees_model;
use App\Models\Teacher_model;
use App\Models\Timetable;
use App\Models\Timetables;
use App\Models\VideoAssignToBatch;
use Illuminate\Auth\Events\Verified;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    use ApiTrait;

    public function homescreen_student(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $user_id = Auth::id();
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
            $allinstitute = Institute_detail::where('status', 'active')
                ->where(function ($query) use ($search_keyword) {
                    $query->where('unique_id', 'like', '%' . $search_keyword . '%')
                        ->orWhere('institute_name', 'like', '%' . $search_keyword . '%');
                })
                ->orderByDesc('created_at')
                ->paginate($perPage);
            $search_list = [];
            foreach ($allinstitute as $value) {
                $search_list[] = array(
                    'id' => $value->id,
                    'institute_name' => $value->institute_name,
                    'address' => $value->address,
                    'logo' => (!empty($value->logo))?url($value->logo):asset('profile/no-image.png'),
                );
            }
            //student search history
            $searchhistory = Search_history::where('user_id', $user_id)->paginate($perPage);
            $searchhistory_list = [];
            foreach ($searchhistory as $value) {
                $existingTitles = array_column($searchhistory_list, 'title');
                if (!in_array($value->title, $existingTitles)) {
                    $instdl = Institute_detail::where('id',$value->institute_id)
                    ->select('institute_name','logo','address')
                    ->first();
                    $searchhistory_list[] = [
                        'id' => $value->id,
                        'institute_id' => (int) $value->institute_id,
                        'institute_name'=>$instdl ? $instdl->institute_name : '',
                        'address'=>$instdl ? $instdl->address : '',
                        'logo' => (!empty($instdl->logo))?asset($instdl->logo):asset('profile/no-image.png'),
                        'user_id' => $value->user_id,
                        'title' => $value->title,
                    ];
                }
            }
            //requested institute
            $requestnstitute = Student_detail::join('institute_detail', 'institute_detail.id', '=', 'students_details.institute_id')
            ->where('students_details.status', '!=', '1')
                ->where('students_details.student_id', $user_id)
                ->select('institute_detail.*', 'students_details.status as sstatus', 'students_details.student_id')
                ->orderByDesc('institute_detail.created_at')
                ->paginate($perPage);

            $requested_institute = [];
            foreach ($requestnstitute as $value) {
                $requested_institute[] = array(
                    'id' => $value->id,
                    'institute_name' => $value->institute_name,
                    'address' => $value->address,
                    'logo' => asset($value->logo),
                    'status' => (int)$value->sstatus,
                );
            }
            //join with
            $date = Carbon::now()->format('Y-m-d');
            $joininstitute = Institute_detail::where('status', 'active')
                ->whereIn('id', function ($query) use ($user_id) {
                    $query->select('institute_id')
                        ->from('students_details')
                        ->where('student_id', $user_id)
                        ->where('status', '=', '1')
                        ->whereNull('deleted_at');
                })
                ->whereDate('end_academic_year', '>=', $date)
                ->paginate($perPage);
            $join_with = [];
            foreach ($joininstitute as $value) {
                $join_with[] = array(
                    'id' => $value->id,
                    'institute_name' => $value->institute_name . '(' . $value->unique_id . ')',
                    'address' => $value->address.' '.$value->state. ' '.$value->city.' '.$value->pincode,
                    'logo' => asset($value->logo),
                );
            }
            $parentsdt = Parents::where('student_id', $user_id)
                ->orderByDesc('created_at')
                ->get();
            $veryfy = [];
            foreach ($parentsdt as $checkvery) {
                $veryfy[] = array('relation' => $checkvery->relation, 'verify' => (int) $checkvery->verify);
            }
            if ($parentsdt->isEmpty()) {
                $studentparents = 0;
            } else {
                $studentparents = 1;
            }
            $studentUser = User::where('id', $user_id)->first();
            if ($studentUser->image) {
                $img = $studentUser->image;
            } else {
                $img = asset('profile/no-image.png');
            }
            $data = [
                'profile_image' => $img,
                'banner' => $banners_data,
                'search_list' => $search_list,
                'searchhistory_list' => $searchhistory_list,
                'requested_institute' => $requested_institute,
                'join_with' => $join_with,
                'parents_detail' => $studentparents,
                'parents_verification' => $veryfy
            ];
            return $this->response($data, "Successfully fetch data.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function student_searchhistory_add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'institute_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $search_add = Search_history::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'institute_id' => $request->institute_id,
            ]);
            return $this->response([], "Serach History Added");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    //clear search history
    public function clear_search_history(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            Search_history::where('user_id',$request->user_id)->delete(); 
            return $this->response([], "History clear successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    //add parents details
    public function student_parents_details_add(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'parents.*.firstname' => 'required',
            'parents.*.lastname' => 'required',
            'parents.*.email' => 'required|email', //|unique:users,email
            'parents.*.mobile' => 'required',
            'parents.*.relation' => 'required',
            'parents.*.country_code' => 'required',
            'parents.*.country_code_name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $parents = json_decode($request->parents, true);
            foreach ($parents as $parentData) {
                $emilfin = user::where('email', $parentData['email'])
                            //->where('role_type',5)
                            ->first();
                $tomail = $parentData['email'];
                if ($parentData['firstname'] == '') {
                    return $this->response([], 'firstname Requied field are missing', false, 400);
                } elseif ($parentData['lastname'] == '') {
                    return $this->response([], 'lastname Requied field are missing', false, 400);
                } elseif ($parentData['email'] == '') {
                    return $this->response([], 'email Requied field are missing', false, 400);
                } elseif ($parentData['mobile'] == '') {
                    return $this->response([], 'mobile Requied field are missing', false, 400);
                } elseif ($parentData['relation'] == '') {
                    return $this->response([], 'relation Requied field are missing', false, 400);
                }
                // elseif (!empty($emilfin)) {
                //     return $this->response([], 'email is already exist', false, 400);
                // } 
                else {
                    $parent_id = '';
                    if($parentData['upid']){

                        $updateData = [
                            'firstname' => $parentData['firstname'],
                            'lastname' => $parentData['lastname'],
                            'country_code' => $parentData['country_code'],
                            'country_code_name' => $parentData['country_code_name'],
                            'mobile' => $parentData['mobile'],
                            'role_type' => '5',
                            'status' => '1'
                        ];

                        if (!empty($parentData['email'])) {
                            $updateData['email'] = $parentData['email'];
                        }
                        User::where('id', $parentData['upid'])->update($updateData);

                        $parnsad = Parents::where('student_id',auth()->id())
                        ->where('parent_id',$parentData['upid'])
                        ->where('verify','0')
                        ->update([
                            'relation' => $parentData['relation'],
                        ]);
                    }else{
                        if ($emilfin && $emilfin->role_type != 5) {
                            return $this->response([], "Someone else has already used this email.", false, 400);
                        }elseif($emilfin && $emilfin->role_type == 5){
                            $parent_id = $emilfin->id;
                        } else {
                            $user = User::create([
                                'firstname' => $parentData['firstname'],
                                'lastname' => $parentData['lastname'],
                                'email' => $parentData['email'],
                                'country_code' => $parentData['country_code'],
                                'country_code_name' => $parentData['country_code_name'],
                                'mobile' => $parentData['mobile'],
                                'role_type' => '5',
                                'status' => '1'
                            ]);
                            $parent_id = $user->id;
                        }
                        if (!empty($parent_id)) {
                            $prexis = Parents::where('student_id',auth()->id())
                            ->where('parent_id',$parent_id)
                            ->whereNull('institute_id')
                            ->where('verify','0')
                            ->first();
                            if(empty($prexis)){
                                $parnsad = Parents::create([
                                    'student_id' =>  auth()->id(),
                                    'parent_id' => $parent_id,
                                    'relation' => $parentData['relation'],
                                    'verify' => '0',
                                ]);
                                if (empty($parnsad->id)) {
                                    User::where('id', $parent_id)->delete();
                                    return $this->response([], 'Data not added Successfuly.');
                                }
                            }
                        } else {
                            return $this->response([], 'Data not added Successfuly', false, 500);
                        }
                    }                   
                }
            }
            return $this->response([], 'Parent details uploaded successfully');
        } catch (\Exception $e) {
            return $e;
            return $this->response([], "Invalid token.", false, 400);
        }
    }
   public function verifyEmail($updateid)
    {
        $parent = Parents::find($updateid);
        if ($parent) {
            if ($parent->verify == 0) {
                $parent->verify = '1';
                $parent->save();

                $passcheck = User::where('id', $parent->parent_id)->first();
                if ($passcheck->password == null) {
                    $password = Str::random(12);
                    user::where('id', $parent->parent_id)->update(['password' => Hash::make($password)]);
                    $parDT = [
                        'password' => $password,
                        'email' =>    $passcheck->email,
                        'institute' => ''
                    ];
                   Mail::to($passcheck->email)->send(new WelcomeMail($parDT));
                }
                return view('already_verify')->with('data', 1);
            } else {
                return view('already_verify')->with('data', 2);
            }
        } else {
            return view('already_verify')->with('data', 3);
        }
    }
    public function student_add_institute_request(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $instituteid = $request->institute_id;
            $getsid = Student_detail::where('student_id', $request->user_id)
                ->where('institute_id', $instituteid)->first();
            if ($getsid) {
                return $this->response([], 'You Already Requestd.', false, 400);
            } else {
                $getuid = Institute_detail::where('id', $instituteid)->select('user_id')->first();
                if ($getuid) {
                    $search_add = Student_detail::create([
                        'user_id' => $getuid->user_id,
                        'institute_id' => $request->input('institute_id'),
                        'student_id' => $request->input('user_id'),
                        'status' => '0',
                    ]);
                    $serverKey = env('SERVER_KEY');
                    $url = "https://fcm.googleapis.com/fcm/send";
                    $users = User::where('id', $getuid->user_id)->pluck('device_key');
                    $notificationTitle = "Student Join Request";
                    $notificationBody = $request->firstname . " Student Join Request to  Your Institute";
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
                    return $this->response([], "Request added successfully");
                } else {
                    return $this->response([], "Institute ID not found.", false, 404);
                }
            }
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
                ->select(
                    'id',
                    'institute_name',
                    'address',
                    'about_us',
                    'contact_no',
                    'email',
                    'website_link',
                    'instagram_link',
                    'facebook_link',
                    'whatsaap_link',
                    'youtube_link',
                    'logo',
                    'cover_photo'
                )
                ->first();
            $institute_id = $request->institute_id;
            $uniqueBoardIds = Institute_board_sub::where('institute_id', $institute_id)
                ->distinct()
                ->pluck('board_id')
                ->toArray();
            // Fetch board details
            $board_list = Board::whereIn('id', $uniqueBoardIds)->get(['id', 'name', 'icon']);
            $boards = [];
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
            $stdcount = Student_detail::where('institute_id', $request->institute_id)->where('status', '1')->count();
            $subcount = Subject_sub::where('institute_id', $request->institute_id)->count();
            $teacherdt = Teacher_model::where('institute_id', $request->institute_id)->where('status', '1')->distinct('teacher_id')->count(); //by priyanka
            $institutedetaa = array(
                'id' => $institutedeta->id,
                'institute_name' => $institutedeta->institute_name,
                'address' => $institutedeta->address,
                'contact_no' => $institutedeta->contact_no,
                'email' => $institutedeta->email,
                'about_us' => $institutedeta->about_us,
                'website_link' => $institutedeta->website_link,
                'instagram_link' => $institutedeta->instagram_link,
                'facebook_link' => $institutedeta->facebook_link,
                'whatsaap_link' => $institutedeta->whatsaap_link,
                'youtube_link' => $institutedeta->youtube_link,
                'logo' => (!empty($institutedeta->logo)) ? asset($institutedeta->logo) : asset('profile/no-image.png'),
                'cover_photo' => (!empty($institutedeta->cover_photo)) ? asset($institutedeta->cover_photo) : asset('cover_photo/cover_image.png'),
                'boards' => $boards,
                'students' => $stdcount,
                'subject' => $subcount,
                'teacher' => $teacherdt,
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

    public function student_added_detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $user_id = Auth::id();
            $existingUser = User::where('id', $user_id)->first();
            $institute_id = $request->institute_id;
            $getstdntdata = Student_detail::where('institute_id', $request->institute_id)
            ->where('student_id',$user_id)
            ->where('status', '=', '1')
            ->whereNull('deleted_at')
            ->first();
            $bannerss = Banner_model::where('status', 'active')
                ->Where('institute_id', $institute_id)
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
           
            $today = date('l');
            $daysidg = DB::table('days')->where('day',$today)->select('id')->first();
            $todays_lecture = [];
            // $subject_ids = explode(",", $getstdntdata->subject_id);
            $todayslect = Timetables::join('subject', 'subject.id', '=', 'timetables.subject_id')
                ->join('users', 'users.id', '=', 'timetables.teacher_id')
                ->join('lecture_type', 'lecture_type.id', '=', 'timetables.lecture_type')
                ->join('batches', 'batches.id', '=', 'timetables.batch_id')
                ->where('timetables.batch_id', $getstdntdata->batch_id)
                //->whereRaw("FIND_IN_SET(time_table.subject_id,?)", [$getstdntdata->subject_id])
                 ->whereIn('timetables.subject_id',explode(",",$getstdntdata->subject_id))
                 ->where('timetables.day', $daysidg->id)
                ->select(
                    'subject.name as subject',
                    'users.firstname',
                    'users.lastname',
                    'users.image',
                    'lecture_type.name as lecture_type_name',
                    'timetables.start_time',
                    'timetables.end_time',
                    'timetables.day'
                )
                ->orderBy('timetables.start_time', 'asc')
                ->get();
            foreach ($todayslect as $todayslecDT) {

                $todays_lecture[] = array(
                    'subject' => $todayslecDT->subject,
                    'teacher' => $todayslecDT->firstname . ' ' . $todayslecDT->lastname,
                    'teacher_image' =>(!empty($todayslecDT->image)) ? asset($todayslecDT->image) : asset('profile/no-image.png'),
                    'lecture_type' => $todayslecDT->lecture_type_name,
                    'start_time' => $this->convertTo12HourFormat($todayslecDT->start_time),  //$todayslecDT->start_time,
                    'end_time' => $this->convertTo12HourFormat($todayslecDT->end_time),  //$todayslecDT->end_time,
                );
            }
            $subjects = [];
            $result = [];
            $announcement = [];
            $examlist = [];
            $announcQY = announcements_model::where('institute_id', $institute_id)
                ->where('standard_id', $getstdntdata->standard_id)
                //->WhereRaw("FIND_IN_SET($getstdntdata->batch_id, batch_id)")
                ->Where('batch_id', 'like', '%' . $getstdntdata->batch_id . '%')
                ->whereRaw("FIND_IN_SET('6', role_type)")
                ->whereNull('deleted_at')
                ->orderByDesc('created_at')
                ->get();
            foreach ($announcQY as $announcDT) {
                $announcement[] = array(
                    'title' => $announcDT->title,
                    'desc' => $announcDT->detail,
                    'time' => $announcDT->created_at
                );
            }
            $resultQY = Marks_model::join('exam', 'exam.id', '=', 'marks.exam_id')
                ->join('subject', 'subject.id', '=', 'exam.subject_id')
                ->where('marks.student_id', $user_id)
                ->where('exam.institute_id', $institute_id)
                ->whereIN('exam.subject_id', explode(",",$getstdntdata->subject_id))
                ->select(
                    'marks.*',
                    'subject.name as subject',
                    'exam.subject_id',
                    'exam.total_mark',
                    'exam.exam_type',
                    'exam.exam_date',
                    'exam.exam_title',
                )
                ->orderBy('marks.id', 'desc')
                ->get();
            foreach ($resultQY as $resultDDt) {
                $highestMarks = Marks_model::where('exam_id', $resultDDt->exam_id)
                    ->max('mark');
                $result[] = array(
                    'subject' => $resultDDt->subject,
                    'title' => $resultDDt->exam_title . '(' . $resultDDt->exam_type . ')',
                    'total_marks' => intval($resultDDt->total_mark),
                    'achiveddmarks_marks' => $resultDDt->mark,
                    'date' => date('d-m-Y',strtotime($resultDDt->exam_date)),
                    'class_highest' => $highestMarks
                );
            }
            $subdta = Student_detail::where('student_id', $user_id)
                ->where('institute_id', $institute_id)
                ->whereNull('deleted_at')->select('students_details.*')->first();
            if (!empty($subdta)) {
                $subjecqy = Subject_model::whereIN('id', explode(",", $subdta->subject_id))->get();
                foreach ($subjecqy as $subjcdt) {
                    if ($subjcdt->image) {
                        $img = asset($subjcdt->image);
                    } else {
                        $img = asset('default.jpg');
                    }
                    $subjects[] = array('id' => $subjcdt->id, 'name' => $subjcdt->name, 'image' => $img);
                }
                $stdetail = Student_detail::where('institute_id', $institute_id)
                ->where('student_id', $user_id)
                ->whereNull('deleted_at')
                ->first();
                $subjectIds = explode(',', $stdetail->subject_id);
                $tdasy = date('Y-m-d');
                $pdate = new \DateTime($tdasy);
                $pdate->modify('-1 day');
                $prDayStr = $pdate->format('Y-m-d');
                $exams = Exam_Model::join('subject', 'subject.id', '=', 'exam.subject_id')
                    ->join('standard', 'standard.id', '=', 'exam.standard_id')
                    ->where('exam.institute_id', $stdetail->institute_id)
                    ->where('exam.board_id', $stdetail->board_id)
                    ->where('exam.medium_id', $stdetail->medium_id)
                    ->when($stdetail->batch_id, function ($query, $batch_id) {
                        return $query->where('exam.batch_id', $batch_id);
                    })
                    ->where('exam.standard_id', $stdetail->standard_id)
                    ->whereIn('exam.subject_id', $subjectIds)
                    ->where('exam.exam_date', '>', $prDayStr)
                    ->orderBy('exam.created_at', 'desc')
                    ->select('exam.*', 'subject.name as subject', 'standard.name as standard')
                    ->orderBy('exam.id', 'desc')
                    ->get();
                foreach ($exams as $examsDT) {
                    $examlist[] = array(
                        'exam_title' => $examsDT->exam_title,
                        'total_mark' => intval($examsDT->total_mark),
                        'exam_type' => $examsDT->exam_type,
                        'subject' => $examsDT->subject,
                        'standard' => $examsDT->standard,
                        'date' => $examsDT->exam_date,
                        'time' => $this->convertTo12HourFormat($examsDT->start_time) . ' to ' . $this->convertTo12HourFormat($examsDT->end_time),
                    );
                }
            }
            $totalattendlec = [];
            $cumnth = date('Y-m');
            $cmtoday = date('Y-m-d');
            $date = new \DateTime($cmtoday);
            $date->modify('+1 day');
            $nextDayStr = $date->format('Y-m-d');
            $totalattlec = Attendance_model::where('institute_id', $institute_id)
                ->where('student_id', $user_id)
                ->where('created_at', 'like', '%' . $cumnth . '%')
                ->where('created_at', '<', $nextDayStr)
                ->whereIn('subject_id', explode(',',$getstdntdata->subject_id))
                ->where('attendance', 'P')->count();

            $totalmissattlec = Attendance_model::where('institute_id', $institute_id)
                ->where('student_id', $user_id)
                ->where('created_at', 'like', '%' . $cumnth . '%')
                ->where('created_at', '<', $nextDayStr)
                ->whereIn('subject_id', explode(',',$getstdntdata->subject_id))
                ->where('attendance', 'A')->count();
            $totalattendlec = [
                'total_lectures' => $totalmissattlec + $totalattlec,
                'attend_lectures' => $totalattlec,
                'miss_lectures' => $totalmissattlec //$totalmissattlec
            ];
            $studentdata = [
                'banners_data' => $banners_data,
                'todays_lecture' => $todays_lecture,
                'announcement' => $announcement,
                'upcoming_exams' => $examlist,
                'subjects' => $subjects,
                'result' => $result,
                'attendance' => $totalattendlec
            ];
            return $this->response($studentdata, "Successfully fetch data.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    public function subject_chapers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subject,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $chapers = [];
            $cptquy = Chapter::where('subject_id', $request->subject_id)->get();
            foreach ($cptquy as $chval) {
                $subasid = Subject_model::where('id', $chval->subject_id)
                ->select('base_table_id')->first();
                $chapers[] = array(
                    "id" => $chval->id,
                    "base_table_id" => $subasid->base_table_id,
                    "subject_id" => $chval->subject_id,
                    "chapter_name" => $chval->chapter_name,
                    "chapter_no" => $chval->chapter_no,
                    "chapter_image" => asset($chval->chapter_image)
                );
            }
            return $this->response($chapers, "Successfully fetch data.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function topic_videos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subject,id',
            'institute_id' => 'required|exists:institute_detail,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $user_id = Auth::id();
            $subject_id = $request->subject_id;
            $chapter_id = $request->chapter_id;
            $institute_id = $request->institute_id;
            $category = Dobusinesswith_Model::join('video_categories', 'video_categories.id', '=', 'do_business_with.category_id')
                ->select(
                    'do_business_with.id',
                    'do_business_with.name',
                    'video_categories.id as vid',
                    'video_categories.name as vname'
                )
                ->whereIn('do_business_with.id', function ($query) {
                    $query->select('topic.video_category_id')
                        ->from('topic')
                        ->groupBy('topic.video_category_id');
                })
                ->get();
            $response = [];
            foreach ($category as $catvd) {
                $topics = Topic_model::join('subject', 'subject.id', '=', 'topic.subject_id')
                    ->join('chapters', 'chapters.id', '=', 'topic.chapter_id')
                    ->when($chapter_id, function ($query, $chapter_id) {
                        return $query->where('topic.chapter_id', $chapter_id);
                    })
                    ->where('topic.subject_id', $subject_id)
                    ->where('topic.institute_id', $institute_id)
                    ->where('topic.video_category_id', $catvd->id) 
                    ->select('topic.*', 'subject.name as sname', 'chapters.chapter_name as chname')
                    ->orderByDesc('topic.created_at')
                    ->get()
                    ->toarray();


                    if(!empty($topics))  {
                        $topicsArray = [];
                        foreach ($topics as $topval) {

                            $additional_Data=Base_table::join('board','board.id','=','base_table.board')
                                       ->join('medium','medium.id','=','base_table.medium')
                                       ->join('standard','standard.id','=','base_table.standard')
                                       ->select('standard.name as standard_name','board.name as board_name','medium.name as medium_name')
                                       ->where('base_table.id',$topval['base_table_id'])
                                       ->first();


                                $reponse_video = VideoAssignToBatch::join('batches', 'batches.id', '=', 'video_assignbatch.batch_id')
                                    ->where('video_assignbatch.video_id', $topval['id'])
                                    ->where('video_assignbatch.standard_id', $topval['standard_id'])
                                    ->where('video_assignbatch.chapter_id', $topval['chapter_id'])
                                    ->where('video_assignbatch.subject_id', $topval['subject_id'])
                                    ->Select('batches.*', 'video_assignbatch.assign_status')
                                    ->get();
                                    $batch_list = [];
                                    $allTrue = true;
                                foreach ($reponse_video as $value) {
                                    $status = ($value->assign_status == 1) ? true : false;
                                    $batch_list[] = [
                                        'batch_id' => $value->id,
                                        'batch_name' => $value->batch_name,
                                        'status' => ($value->assign_status == 1) ? true : false,
                                    ];
                                    if (!$status) {
                                            $allTrue = false; // If any status is false, set $allTrue to false
                                        }
                                }
                                if(empty($batch_list)){
                                 $final_status = false;
                                }else{
                                 $final_status = $allTrue ? true : false;
                           
                                }
                            $topicsArray[] = [
                                "id" => $topval['id'],
                                "topic_no" => $topval['topic_no'],
                                "topic_name" => $topval['topic_name'] . '',
                                "topic_video" => asset($topval['topic_video']),
                                "topic_description"=>$topval['topic_description'],
                                "subject_id" => $topval['subject_id'],
                                "subject_name" => $topval['sname'],
                                "chapter_id" => $topval['chapter_id'],
                                "chapter_name" => $topval['chname'],
                                "status" => $final_status,
                                'board_name'=> $additional_Data->board_name,
                                'standard_name'=> $additional_Data->standard_name,
                                'medium_name'=>$additional_Data->medium_name,
                                "batch_list" => $batch_list,
                            ];
                        }
                        $response[] = [
                            'id' => $catvd->id,
                            'category_name' => $catvd->name,
                            'parent_category_id' => $catvd->vid,
                            'parent_category_name' => $catvd->vname,
                            'topics' => $topicsArray
                        ];
                }
            }
            return $this->response($response, "Successfully fetch data.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function profile_detail(Request $request)
    {
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            if (Auth::user()->role_type == 6) {
                $student_id = Auth::id();
            } else {
                $student_id = $request->student_id;
            }
            $studentUser = User::where('id', $student_id)->first();
            $institute_id = $request->institute_id;
            $institutes = [];
            $joininstitute = Institute_detail::where('status', 'active')
                ->whereIn('id', function ($query) use ($student_id) {
                    $query->select('institute_id')
                        ->where('student_id', $student_id)
                        ->where('status', '=', '1')
                        ->whereNull('deleted_at')
                        ->from('students_details');
                })
                ->when($institute_id, function ($query, $institute_id) {
                    return $query->where('id', $institute_id);
                })->get();
            foreach ($joininstitute as $value) {
                $substdnt = Student_detail::where('student_id', $student_id)
                    ->where('institute_id', $value->id)
                    ->where('status', '=', '1')
                    ->whereNull('deleted_at')
                    ->first();
                if ($substdnt) {
                    $subids = explode(',', $substdnt->subject_id);
                    $subjectids = Subject_model::whereIn('id', $subids)->get();
                    $subs = [];
                    foreach ($subjectids as $subDT) {
                        $subs[] = array('id' => $subDT->id, 'name' => $subDT->name, 'image' => asset($subDT->image));
                    }
                } else {
                    $subs = [];
                }
                $institutes[] = array(
                    'id' => $value->id,
                    'institute_name' => $value->institute_name . '(' . $value->unique_id . ')',
                    'address' => $value->address.' '.$value->state. ' '.$value->city.' '.$value->pincode,
                    'logo' => asset($value->logo),
                    'subjects' => $subs
                );
            }
            $sdtls =  Student_detail::join('standard', 'standard.id', '=', 'students_details.standard_id')
                ->join('board', 'board.id', '=', 'students_details.board_id')
                ->leftjoin('stream', 'stream.id', '=', 'students_details.stream_id')
                ->join('medium', 'medium.id', '=', 'students_details.medium_id')
                ->where('students_details.student_id', $student_id)
                //->where('students_details.status', '=', '1')
                ->select(
                    'standard.name as standard',
                    'medium.name as medium',
                    'board.name as board',
                    'stream.name as stream'
                )->first();
                $parentsQY = Parents::join('users', 'parents.parent_id', '=', 'users.id')
                ->where('parents.student_id', $student_id)
                ->select('parents.parent_id', 'users.firstname', 'users.lastname', 
                'users.email', 'users.country_code', 'users.country_code_name',
                 'users.mobile', 'parents.relation')
                ->distinct()
                ->get();
            $parents_dt = [];
            foreach ($parentsQY as $parentsDT) {
                $fullName = $parentsDT->firstname . ' ' . $parentsDT->lastname;
                $cleanFullName = preg_replace('/\b(\w+)\b\s*(?=.*\b\1\b)/i', '', $fullName);
                $parents_dt[] = array(
                    'id'=>$parentsDT->parent_id,
                    'name' => $cleanFullName,
                    'email' => $parentsDT->email,
                    'country_code' => $parentsDT->country_code,
                    'country_code_name'=>$parentsDT->country_code_name,
                    'mobile' => $parentsDT->mobile,
                    'relation' => $parentsDT->relation
                );
            }
            if ($studentUser->image) {
                $img = $studentUser->image;
            } else {
                $img = asset('profile/no-image.png');
            }
            $userdetail = array(
                'id' => $studentUser->id,
                'unique_id' => $studentUser->unique_id . '',
                'name' => $studentUser->firstname . ' ' . $studentUser->lastname,
                'email' => $studentUser->email,
                'country_code' => (!empty($studentUser->country_code))?$studentUser->country_code:'+91',
                'country_code_name'=>$studentUser->country_code_name,
                'mobile' => $studentUser->mobile . '',
                'image' => $img . '',
                'dob' => $studentUser->dob . '',
                'address' => $studentUser->address . '',
                'standard' => $sdtls ? $sdtls->standard : '',
                'stream' => $sdtls ? $sdtls->stream : '',
                'medium' => $sdtls ? $sdtls->medium  : '',
                'board' => $sdtls ? $sdtls->board : '',
                'school' => $studentUser->school_name,
                'area' => $studentUser->area,
                'institutes' => $institutes,
                'parents' => $parents_dt,
                'country' => $studentUser ? $studentUser->country . '' : '',
                'state' => $studentUser ? $studentUser->state . '' : '',
                'city' => $studentUser ? $studentUser->city . '' : '',
                'pincode' => $studentUser ? $studentUser->pincode . '' : '',
            );
            return $this->response($userdetail, "Successfully fetch data.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    function removeDuplicateWords($str) {
        $words = array_unique(preg_split('/\s+/', $str));
        return implode(' ', $words);
    }
    public function student_edit_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'dob' => 'required|date|date_format:d-m-Y',
            'school_name' => 'required|string',
            'country_code' => 'required|string',
            'country_code_name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $user = Auth::user();
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->country_code = $request->country_code;
            $user->country_code_name = $request->country_code_name;
            $user->mobile = $request->mobile;
            $user->address = $request->address;
            $user->dob = $request->dob;
            $user->school_name = $request->school_name;
            $user->country = !empty($request->country)?$request->country:'';
            $user->state = !empty($request->state)?$request->state:'';
            $user->city = !empty($request->city)?$request->city:'';
            $user->pincode = !empty($request->pincode)?$request->pincode:'';
            $user->area = $request->area;
            if ($request->file('image')) {
                $iconFile = $request->file('image');
                $imagePath = $iconFile->store('profile', 'public');
                $user->image = $imagePath;
            }
            $user->save();
            if ($request->parents!='null' && !empty($request->parents)) {
                $parents = json_decode($request->parents, true);
                foreach ($parents as $parentData) {
                    // $emilfin = user::where('email', $parentData['email'])->first();
                    if ($parentData['firstname'] == '') {
                        return $this->response([], 'firstname Requied field are missing', false, 400);
                    } elseif ($parentData['lastname'] == '') {
                        return $this->response([], 'lastname Requied field are missing', false, 400);
                    } elseif ($parentData['email'] == '') {
                        return $this->response([], 'email Requied field are missing', false, 400);
                    } elseif ($parentData['mobile'] == '') {
                        return $this->response([], 'mobile Requied field are missing', false, 400);
                    } elseif ($parentData['relation'] == '') {
                        return $this->response([], 'relation Requied field are missing', false, 400);
                    } elseif ($parentData['country_code'] == '') {
                        return $this->response([], 'country_code Requied field are missing', false, 400);
                    }elseif ($parentData['country_code_name'] == '') {
                        return $this->response([], 'country_code_name Requied field are missing', false, 400);
                    }
                    else {
                        $user = User::where('email', $parentData['email'])->first();
                        if (!empty($user)) {
                            $user_data = User::where('id', $user->id);
                            $user_data->update([
                                'firstname' => $parentData['firstname'],
                                'lastname' => $parentData['lastname'],
                                'email' => $parentData['email'],
                                'country_code' => $parentData['country_code'],
                                'country_code_name' => $parentData['country_code_name'], 
                                'mobile' => $parentData['mobile'],
                                'role_type' => '5'
                            ]);
                        } else {
                            $user = User::create([
                                'firstname' => $parentData['firstname'],
                                'lastname' => $parentData['lastname'],
                                'email' => $parentData['email'],
                                'country_code' => $parentData['country_code'],
                                'country_code_name' => $parentData['country_code_name'],
                                'mobile' => $parentData['mobile'],
                                'role_type' => '5'
                            ]);
                        }
                        $parent_id = $user->id;
                        if (!empty($parent_id)) {
                            $parnsad = Parents::create([
                                'student_id' =>  auth()->id(),
                                'parent_id' => $parent_id,
                                'relation' => $parentData['relation'],
                                'verify' => '0',
                            ]);
                            if (empty($parnsad->id)) {
                                User::where('id', $parent_id)->delete();
                                return $this->response([], 'Data not added Successfuly.');
                            }
                        } else {
                            return $this->response([], 'Data not added Successfuly', false, 500);
                        }
                    }
                }
            }
            return $this->response([], "Updated Successfully!");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function exams_list(Request $request)
    {
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $institute_id = $request->institute_id;
            if ($request->child_id) {
                $student_id = $request->child_id;
            } else {
                $student_id = Auth::id();
            }
            $stdetails = Student_detail::where('student_id', $student_id)
                ->when($institute_id, function ($query, $institute_id) {
                    return $query->where('institute_id', $institute_id);
                })
                ->whereNull('deleted_at')
                ->get();
            $examlist = [];
            $tdate = Carbon::now()->format('Y-m-d');
            if (!empty($stdetails)) {
                foreach ($stdetails as $stdetail) {
                    $stream_id = $stdetail->stream_id;
                    $batch_id = $stdetail->batch_id;
                    $subjectIds = explode(',', $stdetail->subject_id);
                    $examsDELS = Exam_Model::join('subject', 'subject.id', '=', 'exam.subject_id')
                        ->join('standard', 'standard.id', '=', 'exam.standard_id')
                        ->join('institute_detail', 'institute_detail.id', '=', 'exam.institute_id')
                        ->whereDate('institute_detail.end_academic_year', '>=', $tdate)
                        ->where('exam.board_id', $stdetail->board_id)
                        ->where('exam.medium_id', $stdetail->medium_id)
                        ->where('exam.institute_id', $stdetail->institute_id)
                        ->when($stdetail->batch_id, function ($query, $batch_id) {
                            return $query->where('exam.batch_id', $batch_id);
                        })
                        ->where('exam.standard_id', $stdetail->standard_id)
                        ->when($stdetail->stream_id, function ($query, $stream_id) {
                            return $query->where('exam.stream_id', $stream_id);
                        })
                        ->whereIN('exam.subject_id', $subjectIds)
                        ->select('exam.*', 'subject.name as subject', 'standard.name as standard', 'institute_detail.institute_name', 'institute_detail.end_academic_year')
                        ->orderByDesc('exam.created_at')
                        ->get();
                        foreach ($examsDELS as $examsDT) {
                        $examlist[] = array(
                            'institute_id' => $examsDT->institute_id,
                            'institute_name' => $examsDT->institute_name,
                            'exam_id' => $examsDT->id,
                            'exam_title' => $examsDT->exam_title,
                            'total_mark' => intval($examsDT->total_mark),
                            'exam_type' => $examsDT->exam_type,
                            'subject' => $examsDT->subject,
                            'standard' => $examsDT->standard,
                            'date' => $examsDT->exam_date,
                            'time' => $examsDT->start_time . ' to ' . $examsDT->end_time,
                        );
                    }
                }
            }
            return $this->response($examlist, "Exams List");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function remove_institute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $institute_id = $request->institute_id;
            $student_id = Auth::id();
            $gette = Student_detail::where('student_id', $student_id)
                ->where('institute_id', $institute_id)
                ->first();
            if (!empty($gette)) {
                $remove = Student_detail::where('student_id', $student_id)
                    ->where('institute_id', $institute_id)
                    ->delete();

                $remove = Student_fees_model::where('student_id', $student_id)
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
    //exam result list for student and parents
    public function exam_result(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exam,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            if ($request->child_id) {
                $student_id = $request->child_id;
            } else {
                $student_id = AUth::id();
            }
            $tdate = Carbon::now()->format('Y-m-d');
            $result = [];
            $stdetails = Exam_Model::join('institute_detail', 'institute_detail.id', '=', 'exam.institute_id')
                ->where('exam.id', $request->exam_id)
                ->whereDate('institute_detail.end_academic_year', '>=', $tdate)
                ->first();
            if (!empty($stdetails)) {
                $resulttQY = Marks_model::join('exam', 'exam.id', '=', 'marks.exam_id')
                    ->join('subject', 'subject.id', '=', 'exam.subject_id')
                    ->where('marks.student_id', $student_id)
                    ->where('marks.exam_id', $request->exam_id)
                    ->select('marks.*', 'subject.name as subjectname', 'exam.subject_id', 'exam.total_mark', 'exam.exam_type', 'exam.exam_date', 'exam.exam_title')
                    ->orderByDesc('marks.created_at')->limit(3)->first();
                $highestMarks = Marks_model::where('exam_id', $request->exam_id)->max('mark');
                $attdence =  Attendance_model::where('institute_id', $stdetails->institute_id)
                ->where('student_id', $student_id)
                ->where('subject_id',$stdetails->subject_id)
                ->where('date',$stdetails->exam_date)
                ->first();
                if (!empty($resulttQY)) {
                    $result[] = array(
                        'subject' => $resulttQY->subjectname,
                        'title' => $resulttQY->exam_title . '(' . $resulttQY->exam_type . ')',
                        'total_marks' => $resulttQY->total_mark,
                        'achiveddmarks_marks' => $resulttQY->mark,
                        'date' => $resulttQY->exam_date,
                        'class_highest' => $highestMarks,
                        'attendance' =>(!empty($attdence)) ? $attdence->attendance : null,
                    );
                }
            }
            return $this->response($result, "Result Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!", false, 400);
        }
    }
    public function student_list(Request $request)
    {
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $query = Student_detail::join('users', 'students_details.student_id', '=', 'users.id')
                ->leftJoin('standard', 'students_details.standard_id', '=', 'standard.id')
                ->leftJoin('board', 'students_details.board_id', '=', 'board.id')
                ->leftJoin('medium', 'students_details.medium_id', '=', 'medium.id')
                ->leftJoin('stream', 'students_details.stream_id', '=', 'stream.id')
                ->leftJoin('batches', 'students_details.batch_id', '=', 'batches.id')
                ->select(
                    'users.*',
                    'students_details.student_id',
                    'standard.name as standard_name',
                    'stream.name as stream_name',
                    'students_details.standard_id',
                    'students_details.stream_id',
                    'students_details.batch_id',
                    'board.name as board_name',
                    'medium.name as medium_name'
                )
                ->where('students_details.institute_id', $request->institute_id)
                ->where('students_details.board_id', $request->board_id)
                ->where('students_details.medium_id', $request->medium_id)
                ->where('students_details.standard_id', $request->standard_id)
                ->where('students_details.status', '1')
                ->whereNotNull('students_details.batch_id')
                ->whereNull('students_details.deleted_at')
                ->whereNull('users.deleted_at');
            if (!empty($request->search)) {
                $query->where(function ($query) use ($request) {
                    $query->where('users.firstname', 'like', "%{$request->search}%")
                        ->orWhere('users.lastname', 'like', "%{$request->search}%")
                        ->orWhere('standard.name', 'like', "%{$request->search}%")
                        ->orWhere('board.name', 'like', "%{$request->search}%")
                        ->orWhere('medium.name', 'like', "%{$request->search}%");
                });
            }
            if (!empty($request->subject_id)) {
                $subjectIds = explode(',', $request->subject_id);
                foreach ($subjectIds as $subject) {
                    $query->whereRaw("FIND_IN_SET($subject, students_details.subject_id)");
                }
            }
            if (!empty($request->batch_id)) {
                $query->where('students_details.batch_id', $request->batch_id);
            }
            $student_data = $query->get()->toArray();
            if (!empty($student_data)) {
                $student_response = [];
                foreach ($student_data as $value) {
                    if (!empty($request->date && !empty($request->batch_id) && !empty($request->subject_id))) {
                        $attendance_records = Attendance_model::where('student_id', $value['id'])
                            ->whereDate('date', date('Y-m-d', strtotime($request->date)))
                            ->where('batch_id', $request->batch_id)
                            ->where('subject_id', $request->subject_id)
                            ->get()
                            ->toArray();
                    } else {
                        $attendance_records = Attendance_model::where('student_id', $value['id'])
                            ->where('batch_id', $request->batch_id)
                            ->where('subject_id', $request->subject_id)
                            ->get()
                            ->toArray();
                    }
                    $attendances = [];
                    foreach ($attendance_records as $attendance_record) {
                        $attendances[] = [
                            'date' => date('d-m-y', strtotime($attendance_record['date'])),
                            'attendance' => $attendance_record['attendance']
                        ];
                    }
                    $student_response[] = [
                        'student_id' => $value['id'],
                        'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                        'attendance' => $attendances,
                        'photo' => !empty($value['image']) ? url($value['image']) : url('profile/no-image.png'),
                        'board_name' => $value['board_name'] . '',
                        'medium_name' => $value['medium_name'] . '',
                    ];
                }
                $base = [
                    'standard' => $student_data[0]['standard_name'],
                    'stream' => $student_data[0]['stream_name'] . '',
                    'data' => $student_response,
                ];
                return $this->response($base, "Student Fetch Successfully");
            } else {
                return $this->response(null, "Data Not Found");
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
    //timetable list for student and parents
    public function timetable_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            if ($request->child_id) {
                $studentID = $request->child_id;
            } else {
                $studentID = auth::id();
            }
            $dateTime = new DateTime($request->date);
            $day = $dateTime->format('l');
            $daysidg = DB::table('days')->where('day',$day)->select('id')->first();
            $stdntdata = Student_detail::where('student_id', $studentID)
                ->where('institute_id', $request->institute_id)
                ->where('status', '=', '1')
                ->whereNull('deleted_at')
                ->first();
            $lectures = [];
            if ($stdntdata) {
                $todayslect = Timetables::join('subject', 'subject.id', '=', 'timetables.subject_id')
                    ->join('users', 'users.id', '=', 'timetables.teacher_id')
                    ->join('lecture_type', 'lecture_type.id', '=', 'timetables.lecture_type')
                    ->join('batches', 'batches.id', '=', 'timetables.batch_id')
                    ->where('timetables.batch_id', $stdntdata->batch_id)
                    ->where('timetables.day', $daysidg->id)
                    ->whereIN('timetables.subject_id', explode(",",$stdntdata->subject_id))
                    ->select(
                        'subject.name as subject',
                        'users.firstname',
                        'users.lastname',
                        'users.image',
                        'lecture_type.name as lecture_type_name',
                        'timetables.start_time',
                        'timetables.end_time',
                    )
                    ->orderBy('timetables.start_time', 'asc')
                    ->get();
                foreach ($todayslect as $todayslecDT) {
                    $lectures[] = array(
                        'subject' => $todayslecDT->subject,
                        'teacher' => $todayslecDT->firstname . ' ' . $todayslecDT->lastname,
                        'teacher_image' =>(!empty($todayslecDT->image)) ? asset($todayslecDT->image) : asset('profile/no-image.png'),
                        'lecture_type' => $todayslecDT->lecture_type_name,
                        'start_time' => $this->convertTo12HourFormat($todayslecDT->start_time),
                        'end_time' => $this->convertTo12HourFormat($todayslecDT->end_time),
                    );
                }
            }
            return $this->response($lectures, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
    public function announcementlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            if ($request->child_id) {
                $student_id = $request->child_id;
            } else {
                $student_id = Auth::id();
            }
            $announcement = [];
            $getstdntdata = Student_detail::where('student_id', $student_id)
                ->where('institute_id', $request->institute_id)
                ->where('status', '=', '1')
                ->whereNull('deleted_at')
                ->first();
            if (!empty($getstdntdata)) {
                $announcQY = announcements_model::where('institute_id', $request->institute_id)
                    ->where('batch_id', $getstdntdata->batch_id)
                    ->whereRaw("FIND_IN_SET('6', role_type)")
                    ->when($request->child_id ,function($query){
                        $query->orwhereRaw("FIND_IN_SET('5', role_type)");
                    })
                    ->orderByDesc('created_at')
                    ->get();

                if (!empty($announcQY)) {
                    foreach ($announcQY as $announcDT) {
                        $announcement[] = array(
                            'title' => $announcDT->title,
                            'desc' => $announcDT->detail,
                            'time' => $announcDT->created_at
                        );
                    }
                }
            }
            return $this->response($announcement, "Announcement List");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!.", false, 400);
        }
    }
    function add_edit_subject(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'student_id' => 'required|exists:users,id'

        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $selected_subject = Student_detail::where('institute_id', $request->institute_id)
            ->where('student_id', $request->student_id)
            ->first();
            $subject_ids = explode(',', $request->subject_id);
            $enter_subject = array_diff($subject_ids, explode(',',$selected_subject->subject_id));
            foreach($enter_subject as $subject_id){
                $subject_fees=Subject_sub::where('institute_id',$request->institute_id)->where('subject_id',$subject_id)->get();
                    $amount = 0;
                    foreach ($subject_fees as $value) {
                            $amount += $value->amount;
                    }
                    $studentFee = Student_fees_model::where('student_id', $request->student_id)->where('institute_id',$request->institute_id)->first();
                    $get_amount = $studentFee->total_fees; 
                    $studentFee = Student_fees_model::where('student_id', $request->student_id)->where('institute_id',$request->institute_id)->first();
                    if ($studentFee) {
                        $studentFee->update([
                            'subject_id' => $request->subject_id,
                            'total_fees' => (!empty($amount)) ? (float)$amount + $get_amount : 0.00,
                        ]);
                    }
            }
            $batch_ids = explode(',', $request->batch_id);
            $selected_batch_ids = [];
            foreach ($batch_ids as $batch) {
                    $selected_batch_ids[] = $batch;
            }
            if (count(array_unique($selected_batch_ids)) !== 1) {
                return $this->response([], "Please select all batch same!", false, 400); 
            }
            $teacherDetail = Student_detail::where('id', $selected_subject->id)->first();
            if ($teacherDetail) {
                $teacherDetail->update([
                    'batch_id' => !empty($selected_batch_ids[0]) ? $selected_batch_ids[0] : null,
                    'subject_id' => !empty($request->subject_id) ? $request->subject_id : null,
                    'status' => '1',
                ]);
            }
           return $this->response([], "Successfully Update Subject and batch"); 
        
    } catch (Exception $e) {
        return $this->response($e, "Something went wrong!!.", false, 400);
    }    
    }
}
