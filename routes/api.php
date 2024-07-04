<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\API\admin\TeacherController;
use App\Http\Controllers\API\admin\AttendanceController;
use App\Http\Controllers\API\admin\BasetableControllerAPI;
use App\Http\Controllers\API\admin\DeadstockController;
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
use App\Http\Controllers\API\admin\FeedbackController;
use App\Http\Controllers\API\admin\FeesController;
use App\Http\Controllers\API\admin\General_timetableController;
use App\Http\Controllers\API\admin\HomeworkController;
use App\Http\Controllers\API\admin\ParentsController;
use App\Http\Controllers\API\admin\TimetableController;
use App\Http\Controllers\API\BannerApiController;
use App\Http\Controllers\API\staff\StaffController;
use App\Http\Controllers\API\student\StudentAttendance;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductAndInventoryController;
use App\Models\Student_detail;


Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/google', [AuthController::class, 'handleGoogle']);
Route::post('/auth/verify-otp', [AuthController::class, 'verify_otp'])->name('verify_otp.get');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('apilogs');

Route::post('/auth/forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::post('/auth/verify-code', [ForgotPasswordController::class, 'verify_code']);
Route::post('/auth/update-password', [ForgotPasswordController::class, 'update_password']);

Route::group(['middleware' => ['web']], function () {
    Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::get('/auth/forgot-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
    Route::get('/auth/reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('/auth/reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
});


Route::middleware(['auth:api', 'apilogs'])->group(function () {
    Route::post('/institute/upload-video', [VideoController::class, 'upload_video'])->name('upload_Video.get')->middleware('check.permission:9,1');
    Route::post('/institute/delete-video', [VideoController::class, 'delete_video'])->middleware('check.permission:9,3'); //priyanka
    Route::post('/institute/upload-youtube-video', [VideoController::class, 'upload_youtube_video'])->name('upload_youtube_Video.get')->middleware('check.permission:9,1');
    Route::post('/institute/get-base-table-detail', [InstituteApiController::class, 'get_institute_reponse'])->name('institude.get');
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/institute/videoAssign', [VideoController::class, 'videoassign'])->middleware('check.permission:10,1');
    Route::post('/institute/category-list', [VideoController::class, 'video_category']);
    Route::post('/institute/video-category-list', [InstituteApiController::class, 'category_list'])->name('video_category.get');
    Route::post('/institute/get-homescreen-first', [InstituteApiController::class, 'get_homescreen_first']);
    Route::post('/institute/get-homescreen-second', [InstituteApiController::class, 'get_homescreen_second']);
    Route::post('/institute/get-request-list', [InstituteApiController::class, 'get_request_list'])->name('request_list.get')->middleware('check.permission:11,4');
    Route::post('/institute/get-reject-request', [InstituteApiController::class, 'get_reject_request'])->name('reject.get_reject_request')->middleware('check.permission:12,1');
    Route::post('/institute/get-reject-request-list', [InstituteApiController::class, 'get_reject_request_list'])->name('reject.request_list.get')->middleware('check.permission:11,4');
    Route::post('/institute/fetch_student_detail', [InstituteApiController::class, 'fetch_student_detail'])->name('fetch_student_detail')->middleware('check.permission:11,4');
    Route::post('/institute/add-exam', [ExamController::class, 'add_exam'])->name('add_exam')->middleware('check.permission:2,1');
    Route::post('/institute/get-exam', [ExamController::class, 'get_exam'])->name('get_exam')->middleware('check.permission:2,4');
    Route::post('/institute/delete-exam', [ExamController::class, 'delete_exam'])->name('delete_exam')->middleware('check.permission:2,3');
    Route::post('/institute/edit-exam', [ExamController::class, 'edit_exam'])->name('edit_exam')->middleware('check.permission:2,2');
    Route::post('/institute/exam-report', [ExamController::class, 'exam_report'])->name('exam_report')->middleware('check.permission:2,5');

    Route::post('/institute/add-update-dublicate-exam', [ExamController::class, 'update_exam'])->name('update_exam')->middleware('check.permission:2,2');
    Route::post('/institute/announcements-list', [InstituteApiController::class, 'announcements_list'])->name('announcements_list')->middleware('check.permission:1,4');
    Route::post('/institute/add-announcements', [InstituteApiController::class, 'add_announcements'])->name('add_announcements')->middleware('check.permission:1,1');
    Route::post('/institute/delete-announcements', [InstituteApiController::class, 'delete_announcement'])->name('delete_announcement')->middleware('check.permission:1,3');
    Route::post('/institute/student-list-with-marks', [InstituteApiController::class, 'student_list_with_marks'])->name('student_list_with_marks')->middleware('check.permission:3,4');
    Route::post('/institute/add-marks', [InstituteApiController::class, 'add_marks'])->name('add_marks')->middleware('check.permission:3,1');
    Route::post('/banner/banner-add', [BannerApiController::class, 'banner_add'])->name('banner_add')->middleware('check.permission:16,1');
    Route::post('/banner/banner-status-update', [BannerApiController::class, 'update_status'])->name('update_status')->middleware('check.permission:16,2');
    Route::post('/banner/banner-list', [BannerApiController::class, 'banner_list'])->name('banner_list')->middleware('check.permission:16,4');
    Route::post('/banner/banner-update-details', [BannerApiController::class, 'banner_detail_update'])->name('banner_detail_update')->middleware('check.permission:16,2');
    Route::post('/banner/banner-delete', [BannerApiController::class, 'banner_delete'])->name('banner_delete')->middleware('check.permission:16,3');
    Route::post('/institute/roles', [InstituteApiController::class, 'roles']);
    // ->middleware('check.permission:27,4');

    Route::post('/institute/students_list', [InstituteApiController::class, 'institute_students'])->middleware('check.permission:5,4');
    Route::post('/institute/create-batch', [InstituteApiController::class, 'create_batch'])->middleware('check.permission:8,1');
    Route::post('/institute/pdfAssign', [PdfController::class, 'pdfAssign'])->middleware('check.permission:10,1');
    Route::post('/institute/fetch-batch', [InstituteApiController::class, 'batch_list']);
    // ->middleware('check.permission:8,4');
    Route::post('/institute/student_list', [StudentController::class, 'student_list'])->middleware('check.permission:5,4');
    Route::post('/institute/institute-profile', [InstituteApiController::class, 'institute_profile']);
    Route::post('/institute/institute-profile-edit', [InstituteApiController::class, 'institute_profile_edit']);
    Route::post('/institute/attendance', [AttendanceController::class, 'attendance'])->middleware('check.permission:4,1');
    Route::post('/institute/Subject-List', [InstituteApiController::class, 'subjectList']);
    Route::post('/institute/Subject-edit', [InstituteApiController::class, 'edit_subject']);
    Route::post('/institute/do_business_with', [InstituteApiController::class, 'do_business_with']);
    Route::post('/institute/All-Subject-List', [InstituteApiController::class, 'allsubjectList']);
    Route::post('/institute/fetch-exam-form-detail', [ExamController::class, 'fetch_exam_form_detail'])->name('fetch_exam_form_detail')->middleware('check.permission:2,4');
    Route::post('/institute/register-institute', [InstituteApiController::class, 'register_institute'])->name('institude.register');
    Route::post('institute/update-institute', [InstituteApiController::class, 'update_institute']);
    Route::post('institute/student-fees-calculation', [InstituteApiController::class, 'student_fees_calculation'])->middleware('check.permission:7,1');

    Route::post('/institute/add-student', [InstituteApiController::class, 'add_student'])->name('add_student');
    Route::post('/institute/student-list-exam', [InstituteApiController::class, 'student_list_for_add_marks'])->name('student_list_for_add_marks')->middleware('check.permission:5,4');
    Route::post('/institute/institute-details', [InstituteApiController::class, 'institute_details'])->name('institute_details');
    Route::post('/institute/delete-account', [InstituteApiController::class, 'delete_account']);
    Route::post('/institute/approve-teacher', [InstituteApiController::class, 'approve_teacher'])->middleware('check.permission:14,1');

    //new API
    Route::post('/institute/base-institute-for', [BasetableControllerAPI::class, 'institute_for']);
    Route::post('/institute/base-board', [BasetableControllerAPI::class, 'board']);
    Route::post('/institute/base-medium', [BasetableControllerAPI::class, 'medium']);
    Route::post('/institute/base-class', [BasetableControllerAPI::class, 'class']);
    Route::post('/institute/base-standard', [BasetableControllerAPI::class, 'standard']);
    Route::post('/institute/base-stream', [BasetableControllerAPI::class, 'stream']);
    Route::post('/institute/base-subject', [BasetableControllerAPI::class, 'subject']);
    Route::post('/institute/lecture-type', [TimetableController::class, 'lecture_type_list']);
    Route::post('/institute/add-timetable', [TimetableController::class, 'add_timetable'])->middleware('check.permission:6,1');
    Route::post('/institute/list-timetable', [TimetableController::class, 'list_timetable_institute'])->middleware('check.permission:6,4');
    Route::post('/institute/repeat-list', [TimetableController::class, 'for_repeat_list'])->middleware('check.permission:6,4');
    Route::post('/institute/edit-timetable', [TimetableController::class, 'edit_timetable'])->middleware('check.permission:6,2');
    Route::post('/institute/fetch-teacher-list', [InstituteApiController::class, 'fetch_teacher_list'])->middleware('check.permission:15,4');
    Route::post('/institute/repeat-timetable', [TimetableController::class, 'repeat_timetable'])->middleware('check.permission:6,2');
    Route::post('/institute/get-edit-institute-for', [BasetableControllerAPI::class, 'get_edit_institute_for']);
    Route::post('/institute/get-edit-board', [BasetableControllerAPI::class, 'get_edit_board']);
    Route::post('/institute/get-edit-medium', [BasetableControllerAPI::class, 'get_edit_medium']);
    Route::post('/institute/get-edit-class', [BasetableControllerAPI::class, 'get_edit_class']);
    Route::post('/institute/get-edit-standard', [BasetableControllerAPI::class, 'get_edit_standard']);
    Route::post('/institute/get-edit-subject', [BasetableControllerAPI::class, 'get_edit_subject']);
    Route::post('/institute/add-feedback', [FeedbackController::class, 'addfeedbackforstudent']);
    Route::post('/institute/get-feedback', [FeedbackController::class, 'get_feedback']);
    Route::post('/institute/add-deadstock', [DeadstockController::class, 'add_deadstock'])->middleware('check.permission:25,1');
    Route::post('/institute/list-deadstock', [DeadstockController::class, 'list_deadstock'])->middleware('check.permission:25,4');


    // parent api
    Route::post('/parents/child-list-parents', [ParentsController::class, 'child_list'])->name('child_list');
    Route::post('/parents/child-homescreen-parents', [ParentsController::class, 'parents_child_homescreen'])->name('parents_child_homescreen');
    Route::post('/parents/view-profile', [ParentsController::class, 'view_profile']);
    Route::post('/parents/edit-profile', [ParentsController::class, 'edit_profile']);
    // Student Api 
    Route::post('/student/add-search-history-student', [StudentController::class, 'student_searchhistory_add']);
    Route::post('/student/add-institute-request-student', [StudentController::class, 'student_add_institute_request']);
    Route::post('/student/institute-detail-student', [StudentController::class, 'institute_detail']);
    Route::post('/student/subjectwise-chapters-student', [StudentController::class, 'subject_chapers'])->middleware('check.permission:29,4');
    Route::post('/student/topicwise-videos-student', [StudentController::class, 'topic_videos'])->middleware('check.permission:9,4');

    //new API
    Route::post('/student/list-timetable-student', [StudentController::class, 'timetable_list'])->middleware('check.permission:6,4');
    Route::post('/student/profile-student', [StudentController::class, 'profile_detail']);
    Route::post('/student/profile-edit-student', [StudentController::class, 'student_edit_profile']);
    Route::post('/student/add-parents-details-student', [StudentController::class, 'student_parents_details_add']);
    Route::post('/student/exams-student', [StudentController::class, 'exams_list'])->middleware('check.permission:2,4');
    Route::post('/student/remove-institute-student', [StudentController::class, 'remove_institute']);
    Route::post('/student/exam-result-student', [StudentController::class, 'exam_result'])->middleware('check.permission:3,4');
    Route::post('/student/attendance-student', [StudentAttendance::class, 'attendance_data'])->middleware('check.permission:4,4');
    Route::post('/student/homescreen-student', [StudentController::class, 'homescreen_student']);
    Route::post('/student/institute-homescreen-student', [StudentController::class, 'student_added_detail']);
    Route::post('/student/announcement-list-student', [StudentController::class, 'announcementlist'])->middleware('check.permission:1,4');

    //teacher Api
    Route::post('/teacher/institute-homescreen-teacher', [TeacherController::class, 'teacher_added_detail']);
    Route::post('/teacher/homescreen-teacher', [TeacherController::class, 'homescreen_teacher']);
    Route::post('/teacher/institute-detail-teacher', [TeacherController::class, 'institute_detail']);
    Route::post('/teacher/add-teacher', [TeacherController::class, 'add_teacher']);
    Route::post('/teacher/second_homescreen', [TeacherController::class, 'second_homescreen']);
    Route::post('/teacher/timetable-list-teacher', [TeacherController::class, 'timetable_list_teacher'])->middleware('check.permission:6,4');
    Route::post('/teacher/join-with-teacher', [TeacherController::class, 'join_with_teacher']);
    Route::post('/teacher/get-teacher-request-list', [TeacherController::class, 'get_teacher_request_list'])->middleware('check.permission:13,4');
    Route::post('/teacher/teacher-reject-request', [TeacherController::class, 'teacher_reject_request'])->middleware('check.permission:14,4');
    Route::post('/teacher/get-teacher-reject-request-list', [TeacherController::class, 'get_teacher_reject_request_list'])->middleware('check.permission:13,4');
    Route::post('/teacher/fetch_teacher_detail', [TeacherController::class, 'fetch_teacher_detail']);
    Route::post('/teacher/edit-profile', [TeacherController::class, 'edit_profile']);

    Route::post('/teacher/teacher-profile', [TeacherController::class, 'teacher_profile']);
    Route::post('/teacher/remove-institute-teacher', [TeacherController::class, 'remove_institute_teacher']);

    Route::post('/institute/create-role', [StaffController::class, 'create_role'])->middleware('check.permission:27,1');
    Route::post('/institute/edit-role', [StaffController::class, 'edit_role'])->middleware('check.permission:27,2');
    Route::post('/institute/delete-role', [StaffController::class, 'delete_role'])->middleware('check.permission:27,3');
    // ->middleware('check.permission:1,1');
    Route::post('/institute/institute-view-roles', [StaffController::class, 'view_roles'])->middleware('check.permission:27,4');
    Route::get('/institute/institute-get-permission', [StaffController::class, 'Get_Permission']);
    Route::get('/institute/user-get-permission', [StaffController::class, 'User_Get_Permission']);
    Route::post('/institute/assign-update-permissions', [StaffController::class, 'updateRolePermissions'])->middleware('check.permission:28,2');
    Route::post('/institute/institute-add-staff', [StaffController::class, 'add_staff'])->middleware('check.permission:26,1');
    Route::post('/institute/institute-view-staff', [StaffController::class, 'view_staff'])->middleware('check.permission:26,4');
    Route::post('/institute/institute-delete-staff', [StaffController::class, 'delete_staff'])->middleware('check.permission:26,3');

    // Route::post('/institute/add-fees', [FeesController::class, 'add_fees']);
    // Route::post('/institute/view-fees-detail', [FeesController::class, 'view_fees_detail']);
    Route::post('/institute/paid-fees-student', [FeesController::class, 'paid_fees_student'])->middleware('check.permission:7,4');
    Route::post('/institute/pending-fees-student', [FeesController::class, 'pending_fees_student'])->middleware('check.permission:7,1');



    Route::post('/institute/display-subject-fees', [FeesController::class, 'display_subject_fees'])->middleware('check.permission:19,4');
    Route::post('/institute/filters-data', [InstituteApiController::class, 'filters_data']);
    Route::post('/institute/subject-amount', [FeesController::class, 'subject_amount'])->middleware('check.permission:19,1');
    Route::post('/institute/student-list-for-discount', [FeesController::class, 'student_list_for_discount'])->middleware('check.permission:20,4');
    Route::post('/institute/fetch-discount-for-student', [FeesController::class, 'fetch_discount_for_student'])->middleware('check.permission:20,4');
    Route::post('/institute/add-discount', [FeesController::class, 'add_discount'])->middleware('check.permission:20,1');
    Route::post('/institute/payment-type', [FeesController::class, 'payment_type_new']);
    Route::post('/institute/fees-collection', [FeesController::class, 'fees_collection'])->middleware('check.permission:7,1');

    Route::post('/institute/create-general-timetable', [General_timetableController::class, 'create_general_timetable']);
    Route::post('/institute/display-general-timetable', [General_timetableController::class, 'display_general_timetable']);
    Route::post('/institute/edit-general-timetable', [General_timetableController::class, 'edit_general_timetable']);
    Route::post('/institute/delete-general-timetable', [General_timetableController::class, 'delete_general_timetable']);
    Route::post('/institute/institute-day-filter-general-timetable', [General_timetableController::class, 'institute_day_filter_general_timetable']);
    Route::post('/institute/batch-standard-filter-general-timetable', [General_timetableController::class, 'batch_standard_filter_general_timetable']);
    Route::post('/institute/add-product', [ProductAndInventoryController::class, 'create_product']);

    //remainder
    Route::post('/institute/create-remainder', [InstituteApiController::class, 'create_remainder']);
    Route::post('/institute/create-greeting', [InstituteApiController::class, 'create_greeting']);
    Route::post('/institute/create-greeting', [InstituteApiController::class, 'create_greeting']);
    Route::post('/institute/get-subject', [InstituteApiController::class, 'get_subject']);

    //homework
    Route::post('/teacher/add-homework', [HomeworkController::class, 'add_homework']);
    Route::post('/student/view-homework', [HomeworkController::class, 'view_homework']);
    Route::post('/student/delete-homework', [HomeworkController::class, 'delete_homework']);
    Route::post('/student/open-homework', [HomeworkController::class, 'open_homework']);

    //classroom
    Route::post('/institute/add-classroom', [InstituteApiController::class, 'Add_classRoom']);
    Route::post('/institute/view-classroom', [InstituteApiController::class, 'view_classRoom']);
    Route::post('/institute/delete-classroom', [InstituteApiController::class, 'delete_classRoom']);
    Route::post('/institute/delete-classroom', [InstituteApiController::class, 'delete_classRoom']);

    Route::post('/institute/user-list', [InstituteApiController::class, 'user_list']);

});


   

    // Route::post('test', [InstituteApiController::class, 'test']);


// Route::post('/institude/get-institute', [InstituteApiController::class, 'get_institute'])->name('get_institude.get');
// Route::post('/institute/get-board', [InstituteApiController::class, 'get_board'])->name('get_board.get');
// Route::post('/institute/get-class', [InstituteApiController::class, 'get_class'])->name('get_class.get');
// Route::post('/institute/get-subject-or-stream', [InstituteApiController::class, 'get_subject_stream'])->name('get_subject_stream.get');



  
//parents API's

// Route::post('/institute/delete-account', [InstituteApiController::class, 'delete_account']);
// Route::post('/child-detail', [StudentController::class, 'child_detail']);
