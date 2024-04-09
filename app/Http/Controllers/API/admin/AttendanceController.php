<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance_model;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function attendance(Request $request)
    {
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            $request->validate([
                'user_id' => 'required|integer',
                'institute_id' => 'required|integer',
                'student_id' => 'required|integer',
                'status' => 'required|in:P,A',
            ]);

            $existingAttendance = Attendance_model::where([
                'user_id' => $request->user_id,
                'institute_id' => $request->institute_id,
                'student_id' => $request->student_id,
                'date' => $request->date,
            ])->first();

            if ($existingAttendance) {
                $existingAttendance->attendance = $request->status;
                $existingAttendance->save();
                return response()->json(['status' => 200, 'message' => 'Attendance updated successfully'], 200);
            }

            Attendance_model::create([
                'user_id' => $request->user_id,
                'institute_id' => $request->institute_id,
                'student_id' => $request->student_id,
                'attendance' => $request->status,
                'date' => $request->date,
            ]);

            return response()->json(['status' => 200, 'message' => 'Attendance marked successfully'], 201);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
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
