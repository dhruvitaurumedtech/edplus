<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Home_work_model;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;

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
            $existinghomwork = Home_work_model::where([
                'batch_id' => $request->batch_id,
                'subject_id' => $request->subject_id,
                'date' => $request->date,
            ])->first();
            if ($existinghomwork) {
                return $this->response([], 'Homework entry already exists for this day.', false, 400);
            } else {
                Home_work_model::create($request->all());
                return $this->response([], "Homework Inserted Successfully!");
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
                'date' =>'required|date'
            ]);
            if ($validator->fails()) {
                return $this->response([], $validator->errors()->first(), false, 400);
            }
            $data=Home_work_model::where('batch_id',$request->batch_id)->where('subject_id',$request->subject_id)->get();
            
            $response=[];
            if(!empty($data)){
            foreach($data as $value){
                $response[] =['id'=>$value->id,
                              'title'=>$value->title,
                              'description'=>$value->description,
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
