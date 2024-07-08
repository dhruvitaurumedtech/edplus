<?php

namespace App\Http\Controllers\Api;

use App\Models\Products;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Products_inventory;
use App\Models\Products_status;

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
            $product = new Products();
            $product->name = $request->name;
            $product->institute_id = $request->institute_id;
            $product->status = '1';
            $product->save();
            return $this->response([], "Created successfully.");
        }catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }

    public function inventory_status(Request $request){
        try {
            $product = [];
            $product = new Products_status();
            return $this->response($product, "Inventory Status.");
        }catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        } 
    }

    public function add_inventory(Request $request){
        $validator = Validator::make($request->all(), [
            'status' => 'required|exists:products_inventory_status,id',
            'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $product = new Products_inventory();
            $product->product_id = $request->name;
            $product->status = $request->status;
            $product->quantity = $request->quantity;
            $product->save();
            return $this->response([], "Created successfully.");
        }catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }

}
