<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackModel;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    use ApiTrait;
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

    public function get_feedback(Request $request){
        
        // $validator = Validator::make($request->all(), [
        //     'institute_id' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return $this->response([], $validator->errors()->first(), false, 400);
        // }

        try {
            $feedback_list = FeedbackModel::select(
                'feedback.id as feedback_id',
                'feedback.feedback',
                'feedback.feedback_to_id',
                'feedback.institute_id',
                'institute_detail.institute_name',
            )
            ->Join('users', 'users.id', '=', 'feedback.feedback_to_id')
            ->Join('institute_detail', 'institute_detail.id', '=', 'feedback.institute_id')
            ->where('feedback.feedback_to_id', Auth::id())
            ->whereNull('feedback.deleted_at')
            ->orderByDesc('feedback.created_at')
            ->get()->toArray();

            if (!empty($feedback_list)) {
                return $this->response($feedback_list, "Successfully Fetch Exam List");
            } else {
                return $this->response([], "No Data Found.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
}
