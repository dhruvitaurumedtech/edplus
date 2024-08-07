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
            $products = Products_status::select('id', 'name')->get();
            return $this->response($products, "Inventory Status.");
        }catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        } 
    }
    public function add_inventory(Request $request){
        $validator = Validator::make($request->all(), [
            'status' => 'required|exists:products_inventory_status,id',
            'product_id' => 'required|exists:products,id',
            'quantity' =>'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $product = new Products_inventory();
            $product->product_id = $request->product_id; 
            $product->status = $request->status;
            $product->quantity = $request->quantity;
            $product->save();
            return $this->response([], "Created successfully.");
        }catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }
    public function product_list(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $products = Products::where('institute_id',$request->institute_id)
            ->where('status','1')
            ->select('id', 'name')
            ->get();
            $productsList = [];
            foreach($products as $prdt){
                $addinventory = Products_inventory::where('status','1')
                ->where('product_id',$prdt->id)->sum('quantity');
                
                $assigninventory = Products_inventory::where('status','2')
                ->where('product_id',$prdt->id)->sum('quantity');
                $availableqty = $addinventory - $assigninventory;

                $productsList[] = ['id'=>$prdt->id,
                'name'=>$prdt->name,
                'available'=>$availableqty];
            }
            
            return $this->response($productsList, "Products List.");
        }catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        } 
    }

}
