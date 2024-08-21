<?php

namespace App\Http\Controllers;

use App\Models\Banner_model;
use App\Models\board;
use App\Models\Class_model;
use App\Models\Common_announcement;
use App\Models\Institute_detail;
use App\Models\Institute_for_model;
use App\Models\Standard_model;
use App\Models\Student_detail;
use App\Models\Subject_model;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $institute_count = Institute_detail::where('status', 'active')->count();
        $student_count = Student_detail::where('status', '1')->count();
        $banner_count = Banner_model::where('status', 'active')->count();
        $institute_for_count = Institute_for_model::where('status', 'active')->count();
        $board_for_count = board::where('status', 'active')->count();
        $class_for_count = Class_model::where('status', 'active')->count();
        $standard_for_count = Standard_model::where('status', 'active')->count();
        $subject_for_count = Subject_model::where('status', 'active')->count();
        $announcement = Common_announcement::get()->toarray();
        $response = [];
        foreach ($announcement as $value) {
            $institute_response = Institute_detail::whereIn('id', explode(',', $value['institute_id']))->get()->toarray();
            $teacher_response = User::whereIn('id', explode(',', $value['teacher_id']))->where('role_type', 4)->get()->toarray();
            $parent_response = User::whereIn('id', explode(',', $value['teacher_id']))->where('role_type', 5)->get()->toarray();
            $student_response = User::whereIn('id', explode(',', $value['teacher_id']))->where('role_type', 6)->get()->toarray();
            $response[] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'announcement' => $value['announcement'],
                'institute_show' => $institute_response,
                'teacher_show' => $teacher_response,
                'parent_show' => $parent_response,
                'student_show' => $student_response
            ];
        }
        return view('dashboard01',compact('institute_count','student_count','banner_count',
                'institute_for_count','board_for_count','class_for_count','standard_for_count','subject_for_count','response'));
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
