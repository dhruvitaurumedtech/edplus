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
                  // print_r($exam_wise_student_response);exit;


                $exam_student_result = [];
                $html = '';
                $all_subjects = []; // To store unique subjects

                foreach ($exam_wise_student_response as $student_index => $exam_student_value) {
                  $query = Student_detail::leftJoin('users', 'users.id', '=', 'students_details.student_id')
                  ->select(
                    'users.id',
                    'users.firstname',
                    'users.lastname',
                    'users.image',
                    'users.mobile',
                    'students_details.student_id',
                  )
                  ->where('students_details.status', '1')
                  ->whereNull('users.deleted_at')
                  ->whereNull('students_details.deleted_at')
                  ->groupBy(
                    'users.id',
                    'users.firstname',
                    'users.lastname',
                    'users.image',
                    'students_details.student_id',
                  )

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
                  // ->when(!empty($request->mobile), function ($query) use ($request) { 
                  //     return $query->where('users.mobile', $request->mobile);
                  // })
                  ->when(!empty($exam_student_value['id']), function ($query) use ($exam_student_value) {
                    return $query->where('students_details.student_id', $exam_student_value['id']);
                  });
                $student_response = $query->get()->toArray();
                $data_final = [];
                foreach ($student_response as $value) {
                  $student_id = $value['student_id'];

                  $student = User::where('id', $student_id)->first();
                  $student_name = $student->firstname . ' ' . $student->lastname;
                  // Fetch student fee and discount information

                  $student_fees = Student_fees_model::where('student_id',  $student_id)
                    ->where('institute_id', $request->institute_id)
                    ->first();


                  $discount = Discount_model::where('student_id',  $student_id)
                    ->where('institute_id', $request->institute_id)
                    ->first();

                  // Calculate revised fee and discount data
                  $revise_fee = 0;
                  $discount_data = '00.00';
                  if ($discount) {
                    if ($discount->discount_by == 'Rupee') {
                      $revise_fee = $discount->discount_amount;
                      $discount_data = !empty($discount->discount_amount) ? $discount->discount_amount . '.00' : '00.00';
                    } elseif ($discount->discount_by == 'Percentage') {
                      $revise_fee = $student_fees->total_fees * ($discount->discount_amount / 100);
                      $discount_data = !empty($discount->discount_amount) ? $discount->discount_amount . '%' : '0%';
                    }
                  }

                  // Fetch student payment history
                  $student_history = Fees_colletion_model::where('student_id',  $student_id)
                    ->where('institute_id', $request->institute_id)
                    ->orderBy('id', 'desc')
                    ->get();

                  // Prepare history and calculate paid amount
                  $history = [];
                  $paid_amount = 0;
                  foreach ($student_history as $value) {
                    $dateTime = Carbon::parse($value->created_at);
                    $time = $dateTime->format('Y-m-d h:i:s A');
                    // $history[] = [
                    //   'paid_amount' => $value->payment_amount,
                    //   'date' => $time,
                    //   'payment_mode' => $value->payment_type,
                    //   'invoice_no' => $value->invoice_no,
                    //   'transaction_id' => $value->transaction_id,
                    // ];
                    if (!empty($value->payment_amount)) {
                      $paid_amount += $value->payment_amount;
                    }
                  }

                  $remaing_amount = $student_fees->total_fees - $paid_amount - $revise_fee;
                  // Prepare the final data structure

                  $data_final[] = [
                    'student_name' => $student_name,
                    'student_fees' => !empty($student_fees->total_fees) ? $student_fees->total_fees . '.00' : '00.00',
                    'discount' => $discount_data,
                    'paid_amount' => !empty($paid_amount) ? $paid_amount . '.00' : '00.00',
                    'remaing_amount' => !empty($remaing_amount) ? $remaing_amount . '.00' : '00.00',
                    'status' => (!empty($remaing_amount)) ? 'pending' : 'paid',
                    // 'history' => $history,
                  ];
                }

                    // Fetch subject IDs for the current student
                    $subject_get = Student_detail::leftJoin('standard', 'standard.id', '=', 'students_details.standard_id')
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
                        ->when(!empty($exam_student_value['id']), function ($query) use ($exam_student_value) {
                            return $query->where('students_details.student_id', $exam_student_value['id']);
                        })
                        ->when(!empty($batch_id), function ($query) use ($batch_id) {
                            return $query->where('students_details.batch_id', $batch_id);
                        })
                        ->select('students_details.subject_id')
                        ->distinct()
                        ->get()
                        ->toArray();
                
                    // Collect all subject IDs from the result
                    foreach ($subject_get as $item) {
                        $ids = explode(',', $item['subject_id']); // Handle comma-separated subject IDs
                
                        // Fetch subject details for these IDs
                        $subject_response = Subject_model::whereIn('id', $ids)
                            ->distinct() // Ensure distinct subjects
                            ->get()
                            ->toArray();


                            

                          
                        // Merge unique subjects into $all_subjects array
                        foreach ($subject_response as $subject_array_value) {
                              // print_r($subject_response);exit;

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
                          ->when(!empty($subject_array_value['id']), function ($query) use ($subject_array_value) {
                            return $query->where('exam.subject_id', $subject_array_value['id']);
                          })
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
                            // 'subject.name as subject_name'
                          )
                          ->get()
                          ->toArray();
                        
      
                          $exam_result = [];
                         $exam_result = [];
                          $labels = '';
                          $chartBars = '';
                          $xAxisLabels = [];
                          $chart = '';
                          
                          // Loop through the exam responses
                          // print_r($exam_response);exit;
                          foreach ($exam_response as $exam_index => $exam_value) {
                              // Calculate percentage
                              // print_r($exam_response);exit;
                              $percentage = round(($exam_value['marks_obtained'] / $exam_value['total_marks']) * 100, 2);
                              
                              // Construct SVG labels
                              $labels .= '<g class="x-labels">
                                              <text x="' . (150 + ($exam_index * 100)) . '" y="630">' . date('d-m-Y', strtotime($exam_value['exam_date'])) . '</text>
                                          </g>';
                              
                              // Y-axis labels
                              $labels .= '<g class="y-labels">';
                              for ($i = 100; $i >= 0; $i -= 10) {
                                  $labels .= '<text x="42" y="' . (610 - ($i * 5)) . '">' . $i . '</text>';
                              }
                              $labels .= '</g>';
                          
                              // Polyline points for the gradient
                              $points = (50 + ($exam_index * 100)) . ',' . (400 - ($percentage * 4));
                              
                              // Add gradient and polyline
                              $labels .= '<linearGradient id="grad" x1="0%" y1="0%" x2="0%" y2="100%">
                                              <stop offset="0%" style="stop-color:rgba(99,224,238,.5);stop-opacity:1"></stop>
                                              <stop offset="100%" style="stop-color:white;stop-opacity:0"></stop>
                                          </linearGradient>';
                              $labels .= '<polyline fill="url(#grad)" stroke="#34becd" stroke-width="0" points="' . $points . '"></polyline>';
                              $labels .= '<polyline fill="none" stroke="#34becd" stroke-width="2" points="' . $points . '"></polyline>';
                          
                              // Start dot and current exam dot
                              $labels .= '<circle class="quiz-graph-start-dot" cx="50" cy="400" data-value="0" r="6"></circle>';
                              $labels .= '<circle class="quiz-graph-dot" cx="' . (50 + ($exam_index * 100)) . '" cy="' . (400 - ($percentage * 4)) . '" 
                                              data-value="' . $percentage . '" 
                                              q-title="Q' . ($exam_index + 1) . '" 
                                              answer-count="" 
                                              percent-value="' . $percentage . '%">
                                          </circle>';
                          
                              // Prepare data for the exam result
                              $exam_result[] = [
                                  'exam_name'  => $exam_value['exam_title'],
                                  'mark'       => $exam_value['marks_obtained'],
                                  'total_mark' => $exam_value['total_marks'],
                                  'percentage' => round($percentage, 2),
                                  'exam_date'  => $exam_value['exam_date'],
                              ];
                          }
                          
                          // Generate chart grid
                          if (!empty($exam_result)) {
                              $chart .= '<defs>
                                              <pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse">
                                                  <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#e5e5e5" stroke-width="1"></path>
                                              </pattern>
                                          </defs>
                                          <rect x="50" width="calc(800% - 50px)" height="600px" fill="url(#grid)" stroke="gray"></rect>
                                          <g class="label-title">
                                              <text x="-250" y="10" transform="rotate(-90)">Percentage</text>
                                          </g>
                                          <g class="label-title">
                                              <text x="50%" y="640">Date</text>
                                          </g>';
                          }
                          
                          // Create the HTML content
                          $htmlContent = '<html>
                                               <head>
                                                   <style>
                                                       .quiz-chartTip {
                                                           padding: 5px 10px;
                                                           border: 1px solid rgba(0,0,0,.1);
                                                           border-radius: 4px;
                                                           background-color: rgba(255,255,255,.9);
                                                           box-shadow: 3px 3px 10px rgba(0,0,0,.1);
                                                           position: absolute;
                                                           z-index: 50;
                                                           max-width: 250px;
                                                       }
                                                       .quiz-graph {
                                                           padding: 10px; 
                                                           height: 650px; 
                                                           width: 100%;
                                                       }
                                                       .quiz-graph .x-labels {
                                                           text-anchor: middle;
                                                           fill: #333; 
                                                           font-size: 12px;
                                                       }
                                                       .quiz-graph .y-labels {
                                                           text-anchor: end;
                                                           fill: #333; 
                                                           font-size: 12px;
                                                       }
                                                       .label-title {
                                                           text-anchor: middle;
                                                           text-transform: uppercase;
                                                           font-size: 14px;
                                                           fill: gray;
                                                           font-weight: bold; /* Bolder text for title */
                                                       }
                                                       .quiz-graph-dot, .quiz-graph-start-dot {
                                                           fill: rgba(0,112,210,1);
                                                           stroke-width: 2;
                                                           stroke: white;   
                                                       }
                                                   </style>
                                               </head>
                                               <body>
                                                   <svg version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="quiz-graph">
                                                       ' . $chart . $labels . '
                                                   </svg>
                                               </body>
                                           </html>';
                                           
                          $imagePath = public_path('student_report_graph/student_image_report_' . $board_index . $medium_index . $class_index . $standard_index . $batch_index . $student_index .$subject_array_value['id'] . '.png');
                    $directoryPath = public_path('student_report_graph');
                    if (!file_exists($directoryPath)) {
                      mkdir($directoryPath, 0755, true);
                    }
  
                    try {
                      Browsershot::html($htmlContent)
                        ->windowSize(1200, 750)
                        ->save($imagePath);
  
                      // return response()->download($imagePath);
                    } catch (\Exception $e) {
  
                      return response()->json(['error' => 'Failed to create image: ' . $e->getMessage()], 500);
                    }
                 }

                       
  
                    // Create complete HTML content
                    
  
  
  
                    
                            if (!isset($all_subjects[$subject_array_value['id']])) {
                                // Add the subject if it's not already in the array
                                $all_subjects[$subject_array_value['id']] = [
                                    'id' => $subject_array_value['id'],
                                    'subject_name' => $subject_array_value['name'],
                                    'exam_result' => $exam_result,
                                    
                                    
                                ];
                            }
                        
                    }

                   $attendance_data = Student_detail::leftJoin('attendance', 'attendance.student_id', '=', 'students_details.student_id')
                    ->when(!empty($request->institute_id), function ($query) use ($request) {
                      return $query->where('students_details.institute_id', $request->institute_id);
                    })
                    ->when(!empty($exam_student_value['id']), function ($query) use ($exam_student_value) {
                      return $query->where('attendance.student_id', $exam_student_value['id']);
                    })
                    ->when(!empty($request->month), function ($query) use ($request) {
                      return $query->whereRaw('MONTH(attendance.date) = ?', [$request->month]);
                    })
                    ->select(
                      DB::raw('SUM(CASE WHEN attendance.attendance = "P" THEN 1 ELSE 0 END) as total_present'),
                      DB::raw('SUM(CASE WHEN attendance.attendance = "A" THEN 1 ELSE 0 END) as total_absent')
                    )
                    ->first();
                    $total_lectures = Student_detail::leftJoin('attendance', 'attendance.student_id', '=', 'students_details.student_id')
                      ->select(DB::raw('COUNT(DISTINCT date) as total_lectures'))
                      ->when(!empty($request->institute_id), function ($query) use ($request) {
                        return $query->where('students_details.institute_id', $request->institute_id);
                      })
                      ->when(!empty($exam_student_value['id']), function ($query) use ($exam_student_value) {
                        return $query->where('attendance.student_id', $exam_student_value['id']);
                      })
                      ->when(!empty($request->month), function ($query) use ($request) {
                        return $query->whereRaw('MONTH(date) = ?', [$request->month]);
                      })
                      ->first();
                  $exam_student_result[] = [
                    'student_id' => $exam_student_value['id'],
                    'student_name' => $exam_student_value['firstname'] . '' . $exam_student_value['lastname'],
                    'subject_reponse'=>$all_subjects,
                    'fees_response' => $data_final,
                    'imagePath'=>$imagePath,
                    'total_lecture'=>$total_lectures['total_lectures'],
                    'total_present'=>$attendance_data['total_present'],
                    'total_absent'=>$attendance_data['total_absent']
               


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
      // print_r($data);exit;


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
