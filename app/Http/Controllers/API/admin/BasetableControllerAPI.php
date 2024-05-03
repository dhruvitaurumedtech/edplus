<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Base_table;
use App\Models\board;
use App\Models\Class_model;
use App\Models\Institute_for_model;
use App\Models\Medium_model;
use App\Models\Standard_model;
use App\Models\Stream_model;
use App\Models\Subject_model;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasetableControllerAPI extends Controller
{
    use ApiTrait;
    public function institute_for(Request $request)
    {
        try {
            $base_institutfor = Institute_for_model::join('base_table', 'base_table.institute_for', '=', 'institute_for.id')
                ->select('institute_for.id', 'institute_for.name', 'institute_for.icon')
                ->distinct()
                ->get();
            $data = [];
            foreach ($base_institutfor as $basedata) {
                $data[] = array('id' => $basedata->id, 'name' => $basedata->name, 'icon' => url($basedata->icon));
            }
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    public function board(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_for_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $getBoardsId  = Base_table::whereIn('institute_for', $institute_for_ids)->distinct()->pluck('board');
            $base_board = board::whereIn('id', $getBoardsId)->get();
            foreach ($base_board as $baseboard) {
                $data[] = array('id' => $baseboard->id, 'name' => $baseboard->name, 'icon' => url($baseboard->icon));
            }

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    public function medium(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $getBoardsId  = Base_table::whereIn('institute_for', $institute_for_ids)->whereIn('board', $board_ids)->distinct()->pluck('medium');
            $base_medium = Medium_model::whereIn('id', $getBoardsId)->get();
            $data = [];
            foreach ($base_medium as $basemedium) {
                $data[] = array(
                    'id' => $basemedium->id, 'name' => $basemedium->name,
                    'icon' => url($basemedium->icon)
                );
            }

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }


    public function class(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);

            $base_class = Class_model::join('base_table', 'base_table.institute_for_class', '=', 'class.id')
                ->whereIN('base_table.institute_for', $institute_for_ids)
                ->whereIN('base_table.board', $board_ids)
                ->whereIN('base_table.medium', $medium_ids)
                ->select('class.id', 'class.name', 'class.icon')
                ->distinct()
                ->get();
            $data = [];
            foreach ($base_class as $baseclass) {
                $data[] = array(
                    'id' => $baseclass->id, 'name' => $baseclass->name,
                    'icon' => url($baseclass->icon)
                );
            }

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    // public function standard(Request $request)
    // {

    //     $validator = \Validator::make($request->all(), [
    //         'institute_for_id' => 'required',
    //         'board_id' => 'required',
    //         'medium_id' => 'required',
    //         'class_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->response([], $validator->errors()->first(), false, 400);
    //     }

    //     try {
    //         $institute_for_ids = explode(',', $request->institute_for_id);
    //         $board_ids = explode(',', $request->board_id);
    //         $medium_ids = explode(',', $request->medium_id);
    //         $class_ids = explode(',', $request->class_id);

    //         $base_standard = Standard_model::join('base_table', 'base_table.standard', '=', 'standard.id')
    //             ->join('class', 'base_table.institute_for_class', '=', 'class.id')
    //             ->join('medium', 'base_table.medium', '=', 'medium.id')
    //             ->whereIN('base_table.institute_for', $institute_for_ids)
    //             ->whereIN('base_table.board', $board_ids)
    //             ->whereIN('base_table.medium', $medium_ids)
    //             ->whereIN('base_table.institute_for_class', $class_ids)
    //             ->select('standard.id', 'standard.name', 'class.name as class_name', 'medium.name as medium_name')
    //             ->distinct()
    //             ->get();
    //         $data = [];
    //         foreach ($base_standard as $basestandard) {
    //             $data[] = array(
    //                 'id' => $basestandard->id,
    //                 'standard_name' => $basestandard->name,
    //                 'class_name' => $basestandard->class_name,
    //                 'medium_name' => $basestandard->medium_name
    //             );
    //         }
    //         // $base_standard = Class_model::join('base_table', 'base_table.institute_for_class', '=', 'class.id')
    //         //     ->join('medium', 'base_table.medium', '=', 'medium.id')
    //         //     ->whereIN('base_table.institute_for', $institute_for_ids)
    //         //     ->whereIN('base_table.board', $board_ids)
    //         //     ->whereIN('base_table.medium', $medium_ids)
    //         //     ->whereIN('base_table.institute_for_class', $class_ids)
    //         //     ->select('base_table.id', 'class.id', 'class.name as class_name', 'medium.name as medium_name')
    //         //     ->distinct()
    //         //     ->get();
    //         // $base_standard = [];
    //         // foreach ($base_standard as $basestandard) {
    //         //     $base_class = Standard_model::join('base_table', 'base_table.standard', '=', 'standard.id')
    //         //         ->whereIN('base_table.id', $basestandard->id)
    //         //         ->select('standard.id', 'standard.name')
    //         //         ->distinct()
    //         //         ->get();
    //         //     $standard = [];
    //         //     foreach ($base_class as $value2) {
    //         //         $standard[] = array(
    //         //             'id' => $value2->id,
    //         //             'standard_name' => $value2->name,
    //         //         );
    //         //     }


    //         $data[] = array(
    //             'id' => $basestandard->id,
    //             'class_name' => $basestandard->class_name,
    //             'medium_name' => $basestandard->medium_name,
    //             // 'standard' => $standard
    //         );
    //         // }

    //         return $this->response($data, "Fetch Data Successfully");
    //     } catch (Exeption $e) {
    //         return $this->response($e, "Something want Wrong!!", false, 400);
    //     }
    // }

    public function standard(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'class_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);
            $class_ids = explode(',', $request->class_id);

            $base_standards = Standard_model::join('base_table', 'base_table.standard', '=', 'standard.id')
                ->join('class', 'base_table.institute_for_class', '=', 'class.id')
                ->join('medium', 'base_table.medium', '=', 'medium.id')
                ->join('board', 'base_table.board', '=', 'board.id')
                ->whereIN('base_table.institute_for', $institute_for_ids)
                ->whereIN('base_table.board', $board_ids)
                ->whereIN('base_table.medium', $medium_ids)
                ->whereIN('base_table.institute_for_class', $class_ids)
                ->select('standard.id', 'standard.name', 'class.name as class_name', 'medium.name as medium_name', 'board.name as board_name')
                ->distinct()
                ->get();

            $data = [];
            foreach ($base_standards as $base_standard) {
                $key = $base_standard->class_name . '_' . $base_standard->medium_name . '_' . $base_standard->board_name;
                if (!array_key_exists($key, $data)) {
                    $data[$key] = [
                        'class_name' => $base_standard->class_name,
                        'medium_name' => $base_standard->medium_name,
                        'board_name' => $base_standard->board_name,
                        'std_data' => [],
                    ];
                }
                $data[$key]['std_data'][] = [
                    'id' => $base_standard->id,
                    'standard_name' => $base_standard->name,
                ];
            }
            $data = array_values($data);

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went Wrong!!", false, 400);
        }
    }

    public function stream(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'class_id' => 'required',
            'standard_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);
            $class_ids = explode(',', $request->class_id);
            $standard_ids = explode(',', $request->standard_id);

            $base_stream = Stream_model::join('base_table', 'base_table.stream', '=', 'stream.id')
                ->whereIN('base_table.institute_for', $institute_for_ids)
                ->whereIN('base_table.board', $board_ids)
                ->whereIN('base_table.medium', $medium_ids)
                ->whereIN('base_table.institute_for_class', $class_ids)
                ->whereIN('base_table.standard', $standard_ids)
                ->select('stream.id', 'stream.name')
                ->distinct()
                ->get();
            $data = [];
            foreach ($base_stream as $basestream) {
                $data[] = array('id' => $basestream->id, 'name' => $basestream->name);
            }

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    public function subject(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'class_id' => 'required',
            'standard_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);
            $class_ids = explode(',', $request->class_id);
            $standard_ids = explode(',', $request->standard_id);
            $stream_ids = explode(',', trim($request->stream));

            $base_subject_query = Subject_model::join('base_table', 'base_table.id', '=', 'subject.base_table_id')
                ->leftjoin('stream', 'base_table.stream', '=', 'stream.id')
                ->whereIn('base_table.institute_for', $institute_for_ids)
                ->whereIn('base_table.board', $board_ids)
                ->whereIn('base_table.medium', $medium_ids)
                ->whereIn('base_table.institute_for_class', $class_ids)
                ->whereIn('base_table.standard', $standard_ids);

            if (!empty($stream_ids) && array_filter($stream_ids)) {
                $base_subject_query->whereIn('base_table.stream', $stream_ids);
            }
            $base_subject = $base_subject_query
                ->select('subject.id', 'subject.name', 'subject.image', 'stream.name as stream_name', 'base_table.stream as stream_id')
                ->distinct()
                ->get();

            $data = [];
            foreach ($base_subject as $basesubject) {
                $data[] = array(
                    'id' => $basesubject->id,
                    'name' => $basesubject->name,
                    'image' => !empty($basesubject->image) ? asset($basesubject->image) : '',
                    'stream_id' => $basesubject->stream_id,
                    'stream_name' => $basesubject->stream_name
                );
            }
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }
}
