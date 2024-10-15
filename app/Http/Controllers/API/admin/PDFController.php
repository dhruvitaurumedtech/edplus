<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\board;
use App\Models\Class_sub;
use App\Models\DeadStock;
use App\Models\Institute_board_sub;
use App\Models\Institute_detail;
use App\Models\Institute_for_sub;
use App\Models\Medium_model;
use App\Models\Medium_sub;
use App\Models\Module;
use App\Models\Parents;
use App\Models\RoleHasPermission;
use App\Models\Roles;
use App\Models\Standard_model;
use App\Models\Standard_sub;
use App\Models\Student_detail;
use App\Models\Subject_model;
use App\Models\Subject_sub;
use App\Models\Teacher_model;
use App\Models\Timetables;
use App\Models\User;
use App\Models\UserHasRole;
use App\Models\Users_sub_emergency;
use App\Models\Users_sub_experience;
use App\Models\Users_sub_model;
use App\Models\Users_sub_qualification;
use App\Traits\ApiTrait;
use Exception;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;

class PDFController extends Controller
{
    use ApiTrait;
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $data=Student_detail::join('users','users.id','=','students_details.student_id')
                      ->join('standard','standard.id','=','students_details.standard_id')
                      ->join('class','class.id','=','students_details.class_id')
                      ->join('board','board.id','=','students_details.board_id')
                      ->join('batches','batches.id','=','students_details.batch_id')
                      ->join('medium','medium.id','=','students_details.medium_id')
                      ->select('users.*','board.name as board_name','standard.name as standard_name','medium.name as medium_name','class.name as class_name')
                      ->when(!empty($request->institute_id), function ($query) use ($request) {
                        return $query->where('students_details.institute_id', $request->institute_id);
                        })
                        ->when(!empty($request->class_id), function ($query) use ($request) {
                            return $query->where('students_details.class_id', $request->class_id);
                        })
                        ->when(!empty($request->medium_id), function ($query) use ($request) {
                            return $query->where('students_details.medium_id', $request->medium_id);
                        })
                        ->when(!empty($request->board_id), function ($query) use ($request) {
                            return $query->where('students_details.board_id', $request->board_id);
                        })
                        ->when(!empty($request->batch_id), function ($query) use ($request) {
                            return $query->where('students_details.batch_id', $request->batch_id);
                        })
                        ->when(!empty($request->subject_id), function ($query) use ($request) {
                            return $query->where('students_details.subject_id', 'LIKE', '%' . $request->subject_id . '%');
                        })
                        
                      ->get()->toarray();
        
        $pdf = PDF::loadView('pdf.studentlist', ['data' => $data])->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);;

        $folderPath = public_path('pdfs');

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $baseFileName = 'studentlist.pdf';
        $pdfPath = $folderPath . '/' . $baseFileName;

        $counter = 1;
        while (File::exists($pdfPath)) {
            $pdfPath = $folderPath . '/studentlist' . $counter . '.pdf'; 
            $counter++;
        }
        
        file_put_contents($pdfPath, $pdf->output());
        $pdfUrl = asset('pdfs/' . basename($pdfPath));
        return $this->response($pdfUrl);
        } catch (Exception $e) {
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }
     public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function teacher_reports(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $teacherDetails = Teacher_model::leftJoin('board', 'board.id', '=', 'teacher_detail.board_id')
            ->leftJoin('medium', 'medium.id', '=', 'teacher_detail.medium_id')
            ->leftJoin('class', 'class.id', '=', 'teacher_detail.class_id')
            ->leftJoin('standard', 'standard.id', '=', 'teacher_detail.standard_id')
            ->leftJoin('subject', 'subject.id', '=', 'teacher_detail.subject_id')
            ->leftJoin('users', 'users.id', '=', 'teacher_detail.teacher_id')
            ->where('teacher_detail.institute_id', $request->institute_id)
            ->when(!empty($request->subject_id) ,function ($query) use ($request){
                return $query->where('teacher_detail.subject_id', $request->subject_id);
            })
            ->when(!empty($request->class_id) ,function ($query) use ($request){
                return $query->where('teacher_detail.class_id', $request->class_id);
            })
            ->when(!empty($request->board_id), function ($query) use ($request){
                return $query->where('teacher_detail.board_id', $request->board_id);
            })
            ->when(!empty($request->medium_id) ,function ($query) use ($request){
                return $query->where('teacher_detail.medium_id', $request->medium_id);
            })
            ->when(!empty($request->creatdate) ,function ($query) use ($request){
                return $query->where('teacher_detail.created_at', $request->creatdate);
            })
            ->when(!empty($request->standard_id) ,function ($query) use ($request){
                return $query->where('teacher_detail.standard_id', $request->standard_id);
            })
            ->select(
                'board.id as board_id', 'board.name as board_name',
                'medium.id as medium_id', 'medium.name as medium_name',
                'class.id as class_id', 'class.name as class_name',
                'standard.id as standard_id', 'standard.name as standard_name',
                'subject.id as subject_id', 'subject.name as subject_name',
                'users.id as teacher_id', 'users.firstname', 'users.lastname'
            )
            ->distinct()
            ->get()
            ->toArray();

            $board_result = [];

            foreach ($teacherDetails as $detail) {
                $boardIndex = array_search($detail['board_id'], array_column($board_result, 'board_id'));
                
                if ($boardIndex === false) {
                    $boardIndex = count($board_result);
                    $board_result[$boardIndex] = [
                        'board_id' => $detail['board_id'],
                        'board_name' => $detail['board_name'],
                        'medium' => []
                    ];
                }

                $mediumIndex = array_search($detail['medium_id'], array_column($board_result[$boardIndex]['medium'], 'medium_id'));

                if ($mediumIndex === false) {
                    $mediumIndex = count($board_result[$boardIndex]['medium']);
                    $board_result[$boardIndex]['medium'][$mediumIndex] = [
                        'medium_id' => $detail['medium_id'],
                        'medium_name' => $detail['medium_name'],
                        'class' => []
                    ];
                }

                $classIndex = array_search($detail['class_id'], array_column($board_result[$boardIndex]['medium'][$mediumIndex]['class'], 'class_id'));

                if ($classIndex === false) {
                    $classIndex = count($board_result[$boardIndex]['medium'][$mediumIndex]['class']);
                    $board_result[$boardIndex]['medium'][$mediumIndex]['class'][$classIndex] = [
                        'class_id' => $detail['class_id'],
                        'class_name' => $detail['class_name'],
                        'standard' => []
                    ];
                }

                $standardIndex = array_search($detail['standard_id'], array_column($board_result[$boardIndex]['medium'][$mediumIndex]['class'][$classIndex]['standard'], 'standard_id'));

                if ($standardIndex === false) {
                    $standardIndex = count($board_result[$boardIndex]['medium'][$mediumIndex]['class'][$classIndex]['standard']);
                    $board_result[$boardIndex]['medium'][$mediumIndex]['class'][$classIndex]['standard'][$standardIndex] = [
                        'standard_id' => $detail['standard_id'],
                        'standard_name' => $detail['standard_name'],
                        'subject' => []
                    ];
                }

                $subjectIndex = array_search($detail['subject_id'], array_column($board_result[$boardIndex]['medium'][$mediumIndex]['class'][$classIndex]['standard'][$standardIndex]['subject'], 'subject_id'));

                if ($subjectIndex === false) {
                    $board_result[$boardIndex]['medium'][$mediumIndex]['class'][$classIndex]['standard'][$standardIndex]['subject'][] = [
                        'subject_id' => $detail['subject_id'],
                        'subject_name' => $detail['subject_name'],
                        'teachers' => [[
                            'id' => $detail['teacher_id'],
                            'name' => $detail['firstname'] . ' ' . $detail['lastname']
                        ]]
                    ];
                } else {
                    $board_result[$boardIndex]['medium'][$mediumIndex]['class'][$classIndex]['standard'][$standardIndex]['subject'][$subjectIndex]['teachers'][] = [
                        'id' => $detail['teacher_id'],
                        'name' => $detail['firstname'] . ' ' . $detail['lastname']
                    ];
                }
            }

            
            // $teacherdata=Teacher_model::join('users','users.id','=','teacher_detail.teacher_id')
            // ->join('standard','standard.id','=','teacher_detail.standard_id')
            // ->join('board','board.id','=','teacher_detail.board_id')
            // ->join('medium','medium.id','=','teacher_detail.medium_id')
            // ->join('class','class.id','=','teacher_detail.class_id')
            // ->join('subject','subject.id','=','teacher_detail.subject_id')
            // ->select('users.*','board.name as board_name',
            // 'standard.name as standard_name','medium.name as medium_name','subject.name as subjectname',
            // 'class.name as class_name','teacher_detail.created_at')
            // ->where('teacher_detail.institute_id',$request->institute_id)
            // ->when(!empty($request->subject_id) ,function ($query) use ($request){
            //     return $query->where('teacher_detail.subject_id', $request->subject_id);
            // })
            // ->when(!empty($request->class_id) ,function ($query) use ($request){
            //     return $query->where('teacher_detail.class_id', $request->class_id);
            // })
            // ->when(!empty($request->board_id), function ($query) use ($request){
            //     return $query->where('teacher_detail.board_id', $request->board_id);
            // })
            // ->when(!empty($request->medium_id) ,function ($query) use ($request){
            //     return $query->where('teacher_detail.medium_id', $request->medium_id);
            // })
            // ->when(!empty($request->creatdate) ,function ($query) use ($request){
            //     return $query->where('teacher_detail.created_at', $request->creatdate);
            // })
            // ->when(!empty($request->standard_id) ,function ($query) use ($request){
            //     return $query->where('teacher_detail.standard_id', $request->standard_id);
            // })
            // ->get()->toarray();

             $data = ['teacherdata'=>$board_result,'requestdata'=>$request];

                $pdf = PDF::loadView('pdf.teacherlist', ['data' => $data]);

                $folderPath = public_path('pdfs');

                if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
                }

                $baseFileName = 'teacherlist.pdf';
                $pdfPath = $folderPath . '/' . $baseFileName;

                $counter = 1;
                while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/teacherlist' . $counter . '.pdf'; 
                $counter++;
                }

                file_put_contents($pdfPath, $pdf->output());
                $pdfUrl = asset('pdfs/' . basename($pdfPath));
                return $this->response($pdfUrl);
        }catch(Exception $e){
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }

    public function parents_reports(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            if(!empty($request->batch_id)){
                $batchids = Student_detail::where('institute_id',$request->institute_id)
                ->where('batch_id',$request->batch_id)->pluck('student_id');
            }

            $parentsdata = Parents::join('users as parents_users', function($join) {
                $join->on('parents_users.id', '=', 'parents.parent_id');
            })
            ->join('users as students_users', function($join) {
                $join->on('students_users.id', '=', 'parents.student_id');
            })
            ->select('parents_users.*', 'students_users.firstname as student_name')
            ->where('parents.institute_id', $request->institute_id)
            ->when(!empty($request->mobile), function ($query) use ($request) {
                return $query->where('parents_users.mobile', $request->mobile);
            })
            ->when(!empty($request->email), function ($query) use ($request) {
                return $query->where('parents_users.email', $request->email);
            })
            ->when(!empty($request->name), function ($query) use ($request) {
                return $query->where('parents_users.firstname', $request->name);
            })
            ->when(!empty($request->batch_id), function ($query) use ($batchids) {
                return $query->whereIN('parents.student_id', $batchids);
            })
            ->get()
            ->toArray();

            $data = ['parents'=>$parentsdata,'requestdata'=>$request];
                $pdf = PDF::loadView('pdf.parentslist', ['data' => $data]);

                $folderPath = public_path('pdfs');

                if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
                }

                $baseFileName = 'parentslist.pdf';
                $pdfPath = $folderPath . '/' . $baseFileName;

                $counter = 1;
                while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/parentslist' . $counter . '.pdf'; 
                $counter++;
                }

                file_put_contents($pdfPath, $pdf->output());
                $pdfUrl = asset('pdfs/' . basename($pdfPath));
                return $this->response($pdfUrl);
        }catch(Exception $e){
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }


    public function instituteregisteredetail(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try{
            $ownerdata=Institute_detail::join('users','users.id','=','institute_detail.user_id')
            ->where('institute_detail.id',$request->institute_id)
            ->select('users.*')
            ->get()->toarray();

            $instituteforsub = Institute_for_sub::join('institute_for','institute_for.id','=','institute_for_sub.institute_for_id')
            ->where('institute_for_sub.institute_id',$request->institute_id)
            ->select('institute_for.*')->get()->toarray();

            $instituteboard = Institute_board_sub::join('board','board.id','=','board_sub.board_id')
            ->where('board_sub.institute_id',$request->institute_id)
            ->select('board.*')->get()->toarray();

            $institutemedium = Medium_sub::join('medium','medium.id','=','medium_sub.medium_id')
            ->where('medium_sub.institute_id',$request->institute_id)
            ->select('medium.*')->get()->toarray();

            $instituteclass = Class_sub::join('class','class.id','=','class_sub.class_id')
            ->where('class_sub.institute_id',$request->institute_id)
            ->select('class.*')->get()->toarray();

            $institutestandard = Standard_sub::join('standard','standard.id','=','standard_sub.standard_id')
            ->where('standard_sub.institute_id',$request->institute_id)
            ->select('standard.*')->get()->toarray();

            $institutesubject = Subject_sub::join('subject','subject.id','=','subject_sub.subject_id')
            ->where('subject_sub.institute_id',$request->institute_id)
            ->select('subject.*')->get()->toarray();

            $data=[
                'institute_detail'=>$ownerdata,
                'institute_for'=>$instituteforsub,
                'institute_board'=>$instituteboard,
                'institute_medium'=>$institutemedium,
                'institute_class'=>$instituteclass,
                'institute_standard'=>$institutestandard,
                'institute_subject'=>$institutesubject

            ];

                $pdf = PDF::loadView('pdf.institutedetail', ['data' => $data]);

                $folderPath = public_path('pdfs');

                if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
                }

                $baseFileName = 'institutedetail.pdf';
                $pdfPath = $folderPath . '/' . $baseFileName;

                $counter = 1;
                while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/institutedetail' . $counter . '.pdf'; 
                $counter++;
                }

                file_put_contents($pdfPath, $pdf->output());
                $pdfUrl = asset('pdfs/' . basename($pdfPath));
                return $this->response($pdfUrl);
        }catch(Exception $e){
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }

    public function timetable_reports(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            
            $timetable=Timetables::join('batches','batches.id','=','timetables.batch_id')
            ->join('users','users.id','=','timetables.teacher_id')
            ->join('subject','subject.id','=','timetables.subject_id')
            ->leftjoin('class_room','class_room.id','=','timetables.class_room_id')
            ->leftjoin('lecture_type','lecture_type.id','=','timetables.lecture_type')
            ->join('standard','standard.id','=','batches.standard_id')
            ->select('timetables.*','batches.batch_name','users.firstname','users.lastname','standard.name as standardname','subject.name as subjectname','lecture_type.name as lecturtype','class_room.name as classroom')
            ->where('batches.institute_id',$request->institute_id)
            ->when(!empty($request->batch_id) ,function ($query) use ($request){
                return $query->where('batches.id', $request->batch_id);
            })
            ->when(!empty($request->standard_id) ,function ($query) use ($request){
                return $query->where('batches.standard_id', $request->standard_id);
            })
            ->when(!empty($request->teacher_id) ,function ($query) use ($request){
                return $query->where('timetables.teacher_id', $request->teacher_id);
            })
            ->get()->toarray();
                $dayslt = DB::table('days')->get()->map(function($day) {
                    return [
                        'id' => $day->id,
                        'day' => $day->day,
                    ];
                })->toArray();
                $data = ['timetable'=>$timetable,'requestdata'=>$request,'days'=>$dayslt]; 
                $pdf = PDF::loadView('pdf.timetablereport', ['data' => $data]);

                $folderPath = public_path('pdfs');

                if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
                }

                $baseFileName = 'timetablereport.pdf';
                $pdfPath = $folderPath . '/' . $baseFileName;

                $counter = 1;
                while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/timetablereport' . $counter . '.pdf'; 
                $counter++;
                }

                file_put_contents($pdfPath, $pdf->output());
                $pdfUrl = asset('pdfs/' . basename($pdfPath));
                return $this->response($pdfUrl);
        }catch(Exception $e){
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }

    
    public function teacher_profile_report(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required',
        ]);

        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);

        try {
            $teacher_id = $request->teacher_id;            
            $userdetl = user::where('id', $teacher_id)->first();
            $user_sub=Users_sub_model::where('user_id', $teacher_id)->first();
            
            if ($userdetl->image) {
                $img = $userdetl->image;
            } else {
                $img = asset('profile/no-image.png');
            }

            $standardids = Teacher_model::where('teacher_id', $teacher_id)
                ->whereNull('deleted_at')->pluck('standard_id');

            $standards = Standard_model::whereIN('id', $standardids)->get();
            $stds = [];
            foreach ($standards as $stddata) {
                $stds[] = ['id' => $stddata->id, 'name' => $stddata->name];
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
            'experience'=>$expdata->experience];
          }
          $emergency = Users_sub_emergency::where('user_id',$teacher_id)->get();
          $emergency_contacts = [];
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
            'about_us' => $user_sub ? $user_sub->about_us .'' : '',
            'standard' => $stds,
            'education' => $education,
            'experience'=>$experience,
            'emergency_contacts' => $emergency_contacts
        );

        $pdf = PDF::loadView('pdf.teacher_profile', ['data' => $userdetail]);

        $folderPath = public_path('pdfs');

        if (!File::exists($folderPath)) {
        File::makeDirectory($folderPath, 0755, true);
        }

        $baseFileName = 'teacher_profile.pdf';
        $pdfPath = $folderPath . '/' . $baseFileName;

        $counter = 1;
        while (File::exists($pdfPath)) {
        $pdfPath = $folderPath . '/teacher_profile' . $counter . '.pdf'; 
        $counter++;
        }

        file_put_contents($pdfPath, $pdf->output());
        $pdfUrl = asset('pdfs/' . basename($pdfPath));
        return $this->response($pdfUrl);
        } catch (Exception $e) {
            return $this->response([], "Somthing went wrong!!", false, 400);
        }
    
    }

    public function general_timetable_reports(REquest $request){    
        
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            
            $timetable=Timetables::join('batches','batches.id','=','timetables.batch_id')
            ->join('users','users.id','=','timetables.teacher_id')
            ->join('subject','subject.id','=','timetables.subject_id')
            ->leftjoin('class_room','class_room.id','=','timetables.class_room_id')
            ->leftjoin('lecture_type','lecture_type.id','=','timetables.lecture_type')
            ->join('standard','standard.id','=','batches.standard_id')
            ->select('timetables.*','batches.batch_name','users.firstname','users.lastname','standard.name as standardname','subject.name as subjectname','lecture_type.name as lecturtype','class_room.name as classroom')
            ->where('batches.institute_id',$request->institute_id)
            ->when(!empty($request->batch_id) ,function ($query) use ($request){
                return $query->where('batches.id', $request->batch_id);
            })
            ->when(!empty($request->standard_id) ,function ($query) use ($request){
                return $query->where('batches.standard_id', $request->standard_id);
            })
            ->when(!empty($request->teacher_id) ,function ($query) use ($request){
                return $query->where('timetables.teacher_id', $request->teacher_id);
            })
            ->get()->toarray();
                $dayslt = DB::table('days')->get()->map(function($day) {
                    return [
                        'id' => $day->id,
                        'day' => $day->day,
                    ];
                })->toArray();
                $data = ['timetable'=>$timetable,'requestdata'=>$request,'days'=>$dayslt]; 
                $pdf = PDF::loadView('pdf.timetablereport', ['data' => $data]);

                $folderPath = public_path('pdfs');

                if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
                }

                $baseFileName = 'timetablereport.pdf';
                $pdfPath = $folderPath . '/' . $baseFileName;

                $counter = 1;
                while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/timetablereport' . $counter . '.pdf'; 
                $counter++;
                }

                file_put_contents($pdfPath, $pdf->output());
                $pdfUrl = asset('pdfs/' . basename($pdfPath));
                return $this->response($pdfUrl);
        }catch(Exception $e){
            return $this->response([], "Something went wrong!.", false, 400);
        }
    
    }

    public function dead_stock(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            
            $deastockqy=DeadStock::where('institute_id',$request->institute_id)
            ->when(!empty($request->item_name) ,function ($query) use ($request){
                return $query->where('deadstocks.item_name', $request->item_name);
            })
            ->get()->toarray();
                
                $data = ['deastock'=>$deastockqy,'requestdata'=>$request]; 
                $pdf = PDF::loadView('pdf.deadstockreport', ['data' => $data]);

                $folderPath = public_path('pdfs');

                if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
                }

                $baseFileName = 'deadstockreport.pdf';
                $pdfPath = $folderPath . '/' . $baseFileName;

                $counter = 1;
                while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/deadstockreport' . $counter . '.pdf'; 
                $counter++;
                }

                file_put_contents($pdfPath, $pdf->output());
                $pdfUrl = asset('pdfs/' . basename($pdfPath));
                return $this->response($pdfUrl);
        }catch(Exception $e){
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }

    public function staff_list(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $useid = Institute_detail::where('id',$request->institute_id)->select('user_id','institute_name')->first();
            $stafflistqy=Roles::join('user_has_roles','user_has_roles.role_id','=','roles.id')
            ->join('users','users.role_type','=','user_has_roles.role_id')
            ->where('user_has_roles.user_id',$useid->user_id)
            ->whereNotBetween('users.role_type', [1, 6])
            ->get()->toarray();
                $data = ['stafflist'=>$stafflistqy,'requestdata'=>$request,'institute_data'=>$useid]; 
                $pdf = PDF::loadView('pdf.stafflist', ['data' => $data]);

                $folderPath = public_path('pdfs');

                if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
                }

                $baseFileName = 'stafflist.pdf';
                $pdfPath = $folderPath . '/' . $baseFileName;

                $counter = 1;
                while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/stafflist' . $counter . '.pdf'; 
                $counter++;
                }

                file_put_contents($pdfPath, $pdf->output());
                $pdfUrl = asset('pdfs/' . basename($pdfPath));
                return $this->response($pdfUrl);
        }catch(Exception $e){
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }

    public function rolewisepermission(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $userole = [];
            if($request->user_id){
                $userole = user::where('id',$request->user_id)->select('role_type')->first();
            }
            $useid = Institute_detail::where('id', $request->institute_id)
            ->select('user_id', 'institute_name')
            ->first();
        
        $actions = Action::select('id', 'name')->get();
        $modules = Module::where('type', 2)
            ->with(['Features' => function ($query) {
                $query->select('id', 'module_id', 'feature_name');
            }])
            ->select('id', 'module_name')
            ->where('status', 1)
            ->get();
        
        $user_has_role = UserHasRole::join('roles', 'roles.id', '=', 'user_has_roles.role_id')
            ->where('user_has_roles.user_id', $useid->user_id) 
            ->when(!empty($userole) ,function ($query) use ($userole){ //user wise role and permission
                return $query->where('user_has_roles.role_id',$userole->role_type);
            })
            ->get();
            
        $permissiondata = [];
        
        foreach ($user_has_role as $userroles) {
            $permissionsIds = RoleHasPermission::where('user_has_role_id', $userroles->id)
                ->get(['feature_id', 'action_id']);
        
            $modulesDT = []; // Initialize array for each role
        
            foreach ($modules as $module) {
                $featureActions = [];
                foreach ($module->Features as $feature) {
                    $actionsDT = []; // Initialize array for each feature
        
                    foreach ($actions as $action) {
                        $hasPermission = $permissionsIds->contains(function ($permission) use ($feature, $action) {
                            return $permission->feature_id == $feature->id && $permission->action_id == $action->id;
                        });
        
                        $actionsDT[] = [
                            'id' => $action->id,
                            'name' => $action->name,
                            'has_permission' => $hasPermission
                        ];
                    }
        
                    $featureActions[] = [
                        'feature_id' => $feature->id,
                        'feature_name' => $feature->feature_name,
                        'actions' => $actionsDT
                    ];
                }
        
                $modulesDT[] = [
                    'module_id' => $module->id,
                    'module_name' => $module->module_name,
                    'permissions' => $featureActions
                ];
            }
        
            $permissiondata[] = [
                'role_id' => $userroles->role_id,
                'role_name' => $userroles->role_name,
                'modules' => $modulesDT
            ];
        }
        
        $data = [
            'roleandpermission' => $permissiondata,
            'requestdata' => $request,
            'institute_data' => $useid
        ];
                $pdf = PDF::loadView('pdf.roleswisepermission', ['data' => $data]);

                $folderPath = public_path('pdfs');
             
                if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
                }

                $baseFileName = 'roleswisepermission.pdf';
                $pdfPath = $folderPath . '/' . $baseFileName;

                $counter = 1;
                while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/roleswisepermission' . $counter . '.pdf'; 
                $counter++;
                }

                file_put_contents($pdfPath, $pdf->output());
                $pdfUrl = asset('pdfs/' . basename($pdfPath));
                // print_r()
                return $this->response($pdfUrl);
        }catch(Exception $e){
            return $this->response([], "Something went wrong!.", false, 400);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
