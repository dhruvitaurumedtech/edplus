<?php

namespace App\Http\Controllers\Api;

use App\Models\Products;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Products_assign;
use App\Models\Products_inventory;
use App\Models\Products_status;
use App\Models\Student_detail;
use App\Models\User;
use Carbon\Carbon;

// status
// 1 - Add
// 2 - Assign
// 3 - Damaged
// 4 - Lost
// 5-return

//if is_returnable then 1 and 0 means non returnable

class ProductAndInventoryController extends Controller
{
    use ApiTrait;
    public function create_product(Request $request)
    { //not in use
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
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }
    public function inventory_status(Request $request)
    {
        try {
            $products = Products_status::select('id', 'name')->get();
            return $this->response($products, "Inventory Status.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }
    public function add_inventory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $product_inventory = json_decode($request->product_inventory, true);
            foreach ($product_inventory as $productinventory) {
                
                if ($productinventory['product_name']) {
                    $product = new Products();
                    $product->name = $productinventory['product_name'];
                    $product->institute_id = $request->institute_id;
                    $product->status = '1';
                    $product->save();
                    $product_id = $product->id;
                } else {
                    
                    $product_id = $productinventory['product_id'];
                }
                if ($productinventory['quantity']) {
                    $product = new Products_inventory();
                    $product->product_id = $product_id;
                    $product->status = $productinventory['status'];
                    $product->quantity = $productinventory['quantity'];
                    $product->save();
                }
            }
            return $this->response([], "Created successfully.");
        } catch (Exception $e) {
            return $e;
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }
    public function product_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $products = Products::where('institute_id', $request->institute_id)
                ->where('status', '1')
                ->select('id', 'name')
                ->get();
            $productsList = [];
            foreach ($products as $prdt) {
                $addinventory = Products_inventory::whereIn('status', ['1','5'])
                    ->where('product_id', $prdt->id)->sum('quantity');

                $assigninventory = Products_inventory::whereNotIn('status', ['1', '5'])
                    ->where('product_id', $prdt->id)->sum('quantity');
                $availableqty = $addinventory - $assigninventory;

                $productsList[] = [
                    'id' => $prdt->id,
                    'name' => $prdt->name,
                    'available' => $availableqty
                ];
            }

            return $this->response($productsList, "Products List.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }
    public function return_product(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id'=>'required|exists:products,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $this->return_product_method($request,$request->return_quantity,5);
            $this->return_product_method($request,$request->damaged_quantity,3);
            $this->return_product_method($request,$request->lost_quantity,4);
            return $this->response([], "Return successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }

    private function return_product_method($request,$quantity,$status)
    {
        
            $product = new Products_assign();
            $product->user_id = $request->user_id;
            $product->product_id = $request->product_id;
            $product->status = $status;
            $product->quantity = $quantity;
            $product->return_date = (!empty($request->return_date)) ? Carbon::createFromFormat('d-m-Y', $request->input('return_date'))->format('Y-m-d') : null;
            $product->is_returnable = $request->is_returnable ? $request->is_returnable : '0';
            $product->save();

            $product = new Products_inventory();
            $product->product_id = $request->product_id;
            $product->status = $status;
            $product->quantity = $quantity;
            $product->save();
            return 1;
        
    }

    public function product_assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $product = new Products_assign();
            $product->user_id = $request->user_id;
            $product->product_id = $request->product_id;
            $product->status = $request->status;
            $product->quantity = $request->quantity;
            $product->return_date = (!empty($request->return_date)) ? Carbon::createFromFormat('d-m-Y', $request->input('return_date'))->format('Y-m-d') : null;
            $product->is_returnable = $request->is_returnable;
            $product->save();

            $product = new Products_inventory();
            $product->product_id = $request->product_id;
            $product->status = $request->status;
            $product->quantity = $request->quantity;
            $product->save();
            return $this->response([], "Assign successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }

    public function product_assign_history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $productshis = Products_assign::join('users', 'users.id', '=', 'products_assign.user_id')
                ->join('products_inventory_status', 'products_inventory_status.id', '=', 'products_assign.status')
                ->join('products', 'products.id', '=', 'products_assign.product_id')
                ->join('roles', 'roles.id', '=', 'users.role_type')
                ->where('products.institute_id', $request->institute_id)
                ->select('products_assign.product_id','products.name as productname','roles.role_name','roles.id as role_id',
                'products_assign.id', 'users.firstname', 'users.lastname', 'products_assign.quantity', 'products_assign.created_at', 
                'products_inventory_status.name as statusname','products_assign.is_returnable','products_assign.user_id')
                ->get();
            $productsList = [];
            foreach ($productshis as $prdt) {
                $productsList[] = [
                    'id' => $prdt->id,
                    'user_id'=>$prdt->user_id,
                    'firstname' => $prdt->firstname,
                    'lastname' => $prdt->lastname,
                    'role_id'=>$prdt->role_id,
                    'role_name'=>$prdt->role_name,
                    'product_id'=>$prdt->product_id,
                    'product_name'=>$prdt->productname,
                    'quantity' => $prdt->quantity,
                    'created_at' => $prdt->created_at,
                    'status' => $prdt->statusname,
                    'is_returnable' =>$prdt->is_returnable,
                ];
            }

            return $this->response($productsList, "Products List.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }
    function assign_inventory(Request $request) //assign time filter wise user list 
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'role_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $user_list = User::leftJoin('students_details', 'students_details.student_id', '=', 'users.id')
                ->leftJoin('teacher_detail', 'teacher_detail.teacher_id', '=', 'users.id')
                ->when(!empty($request->role_id), function ($query) use ($request) {
                    return $query->where('users.role_type', $request->role_id);
                })
                ->when(!empty($request->institute_id), function ($query) use ($request) {
                    return $query->where(function ($query) use ($request) {
                        $query->where('students_details.institute_id', $request->institute_id)
                            ->orWhere('teacher_detail.institute_id', $request->institute_id);
                    });
                })
                ->when(!empty($request->standard_id), function ($query) use ($request) {
                    return $query->where('students_details.standard_id', $request->standard_id);
                })
                ->when(!empty($request->medium_id), function ($query) use ($request) {
                    return $query->where('students_details.medium_id', $request->medium_id);
                })
                ->when(!empty($request->board_id), function ($query) use ($request) {
                    return $query->where('students_details.board_id', $request->board_id);
                })
                ->when(!empty($request->batch_id), function ($query) use ($request) {
                    return $query->where('students_details.batch_id', $request->batch_id);
                })
                ->when(!empty($request->subject_id), function ($query) use ($request) {
                    return $query->where('students_details.subject_id', 'LIKE', '%' . $request->subject_id . '%');
                })
                ->select('users.firstname', 'users.lastname', 'users.id')
                ->distinct()
                ->get()
                ->toArray();

            $user_response = [];
            foreach ($user_list as $value) {
                $user_response[] = ['id' => $value['id'], 'username' => $value['firstname'] . $value['lastname']];
            }
            return $this->response($user_response, "User List.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!.", false, 400);
        }
    }
}
