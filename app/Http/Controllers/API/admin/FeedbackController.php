<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackModel;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function addfeedbackforstudent(Request $request){
        
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'student_id' => 'required|exists:users,id',
            'feedback'=>'required'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            
            $feedback = new FeedbackModel();
            $feedback->fill($request->all());
            $feedback->feedback_to_id = $request->student_id;
            $feedback->save();
            if (!empty($feedback->id)) {
                return $this->response([], "Successfully Add Feedback.");
            } else {
                return $this->response([], "Failed to Add Feedback.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }
}
