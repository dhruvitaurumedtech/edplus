<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Discount_model;
use App\Models\Fees_colletion_model;
use App\Models\Institute_detail;
use App\Models\Student_detail;
use App\Models\Student_fees_model;
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
                  ->when(!empty($batch_id), function ($query) use ($batch_id) {
                    return $query->where('students_details.batch_id', $batch_id);
                  })

                  ->select('students_details.subject_id')
                  ->distinct()
                  ->pluck('students_details.subject_id');

                $mergedArray = [];
                foreach ($subject_get as $item) {
                  $mergedArray = array_merge($mergedArray, explode(',', $item));
                }

                $uniqueArray = array_unique($mergedArray);

                $uniqueArray = array_values($uniqueArray);

                $subject_id = !empty($request->subject_id) ? explode(',', $request->subject_id) : $uniqueArray;

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
                      $history[] = [
                        'paid_amount' => $value->payment_amount,
                        'date' => $time,
                        'payment_mode' => $value->payment_type,
                        'invoice_no' => $value->invoice_no,
                        'transaction_id' => $value->transaction_id,
                      ];
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
                      'history' => $history,
                    ];
                  }

                  // print_r($data_final);
                  //    exit;


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
                    ->get()
                    ->toArray();
                  $total_lectures = Student_detail::leftJoin('attendance', 'attendance.student_id', '=', 'students_details.student_id')
                    ->select(DB::raw('COUNT(DISTINCT date) as total_lectures'))
                    ->when(!empty($request->institute_id), function ($query) use ($request) {
                      return $query->where('students_details.institute_id', $request->institute_id);
                    })
                    ->when(!empty($request->student_id), function ($query) use ($request) {
                      return $query->where('attendance.student_id', $request->student_id);
                    })
                    ->when(!empty($request->month), function ($query) use ($request) {
                      return $query->whereRaw('MONTH(date) = ?', [$request->month]);
                    })
                    ->pluck('total_lectures');
                  // print_r($total_lectures[0]);exit;

                  // Calculate the present and absent percentages as degrees
                  $present_percentage_deg = ($attendance_data[0]['total_present'] / $total_lectures[0]) * 360;
                  $absent_percentage_deg = ($attendance_data[0]['total_absent'] / $total_lectures[0]) * 360;

                  // Embed the HTML for the pie chart and text dynamically
                  $html .= "
<!DOCTYPE html>
<html lang='en-US'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Attendance Chart for {$exam_student_value['firstname']}</title>
    <style>
        .piechart-container {
            width: 300px;
            height: 260px;
            border-radius: 50%;
            background-image: conic-gradient(
                #4CAF50 0deg {$present_percentage_deg}deg,    /* Present */
                #F44336 {$present_percentage_deg}deg 360deg   /* Absent */
            );
            margin: 0 auto;
            position: relative;
            font-family: 'Times New Roman', Times, serif;
        }

        .piechart-text {
            position: absolute;
            font-family: Arial, sans-serif;
            font-size: 18px;
            font-weight: bold;
            color: white;
            text-align: center;
        }

        .present {
            top: 20%;
            left: 55%;
        }

        .absent {
            top: 70%;
            left: 40%;
        }

        .labels-container {
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 20px;
            margin-top: 20px;
        }

        .labels-container .label {
            display: inline-block;
            margin: 0 15px;
        }

        .label-color-box {
            width: 15px;
            height: 15px;
            display: inline-block;
            margin-right: 5px;
        }

        .present-color {
            background-color: #4CAF50;
        }

        .absent-color {
            background-color: #F44336;
        }
        .black-color {
            background-color: black;
        }
    </style>
</head>
<body>

<h1></h1>
<br><br>
<div class='piechart-container'>
    <!-- Text labels inside the pie chart -->
    <div class='piechart-text present'></div>
    <div class='piechart-text absent'> </div>
</div>

<!-- Labels below the pie chart -->
<div class='labels-container'>
    <div class='label'>
        <span class='label-color-box present-color'></span>Present " . number_format(($present_percentage_deg / 360) * 100, 2) . "%
    </div>
    <div class='label'>
        <span class='label-color-box absent-color'></span>Absent " . number_format(($absent_percentage_deg / 360) * 100, 2) . "%
    </div>
    <div class='label'>
        <span class='label-color-box black-color'></span>Total Lectures " . $total_lectures[0] . "
    </div>
</div>

<br><br>

</body>
</html>";


                  // print_r($total_lectures);exit;
                  // Calculate total and percentages

                  $imagePath2 = public_path('student_report_graph/student_attendance_report_' . $board_index . $medium_index . $class_index . $standard_index . $batch_index . $student_index . '.png');
                  $directoryPath = public_path('student_report_graph');
                  if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                  }

                  try {
                    Browsershot::html($html)
                      ->windowSize(800, 400)
                      ->save($imagePath2);

                    // return response()->download($imagePath);
                  } catch (\Exception $e) {

                    return response()->json(['error' => 'Failed to create image: ' . $e->getMessage()], 500);
                  }


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
                    ->when(!empty($subject_id), function ($query) use ($subject_id) {
                      return $query->whereIn('exam.subject_id', $subject_id);
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
                      'exam.exam_date'
                    )
                    ->get()
                    ->toArray();

                  $exam_result = [];
                  $chartBars = '';
                  $xAxisLabels = []; // Date X-axis labels

                  foreach ($exam_response as $exam_index => $exam_value) {
                    $percentage = ($exam_value['marks_obtained'] / $exam_value['total_marks']) * 100;


                    $yAxisLabels = '';
                    for ($i = 100; $i >= 0; $i -= 10) {
                      $yAxisLabels .= "<div class='y-label'>{$i}</div>";
                    }
                    $xAxisLabels[] = "<div class='x-label'>" . date('d-m-Y', strtotime($exam_value['exam_date'])) . "</div>"; // Store exam date for x-axis

                    $chartBars .= "<div class='test-{$exam_index}' style='height: {$percentage}%; width: 70px; background-color: #4CAF50; text-align:center; color:#fff'>{$percentage}%<br>{$exam_value['marks_obtained']}/{$exam_value['total_marks']}</div>";


                    $exam_result[] = [
                      'exam_name'  => $exam_value['exam_title'],
                      'mark'       => $exam_value['marks_obtained'],
                      'total_mark' => $exam_value['total_marks'],
                      'percentage' => round($percentage, 2),
                      'exam_date'  => $exam_value['exam_date'],

                    ];
                  }

                  // Render the chart and labels
                  $chartContainer = "
                      <div class='chart-container' style='display:flex; align-items: flex-end; height: 300px; position: relative; margin: 20px; border-left: 2px solid #333; border-bottom: 2px solid #333; background-color: #fff;'>
                          <div class='y-axis-labels' style='position: absolute; left: -40px; top: 10; height: 100%; display: flex; flex-direction: column; justify-content: space-between;'>$yAxisLabels</div>
                          <div style='display:flex; gap: 80px; margin-left: 100px; position: relative; height:100%; align-items: end;'>$chartBars</div>
                        
                      </div>
                      <div class='x-axis-labels' style='position: absolute'>
                        <div class='x-axis-labels' style='display:flex; gap: 70px; margin-left: 120px; position: relative; height:100%; align-items: end;'>
                              " . implode('', $xAxisLabels) . "
                          </div>
                          </div>
                  ";

                  // Create complete HTML content
                  $htmlContent = "
                      <html>
                      <head>
                          <style>
                              body {
                                  font-family: 'Times New Roman', Times, serif;
                                  margin: 0;
                                  padding: 20px;
                                  background-color: #f4f4f4;
                              }
                              .chart-container {
                                  display: flex;
                                  align-items: flex-end;
                                  height: 300px;
                                  position: relative;
                                  margin: 20px;
                                  border-left: 2px solid #333;
                                  border-bottom: 2px solid #333;
                                  background-color: #fff;
                              }
                              .y-label {
                                  text-align: right;
                              }
                          </style>
                      </head>
                      <body>
                          $chartContainer
                      </body>
                      </html>
                  ";



                  $imagePath = public_path('student_report_graph/student_image_report_' . $board_index . $medium_index . $class_index . $standard_index . $batch_index . $student_index . '.png');
                  $directoryPath = public_path('student_report_graph');
                  if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                  }

                  try {
                    Browsershot::html($htmlContent)
                      ->windowSize(800, 400)
                      ->save($imagePath);

                    // return response()->download($imagePath);
                  } catch (\Exception $e) {

                    return response()->json(['error' => 'Failed to create image: ' . $e->getMessage()], 500);
                  }
                  $exam_student_result[] = [
                    'student_id' => $exam_student_value['id'],
                    'student_name' => $exam_student_value['firstname'] . '' . $exam_student_value['lastname'],
                    'exam' => $exam_result,
                    'imagePath' => $imagePath,
                    'fees_response' => $data_final,
                    // 'imagePath2' => $imagePath2,



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
