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
use App\Models\Timetables;
use App\Traits\ApiTrait;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
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
            
            $timetable=Timetables::join('batches','batches.id','=','timetables.batch_id')
            ->join('users','users.id','=','timetables.teacher_id')
            ->join('standard','standard.id','=','batches.standard_id')
            ->select('batches.batch_name','users.firstname','users.lastname','standard.name as standardname')
            ->where('timetables.*','batches.institute_id',$request->institute_id)
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

                $dayslt = Days::get()->toarray();
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
        }catch(Exception $e){
            return $this->response([], "Something want wrong!.", false, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
