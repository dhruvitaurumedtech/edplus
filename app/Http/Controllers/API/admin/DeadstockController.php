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
            'item_name_qty' => 'required',
            //'no_of_item' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        
        try{
            $dedst = json_decode($request->item_name_qty, true);
            foreach ($dedst as $deadST) {
                $deadStock = new DeadStock();
                $deadStock->institute_id = $request->institute_id;
                $deadStock->item_name = $deadST['item_name'];
                $deadStock->no_of_item = $deadST['no_of_item'];
                $deadStock->save();
            }
            return $this->response([], "DeadStock inserted successfully.");
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
            $deadStocklist = DeadStock::where('institute_id',$request->institute_id)
            ->select('id','institute_id','item_name','no_of_item')
            ->get()->toArray();
            return $this->response($deadStocklist, "Data fetch successfully.");
        }catch(Exception $e){
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
}
