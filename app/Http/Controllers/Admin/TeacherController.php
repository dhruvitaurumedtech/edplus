<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function list_teacher(Request $request, $institute_id)
    {
        $student = User::leftjoin('students_details', 'users.id', '=', 'students_details.student_id')
            ->where('users.role_type', [4])
            ->where('students_details.institute_id', $institute_id)
            ->select('users.*', 'students_details.status')->orderBy('users.id', 'desc')->paginate(10);
    }
    
}
