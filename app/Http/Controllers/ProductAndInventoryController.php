<?php

namespace App\Http\Controllers;

use App\Models\Product_and_inventory;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProductAndInventoryController extends Controller
{
    use ApiTrait;
    public function create_product(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $product = new Product_and_inventory();
            $product->name = $request->name;
            $product->institute_id = $request->institute_id;
            $product->status = '1';
            $product->save();
            return $this->response([], "Created successfully.");
        }catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }
}
