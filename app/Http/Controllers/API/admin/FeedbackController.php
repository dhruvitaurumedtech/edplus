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
            'feedback_to_id' => 'required|exists:users,id', 
            'feedback'=>'required',
            'role_type'=>'required|in:1,2', //if 1 then feedback is for institute if 2 then feedback is for student and teacher
            'rating'=>'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $feedback = new FeedbackModel();
            $feedback->fill($request->all());
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
        try {
            $role_type = auth()->user()->role_type;
            $institute_id = $request->institute_id;
            $feedback_list = FeedbackModel::select(
                'feedback.id as feedback_id',
                'feedback.feedback',
                'feedback.feedback_to_id',
                'feedback.institute_id',
                'feedback.rating',
                'institute_detail.institute_name',
                'users.firstname',
                'users.lastname',
                'users.image',
                'institute_detail.logo'
            )
            ->Join('users', 'users.id', '=', 'feedback.feedback_to_id')
            ->Join('institute_detail', 'institute_detail.id', '=', 'feedback.institute_id')
            ->whereNull('feedback.deleted_at')
            ->orderByDesc('feedback.created_at');
            if (!empty($institute_id)) {
                $feedback_list = $feedback_list->where('feedback.institute_id', $institute_id);
            } else {
                // Otherwise, filter by feedback_to_id with Auth::id()
                $feedback_list = $feedback_list->where('feedback.feedback_to_id', Auth::id());
            }
            
            $feedback_list = $feedback_list->get();
            $feedback_data = [];
            foreach($feedback_list as $feedbackdata){
                if($role_type == 3){
                    $dedsf = array('name'=>$feedbackdata->institute_name,
                                    'image'=>$feedbackdata->logo);
                }else{
                    $dedsf = array('name'=>$feedbackdata->firstname .' '.$feedbackdata->lastname,
                                    'image'=>$feedbackdata->image);
                }
                $feedback_item=array('feedback_id'=>$feedbackdata->feedback_id,
                'feedback'=>$feedbackdata->feedback,
                'rating'=>$feedbackdata->rating);

                $merged_data = array_merge($dedsf, $feedback_item);
                $feedback_data[] = $merged_data;                     


            }


            if (!empty($feedback_data)) {
                return $this->response($feedback_data, "Successfully Fetch Exam List");
            } else {
                return $this->response([], "No Data Found.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
}
