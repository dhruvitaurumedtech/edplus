<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Discount_model;
use App\Models\Fees_colletion_model;
use App\Models\Institute_detail;
use App\Models\Student_detail;
use App\Models\Student_fees_model;
use App\Models\Subject_model;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Traits\ApiTrait;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use PDF; // Ensure this line is present

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;



use ConsoleTVs\Charts\Facades\Charts;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class StudentProgressPdfController extends Controller
{
  use ApiTrait;
  function studentprogress_report(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'institute_id' => 'required',
    ]);
    if ($validator->fails()) {
      return $this->response([], $validator->errors()->first(), false, 400);
    }
    try {
      // ini_set('memory_limit', '512M');
      $board_id = !empty($request->board_id) ? $request->board_id : '';
      $board_response = Student_detail::leftjoin('board', 'board.id', '=', 'students_details.board_id')
        ->when(!empty($request->institute_id), function ($query) use ($request) {
          return $query->where('students_details.institute_id', $request->institute_id);
        })
        ->when(!empty($board_id), function ($query) use ($board_id) {
          return $query->where('students_details.board_id', $board_id);
        })
        ->distinct()
        ->select('board.id as board_id', 'board.name as board_name')
        ->get()->toarray();
      $board_result = [];
      foreach ($board_response as $board_index => $board_value) {
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
          ->distinct()
          ->select('medium.id as medium_id', 'medium.name as medium_name')
          ->get()->toarray();
        $medium_result = [];
        foreach ($medium_response as $medium_index => $medium_value) {
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
            ->distinct()
            ->select('class.id as class_id', 'class.name as class_name')
            ->get()->toarray();
          $class_result = [];
          foreach ($class_response as $class_index => $class_value) {
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
              ->distinct()
              ->select('standard.id as standard_id', 'standard.name as standard_name')
              ->get()->toarray();

            $standard_result = [];
            foreach ($standard_response as $standard_index => $standard_value) {

              $standard_id = !empty($request->standard_id) ? $request->standard_id : $standard_value['standard_id'];
              $batch_id = !empty($request->batch_id) ? $request->batch_id : '';
              $batch_response = Student_detail::leftjoin('batches', 'batches.id', '=', 'students_details.batch_id')
                ->where('students_details.institute_id', $request->institute_id)
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
                ->select('batches.id as batch_id', 'batches.batch_name as batch_name')
                ->distinct()
                ->get()->toarray();
              $batch_result = [];
              foreach ($batch_response as $batch_index => $batch_value) {
                $batch_id = !empty($request->batch_id) ? $request->batch_id : $batch_value['batch_id'];

               
                // $subject_id = !empty($request->subject_id) ? explode(',', $request->subject_id) : explode(',',$item['subject_id']);
                
                //         $subject_response = Subject_model::whereIn('id', $subject_all_get)
                //         ->select('subject.id as subject_id', 'subject.name as subject_name')
                //         ->get()->toarray();  
                //         $subject_result= [];

                //           foreach($subject_response as  $subject_value){
                // $subject_id=!empty($request->subject_id) ? $request->subject_id : $subject_value['subject_id'];

                // $subject_id_array = explode(',',$subject_ids);
                // print_r($subject_id);




                $exam_wise_student_response = Student_detail::leftJoin('users', 'users.id', '=', 'students_details.student_id')
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

                  ->where('students_details.reject_count', '0')
                  ->whereNull('students_details.deleted_at')
                  ->select('users.*')
                  ->distinct()
                  ->get()
                  ->toArray();


                $exam_student_result = [];
                $html = '';
                foreach ($exam_wise_student_response as $student_index => $exam_student_value) {

                  $subject_get = Student_detail::leftjoin('standard', 'standard.id', '=', 'students_details.standard_id')
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
                  ->when(!empty($exam_student_value['student_id']), function ($query) use ($exam_student_value) {
                    return $query->where('students_details.student_id', $exam_student_value['student_id']);
                  })
                  ->when(!empty($batch_id), function ($query) use ($batch_id) {
                    return $query->where('students_details.batch_id', $batch_id);
                  })

                  ->select('students_details.subject_id')
                  ->distinct()
                  ->pluck('students_details.subject_id');

                $mergedArray = [];
                print_r($subject_get);exit;
                foreach ($subject_get as $item) {
                  // print_r($item);exit;
                  $subject_ids = explode(',', $item);
                  // print_r($subject_ids);exit; // Convert comma-separated IDs to an array
                  $subject_response = Subject_model::whereIn('id', $subject_ids)->groupBy('id')->get()->toArray(); // Fetch subjects
                  // print_r($subject_response);exit;


                  foreach($subject_response as $subject_array_value){
                    $exam_response = Student_detail::leftJoin('marks', 'marks.student_id', '=', 'students_details.student_id')
                    ->leftJoin('exam', 'exam.id', '=', 'marks.exam_id')
                    ->leftJoin('subject', 'subject.id', '=', 'exam.subject_id')
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
                    // ->when(!empty(explode(',',$item['subject_id'])), function ($query) use ($item) {
                    //   return $query->whereIn('exam.subject_id', explode(',',$item['subject_id']));
                    // })
                    ->when(!empty($exam_student_value['id']), function ($query) use ($exam_student_value) {
                      return $query->where('marks.student_id', $exam_student_value['id']);
                    })
                    ->when(!empty($request->exam_name), function ($query) use ($request) {
                      return $query->where('exam.exam_title', $request->exam_name);
                    })
                    ->when(!empty($request->start_date) && !empty($request->end_date), function ($query) use ($request) {
                      return $query->whereBetween('exam.exam_date', [$request->start_date, $request->end_date]);
                    })
                    ->where('students_details.reject_count', '0')
                    ->whereNull('students_details.deleted_at')
                    ->select(
                      'marks.mark as marks_obtained',
                      'exam.total_mark as total_marks',
                      'exam.exam_title',
                      'exam.exam_date',
                      'subject.name as subject_name'
                    )
                    ->get()
                    ->toArray();
                    // print_r($exam_response);exit;

                  $exam_result = [];
                  $chartBars = '';
                  $xAxisLabels = []; // Date X-axis labels

                  foreach ($exam_response as $exam_index => $exam_value) {
                

                    $exam_result[] = [
                      'exam_name'  => $exam_value['exam_title'],
                      'mark'       => $exam_value['marks_obtained'],
                      'total_mark' => $exam_value['total_marks'],
                      // 'percentage' => round($percentage, 2),
                      'exam_date'  => $exam_value['exam_date'],

                    ];
                  }
                  $subject_reponse[] = [
                                   'id'=>$subject_array_value['id'],
                                   'subject_name'=>$subject_array_value['name']];
               
                  }                                    

                   }
                  $exam_student_result[] = [
                    'student_id' => $exam_student_value['id'],
                    'student_name' => $exam_student_value['firstname'] . '' . $exam_student_value['lastname'],
                    'subject_reponse'=>$subject_reponse
                  //   'exam' => $exam_result,
                  //   'imagePath' => $imagePath,
                  //   'fees_response' => $data_final,
                  //   // 'imagePath2' => $imagePath2,



                  ];
                }
                $batch_result[] = [
                  'batch_id' => $batch_value['batch_id'],
                  'batch_name' => $batch_value['batch_name'],
                  'student' => $exam_student_result,


                ];
              }

              $standard_result[] = [
                'standard_id' => $standard_value['standard_id'],
                'standard_name' => $standard_value['standard_name'],
                'batch' => $batch_result
              ];
            }
            $class_result[] = [
              'class_id' => $class_value['class_id'],
              'class_name' => $class_value['class_name'],
              'standard' => $standard_result

            ];
          }

          $medium_result[] = [
            'medium_id' => $medium_value['medium_id'],
            'medium_name' => $medium_value['medium_name'],
            'class' => $class_result,

          ];
        }
        $board_result[] = [
          'board_id' => $board_value['board_id'],
          'board_name' => $board_value['board_name'],
          'medium' => $medium_result,
        ];
      }
      $response_institute = Institute_detail::where('id', $request->institute_id)->first();
      $institute = [
        'institute_name' => $response_institute->institute_name,
        'address' => $response_institute->address
      ];
      $data = ['board_result' => $board_result, 'institute' => $institute];
      print_r($data);exit;


      try {
        $pdf = FacadePdf::loadView('pdf.studentprogressreport', ['data' => $data])->setPaper('a4', 'portrait');
        $folderPath = public_path('pdfs');

        if (!File::exists($folderPath)) {
          File::makeDirectory($folderPath, 0755, true);
        }

        $baseFileName = 'studentprogressreport.pdf';
        $pdfPath = $folderPath . '/' . $baseFileName;

        $counter = 1;
        while (File::exists($pdfPath)) {
          $pdfPath = $folderPath . '/studentprogressreport' . $counter . '.pdf';
          $counter++;
        }

        $pdf->save($pdfPath);

        $pdfUrl = asset('pdfs/' . basename($pdfPath));

        return response()->json(['pdf_url' => $pdfUrl], 200);
      } catch (\Exception $e) {

        return response()->json(['error' => 'PDF Generation Failed: ' . $e->getMessage()], 500);
      }
    } catch (Exception $e) {
      return $this->response([], "Something want wrong!.", false, 400);
    }
  }
}
