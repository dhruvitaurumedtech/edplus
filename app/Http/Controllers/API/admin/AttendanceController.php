<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance_model;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiTrait;


class AttendanceController extends Controller
{


    use ApiTrait;
    /**
     * Display a listing of the resource.
     */
public function attendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'student_id' => 'required',
            'batch_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'status' => 'required|in:P,A',
            'date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $student_ids = explode(',',$request->student_id);
            foreach($student_ids as $student_id){
                $attendanceDate = date('Y-m-d', strtotime($request->date));

                $attendance = Attendance_model::where('institute_id', $request->institute_id)
                            ->where('student_id', $student_id)
                            ->where('subject_id', $request->subject_id)
                            ->where('date', $attendanceDate)
                            ->count();
                       
            
                if ($attendance != 0) {
                    Attendance_model::where('institute_id', $request->institute_id)
                                    ->where('student_id', $student_id)
                                    ->where('subject_id', $request->subject_id)
                                    ->where('date', $attendanceDate)
                                    ->update([
                                        'user_id' => auth()->user()->id,
                                        'batch_id' => $request->batch_id,
                                        'subject_id' => $request->subject_id,
                                        'attendance' => $request->status,
                                    ]);
                } else {
                    Attendance_model::create([
                        'user_id' => auth()->user()->id,
                        'institute_id' => $request->institute_id,
                        'student_id' => $student_id,
                        'batch_id' => $request->batch_id,
                        'subject_id' => $request->subject_id,
                        'date' => $attendanceDate,
                        'attendance' => $request->status,
                    ]);
                }
                
            }
            
            return $this->response([], "Attendance marked successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
