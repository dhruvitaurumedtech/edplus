<?php

namespace App\Http\Controllers\API\admin;
use Maatwebsite\Excel\Facades\Excel;


use App\Http\Controllers\Controller;
use App\Models\Batches_model;
use App\Models\board;
use App\Models\Class_model;
use App\Models\Exam_Model;
use App\Models\Institute_for_model;
use App\Models\Medium_model;
use App\Models\Standard_model;
use App\Models\Stream_model;
use App\Models\Subject_model;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiTrait;
use function PHPSTORM_META\map;
use Illuminate\Support\Facades\Auth;
use App\Exports\ExamsExport;
use Illuminate\Support\Str;





class ExamController extends Controller
{
    use ApiTrait;

    /**
     * Display a listing of the resource.
     */
    
    public function add_exam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'exam_title' => 'required|string|max:255',
            'total_mark' => 'required|integer',
            'exam_type' => 'required|string|max:255',
            'exam_date' => 'required|date_format:d-m-Y',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'board_id' => 'required',
            'medium_id' => 'required',
            'batch_id' => 'required',
            'standard_id' => 'required',
            'subject_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $existing_exam = Exam_Model::where([
                ['user_id', $request->user_id],
                ['institute_id', $request->institute_id],
                ['standard_id', $request->standard_id],
                ['exam_date', Carbon::createFromFormat('d-m-Y', $request->exam_date)->format('Y-m-d')],
                ['start_time', $request->start_time],
                ['end_time', $request->end_time],
                ['batch_id',$request->batch_id],

            ])->exists();

            if ($existing_exam) {
                return $this->response([], "Exam already exists for this standard!", false, 400);
            }

            $exam = new Exam_Model;
            $exam->fill($request->all());
            $exam->exam_date = Carbon::createFromFormat('d-m-Y', $request->exam_date);
            $exam->save();
            if (!empty($exam->id)) {
                return $this->response([], "Successfully created Exam.");
            } else {
                return $this->response([], "Failed to insert Exam.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }


    public function get_exam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $exam_list = Exam_Model::select(
                'exam.id as exam_id',
                'exam.exam_title',
                'exam.exam_type',
                'exam.exam_date',
                'exam.total_mark',
                DB::raw("TIME_FORMAT(exam.start_time, '%h:%i %p') as start_time"),
                DB::raw("TIME_FORMAT(exam.end_time, '%h:%i %p') as end_time"),
                'board.name as board',
                'medium.name as medium',
                'standard.name as standard',
                'stream.name as stream',
                'subject.name as subject',
                'batches.id as batch_id',
                'batches.batch_name'
            )
                ->leftJoin('board', 'board.id', '=', 'exam.board_id')
                ->leftJoin('medium', 'medium.id', '=', 'exam.medium_id')
                ->leftJoin('standard', 'standard.id', '=', 'exam.standard_id')
                ->leftJoin('stream', 'stream.id', '=', 'exam.stream_id')
                ->leftJoin('subject', 'subject.id', '=', 'exam.subject_id')
                ->leftJoin('batches', 'batches.id', '=', 'exam.batch_id')
                ->where('exam.institute_id', $request->institute_id)
                ->where('exam.standard_id', $request->standard_id)
                ->where('exam.board_id', $request->board_id)
                ->where('exam.medium_id', $request->medium_id)
                ->whereNull('exam.deleted_at')
                ->orderByDesc('exam.created_at')
                ->get()->toArray();

            if (!empty($exam_list)) {
                return $this->response($exam_list, "Successfully Fetch Exam List");
            } else {
                return $this->response([], "No Data Found.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }


    public function exam_report(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        
        try {
            $board = $request->board;
            $medium = $request->medium;
            $standard = $request->standard;
            $stream = $request->stream;
            $subject = $request->subject;
            $batch = $request->batch;


            $exam_list = Exam_Model::select(
                'exam.id as exam_id',
                'exam.exam_title',
                'exam.exam_type',
                'exam.exam_date',
                'exam.total_mark',
                DB::raw("TIME_FORMAT(exam.start_time, '%h:%i %p') as start_time"),
                DB::raw("TIME_FORMAT(exam.end_time, '%h:%i %p') as end_time"),
                'board.name as board',
                'medium.name as medium',
                'standard.name as standard',
                'stream.name as stream',
                'subject.name as subject',
                'batches.id as batch_id',
                'batches.batch_name'
            )
                ->leftJoin('board', 'board.id', '=', 'exam.board_id')
                ->leftJoin('medium', 'medium.id', '=', 'exam.medium_id')
                ->leftJoin('standard', 'standard.id', '=', 'exam.standard_id')
                ->leftJoin('stream', 'stream.id', '=', 'exam.stream_id')
                ->leftJoin('subject', 'subject.id', '=', 'exam.subject_id')
                ->leftJoin('batches', 'batches.id', '=', 'exam.batch_id')
                ->where('exam.institute_id', $request->institute_id)
                ->when($board, function ($query, $board) {
                return $query->where('exam.board_id', $board);
                })
                ->when($medium, function ($query, $medium) {
                return $query->where('exam.medium_id', $medium);
                })
                ->when($standard, function ($query, $standard) {
                return $query->where('exam.standard_id', $standard);
                })
                ->when($stream, function ($query, $stream) {
                return $query->where('exam.stream_id', $stream);
                })
                ->when($subject, function ($query, $subject) {
                return $query->where('exam.subject_id', $subject);
                })
                ->when($batch, function ($query, $batch) {
                return $query->where('exam.batch_id', $batch);
                })    
                ->where('exam.user_id', Auth::id())
                ->whereNull('exam.deleted_at')
                ->orderByDesc('exam.created_at')
                ->get();

              
                $export = new ExamsExport($exam_list);
            if (!empty($exam_list)) {
                
                Excel::store($export, 'exam_report'.Str::random(10).'.csv', 'local');
                return $this->response($exam_list, "Successfully Fetch Exam List");
            } else {
                return $this->response([], "No Data Found.", false, 400);
            }
            
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }

    }

    public function delete_exam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exam,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $exam  = Exam_Model::where('id', $request->exam_id)->delete();
            return $this->response([], "Successfully Deleted Exam.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }


    public function edit_exam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exam,id',
            'institute_id' => 'required',
            'exam_title' => 'required|string|max:255',
            'total_mark' => 'required|integer',
            'exam_type' => 'required|string|max:255',
            'exam_date' => 'required|date_format:d-m-Y',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'board_id' => 'required',
            'medium_id' => 'required',
            'batch_id' => 'required',
            'standard_id' => 'required',
            'subject_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $exam = Exam_Model::where('id', $request->exam_id)->first();
            if (!empty($exam)) {
                $exam->user_id = $request->user_id;
                $exam->institute_id = $request->institute_id;
                $exam->batch_id = $request->batch_id;
                $exam->exam_title = $request->exam_title;
                $exam->total_mark = $request->total_mark;
                $exam->exam_type = $request->exam_type;
                $exam->exam_date = Carbon::createFromFormat('d-m-Y', $request->exam_date);
                $exam->start_time = $request->start_time;
                $exam->end_time = $request->end_time;
                $exam->board_id = $request->board_id;
                $exam->medium_id = $request->medium_id;
                $exam->standard_id = $request->standard_id;
                $exam->stream_id = $request->stream_id;
                $exam->subject_id = $request->subject_id;
                $exam->save();
                return $this->response([], "Successfully Updated Exam.");
            } else {
                return $this->response([], "Exam Not Found.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }

    public function update_exam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
            'exam_title' => 'required|string|max:255',
            'total_mark' => 'required|integer',
            'exam_type' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'board_id' => 'required',
            'medium_id' => 'required',
            'batch_id' => 'required',
            'standard_id' => 'required',
            'subject_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $exam_date = date('Y-m-d',strtotime($request->exam_date));
            
            $existing_exam = Exam_Model::where('user_id', $request->user_id)
                ->where('institute_id', $request->institute_id)
                ->where('standard_id', $request->standard_id)
                ->where('exam_date', $exam_date)
                ->where('start_time', '<', $request->end_time)
                ->where('end_time','>', $request->start_time)
                ->where('batch_id', $request->batch_id)
                ->exists();


            
            if ($existing_exam) {
                return $this->response([], "Exam already exists for this standard!", false, 400);
            }
            if ($request->exam_id) {
                $exam = Exam_Model::where('id', $request->exam_id)->first();
            } else {
                $exam = new Exam_Model();
            };
            $exam->user_id = $request->user_id;
            $exam->institute_id = $request->institute_id;
            $exam->batch_id = $request->batch_id;
            $exam->exam_title = $request->exam_title;
            $exam->total_mark = $request->total_mark;
            $exam->exam_type = $request->exam_type;
            $exam->exam_date = Carbon::createFromFormat('d-m-Y', $request->exam_date);
            $exam->start_time = $request->start_time;
            $exam->end_time = $request->end_time;
            $exam->board_id = $request->board_id;
            $exam->medium_id = $request->medium_id;
            $exam->standard_id = $request->standard_id;
            $exam->stream_id = $request->stream_id;
            $exam->subject_id = $request->subject_id;
            $exam->save();
            if ($request->exam_id) {
                return $this->response([], "Successfully Updated Exam.");
            } else {
                return $this->response([], "Successfully Created Exam.");
            }
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }

    public function fetch_exam_form_detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $user_id  = Auth::id();
            $institute_for = Institute_for_model::join('institute_for_sub', 'institute_for.id', '=', 'institute_for_sub.institute_for_id')
                ->where('institute_for_sub.institute_id', $request->institute_id)
                ->where('institute_for_sub.user_id', $user_id)
                ->select('institute_for.*')->get();

            $institute_for_list = [];
            foreach ($institute_for as $institute_for_value) {
                $institute_for_list[] = [
                    'id' => $institute_for_value['id'],
                    'name' => $institute_for_value['name'],
                ];
            }
            $board = board::join('board_sub', 'board.id', '=', 'board_sub.board_id')
                ->where('board_sub.institute_id', $request->institute_id)
                ->where('board_sub.user_id', $user_id)
                ->select('board.*')->get()->toarray();
            $board_list = [];
            foreach ($board as $board_value) {
                $board_list[] = [
                    'id' => $board_value['id'],
                    'name' => $board_value['name'],
                ];
            }
            $medium = Medium_model::join('medium_sub', 'medium.id', '=', 'medium_sub.medium_id')
                ->where('medium_sub.institute_id', $request->institute_id)
                ->where('medium_sub.user_id', $user_id)
                ->select('medium.*')->get();
            $medium_list = [];
            foreach ($medium as $medium_value) {
                $medium_list[] = [
                    'id' => $medium_value['id'],
                    'name' => $medium_value['name'],
                ];
            }
            $class = Class_model::join('class_sub', 'class.id', '=', 'class_sub.class_id')
                ->where('class_sub.institute_id', $request->institute_id)
                ->where('class_sub.user_id', $user_id)
                ->select('class.*')->get();

            $class_list = [];
            foreach ($class as $class_value) {
                $class_list[] = [
                    'id' => $class_value['id'],
                    'name' => $class_value['name'],
                ];
            }
            $standard = Standard_model::join('standard_sub', 'standard.id', '=', 'standard_sub.standard_id')
                ->where('standard_sub.institute_id', $request->institute_id)
                ->where('standard_sub.user_id', $user_id)
                ->select('standard.*')->get();

            $standard_list = [];
            foreach ($standard as $standard_value) {
                $standard_list[] = [
                    'id' => $standard_value['id'],
                    'name' => $standard_value['name'],
                ];
            }
            $stream = Stream_model::join('stream_sub', 'stream.id', '=', 'stream_sub.stream_id')
                ->where('stream_sub.institute_id', $request->institute_id)
                ->where('stream_sub.user_id', $request->user_id)
                ->select('stream.*')->get();
            $stream_list = [];
            foreach ($stream as $stream_value) {
                $stream_list[] = [
                    'id' => $stream_value['id'],
                    'name' => $stream_value['name'],
                ];
            }
            $subject = Subject_model::join('subject_sub', 'subject.id', '=', 'subject_sub.subject_id')
                ->where('subject_sub.institute_id', $request->institute_id)
                ->where('subject_sub.user_id', $user_id)
                ->select('subject.*')->get();
            $subject_list = [];
            foreach ($subject as $subject_value) {
                $subject_list[] = [
                    'id' => $subject_value['id'],
                    'name' => $subject_value['name'],
                ];
            }

            $batches = Batches_model::where('institute_id', $request->institute_id)
                ->where('user_id', $user_id)->get();
            $batches_list = [];
            foreach ($batches as $batches_value) {
                $batches_list[] = [
                    'id' => $batches_value['id'],
                    'batch_name' => $batches_value['batch_name'],
                ];
            }

            $response_data = [
                'institute_for' => $institute_for_list,
                'board' => $board_list,
                'medium' => $medium_list,
                'class_list' => $class_list,
                'standard_list' => $standard_list,
                'stream_list' => $stream_list,
                'subject_list' => $subject_list,
                'batches_list' => $batches_list

            ];
            return $this->response($response_data, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
}
