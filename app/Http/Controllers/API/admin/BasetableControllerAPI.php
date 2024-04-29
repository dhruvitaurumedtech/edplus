<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
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

    public function board(Request $request){

        $validator = \Validator::make($request->all(), [
            'institute_for_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
                $institute_for_ids = explode(',', $request->institute_for_id);
                $base_board = board::join('base_table', 'base_table.board', '=', 'board.id')
                    ->whereIN('base_table.board',$institute_for_ids)
                    ->select('board.id', 'board.name', 'board.icon')
                    ->distinct()
                    ->get();
                $data = [];
                foreach ($base_board as $baseboard) {
                    $data[] = array('id' => $baseboard->id, 'name' => $baseboard->name, 'icon' => url($baseboard->icon));
                }
                
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    public function medium(Request $request){

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
                $base_medium = Medium_model::join('base_table', 'base_table.medium', '=', 'medium.id')
                    ->whereIN('base_table.institute_for',$institute_for_ids)
                    ->whereIN('base_table.board',$board_ids)
                    ->select('medium.id', 'medium.name', 'medium.icon')
                    ->distinct()
                    ->get();
                $data = [];
                foreach ($base_medium as $basemedium) {
                    $data[] = array('id' => $basemedium->id, 'name' => $basemedium->name,
                     'icon' => url($basemedium->icon));
                }
                
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }


    public function class(Request $request){

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
                    ->whereIN('base_table.institute_for',$institute_for_ids)
                    ->whereIN('base_table.board',$board_ids)
                    ->whereIN('base_table.medium',$medium_ids)
                    ->select('class.id', 'class.name', 'class.icon')
                    ->distinct()
                    ->get();
                $data = [];
                foreach ($base_class as $baseclass) {
                    $data[] = array('id' => $baseclass->id, 'name' => $baseclass->name,
                     'icon' => url($baseclass->icon));
                }
                
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    public function standard(Request $request){

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

                $base_standard = Standard_model::join('base_table', 'base_table.standard', '=', 'standard.id')
                    ->whereIN('base_table.institute_for',$institute_for_ids)
                    ->whereIN('base_table.board',$board_ids)
                    ->whereIN('base_table.medium',$medium_ids)
                    ->whereIN('base_table.institute_for_class',$class_ids)
                    ->select('standard.id', 'standard.name')
                    ->distinct()
                    ->get();
                $data = [];
                foreach ($base_standard as $basestandard) {
                    $data[] = array('id' => $basestandard->id, 'name' => $basestandard->name);
                }
                
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    public function stream(Request $request){

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
                    ->whereIN('base_table.institute_for',$institute_for_ids)
                    ->whereIN('base_table.board',$board_ids)
                    ->whereIN('base_table.medium',$medium_ids)
                    ->whereIN('base_table.institute_for_class',$class_ids)
                    ->whereIN('base_table.standard',$standard_ids)
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
                ->whereIn('base_table.institute_for', $institute_for_ids)
                ->whereIn('base_table.board', $board_ids)
                ->whereIn('base_table.medium', $medium_ids)
                ->whereIn('base_table.institute_for_class', $class_ids)
                ->whereIn('base_table.standard', $standard_ids);

            if (!empty($stream_ids) && array_filter($stream_ids)) {
                $base_subject_query->whereIn('base_table.stream', $stream_ids);
            }
            $base_subject = $base_subject_query
                ->select('subject.id', 'subject.name', 'subject.image')
                ->distinct()
                ->get();

            $data = [];
            foreach ($base_subject as $basesubject) {
                $data[] = array('id' => $basesubject->id, 'name' => $basesubject->name, 'image' => $basesubject->image);
            }
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exeption $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }
}
