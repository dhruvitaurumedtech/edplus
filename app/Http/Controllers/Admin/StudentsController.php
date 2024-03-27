<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\board;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Institute_for_model;
use App\Models\Medium_model;
use App\Models\Class_model;
use App\Models\Stream_model;
use App\Models\subject;
use App\Models\institute_for_sub;
use App\Models\Institute_detail;
use App\Models\Subject_model;
use App\Models\Standard_model;
use App\Models\Student_detail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class StudentsController extends Controller
{
    public function list_student(Request $request): View
    {
        $id = Auth::id();
        $institute_id = $request->institute_id;

        $student = User::leftjoin('students_details', 'users.id', '=', 'students_details.student_id')
            ->where('users.role_type', [6])
            ->where('students_details.institute_id', $institute_id)
            ->select('users.*', 'students_details.status')->paginate(10);

        $institute_for = Institute_for_model::join('institute_for_sub', 'institute_for.id', '=', 'institute_for_sub.institute_for_id')->where('institute_for_sub.institute_id', $institute_id)->select('institute_for.*')->get();
        $board = board::join('board_sub', 'board.id', '=', 'board_sub.board_id')->where('board_sub.institute_id', $institute_id)->select('board.*')->get();
        $medium = Medium_model::join('medium_sub', 'medium.id', '=', 'medium_sub.medium_id')->where('medium_sub.institute_id', $institute_id)->select('medium.*')->get();
        $class = Class_model::join('class_sub', 'class.id', '=', 'class_sub.class_id')->where('class_sub.institute_id', $institute_id)->select('class.*')->get();
        $stream = Stream_model::join('stream_sub', 'stream.id', '=', 'stream_sub.stream_id')->where('stream_sub.institute_id', $institute_id)->select('stream.*')->get();
        $subject = Subject_model::join('subject_sub', 'subject.id', '=', 'subject_sub.subject_id')->where('subject_sub.institute_id', $institute_id)->select('subject.*')->get();
        $standard = Standard_model::join('standard_sub', 'standard.id', '=', 'standard_sub.standard_id')->where('standard_sub.institute_id', $institute_id)->select('standard.*')->get();
        return view('student.list', compact('institute_id', 'student', 'institute_for', 'board', 'medium', 'class', 'stream', 'subject', 'standard'));
    }

    public function create_student(Request $request)
    {
        $id = Auth::id();
        $institute_id = $request->institute_id;

        $institute_for = Institute_for_model::join('institute_for_sub', 'institute_for.id', '=', 'institute_for_sub.institute_for_id')->where('institute_for_sub.institute_id', $institute_id)->select('institute_for.*')->get();
        $board = board::join('board_sub', 'board.id', '=', 'board_sub.board_id')->where('board_sub.institute_id', $institute_id)->select('board.*')->get();
        $medium = Medium_model::join('medium_sub', 'medium.id', '=', 'medium_sub.medium_id')->where('medium_sub.institute_id', $institute_id)->select('medium.*')->get();
        $class = class_model::join('class_sub', 'class.id', '=', 'class_sub.class_id')->where('class_sub.institute_id', $institute_id)->select('class.*')->get();
        $stream = Stream_model::join('stream_sub', 'stream.id', '=', 'stream_sub.stream_id')->where('stream_sub.institute_id', $institute_id)->select('stream.*')->get();
        $subject = Subject_model::join('subject_sub', 'subject.id', '=', 'subject_sub.subject_id')->where('subject_sub.institute_id', $institute_id)->select('subject.*')->get();
        $standard = Standard_model::join('standard_sub', 'standard.id', '=', 'standard_sub.standard_id')->where('standard_sub.institute_id', $institute_id)->select('standard.*')->get();
        return view('student.create', compact('institute_id', 'institute_for', 'board', 'medium', 'class', 'stream', 'subject', 'standard'));
    }

    public function createformdata(Request $request)
    {
        $inst_id = $request->institute_id;
        $instfor_id = $request->institutefor_id;
        $boardDT = board::join('board_sub', 'board.id', '=', 'board_sub.board_id')
            ->where('board_sub.institute_id', $inst_id)
            ->where('board.institute_for_id', $instfor_id)
            ->select('board.*')
            ->get();

        if (!empty($boardDT)) {
            return response()->json(['boards' => $boardDT]);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function save_student(Request $request)
    {

        $validator = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|max:16|min:8',
            'mobile' => 'required',
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('public/profile'), $imageName);
        }

        $student = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'mobile' =>  $request->mobile,
            'address' => $request->address,
            'dob' => $request->dob,
            'image' => 'public/profile/' . $imageName,
            'password' => Hash::make($request->password),
            'role_type' => 6,
            'status' => $request->status,
        ]);

        $student_id = $student->id;
        $id = Auth::id();
        $studentdetail = Student_detail::create([
            'user_id' => $id,
            'institute_id' => $request->institute_id,
            'student_id' => $student_id,
            'institute_for_id' => $request->institute_for_id,
            'board_id' =>  $request->board_id,
            'medium_id' => $request->medium_id,
            'class_id' => $request->class_id,
            'standard_id' => $request->standard_id,
            'stream_id' => $request->stream_id,
            'subject_id' => implode(",", $request->subject_id),
            'status' => $request->status,
        ]);
        return Redirect::route('student.create')->with('success', 'profile-created');
    }

    public function edit_student(Request $request)
    {
        $student_id = $request->input('student_id');
        $institute_id = $request->input('institute_id');
        $studentDT = User::find($student_id);


        if (!empty($studentDT)) {
            $user_id = Auth::id();

            $studentdetailsDT = Student_detail::where('student_id', $student_id)->where('institute_id', $institute_id)->where('user_id', $user_id)->first();
            return response()->json(['studentDT' => $studentDT, 'studentsdetailsDT' => $studentdetailsDT]);
        } else {
            return response()->json(['error' => 'Student not found'], 404);
        }
    }

    public function update_student(Request $request)
    {
        $student_id = $request->student_id;
        $studentUP = User::find($student_id);

        $validator = $request->validate([
            'firstname' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'email')->ignore($studentUP),
            ],
            'lastname' => 'required|string|max:255',
        ]);

        //image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('public/profile'), $imageName);
            $imagepath = 'public/profile/' . $imageName;
        } else {
            $imagepath = $request->uploded_image;
        }

        //update

        $studentUP->update([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'address' => $request->input('address'),
            'dob' => $request->input('dob'),
            'image' => $imagepath
        ]);

        $user_id = Auth::id();
        $Student_detail_id = $request->Student_detail_id;
        $studentdetailsDT = Student_detail::where('id', $Student_detail_id)->first();
        $studentdetailsDT->update([
            'institute_for_id' => $request->institute_for_id,
            'board_id' =>  $request->board_id,
            'medium_id' => $request->medium_id,
            'class_id' => $request->class_id,
            'standard_id' => $request->standard_id,
            'stream_id' => $request->stream_id,
            'subject_id' => implode(",", $request->subject_id),
            'status' => $request->status,

        ]);

        return Redirect::route('student.list')->with('success', 'profile-created');
    }

    public function view_student(Request $request)
    {
        $sid = $request->student_id;
        $institute_id = $request->institute_id;

        $student = User::leftjoin('students_details', 'users.id', '=', 'students_details.student_id')
            ->where('users.id', $sid)
            ->where('students_details.institute_id', $institute_id)
            ->select('users.*')->paginate(10);
        return view('student.view', compact('student'));
    }

    public function delete_student(Request $request)
    {
        $did = $request->input('student_id');
        $student = User::find($did);
        if (Auth::role_type() == 1 && $student) {
            Student_detail::where('student_id', $did)->delete();
            $student->delete();
        } elseif (Auth::role_type() == 3 && $student) {
            $institute_id = Auth::id();
            Student_detail::where('student_id', $did)->where('institute_id', $institute_id)->delete();
        } else {
            return response()->json(['error' => 'Student not found'], 404);
        }
    }
}
