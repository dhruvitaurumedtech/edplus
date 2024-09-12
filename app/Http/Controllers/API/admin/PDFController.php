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
use App\Models\Subject_model;
use App\Models\Subject_sub;
use App\Models\Teacher_model;
use App\Traits\ApiTrait;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        try {
            $board_id = !empty($request->board_id) ? $request->board_id :'';
            $board_response = Student_detail::leftjoin('board', 'board.id', '=', 'students_details.board_id')
                ->when(!empty($request->institute_id), function ($query) use ($request) {
                        return $query->where('students_details.institute_id', $request->institute_id);
                    })
                ->when(!empty($board_id), function ($query) use ($board_id) {
                            return $query->where('students_details.board_id', $board_id);
                        })
                // ->where('students_details.institute_id', $request->institute_id)
                // ->where('students_details.board_id', $board_id)
                ->distinct()
                ->select('board.id as board_id', 'board.name as board_name')
                ->get()->toarray();
            $board_result = [];
            foreach ($board_response as $board_value) {
                 $board_id = !empty($request->board_id) ? $request->board_id : $board_value['board_id'];
                 $medium_id = !empty($request->medium_id) ? $request->medium_id : '';
                 $medium_response = Student_detail::leftjoin('medium', 'medium.id', '=', 'students_details.medium_id')
                  ->when(!empty($request->institute_id), function ($query) use ($request) {
                    return $query->where('students_details.institute_id', $request->institute_id);
                  })
                  ->when(!empty($board_id), function ($query) use ($board_id) {
                    return $query->where('students_details.board_id', $board_id);
                    })
                  ->when(!empty($medium_id), function ($query) use ($medium_id) {
                    return $query->where('students_details.medium_id', $medium_id);
                  })
                    // ->where('students_details.board_id', $board_id)
                    // ->where('students_details.medium_id', $medium_id)
                    ->distinct()
                    ->select('medium.id as medium_id', 'medium.name as medium_name')
                    ->get()->toarray();
                $medium_result = [];
                foreach ($medium_response as $medium_value) {
                    $medium_id = !empty($request->medium_id) ? $request->medium_id : $medium_value['medium_id'];
                    $class_id = !empty($request->class_id) ? $request->class_id : '';
                    $class_response = Student_detail::leftjoin('class', 'class.id', '=', 'students_details.class_id')
                     ->when(!empty($request->institute_id), function ($query) use ($request) {
                        return $query->where('students_details.institute_id', $request->institute_id);
                      })
                      ->when(!empty($board_id), function ($query) use ($board_id) {
                        return $query->where('students_details.board_id', $board_id);
                        })
                      ->when(!empty($medium_id), function ($query) use ($medium_id) {
                        return $query->where('students_details.medium_id', $medium_id);
                      })
                      ->when(!empty($class_id), function ($query) use ($class_id) {
                        return $query->where('students_details.class_id', $class_id);
                      })
                    // ->where('students_details.institute_id', $request->institute_id)
                    // ->where('students_details.board_id', $board_id)
                    // ->where('students_details.medium_id', $medium_id)
                    // ->where('students_details.class_id', $class_id)
                    ->distinct()
                    ->select('class.id as class_id', 'class.name as class_name')
                    ->get()->toarray();
                    $class_result= [];
                    foreach($class_response as $class_value){
                        $class_id = !empty($request->class_id) ? $request->class_id : $class_value['class_id'];
                        $standard_id = !empty($request->standard_id) ? $request->standard_id : '';
                        $standard_response = Student_detail::leftjoin('standard', 'standard.id', '=', 'students_details.standard_id')
                          ->when(!empty($request->institute_id), function ($query) use ($request) {
                            return $query->where('students_details.institute_id', $request->institute_id);
                          })
                          ->when(!empty($board_id), function ($query) use ($board_id) {
                            return $query->where('students_details.board_id', $board_id);
                            })
                          ->when(!empty($medium_id), function ($query) use ($medium_id) {
                            return $query->where('students_details.medium_id', $medium_id);
                          })
                          ->when(!empty($class_id), function ($query) use ($class_id) {
                            return $query->where('students_details.class_id', $class_id);
                          })
                          ->when(!empty($standard_id), function ($query) use ($standard_id) {
                            return $query->where('students_details.standard_id', $standard_id);
                          }) 
                           // ->where('students_details.institute_id', $request->institute_id)
                            // ->where('students_details.board_id', $board_id)
                            // ->where('students_details.medium_id', $medium_id)
                            // ->where('students_details.class_id', $class_id)
                            // ->where('students_details.standard_id', $standard_id)
                            ->distinct()
                            ->select('standard.id as standard_id', 'standard.name as standard_name')
                            ->get()->toarray();
                            
                           $standard_result=[];
                           foreach($standard_response as $standard_value){

                            $standard_id = !empty($request->standard_id) ? $request->standard_id : $standard_value['standard_id'];
                            // print_r($standard_id);exit;
                            $batch_id = !empty($request->batch_id) ? $request->batch_id : '';
                            $batch_response = Student_detail::leftjoin('batches', 'batches.id', '=', 'students_details.batch_id')
                            ->when(!empty($request->institute_id), function ($query) use ($request) {
                                return $query->where('students_details.institute_id', $request->institute_id);
                              })
                              ->when(!empty($board_id), function ($query) use ($board_id) {
                                return $query->where('students_details.board_id', $board_id);
                                })
                              ->when(!empty($medium_id), function ($query) use ($medium_id) {
                                return $query->where('students_details.medium_id', $medium_id);
                              })
                              ->when(!empty($class_id), function ($query) use ($class_id) {
                                return $query->where('students_details.class_id', $class_id);
                              })
                              ->when(!empty($standard_id), function ($query) use ($standard_id) {
                                return $query->where('students_details.standard_id', $standard_id);
                              }) 
                              ->when(!empty($batch_id), function ($query) use ($batch_id) {
                                return $query->where('students_details.batch_id', $batch_id);
                              }) 
                            // ->where('students_details.institute_id', $request->institute_id)
                            // ->where('students_details.board_id', $board_id)
                            // ->where('students_details.medium_id', $medium_id)
                            // ->where('students_details.class_id', $class_id)
                            // ->where('students_details.standard_id', $standard_id)
                            // ->orwhere('students_details.batch_id', $batch_id)
                            ->distinct()
                            ->select('batches.id as batch_id', 'batches.batch_name as batch_name')
                            ->get()->toarray(); 
                            $batch_result=[];
                             foreach($batch_response as $batch_value){
                                
                                $batch_id = !empty($request->batch_id) ? $request->batch_id : $batch_value['batch_id'];
                                
                                $subject_get=Student_detail::leftjoin('standard', 'standard.id', '=', 'students_details.standard_id')
                                ->when(!empty($request->institute_id), function ($query) use ($request) {
                                    return $query->where('students_details.institute_id', $request->institute_id);
                                  })
                                  ->when(!empty($board_id), function ($query) use ($board_id) {
                                    return $query->where('students_details.board_id', $board_id);
                                    })
                                  ->when(!empty($medium_id), function ($query) use ($medium_id) {
                                    return $query->where('students_details.medium_id', $medium_id);
                                  })
                                  ->when(!empty($class_id), function ($query) use ($class_id) {
                                    return $query->where('students_details.class_id', $class_id);
                                  })
                                  ->when(!empty($standard_id), function ($query) use ($standard_id) {
                                    return $query->where('students_details.standard_id', $standard_id);
                                  }) 
                                  ->when(!empty($batch_id), function ($query) use ($batch_id) {
                                    return $query->where('students_details.batch_id', $batch_id);
                                  }) 
                                // ->where('students_details.institute_id', $request->institute_id)
                                // ->where('students_details.board_id', $board_id)
                                // ->where('students_details.medium_id', $medium_id)
                                // ->where('students_details.class_id', $class_id)
                                // ->where('students_details.standard_id', $standard_id)
                                // ->orwhere('students_details.batch_id', $batch_id)
                               
                                ->select('students_details.subject_id')
                                ->distinct()
                                ->pluck('students_details.subject_id'); 
                                $allValues = [];
                                foreach($subject_get as $subject_value)
                                {
                                    $items = explode(',', $subject_value);
                                    $allValues = array_merge($allValues, $items);
                                } 
                                $uniqueValues = array_unique($allValues);
                                sort($uniqueValues);
                                $commaSeparatedValues = implode(',', $uniqueValues);
                                
                                $subjectIds = explode(',', $commaSeparatedValues);

                                
                                $student_id = !empty($request->student_id) ? $request->student_id : '';
                                 
                                $final_subject_get=!empty($request->subject_id) ? explode(',',$request->subject_id) : $subjectIds;
                                   
                                    $student_response=Student_detail::leftjoin('users', 'users.id', '=', 'students_details.student_id')
                                    ->when(!empty($request->institute_id), function ($query) use ($request) {
                                        return $query->where('students_details.institute_id', $request->institute_id);
                                      })
                                      ->when(!empty($board_id), function ($query) use ($board_id) {
                                        return $query->where('students_details.board_id', $board_id);
                                        })
                                      ->when(!empty($medium_id), function ($query) use ($medium_id) {
                                        return $query->where('students_details.medium_id', $medium_id);
                                      })
                                      ->when(!empty($class_id), function ($query) use ($class_id) {
                                        return $query->where('students_details.class_id', $class_id);
                                      })
                                      ->when(!empty($standard_id), function ($query) use ($standard_id) {
                                        return $query->where('students_details.standard_id', $standard_id);
                                      }) 
                                      ->when(!empty($batch_id), function ($query) use ($batch_id) {
                                        return $query->where('students_details.batch_id', $batch_id);
                                      }) 
                                      ->when(!empty($student_id), function ($query) use ($student_id) {
                                        return $query->where('students_details.student_id', $student_id);
                                      }) 
                                    // ->where('students_details.institute_id', $request->institute_id)
                                    // ->where('students_details.board_id', $board_id)
                                    // ->where('students_details.medium_id', $medium_id)
                                    // ->where('students_details.class_id', $class_id)
                                    // ->where('students_details.standard_id', $standard_id)
                                    // ->orwhere('students_details.batch_id', $batch_id)
                                    // ->orwhere('students_details.student_id', $student_id)
                                    ->where(function($query) use ($final_subject_get) {
                                        foreach ($final_subject_get as $subjectId) {
                                            $query->orWhereRaw("FIND_IN_SET(?, students_details.subject_id)", [$subjectId]);
                                        }
                                    })
                                    ->whereNull('students_details.deleted_at')
                                    ->select('users.*')
                                    ->distinct()
                                    ->get()->toarray();
                                    $student_result=[];
                                    foreach($student_response as $student_value){

                                        $subject_response = Subject_model::whereIn('id', explode(',',$commaSeparatedValues))
                                        ->select('subject.id as subject_id', 'subject.name as subject_name')
                                        ->get()->toarray();  
                                        $subject_result= [];
                                        foreach($subject_response as $subject_value){
                                            $subject_result[] = [
                                                'subject_id' => $subject_value['subject_id'],
                                                'subject_name' => $subject_value['subject_name'],
                                                
                                            ];
                                            
                                       
                                    }
                                    $student_result[] = [
                                        'student_id' => $student_value['id'],
                                        'student_name' => $student_value['firstname'].' '.$student_value['lastname'],
                                        'subject' => $subject_result
                                    ];
                                    
                                    
                                }  
                                $batch_result[] = [
                                    'batch_id' => $batch_value['batch_id'],
                                    'batch_name' => $batch_value['batch_name'],
                                    'student'=>$student_result
                                ];
                             }
                            
                            //for subject get
                           
                            
                            
                            
                            $standard_result[] = [
                                    'standard_id' => $standard_value['standard_id'],
                                    'standard_name' => $standard_value['standard_name'],
                                    'batch'=>$batch_result
                                ];
                                

                           } 
                         

                        $class_result[] = [
                            'class_id' => $class_value['class_id'],
                            'class_name' => $class_value['class_name'],
                            'standard'=>$standard_result
                            
                        ];
                    }

                    $medium_result[] = [
                        'medium_id' => $medium_value['medium_id'],
                        'medium_name' => $medium_value['medium_name'],
                        'class' =>$class_result,
                        
                    ];

                }
                $board_result[] = [
                    'board_id' => $board_value['board_id'],
                    'board_name' => $board_value['board_name'],
                    'medium' => $medium_result,
                ];
            }
            
            $pdf = PDF::loadView('pdf.studentlistpdf', ['data' => $board_result])->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);;

            $folderPath = public_path('pdfs');

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $baseFileName = 'studentlistpdf.pdf';
            $pdfPath = $folderPath . '/' . $baseFileName;

            $counter = 1;
            while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/studentlistpdf' . $counter . '.pdf';
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
        try {

            $teacherdata = Teacher_model::join('users', 'users.id', '=', 'teacher_detail.teacher_id')
                ->join('standard', 'standard.id', '=', 'teacher_detail.standard_id')
                ->join('board', 'board.id', '=', 'teacher_detail.board_id')
                ->join('medium', 'medium.id', '=', 'teacher_detail.medium_id')
                ->join('class', 'class.id', '=', 'teacher_detail.class_id')
                ->join('subject', 'subject.id', '=', 'teacher_detail.subject_id')
                ->select(
                    'users.*',
                    'board.name as board_name',
                    'standard.name as standard_name',
                    'medium.name as medium_name',
                    'subject.name as subjectname',
                    'class.name as class_name',
                    'teacher_detail.created_at'
                )
                ->where('teacher_detail.institute_id', $request->institute_id)
                ->when(!empty($request->subject_id), function ($query) use ($request) {
                    return $query->where('teacher_detail.subject_id', $request->subject_id);
                })
                ->when(!empty($request->class_id), function ($query) use ($request) {
                    return $query->where('teacher_detail.class_id', $request->class_id);
                })
                ->when(!empty($request->board_id), function ($query) use ($request) {
                    return $query->where('teacher_detail.board_id', $request->board_id);
                })
                ->when(!empty($request->medium_id), function ($query) use ($request) {
                    return $query->where('teacher_detail.medium_id', $request->medium_id);
                })
                ->when(!empty($request->creatdate), function ($query) use ($request) {
                    return $query->where('teacher_detail.created_at', $request->creatdate);
                })
                ->when(!empty($request->standard_id), function ($query) use ($request) {
                    return $query->where('teacher_detail.standard_id', $request->standard_id);
                })
                ->get()->toarray();

            $data = ['teacherdata' => $teacherdata, 'requestdata' => $request];

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
        } catch (Exception $e) {
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
        try {
            if (!empty($request->batch_id)) {
                $batchids = Student_detail::where('institute_id', $request->institute_id)
                    ->where('batch_id', $request->batch_id)->pluck('student_id');
            }

            $parentsdata = Parents::join('users as parents_users', function ($join) {
                $join->on('parents_users.id', '=', 'parents.parent_id');
            })
                ->join('users as students_users', function ($join) {
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

            $data = ['parents' => $parentsdata, 'requestdata' => $request];
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
        } catch (Exception $e) {
            return $this->response([], "Something want wrong!.", false, 400);
        }
    }


    public function instituteregisteredetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $ownerdata = Institute_detail::join('users', 'users.id', '=', 'institute_detail.user_id')
                ->where('institute_detail.id', $request->institute_id)
                ->select('users.*')
                ->get()->toarray();

            $instituteforsub = Institute_for_sub::join('institute_for', 'institute_for.id', '=', 'institute_for_sub.institute_for_id')
                ->where('institute_for_sub.institute_id', $request->institute_id)
                ->select('institute_for.*')->get()->toarray();

            $instituteboard = Institute_board_sub::join('board', 'board.id', '=', 'board_sub.board_id')
                ->where('board_sub.institute_id', $request->institute_id)
                ->select('board.*')->get()->toarray();

            $institutemedium = Medium_sub::join('medium', 'medium.id', '=', 'medium_sub.medium_id')
                ->where('medium_sub.institute_id', $request->institute_id)
                ->select('medium.*')->get()->toarray();

            $instituteclass = Class_sub::join('class', 'class.id', '=', 'class_sub.class_id')
                ->where('class_sub.institute_id', $request->institute_id)
                ->select('class.*')->get()->toarray();

            $institutestandard = Standard_sub::join('standard', 'standard.id', '=', 'standard_sub.standard_id')
                ->where('standard_sub.institute_id', $request->institute_id)
                ->select('standard.*')->get()->toarray();

            $institutesubject = Subject_sub::join('subject', 'subject.id', '=', 'subject_sub.subject_id')
                ->where('subject_sub.institute_id', $request->institute_id)
                ->select('subject.*')->get()->toarray();

            $data = [
                'institute_detail' => $ownerdata,
                'institute_for' => $instituteforsub,
                'institute_board' => $instituteboard,
                'institute_medium' => $institutemedium,
                'institute_class' => $instituteclass,
                'institute_standard' => $institutestandard,
                'institute_subject' => $institutesubject

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
        } catch (Exception $e) {
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
        try {

            $data = Parents::join('users', 'users.id', '=', 'parents.parent_id')
                //->join('subject','subject.id','=','teacher_detail.subject_id')
                ->select('users.*')
                ->where('parents.institute_id', $request->institute_id)
                ->when(!empty($request->mobile), function ($query) use ($request) {
                    return $query->where('users.mobile', $request->mobile);
                })
                ->when(!empty($request->email), function ($query) use ($request) {
                    return $query->where('users.email', $request->email);
                })
                ->when(!empty($request->name), function ($query) use ($request) {
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
        } catch (Exception $e) {
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