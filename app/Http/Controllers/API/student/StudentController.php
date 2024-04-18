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
use App\Models\Batches_model;
use Illuminate\Support\Facades\Mail;
use App\Models\Chapter;
use App\Models\Dobusinesswith_Model;
use App\Models\Subject_sub;
use App\Models\Class_model;
use App\Models\Exam_Model;
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
use App\Models\VideoAssignToBatch;
use Illuminate\Auth\Events\Verified;


use Illuminate\Broadcasting\Channel;

class StudentController extends Controller
{
    public function homescreen_student(Request $request)
    {
        $token = $request->header('Authorization');


        if (strpos($token, 'Bearer') === 0) {
            $token = substr($token, 7);
        }

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|integer',
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

            $user_id = $request->user_id;
            $search_keyword = $request->search;
            $perPage = $request->input('per_page', 10);
            $existingUser = User::where('token', $token)->where('id', $user_id)->first();
            if ($existingUser) {

                //banner
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
                    })
                    ->orderByDesc('created_at')
                    ->paginate($perPage);

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
                $searchhistory = Search_history::where('user_id', $user_id)->paginate($perPage);
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
                $requestnstitute = Student_detail::join('institute_detail', 'institute_detail.id', '=', 'students_details.institute_id')->where('students_details.status', '!=', '1')
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
                        'status' => $value->sstatus,
                    );
                }

                //join with

                $joininstitute = Institute_detail::where('status', 'active')
                    ->whereIn('id', function ($query) use ($user_id) {
                        $query->select('institute_id')
                            ->where('student_id', $user_id)
                            ->where('status', '=', '1')
                            ->from('students_details')
                            ->whereNull('deleted_at');
                    })
                    ->where('end_academic_year', '>=', now())
                    ->paginate($perPage); // ->where('end_academic_year', '>=', now())
                $join_with = [];
                foreach ($joininstitute as $value) {
                    $join_with[] = array(
                        'id' => $value->id,
                        'institute_name' => $value->institute_name . '(' . $value->unique_id . ')',
                        'address' => $value->address,
                        'logo' => asset($value->logo),
                    );
                }

                $parentsdt = Parents::where('student_id', $user_id)
                ->orderByDesc('created_at')
                ->get();

                $veryfy = [];
                foreach ($parentsdt as $checkvery) {
                    $veryfy[] = array('relation' => $checkvery->relation, 'verify' => $checkvery->verify);
                }
                if ($parentsdt->isEmpty()) {
                    $studentparents = '0';
                } else {
                    $studentparents = '1';
                }
                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch data.',
                    'data' => array(
                        'banner' => $banners_data,
                        'search_list' => $search_list,
                        'searchhistory_list' => $searchhistory_list,
                        'requested_institute' => $requested_institute,
                        'join_with' => $join_with,
                        'parents_detail' => $studentparents,
                        'parents_verification' => $veryfy
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


    public function student_searchhistory_add(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'title' => 'required|string',
            'institute_id' => 'required'
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

                $search_add = Search_history::create([
                    'user_id' => $request->input('user_id'),
                    'title' => $request->input('title'),
                    'institute_id' => $request->input('institute_id'),
                ]);

                return response()->json([
                    'success' => 200,
                    'message' => 'Serach History Added',
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
    //add parents details
    public function student_parents_details_add(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'parents.*.firstname' => 'required',
            'parents.*.lastname' => 'required',
            'parents.*.email' => 'required|email|unique:users,email',
            'parents.*.mobile' => 'required',
            'parents.*.relation' => 'required',
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

            $student_id = $request->input('user_id');
            $existingUser = User::where('token', $token)->where('id', $student_id)->first();
            if ($existingUser) {
                // Iterate over each parent in the request
                $parents = json_decode($request->parents, true);
                // dd($parents);
                // echo 'hello';
                // print_r($parents);exit;
                foreach ($parents as $parentData) {
                    // Create a user for each parent
                    $tomail = $parentData['email'];
                    if($parentData['firstname'] == '' || $parentData['lastname'] == '' || $parentData['email'] == '' || $parentData['mobile'] == '' || $parentData['relation'] == ''){
                        return response()->json([
                            'status' => 400,
                            'message' => 'Requied field are missing',
                        ], 400);
                    }else{
                        $user = User::create([
                            'firstname' => $parentData['firstname'],
                            'lastname' => $parentData['lastname'],
                            'email' => $parentData['email'],
                            'mobile' => $parentData['mobile'],
                            'role_type' => '5'
                        ]);
    
                        // Retrieve the ID of the newly created user
                        $userId = $user->id;
    
                        // Create a parent record associated with the user
                        if(!empty($userId)){
                            $parnsad = Parents::create([
                                'student_id' => $student_id,
                                'parent_id' => $userId,
                                'relation' => $parentData['relation'],
                                'verify' => '0',
                            ]);

                            if(empty($parnsad->id)){
                                User::where('id',$userId)->delete();
                            }else{
                                return response()->json([
                                    'success' => 500,
                                    'message' => 'Something went wrongg',
                                    'data' => [],
                                ], 500);
                            }
                        }else{
                            return response()->json([
                                'success' => 500,
                                'message' => 'Something went wronggg',
                                'data' => [],
                            ], 500);
                        }
                        
                        $data = [
                            'name' => $parentData['firstname'] . ' ' . $parentData['lastname'],
                            'email' => $tomail,
                            'id' => $parnsad->id
                        ];
    
                        Mail::to($tomail)->send(new WelcomeMail($data));
                    }
                    
                    
                }

                return response()->json(['success' => 200, 
                'message' => 'Parent details uploaded successfully',
                 'data' => []], 200);
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

    //verify email
    // public function verifyEmail(Request $request){
    //     $updateid = $request->updateid;
    //     $parent = Parents::where('id', $updateid)->first();
    //     if ($parent) {
    //     Parents::where('id',$updateid)->update(['verify'=>1]);
    //     return view('verification_success');
    //     } else {
    //         return view('verification_failure'); // Display a failure message to the user
    //     }
    // }

    public function verifyEmail($updateid)
    {
        $parent = Parents::find($updateid);

        if ($parent) {
            if ($parent->verify == 0) {
                $parent->verify = '1';
                $parent->save();

                return view('verification_success');
            } else {
                return view('already_verified');
            }
        } else {
            return view('verification_failure');
        }
    }

    public function student_add_institute_request(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|integer',
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

    //institute detail
    public function institute_detail(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'user_id' => 'required'
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

            if (strpos($token, 'Bearer') === 0) {
                $token = substr($token, 7);
            }

            $user_id = $request->input('user_id');
            $existingUser = User::where('token', $token)->where('id', $user_id)->first();
            if ($existingUser) {

                $institute_id = $request->institute_id;
                $institute_data = [];
                $boards = [];

                $institutedeta = Institute_detail::where('id', $institute_id)
                    ->select('id', 'institute_name', 'address', 'about_us')->first();

                $boards = board::join('board_sub', 'board_sub.board_id', '=', 'board.id')
                    ->where('board_sub.institute_id', $institute_id)->select('board.name')->get();

                $stdcount = Student_detail::where('institute_id', $institute_id)->count();
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


                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch data.',
                    'data' => array('institute_data' => $institutedetaa),
                ], 200, [], JSON_NUMERIC_CHECK);
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

    //student added detail
    public function student_added_detail(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
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
                $user_id = $request->user_id;
                $institute_id = $request->institute_id;


                //banner

                $bannerss = Banner_model::where('status', 'active')
                    ->Where('institute_id', $institute_id)
                    //->Where('user_id', $user_id)
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


                $todays_lecture[] = array('subject' => 'Chemistry', 'teacher' => 'Dianne Russell', 'time' => '03:30 To 05:00 PM');
                $announcQY = announcements_model::where('institute_id', $institute_id)
                ->where('batch_id', $existingUser->batch_id)
                ->whereRaw("FIND_IN_SET('6', role_type)")
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
                    ->select('marks.*', 'subject.name as subject', 'exam.subject_id', 'exam.total_mark', 'exam.exam_type', 'exam.exam_date', 'exam.exam_title')
                    ->orderByDesc('marks.created_at')->limit(3)->get();
                $highestMarks = $resultQY->max('marks');
                foreach ($resultQY as $resultDDt) {
                    $result[] = array(
                        'subject' => $resultDDt->subject,
                        'title' => $resultDDt->exam_title . '(' . $resultDDt->exam_type . ')',
                        'total_marks' => $resultDDt->total_marks,
                        'achiveddmarks_marks' => boolval($resultDDt->mark),
                        'date' => $resultDDt->exam_date,
                        'class_highest' => $highestMarks
                    );
                }


                $subdta = Student_detail::where('student_id', $user_id)
                    ->where('institute_id', $institute_id)->whereNull('deleted_at')->select('students_details.*')->first();

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

                    //upcoming exams
                    $stdetail = Student_detail::where('institute_id', $institute_id)->where('student_id', $user_id)->first();
                    $subjectIds = explode(',', $stdetail->subject_id);
                    $exams = Exam_Model::join('subject', 'subject.id', '=', 'exam.subject_id')
                        ->join('standard', 'standard.id', '=', 'exam.standard_id')
                        ->where('institute_id', $stdetail->institute_id)
                        ->where('batch_id', $stdetail->batch_id)
                        ->where('exam.board_id', $stdetail->board_id)
                        ->where('exam.medium_id', $stdetail->medium_id)
                        //->where('exam.class_id', $stdetail->class_id)
                        ->where('exam.standard_id', $stdetail->standard_id)
                        ->orWhere('exam.stream_id', $stdetail->stream_id)
                        ->whereIN('exam.subject_id', $subjectIds)
                        ->select('exam.*', 'subject.name as subject', 'standard.name as standard')
                        ->orderBy('exam.created_at', 'desc')
                        ->limit(3)
                        ->get();

                    foreach ($exams as $examsDT) {
                        $examlist[] = array(
                            'exam_title' => $examsDT->exam_title,
                            'total_mark' => $examsDT->total_mark,
                            'exam_type' => $examsDT->exam_type,
                            'subject' => $examsDT->subject,
                            'standard' => $examsDT->standard,
                            'date' => $examsDT->exam_date,
                            'time' => $examsDT->start_time . ' to ' . $examsDT->end_time,
                        );
                    }
                }
                $studentdata = array(
                    'banners_data' => $banners_data,
                    'todays_lecture' => $todays_lecture,
                    'announcement' => $announcement,
                    'upcoming_exams' => $examlist,
                    'subjects' => $subjects,
                    'result' => $result
                );


                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch data.',
                    'data' => $studentdata,
                ], 200, [], JSON_NUMERIC_CHECK);
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

    //subject wise chapert list
    public function subject_chapers(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'subject_id' => 'required',
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
                $user_id = $request->user_id;
                $subject_id = $request->subject_id;

                $chapers = [];
                $cptquy = Chapter::where('subject_id', $subject_id)->get();
                foreach ($cptquy as $chval) {
                    $subasid = Subject_model::where('id', $chval->subject_id)->select('base_table_id')->first();
                    $chapers[] = array(
                        "id" => $chval->id,
                        "base_table_id" => $subasid->base_table_id,
                        "subject_id" => $chval->subject_id,
                        "chapter_name" => $chval->chapter_name,
                        "chapter_no" => $chval->chapter_no,
                        "chapter_image" => asset($chval->chapter_image)
                    );
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch data.',
                    'data' => $chapers,
                ], 200, [], JSON_NUMERIC_CHECK);
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

    //topic videos
    public function topic_videos(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'subject_id' => 'required',
            'institute_id' => 'required',
            //'video_cayegory'=>'required',
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

            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser) {

                $user_id = $request->user_id;
                $subject_id = $request->subject_id;
                $chapter_id = $request->chapter_id;
                $institute_id = $request->institute_id;
                //$video_cayegory = $request->video_cayegory;

                $topics = [];
                $category = [];
                $catgry = Dobusinesswith_Model::join('video_categories', 'video_categories.id', '=', 'do_business_with.category_id')
                    ->select('do_business_with.id', 'do_business_with.name', 'video_categories.id as vid', 'video_categories.name as vname')
                    ->whereIn('do_business_with.id', function ($query) {
                        $query->select('topic.video_category_id')
                            ->from('topic')
                            ->groupBy('topic.video_category_id');
                    })
                    ->get();

                $batch_list = Batches_model::where('institute_id', $institute_id)
                    ->where('user_id', $user_id)
                    ->whereRaw("FIND_IN_SET($subject_id,subjects)")
                    ->select('*')
                    ->get();
                $batch_response = [];
                foreach ($batch_list as $value) {
                    $batch_response[] = [
                        'batch_id' => $value->id,
                        'batch_name' => $value->batch_name,
                    ];
                }


                foreach ($catgry as $catvd) {
                    $topicqry = Topic_model::join('subject', 'subject.id', '=', 'topic.subject_id')
                        ->join('chapters', 'chapters.id', '=', 'topic.chapter_id')
                        ->where('topic.subject_id', $subject_id)
                        //->where('topic.chapter_id', $chapter_id)
                        ->when($chapter_id, function ($query, $chapter_id) {
                            return $query->where('topic.chapter_id', $chapter_id);
                        })
                        ->where('topic.institute_id', $institute_id)
                        ->where('topic.video_category_id', $catvd->vid)
                        ->select('topic.*', 'subject.name as sname', 'chapters.chapter_name as chname')
                        ->orderByDesc('topic.created_at')
                        ->get();
                    foreach ($topicqry as $topval) {

                        if ($existingUser->role_type == 6) {
                            $batchID = Student_detail::where('institute_id', $institute_id)
                                ->where('student_id', $user_id)->first();
                            $std_batchidd = $batchID->batch_id;

                            $vidasbt = VideoAssignToBatch::where('batch_id', $std_batchidd)
                                ->where('video_id', $topval->id)
                                ->where('standard_id', $topval->standard_id)
                                ->where('chapter_id', $topval->chapter_id)
                                ->where('subject_id', $topval->subject_id)
                                ->select('id')->first();
                            if (!empty($vidasbt->id)) {
                                $topics[] = array(
                                    "id" => $topval->id,
                                    "topic_no" => $topval->topic_no,
                                    "topic_name" => $topval->topic_name . '',
                                    "topic_video" => asset($topval->topic_video),
                                    "subject_id" => $topval->subject_id,
                                    "subject_name" => $topval->sname,
                                    "chapter_id" => $topval->chapter_id,
                                    "chapter_name" => $topval->chname
                                );
                                $category[$catvd->name] = array('id' => $catvd->id, 'category_name' => $catvd->name, 'parent_category_id' => $catvd->vid, 'parent_category_name' => $catvd->vname, 'topics' => $topics);
                            }
                        } else {
                            $topics[] = array(
                                "id" => $topval->id,
                                "topic_no" => $topval->topic_no,
                                "topic_name" => $topval->topic_name . '',
                                "topic_video" => asset($topval->topic_video),
                                "subject_id" => $topval->subject_id,
                                "subject_name" => $topval->sname,
                                "chapter_id" => $topval->chapter_id,
                                "chapter_name" => $topval->chname
                            );
                            $category[$catvd->name] = array('id' => $catvd->id, 'category_name' => $catvd->name, 'parent_category_id' => $catvd->vid, 'parent_category_name' => $catvd->vname, 'topics' => $topics);
                        }
                    }
                    //$category[$catvd->name] = array('id' => $catvd->id, 'category_name' => $catvd->name, 'parent_category_id' => $catvd->vid, 'parent_category_name' => $catvd->vname, 'topics' => $topics);
                }
                if (!empty($chapter_id)) {
                    $response = [
                        'batch_list' => $batch_response,
                        'topics' => $category,
                    ];
                } else {
                    $response = [
                        'topics' => $category,
                    ];
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch data.',
                    'data' => $response,
                ], 200, [], JSON_NUMERIC_CHECK);
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

    public function profile_detail(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
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


            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser->token) {

                if ($existingUser->role_type == 6) {
                    $student_id = $request->user_id;
                } else {
                    $user_id = $request->user_id;
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
                            ->from('students_details');
                    })
                    ->when($institute_id, function ($query, $institute_id) {
                        return $query->where('id', $institute_id);
                    })
                    ->get();

                foreach ($joininstitute as $value) {
                    $substdnt = Student_detail::where('student_id', $student_id)
                        ->where('institute_id', $value->id)->first();

                    $subids = explode(',', $substdnt->subject_id);
                    $subjectids = Subject_model::whereIN('id', $subids)->get();
                    $subs = [];
                    foreach ($subjectids as $subDT) {
                        $subs[] = array('id' => $subDT->id, 'name' => $subDT->name);
                    }
                    $institutes[] = array(
                        'id' => $value->id,
                        'institute_name' => $value->institute_name . '(' . $value->unique_id . ')',
                        'address' => $value->address,
                        'logo' => asset($value->logo),
                        'subjects' => $subs //subject enroll with us
                    );
                }

                //
                $sdtls =  Student_detail::join('standard', 'standard.id', '=', 'students_details.standard_id')
                    ->join('board', 'board.id', '=', 'students_details.board_id')
                    ->leftjoin('stream', 'stream.id', '=', 'students_details.stream_id')
                    ->join('medium', 'medium.id', '=', 'students_details.medium_id')
                    ->where('students_details.student_id', $student_id)
                    ->where('students_details.status', '=', '1')
                    ->select('standard.name as standard', 'medium.name as medium', 'board.name as board', 'stream.name as stream')->first();

                //parents
                $parentsQY = Parents::join('users', 'parents.parent_id', '=', 'users.id')
                    ->where('parents.student_id', $student_id)->get();
                $parents_dt = [];
                foreach ($parentsQY as $parentsDT) {
                    $parents_dt[] = array(
                        'name' => $parentsDT->firstname . ' ' . $parentsDT->lastname,
                        'email' => $parentsDT->email,
                        'mobile' => $parentsDT->mobile,
                        'relation' => $parentsDT->relation
                    );
                }
                //
                if ($studentUser->image) {
                    $img = $studentUser->image;
                } else {
                    $img = asset('default.jpg');
                }
                $userdetail = array(
                    'id' => $studentUser->id,
                    'unique_id' => $studentUser->unique_id . '',
                    'name' => $studentUser->firstname . ' ' . $studentUser->lastname,
                    'email' => $studentUser->email,
                    'mobile' => $studentUser->mobile . '',
                    'image' => $img . '',
                    'dob' => $studentUser->dob,
                    'address' => $studentUser->address,
                    'standard' => $sdtls ? $sdtls->standard . '(' . $sdtls->stream . ')' : '',
                    'medium' => $sdtls ? $sdtls->medium . '(' . $sdtls->board . ')' : '',
                    'school' => $studentUser->school_name,
                    'area' => $studentUser->area,
                    'institutes' => $institutes,
                    'parents' => $parents_dt
                );

                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch data.',
                    'data' => $userdetail,
                ], 200, [], JSON_NUMERIC_CHECK);
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

    public function student_edit_profile(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
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
            $user_id = $request->user_id;
            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser) {
                if ($request->file('image')) {
                    $iconFile = $request->file('image');
                    $imagePath = $iconFile->store('profile', 'public');
                } else {
                    $imagePath = null;
                }


                $updt = User::where('id', $user_id)
                    ->update([
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        //'email'=>$request->email,
                        'mobile' => $request->mobile,
                        'address' => $request->address,
                        'dob' => $request->dob,
                        'school_name' => $request->school_name,
                        'image' => $imagePath,
                        'area' => $request->area
                    ]);

                return response()->json([
                    'success' => 200,
                    'message' => 'Updated Successfully!',
                    'data' => []
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

    public function exams_list(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            //'institute_id'=>'required',
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
            //$institute_id = $request->institute_id;
            $student_id = $request->user_id;

            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser) {
                // $stdetail = Student_detail::where('institute_id',$institute_id)
                // ->where('student_id',$student_id)
                // ->whereNull('deleted_at')
                // ->first();

                $stdetails = Student_detail::where('student_id', $student_id)
                    ->whereNull('deleted_at')
                    ->get();
                $examlist = [];
                if (!empty($stdetails)) {
                    foreach ($stdetails as $stdetail) {
                        $subjectIds = explode(',', $stdetail->subject_id);
                        $exams = Exam_Model::join('subject', 'subject.id', '=', 'exam.subject_id')
                            ->join('standard', 'standard.id', '=', 'exam.standard_id')
                            ->join('institute_detail', 'institute_detail.id', '=', 'exam.institute_id')
                            ->where('institute_detail.end_academic_year', '>=', now())
                            ->where('exam.board_id', $stdetail->board_id)
                            ->where('exam.medium_id', $stdetail->medium_id)
                            //->where('exam.class_id', $stdetail->class_id)
                            ->where('exam.standard_id', $stdetail->standard_id)
                            ->where('exam.institute_id', $stdetail->institute_id)
                            ->where('exam.batch_id', $stdetail->batch_id)
                            ->orWhere('exam.stream_id', $stdetail->stream_id)
                            ->whereIN('exam.subject_id', $subjectIds)

                            ->select('exam.*', 'subject.name as subject', 'standard.name as standard', 'institute_detail.institute_name', 'institute_detail.end_academic_year')
                            ->orderByDesc('exam.created_at')
                            ->get();

                        foreach ($exams as $examsDT) {
                            $examlist[] = array(
                                'institute_id' => $examsDT->institute_id,
                                'institute_name' => $examsDT->institute_name,
                                'exam_id' => $examsDT->id,
                                'exam_title' => $examsDT->exam_title,
                                'total_mark' => $examsDT->total_mark,
                                'exam_type' => $examsDT->exam_type,
                                'subject' => $examsDT->subject,
                                'standard' => $examsDT->standard,
                                'date' => $examsDT->exam_date,
                                'time' => $examsDT->start_time . ' to ' . $examsDT->end_time,
                            );
                        }
                    }
                }

                return response()->json([
                    'success' => 200,
                    'message' => 'Exams List',
                    'data' => $examlist
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

    //remove institute from student 
    public function remove_institute(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
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
            $institute_id = $request->institute_id;
            $student_id = $request->user_id;

            $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
            if ($existingUser) {
                $gette = Student_detail::where('student_id', $student_id)
                    ->where('institute_id', $institute_id)
                    ->first();
                if (!empty($gette)) {
                    $remove = Student_detail::where('student_id', $student_id)
                        ->where('institute_id', $institute_id)
                        ->delete();
                    return response()->json([
                        'success' => 200,
                        'message' => 'Institute Remove',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'success' => 200,
                        'message' => 'Data not found',
                        'data' => []
                    ], 200);
                }
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

    //exam result
    public function exam_result(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'exam_id' => 'required',
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

            $student_id = $request->user_id;
            $exam_id = $request->exam_id;

            $existingUser = User::where('token', $token)->where('id', $student_id)->first();
            if ($existingUser) {

                $stdetails = Exam_Model::join('institute_detail', 'institute_detail.id', '=', 'exam.institute_id')
                    ->where('exam.id', $exam_id)
                    ->where('institute_detail.end_academic_year', '>=', now())
                    ->first();

                $result = [];
                if (!empty($stdetails)) {

                    $resulttQY = Marks_model::join('exam', 'exam.id', '=', 'marks.exam_id')
                        ->join('subject', 'subject.id', '=', 'exam.subject_id')
                        ->where('marks.student_id', $student_id)
                        ->where('marks.exam_id', $exam_id)
                        ->select('marks.*', 'subject.name as subjectname', 'exam.subject_id', 'exam.total_mark', 'exam.exam_type', 'exam.exam_date', 'exam.exam_title')
                        ->orderByDesc('marks.created_at')->limit(3)->first();

                    $highestMarks = Marks_model::where('exam_id', $exam_id)->max('mark');


                    if (!empty($resulttQY)) {
                        $result[] = array(
                            'subject' => $resulttQY->subjectname,
                            'title' => $resulttQY->exam_title . '(' . $resulttQY->exam_type . ')',
                            'total_marks' => $resulttQY->total_mark,
                            'achiveddmarks_marks' => $resulttQY->mark,
                            'date' => $resulttQY->exam_date,
                            'class_highest' => $highestMarks
                        );
                    }
                }

                return response()->json([
                    'success' => 200,
                    'message' => 'Result Fetch Successfully',
                    'data' => $result
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
    public function student_list(Request $request)
    {
        $token = $request->header('Authorization');
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $user_id = $request->user_id;
        $institute_id = $request->institute_id;
        $batch_id = $request->batch_id;
        $subject_ids = $request->subject_id;
        $board_id = $request->board_id;
        $medium_id = $request->medium_id;
        $standard_id = $request->standard_id;


        $existingUser = User::where('token', $token)->where('id', $user_id)->first();
        if ($existingUser) {
            $query = Student_detail::join('users', 'students_details.student_id', '=', 'users.id')
                ->leftJoin('standard', 'students_details.standard_id', '=', 'standard.id')
                ->leftJoin('board', 'students_details.board_id', '=', 'board.id')
                ->leftJoin('medium', 'students_details.medium_id', '=', 'medium.id')
                ->leftJoin('stream', 'students_details.stream_id', '=', 'stream.id')
                ->leftJoin('attendance', 'students_details.student_id', '=', 'attendance.student_id')
                ->leftJoin('batches', 'students_details.batch_id', '=', 'batches.id')
                ->select('users.*', 'students_details.student_id', 'standard.name as standard_name', 'stream.name as stream_name', 'attendance.attendance', 'students_details.standard_id', 'students_details.stream_id', 'students_details.batch_id')
                // , 'students_details.subject_id'
                ->where('students_details.user_id', $user_id)
                // ->where('students_details.batch_id', $batch_id)
                ->where('students_details.institute_id', $institute_id)
                ->where('students_details.board_id', $board_id)
                ->where('students_details.medium_id', $medium_id)
                ->where('students_details.standard_id', $standard_id)
                ->whereNull('students_details.deleted_at');


            if (!empty($subject_ids)) {
                $query->whereIn('students_details.subject_id', function ($query) use ($subject_ids) {
                    $query->select('id')
                        ->from('subject')
                        ->whereIn('id', explode(',', $subject_ids));
                });
            }
            if (!empty($batch_id)) {
                $query->where('students_details.batch_id', $batch_id);
            }

            $student_data = $query->get()->toArray();
            if (!empty($student_data)) {
                foreach ($student_data as $value) {
                    $student_response[] = [
                        'student_id' => $value['id'],
                        'student_name' => $value['firstname'] . ' ' . $value['lastname'],

                        'attendance' => $value['attendance'] . ''
                    ];
                }
                $base = [
                    'standard' => $student_data[0]['standard_name'],
                    'stream' => $student_data[0]['stream_name'] . '',
                    'data' => $student_response,
                ];
                return response()->json([
                    'success' => 200,
                    'message' => 'Student Fetch Successfully',
                    'data' => $base
                ], 200);
            } else {
                return response()->json([
                    'success' => 400,
                    'message' => 'Data Not Found',
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }
    }
    // public function child_detail(Request $request)
    // {
    //     $parent_id = $request->parent_id;
    //     // $user_id = $request->user_id;
    //     $token = $request->header('Authorization');
    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }
    //     $existingUser = User::where('token', $token)->where('id', $parent_id)->first();
    //     if ($existingUser) {

    //         $student_details = Parents::join('users', 'users.id', '=', 'parents.student_id', 'left')
    //             ->join('institute_detail', 'institute_detail.user_id', '=', 'parents.student_id', 'left')
    //             ->select('users.*', 'institute_detail.institute_name')
    //             ->where('parents.parent_id', $parent_id)
    //             ->get();
    //         if (!empty($student_details)) {
    //             foreach ($student_details as $value) {
    //                 $response[] = [
    //                     'student_name' => $value->firstname . ' ' . $value->lastname,
    //                     'email' => $value->email,
    //                     'institute_name' => $value->institute_name
    //                 ];
    //             }
    //             return response()->json([
    //                 'success' => 200,
    //                 'message' => 'Student Fetch Successfully',
    //                 'data' => $response
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'success' => 400,
    //                 'message' => 'Data Not Found',
    //             ], 400);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ], 400);
    //     }
    // }
}
