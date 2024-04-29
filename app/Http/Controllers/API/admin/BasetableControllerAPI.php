<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\board;
use App\Models\Institute_for_model;
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
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
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

    

}
