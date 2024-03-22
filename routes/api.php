<?php

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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  
//     return $request->user();
// });

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/verify-otp', [AuthController::class, 'verify_otp'])->name('verify_otp.get');


Route::post('/auth/login', [AuthController::class, 'login']);
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
Route::post('/student/add-parents-details-student', [StudentController::class, 'student_patents_details_add'])->name('student_patents_details_add.get');
Route::post('/student/profile-student', [StudentController::class, 'profile_detail'])->name('profile_detail.get');
Route::post('/student/profile-edit-student', [StudentController::class, 'profile_profile'])->name('profile_profile.get');

Route::post('/institute/get-base-table-detail', [InstituteApiController::class, 'get_institute_reponse'])->name('institude.get');
Route::post('/institute/register-institute', [InstituteApiController::class, 'register_institute'])->name('institude.register');
// Route::post('/institude/get-institute', [InstituteApiController::class, 'get_institute'])->name('get_institude.get');

Route::post('/institute/get-board', [InstituteApiController::class, 'get_board'])->name('get_board.get');
Route::post('/institute/get-class', [InstituteApiController::class, 'get_class'])->name('get_class.get');
// Route::post('/institute/get-subject-or-stream', [InstituteApiController::class, 'get_subject_stream'])->name('get_subject_stream.get');

Route::post('/institute/get-homescreen-first', [InstituteApiController::class, 'get_homescreen_first'])->name('reset.password.get');
Route::post('/institute/get-homescreen-second', [InstituteApiController::class, 'get_homescreen_second'])->name('reset.password.get');
Route::post('/institute/get-request-list', [StudentController::class, 'get_request_list'])->name('request_list.get');
Route::post('/institute/get-reject-request', [StudentController::class, 'get_reject_request'])->name('reject.request_list.get');
Route::post('/institute/add-student', [StudentController::class, 'add_student'])->name('add_student');
