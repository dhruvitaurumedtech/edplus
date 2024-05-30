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
                'feedbacks.id as feedback_id',
                'feedbacks.feedback',
                'feedbacks.feedback_to_id',
                'feedbacks.institute_id',
                'feedbacks.rating',
                'feedbacks.role_type',
                'feedbacks.created_at',
                'institute_detail.institute_name',
                'users.firstname',
                'users.lastname',
                'users.image',
                'roles.role_name',
                'institute_detail.logo'
            )
            ->Join('users', 'users.id', '=', 'feedbacks.feedback_to_id')
            ->Join('roles', 'roles.id', '=', 'users.role_type')
            ->Join('institute_detail', 'institute_detail.id', '=', 'feedbacks.institute_id')
            ->whereNull('feedbacks.deleted_at')
            ->orderByDesc('feedbacks.created_at');
            if (!empty($institute_id)) {
                $feedback_list = $feedback_list->where('feedbacks.institute_id', $institute_id);
                $feedback_list = $feedback_list->where('feedbacks.role_type', '1');
            } else {
                $feedback_list = $feedback_list->where('feedbacks.feedback_to_id', Auth::id());
                $feedback_list = $feedback_list->where('feedbacks.role_type', '2');
            }
            
            $feedback_list = $feedback_list->get();
            $feedback_data = [];

            

            foreach($feedback_list as $feedbackdata){
                if($feedbackdata->role_type == 2){ 
                    if ($feedbackdata->logo) {
                        $insimg = $feedbackdata->logo;
                    } else {
                        $insimg = asset('no-image.png');
                    }

                    // 2 role type feedback is  for users so which institute give a feedback to user
                    $dedsf = array('name'=>$feedbackdata->institute_name,
                                    'image'=>$insimg);
                }else{
                    if ($feedbackdata->image) {
                        $insimg = $feedbackdata->image;
                    } else {
                        $insimg = asset('no-image.PNG');
                    }

                    //is for which user give a feedback to institute
                    $dedsf = array('name'=>$feedbackdata->firstname .' '.$feedbackdata->lastname,
                                    'image'=>$feedbackdata->image,
                                    'role_name'=>$feedbackdata->role_name);
                }
                $feedback_item=array('feedback_id'=>$feedbackdata->feedback_id,
                'feedback'=>$feedbackdata->feedback,
                'rating'=>$feedbackdata->rating,
                'date'=>$feedbackdata->created_at);

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
