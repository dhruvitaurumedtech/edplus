<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\API\admin\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\institude\board_controller;
use App\Http\Controllers\API\admin\InstituteApiController;
use App\Http\Controllers\API\institude\StandardController;
use App\Http\Controllers\API\institude\SubjectChapterController;
use App\Http\Controllers\API\institude\SubjectController;
use App\Http\Controllers\API\institude\SubjectDetailController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\student\StudentController;
use App\Http\Controllers\API\admin\ExamController;
use App\Http\Controllers\API\admin\ParentsController;
use App\Http\Controllers\API\BannerApiController;
use App\Http\Controllers\API\student\StudentAttendance;
use App\Models\Student_detail;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/verify-otp', [AuthController::class, 'verify_otp'])->name('verify_otp.get');
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::group(['middleware' => ['web']], function () {
    //Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    // Route::get('/auth/forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
    Route::get('/auth/reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('/auth/reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
});
Route::post('/institute/upload-video', [VideoController::class, 'upload_video'])->name('upload_Video.get');
Route::post('/institute/category-list', [VideoController::class, 'video_category'])->name('video_category.get');
Route::post('/student/homescreen-student', [StudentController::class, 'homescreen_student'])->name('homescreen_student.get');
Route::post('/student/add-search-history-student', [StudentController::class, 'student_searchhistory_add'])->name('student_searchhistory_add.get');
Route::post('/student/add-institute-request-student', [StudentController::class, 'student_add_institute_request'])->name('student_add_institute_request.get');
Route::post('/student/institute-detail-student', [StudentController::class, 'institute_detail'])->name('institute_detail.get');
Route::post('/student/institute-homescreen-student', [StudentController::class, 'student_added_detail'])->name('student_added_detail.get');
Route::post('/student/subjectwise-chapters-student', [StudentController::class, 'subject_chapers'])->name('subject_chapers.get');
Route::post('/student/topicwise-videos-student', [StudentController::class, 'topic_videos'])->name('topic_videos.get');
Route::post('/student/add-parents-details-student', [StudentController::class, 'student_parents_details_add'])->name('student_patents_details_add.get');
Route::post('/student/profile-student', [StudentController::class, 'profile_detail'])->name('profile_detail.get');
Route::post('/student/profile-edit-student', [StudentController::class, 'student_edit_profile'])->name('student_edit_profile.get');
Route::post('/student/exams-student', [StudentController::class, 'exams_list'])->name('exams_list.get');
Route::post('/student/remove-institute-student', [StudentController::class, 'remove_institute'])->name('remove_institute.get');
Route::post('/student/exam-result-student', [StudentController::class, 'exam_result'])->name('exam_result.get');
Route::post('/student/attendance-student', [StudentAttendance::class, 'attendance_data'])->name('attendance_data.get');


Route::post('/institute/get-base-table-detail', [InstituteApiController::class, 'get_institute_reponse'])->name('institude.get');
Route::post('/institute/register-institute', [InstituteApiController::class, 'register_institute'])->name('institude.register');
// Route::post('/institude/get-institute', [InstituteApiController::class, 'get_institute'])->name('get_institude.get');
Route::post('/institute/get-board', [InstituteApiController::class, 'get_board'])->name('get_board.get');
Route::post('/institute/get-class', [InstituteApiController::class, 'get_class'])->name('get_class.get');
// Route::post('/institute/get-subject-or-stream', [InstituteApiController::class, 'get_subject_stream'])->name('get_subject_stream.get');
Route::post('/institute/get-homescreen-first', [InstituteApiController::class, 'get_homescreen_first']);
Route::post('/institute/get-homescreen-second', [InstituteApiController::class, 'get_homescreen_second']);
Route::post('/institute/get-request-list', [InstituteApiController::class, 'get_request_list'])->name('request_list.get');
Route::post('/institute/get-reject-request-list', [InstituteApiController::class, 'get_reject_request_list'])->name('reject.request_list.get');

Route::post('/institute/get-reject-request', [InstituteApiController::class, 'get_reject_request'])->name('reject.get_reject_request');
Route::post('/institute/fetch_student_detail', [InstituteApiController::class, 'fetch_student_detail'])->name('fetch_student_detail');
Route::post('/institute/add-student', [InstituteApiController::class, 'add_student'])->name('add_student');
Route::post('/institute/add-exam', [ExamController::class, 'add_exam'])->name('add_exam');
Route::post('/institute/get-exam', [ExamController::class, 'get_exam'])->name('get_exam');
Route::post('/institute/delete-exam', [ExamController::class, 'delete_exam'])->name('delete_exam');
Route::post('/institute/edit-exam', [ExamController::class, 'edit_exam'])->name('edit_exam');
Route::post('/institute/add-update-dublicate-exam', [ExamController::class, 'update_exam'])->name('update_exam');
Route::post('/institute/fetch-exam-form-detail', [ExamController::class, 'fetch_exam_form_detail'])->name('fetch_exam_form_detail');
Route::post('/institute/get-student', [StudentController::class, 'get_student'])->name('get_student');
Route::post('/institute/institute-details', [InstituteApiController::class, 'institute_details'])->name('institute_details');
Route::post('/institute/student-list-exam', [InstituteApiController::class, 'student_list_for_add_marks'])->name('student_list_for_add_marks');
Route::post('/institute/student-list-with-marks', [InstituteApiController::class, 'student_list_with_marks'])->name('student_list_with_marks');
Route::post('/institute/add-marks', [InstituteApiController::class, 'add_marks'])->name('add_marks');
Route::post('/institute/add-announcements', [InstituteApiController::class, 'add_announcements'])->name('add_announcements');
Route::post('/institute/announcements-list', [InstituteApiController::class, 'announcements_list'])->name('announcements_list');
Route::post('/institute/add-timetable', [InstituteApiController::class, 'add_time_table'])->name('add_time_table');
Route::post('/institute/students_list', [InstituteApiController::class, 'institute_students']);
Route::post('/institute/filters-data', [InstituteApiController::class, 'filters_data']);
Route::post('/institute/category-list', [InstituteApiController::class, 'category_list']);
Route::post('/institute/create-batch', [InstituteApiController::class, 'create_batch']);
//banner controller
Route::post('/banner/banner-add', [BannerApiController::class, 'banner_add'])->name('banner_add');
Route::post('/banner/banner-status-update', [BannerApiController::class, 'update_status'])->name('update_status');
Route::post('/banner/banner-update-details', [BannerApiController::class, 'banner_detail_update'])->name('banner_detail_update');
Route::post('/banner/banner-list', [BannerApiController::class, 'banner_list'])->name('banner_list');
Route::post('/banner/banner-delete', [BannerApiController::class, 'banner_delete'])->name('banner_delete');

Route::post('/institute/delete-account', [InstituteApiController::class, 'delete_account']);
Route::post('/institute/attendance', [AttendanceController::class, 'attendance']);


//parents API's
Route::post('/parents/child-list-parents', [ParentsController::class, 'child_list'])->name('child_list');

Route::post('/institute/roles', [InstituteApiController::class, 'roles']);
// Route::post('/institute/delete-account', [InstituteApiController::class, 'delete_account']);
Route::post('/institute/student_list', [StudentController::class, 'student_list']);
Route::post('/institute/institute-profile', [InstituteApiController::class, 'institute_profile']);
Route::post('/institute/institute-profile-edit', [InstituteApiController::class, 'institute_profile_edit']);
Route::post('/institute/fetch-batch', [InstituteApiController::class, 'batch_list']);
// Route::post('/child-detail', [StudentController::class, 'child_detail']);
Route::post('/institute/videoAssign', [VideoController::class, 'videoassign']);


//teacher
Route::post('/teacher/homescreen-teacher', [TeacherController::class, 'homescreen_teacher']);
// Route::post('/teacher/add-institute-request-teacher', [TeacherController::class, 'teacher_add_institute_request']);
Route::post('/teacher/add-teacher', [TeacherController::class, 'add_teacher']);
Route::post('/teacher/institute-detail-teacher', [TeacherController::class, 'institute_detail']);
Route::post('/teacher/institute-homescreen-teacher', [TeacherController::class, 'teacher_added_detail']);
