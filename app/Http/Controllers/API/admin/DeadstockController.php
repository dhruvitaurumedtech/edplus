<?php

namespace App\Http\Controllers\API\admin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeadStock;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Validator;
use Exception;

class DeadstockController extends Controller
{
    use ApiTrait;
    public function add_deadstock(Request $request){
        
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'item_name' => 'required',
            'no_of_item' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        
        try{
            $deadStock = new DeadStock();
            $deadStock->fill($request->all());
            $deadStock->save();

            return $this->response([], "Fees inserted successfully.");
        }catch(Exception $e){
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }

    public function list_deadstock(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $deadStocklist = new DeadStock();
            return $this->response($deadStocklist, "Fees inserted successfully.");
        }catch(Exception $e){
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
}
