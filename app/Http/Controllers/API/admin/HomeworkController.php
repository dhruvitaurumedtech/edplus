<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Home_work_model;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeworkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use ApiTrait;
    public function add_homework(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|date',
                'batch_id' => 'exists:batches,id',
                'subject_id' => 'exists:subject,id',
            ]);
            if ($validator->fails()) {
                return $this->response([], $validator->errors()->first(), false, 400);
            }
            $id=$request->edit_id;
            if (is_null($id)) {
                    $existinghomwork = Home_work_model::where([
                        'batch_id' => $request->batch_id,
                        'subject_id' => $request->subject_id,
                        'date' => $request->date,
                    ])->first();
                    if ($existinghomwork) {
                        return $this->response([], 'Homework entry already exists for this day.', false, 400);
                    } else {
                        Home_work_model::create(['title'=>$request->title,
                                                'description'=>$request->description,
                                                'date'=>$request->date,
                                                'batch_id'=>$request->batch_id,
                                                'subject_id'=>$request->subject_id,
                                                'created_by'=>Auth::user()->id]);
                        return $this->response([], "Homework Inserted Successfully!");
                    }
           } else {
                $homework = Home_work_model::find($id);

                    if (!$homework) {
                        return $this->response([], 'Homework entry not found.', false, 404);
                    }

                    // Update the homework entry
                    $homework->title = $request->title;
                    $homework->description = $request->description;
                    $homework->date = $request->date;
                    $homework->batch_id = $request->batch_id;
                    $homework->subject_id = $request->subject_id;
                    $homework->created_by = Auth::user()->id;

                    $homework->save();

                    return $this->response([], "Homework Updated Successfully!");
                }
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function view_homework(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'batch_id' => 'required|exists:batches,id',
                'subject_id' => 'required|exists:subject,id',
                'date' =>'nullable|date'
            ]);
            if ($validator->fails()) {
                return $this->response([], $validator->errors()->first(), false, 400);
            }
            $data=Home_work_model::join('subject','subject.id','=','home_work.subject_id')
                                 ->join('users','users.id','=','home_work.created_by')
                                 ->select('users.firstname','users.lastname','home_work.*','subject.name as subject_name')
                                 ->where('batch_id',$request->batch_id)
                                 ->where('subject_id',$request->subject_id)->get();
            
            $response=[];
            if(!empty($data)){
            foreach($data as $value){
                $response[] =['id'=>$value->id,
                              'title'=>$value->title,
                              'teacher_name'=>$value->firstname.' '.$value->lastname,
                              'subject_name'=>$value->subject_name,
                              'date'=>$value->date];
                            }
            }
            return $this->response($response, "Homework Display Successfully!");
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function delete_homework(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'homework_id' => 'required|exists:home_work,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $exam  = Home_work_model::where('id', $request->homework_id)->delete();
            return $this->response([], "Successfully Deleted Homework.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }   
    }

    /**
     * Display the specified resource.
     */
    public function open_homework(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:home_work,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $response = Home_work_model::where('id', $request->id)->first();
            $data= ['id'=>$response->id,
                    'title'=>$response->title,
                    'description'=>$response->description
                   ];
                return $this->response($data, "Successfully Display Homework.");
            } catch (Exception $e) {
                return $this->response([], "Invalid token.", false, 400);
            }   
    }
}
