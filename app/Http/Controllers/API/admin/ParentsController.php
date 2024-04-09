<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Parents;
use App\Models\User;
use Illuminate\Http\Request;

class ParentsController extends Controller
{
    public function child_list(Request $request){
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        
        if ($existingUser) {
            $user_id = $request->user_id;
            try{
                //banner
                
                //child
                $childs = [];
                $chilsdata = Parents::join('users','users.id','=','parents.student_id')
                ->join('students_details','students_details.student_id','=','parents.student_id')
                ->join('institute_detail','institute_detail.id','=','students_details.institute_id')
                ->where('parents.parent_id',$user_id)->where('parents.verify','1')
                ->select('users.firstname','users.lastname','institute_detail.institute_name')->get();
                foreach($chilsdata as $chilDT){

                    $childs[] = array('firstname'=>$chilDT->firstname,
                    'lastname'=>$chilDT->lastname,
                    'institute_name'=>$chilDT->institute_name,
                    'subjects'=>$chilDT->subject_id);
                }

                return response()->json([
                    'status' => '200',
                    'message' => 'Something went wrong',
                    'data'=>$childs
                ]);

            }catch (\Exception $e){
                return response()->json([
                    'status' => '200',
                    'message' => 'Something went wrong',
                    'data'=>[]
                ]);
            }
        }else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }  
    }
}
