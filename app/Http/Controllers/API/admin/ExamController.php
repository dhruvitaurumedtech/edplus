<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Exam_Model;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use function PHPSTORM_META\map;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function add_exam(Request $request)
    {
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        $user_id = $request->user_id;
        $existingUser = User::where('token', $token)->where('id', $user_id)->first();
        if ($existingUser) {
            try {
                $validatedData = $request->validate([
                    'user_id' => 'required',
                    'institute_id' => 'required',
                    'exam_title' => 'required|string|max:255',
                    'total_mark' => 'required|integer',
                    'exam_type' => 'required|string|max:255',
                    'exam_date' => 'required|date',
                    'start_time' => 'required|date_format:H:i:s',
                    'end_time' => 'required|date_format:H:i:s|after:start_time',
                    'institute_for_id' => 'required',
                    'board_id' => 'required',
                    'medium_id' => 'required',
                    'class_id' => 'required',
                    'standard_id' => 'required',
                    'subject_id' => 'required',
                ]);
                $exam_data = Exam_Model::where('user_id', $validatedData['user_id'])
                    ->where('institute_id', $validatedData['institute_id'])
                    ->where('standard_id', $validatedData['standard_id'])
                    ->where('exam_date', Carbon::createFromFormat('d-m-Y', $validatedData['exam_date'])->format('Y-m-d'))
                    ->where('start_time', $validatedData['start_time'])
                    ->where('end_time', $validatedData['end_time'])
                    ->get()->toArray();


                if (empty($exam_data)) {
                    $exam = new Exam_Model;
                    $exam->user_id = $validatedData['user_id'];
                    $exam->institute_id = $validatedData['institute_id'];
                    $exam->exam_title = $validatedData['exam_title'];
                    $exam->total_mark = $validatedData['total_mark'];
                    $exam->exam_type = $validatedData['exam_type'];
                    $exam->exam_date = Carbon::createFromFormat('d-m-Y', $validatedData['exam_date']);
                    $exam->start_time = $validatedData['start_time'];
                    $exam->end_time = $validatedData['end_time'];
                    $exam->institute_for_id = $validatedData['institute_for_id'];
                    $exam->board_id = $validatedData['board_id'];
                    $exam->medium_id = $validatedData['medium_id'];
                    $exam->class_id = $validatedData['class_id'];
                    $exam->standard_id = $validatedData['standard_id'];
                    $exam->stream_id = (!empty($validatedData['stream_id'])) ? $validatedData['stream_id'] : '';
                    $exam->subject_id = $validatedData['subject_id'];
                    $exam->save();
                    if (!empty($exam->id)) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'Successfully Create Exam.',
                        ], 200, [], JSON_NUMERIC_CHECK);
                    } else {
                        return response()->json([
                            'status' => 400,
                            'message' => 'Not inserted.',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Already Created This standard Exam!.',
                    ]);
                }
            } catch (ValidationException $e) {
                return response()->json(['errors' => $e->validator->errors()->all()], 422);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }


    public function get_exam(Request $request)
    {
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        $user_id = $request->user_id;
        $existingUser = User::where('token', $token)->where('id', $user_id)->first();
        if ($existingUser) {
            $exam_list = DB::table('exam')
                ->leftJoin('institute_for', 'institute_for.id', '=', 'exam.institute_for_id')
                ->leftJoin('board', 'board.id', '=', 'exam.board_id')
                ->leftJoin('medium', 'medium.id', '=', 'exam.medium_id')
                ->leftJoin('class', 'class.id', '=', 'exam.class_id')
                ->leftJoin('standard', 'standard.id', '=', 'exam.standard_id')
                ->leftJoin('stream', 'stream.id', '=', 'exam.stream_id')
                ->leftJoin('subject', 'subject.id', '=', 'exam.subject_id')
                ->select(
                    'institute_for.name as institute_for_name',
                    'board.name as board_name',
                    'medium.name as medium_name',
                    'class.name as class_name',
                    'standard.name as standard_name',
                    'stream.name as stream_name',
                    'subject.name as subject_name',
                    'exam.*'
                )
                ->where('exam.institute_id', $request->institute_id)
                ->where('exam.user_id', $request->user_id)
                ->get()->toarray();
            if (!empty($exam_list)) {
                $exam_list_array = [];
                foreach ($exam_list as $key => $value) {
                    $exam_list_array[] = [
                        'exam_title' => $value->exam_title,
                        'exam_type' => $value->exam_type,
                        'exam_date' => $value->exam_date,
                        'start_time' => $value->start_time,
                        'end_time' => $value->end_time,
                        'institute_for' => $value->institute_for_name,
                        'board' => $value->board_name,
                        'medium' => $value->medium_name,
                        'class' => $value->class_name,
                        'standard' => $value->standard_name,
                        'stream' => $value->stream_name . '',
                        'subject' => $value->subject_name,
                    ];
                }
                return response()->json([
                    'success' => 200,
                    'message' => 'Successfully Fetch Exam List',
                    'data' => $exam_list_array
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'No Data Found.',
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
}
