<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Fees_model;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
class FeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    use ApiTrait;
    public function add_fees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'standard_id' => 'required',
            // 'stream_id' => 'required',
            'subject_id' => 'required',
            'amount' => 'required',
            // 'due_date'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $existingFee = Fees_model::where('board_id', $request->board_id)
        ->where('medium_id', $request->medium_id)
        ->where('standard_id', $request->standard_id)
        ->where('stream_id', $request->stream_id)
        ->where('subject_id', $request->subject_id)
        ->first();

        if ($existingFee) {
            return $this->response([], "Fees for the given criteria already exist.", false, 400);
        }

        $fee = new Fees_model;
        $fee->institute_id = $request->institute_id;
        $fee->board_id = $request->board_id;
        $fee->medium_id = $request->medium_id;
        $fee->standard_id = $request->standard_id;
        $fee->stream_id = $request->stream_id;
        $fee->subject_id = $request->subject_id;
        $fee->amount = $request->amount;
        // $fee->due_date = $request->due_date;
        $fee->save();
        return $this->response([], "Fees inserted successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function view_fees_detail(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $fees = Fees_model::join('board', 'board.id', '=', 'fees.board_id')
        ->join('medium', 'medium.id', '=', 'fees.medium_id')
        ->join('standard', 'standard.id', '=', 'fees.standard_id')
        ->leftjoin('stream', 'stream.id', '=', 'fees.stream_id')
        ->join('subject', 'subject.id', '=', 'fees.subject_id')
        ->select(
            'fees.amount',
            'board.name as board_name',
            'medium.name as medium_name',
            'standard.name as standard_name',
            'stream.name as stream_name',
            'subject.name as subject_name'
        )
        ->where('fees.institute_id', $request->institute_id)
        ->get()->toarray();
        $data=[];
        foreach($fees as $value){
             $data[] = [
                  'board_name'=>$value['board_name'],
                  'medium_name'=>$value['medium_name'],
                  'standard_name'=>$value['standard_name'],
                  'stream_name'=>$value['stream_name'],
                  'subject_name'=>$value['subject_name'],
                  'amount'=>$value['amount'],
             ];
             return $this->response($data, "Data Fetch Successfully");
              } 
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
     
    }
}
