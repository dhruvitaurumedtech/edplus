<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Class_sub;
use App\Models\Institute_board_sub;
use App\Models\Institute_detail;
use App\Models\Institute_for_sub;
use App\Models\Medium_sub;
use App\Models\Parents;
use App\Models\Standard_sub;
use App\Models\Student_detail;
use App\Models\Subject_sub;
use App\Models\Teacher_model;
use App\Traits\ApiTrait;
use Exception;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
                        ->when(!empty($request->standard_id), function ($query) use ($request) {
                            return $query->where('students_details.standard_id', $request->standard_id);
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
            return $this->response([], "Something want wrong!.", false, 400);
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
            
            $teacherdata=Teacher_model::join('users','users.id','=','teacher_detail.teacher_id')
            ->join('standard','standard.id','=','teacher_detail.standard_id')
            ->join('board','board.id','=','teacher_detail.board_id')
            ->join('medium','medium.id','=','teacher_detail.medium_id')
            ->join('class','class.id','=','teacher_detail.class_id')
            ->join('subject','subject.id','=','teacher_detail.subject_id')
            ->select('users.*','board.name as board_name',
            'standard.name as standard_name','medium.name as medium_name','subject.name as subjectname',
            'class.name as class_name','teacher_detail.created_at')
            ->where('teacher_detail.institute_id',$request->institute_id)
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
            ->get()->toarray();

             $data = ['teacherdata'=>$teacherdata,'requestdata'=>$request];

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
        }catch(Exception $e){
            return $this->response([], "Something want wrong!.", false, 400);
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
        }catch(Exception $e){
            return $this->response([], "Something want wrong!.", false, 400);
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
        }catch(Exception $e){
            return $this->response([], "Something want wrong!.", false, 400);
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
            
            $data=Parents::join('users','users.id','=','parents.parent_id')
            //->join('subject','subject.id','=','teacher_detail.subject_id')
            ->select('users.*')
            ->where('parents.institute_id',$request->institute_id)
            ->when(!empty($request->mobile) ,function ($query) use ($request){
                return $query->where('users.mobile', $request->mobile);
            })
            ->when(!empty($request->email) ,function ($query) use ($request){
                return $query->where('users.email', $request->email);
            })
            ->when(!empty($request->name), function ($query) use ($request){
                return $query->where('users.firstname', $request->name);
            })
            ->get()->toarray();
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
        }catch(Exception $e){
            return $this->response([], "Something want wrong!.", false, 400);
        }
    }
   
    public function get_report_list()
    {
        try{
        $reportList = [
            ['id' => 1, 'name' => 'Student List Report', 'api' => 'institute/studentlist-pdf',
            'filters'=>['institute_id','board_id','medium_id','class_id','standard_id','batch_id','subject_id']],

            ['id' => 2, 'name' => 'Teacher List Report', 'api' => 'institute/teacher-reports',
            'filters'=>['institute_id','board_id','medium_id','class_id','standard_id','subject_id','creatdate']],

            ['id' => 3, 'name' => 'Parents Report', 'api' => 'institute/parents-reports',
            'filters'=>['name','institute_id','mobile','email','board_id','medium_id','class_id','standard_id','batch_id']],

            ['id' => 4, 'name' => 'Attendance Report', 'api' => 'institute/attendance-report-pdf',
            'filters'=>['institute_id','board_id','medium_id','class_id','standard_id','batch_id','subject_id','start_date','end_date','date','attendance_status','student_id']],

            ['id' => 5, 'name' => 'Result Report', 'api' => 'institute/result-report-pdf',
            'filters'=>['institute_id','board_id','medium_id','class_id','standard_id','batch_id','subject_id','exam_name','exam_date','student_id']],

            ['id' => 6, 'name' => 'Fees Report', 'api' => 'institute/fees-report-pdf',
            'filters'=>['board_id','medium_id','class_id','standard_id','batch_id','subject_id','status','mobile','student_id','date']],

            ['id' => 7, 'name' => 'Content list Report', 'api' => 'institute/content-list-video-pdf','filters'=>['institute_id']],
            ['id' => 8, 'name' => 'Timetable Report', 'api' => 'institute/timetable-reports','filters'=>['institute_id','teacher_id','board_id','medium_id','class_id','standard_id','batch_id']],
            ['id' => 9, 'name' => 'Dead Stock Report', 'api' => 'institute/dead-stock','filters'=>['institute_id','item_name']],
            ['id' => 10, 'name' => 'Staff list Report', 'api' => 'institute/staff-list','filters'=>['institute_id']],
            ['id' => 11, 'name' => 'Role Permission Report', 'api' => 'institute/role-wise-permission','filters'=>['institute_id','user_id']],
            ['id' => 12, 'name' => 'Teacher Profile Report', 'api' => 'institute/teacher-profile-report','filters'=>['institute_id','teacher_id','board_id','medium_id','class_id','standard_id']],
            ['id' => 13, 'name' => 'Institute Registered Report', 'api' => 'institute/institute-registered-detail','filters'=>['institute_id']],
            ['id' => 14, 'name' => 'Student Progress Report', 'api' => 'institute/studentprogress-report',
            'filters'=>['institute_id','board_id','medium_id','class_id','standard_id','batch_id','subject_id','start_date','end_date','date','attendance_status','student_id']],

        ];

            return $this->response($reportList, 'Successfully fetch ReportList.');
        }catch(Exception $e){
            return $this->response([], "Something want wrong!.", false, 400);
        }
    }
    function get_report_fields_list(Request $request){
        $validator = Validator::make($request->all(), [
            'report_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        if($request['report_id']==1){
                $response = [
                    ['display_name'=>'Student id', 'associate_name'=>''],
                    ['display_name'=>'First Name', 'associate_name'=>''],
                    ['display_name'=>'Last Name', 'associate_name'=>''],
                    ['display_name'=>'E-mail', 'associate_name'=>''],
                    ['display_name'=>'Board', 'associate_name'=>'board_id'],
                    ['display_name'=>'Batch', 'associate_name'=>'batch_id'],
                    ['display_name'=>'Class', 'associate_name'=>'class_id'],
                    ['display_name'=>'Medium', 'associate_name'=>'medium_id'],
                    ['display_name'=>'Standard', 'associate_name'=>'standard_id']
                ]
                ;
            return $this->response($response, 'Successfully fetch Studentlist Fields.');
        }
        if($request['report_id']==2){
            $response = [
                    ['display_name'=>'id', 'associate_name'=>''],
                    ['display_name'=>'First Name', 'associate_name'=>''],
                    ['display_name'=>'Last Name', 'associate_name'=>''],
                    ['display_name'=>'Board', 'associate_name'=>'board_id'],
                    ['display_name'=>'Medium', 'associate_name'=>'medium_id'],
                    ['display_name'=>'Standard', 'associate_name'=>'standard_id']
            ];
        return $this->response($response, 'Successfully fetch Teacherlist Fields.');
         }
         if($request['report_id']==3){
            $response = [
                ['display_name'=>'id', 'associate_name'=>''],
                ['display_name'=>'First Name', 'associate_name'=>''],
                ['display_name'=>'Last Name', 'associate_name'=>''],
                ['display_name'=>'Name', 'associate_name'=>'name'],
                ['display_name'=>'Mobile', 'associate_name'=>'mobile'],
                ['display_name'=>'Address', 'associate_name'=>'']
                
            ];
        return $this->response($response, 'Successfully fetch Parentlist Fields.');
         }
         if($request['report_id']==4){
            $response = [
                ['display_name'=>'Board', 'associate_name'=>'board_id'],
                ['display_name'=>'Medium', 'associate_name'=>'medium_id'],
                ['display_name'=>'Class', 'associate_name'=>'class_id'],
                ['display_name'=>'Standard', 'associate_name'=>'standard_id'],
                ['display_name'=>'Batch', 'associate_name'=>'batch_id'],
                ['display_name'=>'Subject', 'associate_name'=>'subject_id'],
                ['display_name'=>'Start Date', 'associate_name'=>'start_date'],
                ['display_name'=>'End Date', 'associate_name'=>'end_date'],
                ['display_name'=>'Student Name', 'associate_name'=>''],
                ['display_name'=>'Present Count', 'associate_name'=>''],
                ['display_name'=>'Absent Count', 'associate_name'=>'']
            ];
        return $this->response($response, 'Successfully fetch Attendancelist Fields.');
         }
         if($request['report_id']==5){
            $response = [
                ['display_name'=>'Board', 'associate_name'=>'board_id'],
                ['display_name'=>'Medium', 'associate_name'=>'medium_id'],
                ['display_name'=>'Class', 'associate_name'=>'class_id'],
                ['display_name'=>'Standard', 'associate_name'=>'standard_id'],
                ['display_name'=>'Batch', 'associate_name'=>'batch_id'],
                ['display_name'=>'Subject', 'associate_name'=>'subject_id'],
                ['display_name'=>'Exam Date', 'associate_name'=>'exam_date'],
                ['display_name'=>'Exam', 'associate_name'=>'exam_name'],
                ['display_name'=>'Student Name', 'associate_name'=>'student_id'],
                //['display_name'=>'subject', 'associate_name'=>''],
                ['display_name'=>'Total Marks', 'associate_name'=>''],
                ['display_name'=>'Mark', 'associate_name'=>'']

         ];
         return $this->response($response, 'Successfully fetch Resultreportlist Fields.');
        }
         if($request['report_id']==6){
            $response = [
                ['display_name'=>'Board', 'associate_name'=>'board_id'],
                ['display_name'=>'Medium', 'associate_name'=>'medium_id'],
                ['display_name'=>'Class', 'associate_name'=>'class_id'],
                ['display_name'=>'Standard', 'associate_name'=>'standard_id'],
                ['display_name'=>'Batch', 'associate_name'=>'batch_id'],
                ['display_name'=>'Student Name', 'associate_name'=>'student_id'],
                ['display_name'=>'Student Fees', 'associate_name'=>''],
                ['display_name'=>'Remaining Fees', 'associate_name'=>''],
                ['display_name'=>'Paid Fees', 'associate_name'=>''],
                ['display_name'=>'Status', 'associate_name'=>'status'],
                ['display_name'=>'History', 'associate_name'=>''],

            ];
             return $this->response($response, 'Successfully fetch Fees Report Fields.');
         }
         if($request['report_id']==7){
            $response = [
                ['display_name'=>'Board', 'associate_name'=>''],
                ['display_name'=>'Medium', 'associate_name'=>''],
                ['display_name'=>'Standard', 'associate_name'=>''],
                ['display_name'=>'Subject', 'associate_name'=>''],
                ['display_name'=>'Topic', 'associate_name'=>''],
                ['display_name'=>'Description', 'associate_name'=>''],
                ['display_name'=>'Chapter.No', 'associate_name'=>''],
                ['display_name'=>'Chapter Name', 'associate_name'=>'']

            ];
             return $this->response($response, 'Successfully fetch Contentlist Fields.');
         }
         if($request['report_id']==8){
            $response = [
                ['display_name'=>'Standard', 'associate_name'=>'standard_id'],
                ['display_name'=>'Batch', 'associate_name'=>'batch_id'],
                ['display_name'=>'First Name', 'associate_name'=>''],
                ['display_name'=>'Last Name', 'associate_name'=>''],
                ['display_name'=>'Day', 'associate_name'=>''],
                ['display_name'=>'Subject', 'associate_name'=>''],
                ['display_name'=>'Lecture Type', 'associate_name'=>''],
                ['display_name'=>'Class', 'associate_name'=>'']

            ];
             return $this->response($response, 'Successfully fetch Timetablelist Fields.');
         }
         if($request['report_id']==9){
            $response = [
                ['display_name'=>'Item Name', 'associate_name'=>'item_name'],
                ['display_name'=>'No Of Qty', 'associate_name'=>'']
            ];
             return $this->response($response, 'Successfully fetch Deadstock Fields.');
         }
         if($request['report_id']==10){
            $response = [
                ['display_name'=>'Institute Name', 'associate_name'=>''],
                ['display_name'=>'First Name', 'associate_name'=>''],
                ['display_name'=>'Last Name', 'associate_name'=>''],
                ['display_name'=>'Role', 'associate_name'=>''],
                ['display_name'=>'Mobile', 'associate_name'=>'']
            ];
             return $this->response($response, 'Successfully fetch Stafflist Fields.');
         }
         if($request['report_id']==11){
            $response = [
                ['display_name'=>'Board', 'associate_name'=>''],
                ['display_name'=>'Medium', 'associate_name'=>''],
                ['display_name'=>'Standard', 'associate_name'=>''],
                ['display_name'=>'Subject', 'associate_name'=>''],
                ['display_name'=>'Topic', 'associate_name'=>''],
                ['display_name'=>'Description', 'associate_name'=>''],
                ['display_name'=>'Chapter.No', 'associate_name'=>''],
                ['display_name'=>'Chapter Name', 'associate_name'=>'']

            ];
             return $this->response($response, 'Successfully fetch RolePermission  Fields.');
         }
         if($request['report_id']==12){
            $response = [
                ['display_name'=>'Profile Image', 'associate_name'=>''],
                ['display_name'=>'First Name', 'associate_name'=>''],
                ['display_name'=>'Last Name', 'associate_name'=>''],
                ['display_name'=>'Email', 'associate_name'=>''],
                ['display_name'=>'Mobile', 'associate_name'=>''],
                ['display_name'=>'DOB', 'associate_name'=>''],
                ['display_name'=>'Address', 'associate_name'=>''],
                ['display_name'=>'Country', 'associate_name'=>''],
                ['display_name'=>'State', 'associate_name'=>''],
                ['display_name'=>'City', 'associate_name'=>''],
                ['display_name'=>'Pincode', 'associate_name'=>''],
                ['display_name'=>'About Us', 'associate_name'=>''],
                ['display_name'=>'Education', 'associate_name'=>''],
                ['display_name'=>'Experience', 'associate_name'=>''],
                ['display_name'=>'Emergency Contact', 'associate_name'=>'']
                
            ];
             return $this->response($response, 'Successfully fetch TeacherProfile Fields.');
         }
         if($request['report_id']==13){
            $response = [
                ['display_name'=>'First Name', 'associate_name'=>''],
                ['display_name'=>'Last Name', 'associate_name'=>''],
                ['display_name'=>'Email', 'associate_name'=>''],
                ['display_name'=>'Address', 'associate_name'=>''],
                ['display_name'=>'Mobile', 'associate_name'=>''],
                ['display_name'=>'Institute For', 'associate_name'=>''],
                ['display_name'=>'Boards', 'associate_name'=>''],
                ['display_name'=>'Medium', 'associate_name'=>''],
                ['display_name'=>'Classes', 'associate_name'=>''],
                ['display_name'=>'Standards', 'associate_name'=>''],
                ['display_name'=>'Subjects', 'associate_name'=>'']
                
            ];
             return $this->response($response, 'Successfully fetch Institute Registerted Fields.');
         }
         if($request['report_id']==14){
            $response = [
                ['display_name'=>'Institute Name', 'associate_name'=>''],
                ['display_name'=>'Address', 'associate_name'=>''],
                ['display_name'=>'Student Name', 'associate_name'=>'student_id'],
                ['display_name'=>'Board', 'associate_name'=>'board_id'],
                ['display_name'=>'Standard', 'associate_name'=>'standard_id'],
                ['display_name'=>'Batch', 'associate_name'=>'batch_id'],
                ['display_name'=>'Subject', 'associate_name'=>'subject_id'],
                ['display_name'=>'Student Attendance', 'associate_name'=>''],
                ['display_name'=>'Student Fees', 'associate_name'=>'']

            ];
             return $this->response($response, 'Successfully fetch Student Progress Fields.');
         }
    
        
       
    }
    
}
