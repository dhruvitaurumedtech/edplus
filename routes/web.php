<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BannerSizeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\InstituteController;
use App\Http\Controllers\Admin\BoardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DoBusinessWithController;
use App\Http\Controllers\Admin\MediumController;
use App\Http\Controllers\Admin\StandardController;
use App\Http\Controllers\Admin\StreamController;
use App\Http\Controllers\Admin\StudentsController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\VideoCategoryController;
use App\Http\Controllers\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\student\StudentController;
use App\Http\Controllers\Videotimelimitcontroller;
use App\Http\Controllers\WebNotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//added123
Route::get('/', function () {
    return view('auth.login');
});
Route::get('/create-role', [ProfileController::class, 'create_role'])->name('role.create');


Route::get('/dashboard', function () {

    switch (Auth::user()->role_type) {
        case '1':
            return view('dashboard01');
            break;

        case '2':
            return view('dashboard01');
            break;

        case '3':
            return view('dashboard01');
            break;


        default:
            return view('dashboard');
            break;
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('profile-edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile-update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('create/role', [RoleController::class, 'create_role'])->name('roles.create');
    Route::post('roles/save', [RoleController::class, 'save_role'])->name('roles.insert');
    Route::get('role_list', [RoleController::class, 'list_role'])->name('roles.list')->middleware('superadmin_permission');
    Route::post('/roles/edit', [RoleController::class, 'edit_role'])->name('roles.edit');
    Route::post('/roles/update', [RoleController::class, 'update_role'])->name('roles.update');
    Route::post('/roles/delete', [RoleController::class, 'delete_role'])->name('roles.delete');
    Route::get('/permission', [PermissionController::class, 'create_permission'])->name('permission.create');
    Route::post('permission/insert', [PermissionController::class, 'insert_permission'])->name('permission.insert');

    Route::get('admin', [Users::class, 'list_admin'])->name('admin.list');
    Route::get('create/admin', [Users::class, 'subadmin_create'])->name('admin.create');
    Route::post('store/admin', [Users::class, 'subadmin_store'])->name('admin.store');
    Route::post('admin/edit', [Users::class, 'subadmin_edit'])->name('admin.edit');
    Route::post('admin/update', [Users::class, 'subadmin_update'])->name('admin.update');
    Route::post('admin/delete', [Users::class, 'subadmin_delete'])->name('admin.delete');

    //institute
    Route::get('institute-admin', [Users::class, 'list_institute'])->name('institute_admin.list');
    Route::get('institute-list', [InstituteController::class, 'list_institute'])->name('institute.list');
    Route::get('/create/institute', [InstituteController::class, 'create_institute'])->name('institute.create');

    Route::get('institute-for-list', [InstituteController::class, 'list_institute_for'])->name('institute_for.list');
    Route::get('/create/institute_for', [InstituteController::class, 'create_institute_for'])->name('institute_for.create');
    Route::post('institute-for/save', [InstituteController::class, 'institute_for_save'])->name('institute_for.save');
    Route::post('/institute-for/edit', [InstituteController::class, 'institute_for_edit'])->name('institute_for.edit');
    Route::post('institute-for/update', [InstituteController::class, 'institute_for_update'])->name('institute.update');
    Route::post('institute-for/delete', [InstituteController::class, 'institute_for_delete'])->name('institute_for.delete');

    Route::post('institute/register', [InstituteController::class, 'institute_register'])->name('institute_register.delete');

    //board

    Route::get('board-list', [BoardController::class, 'list'])->name('board.list');
    Route::get('board-create', [BoardController::class, 'create'])->name('board.create');
    Route::post('board-save', [BoardController::class, 'save'])->name('board.save');
    Route::post('board-edit', [BoardController::class, 'edit'])->name('board.edit');
    Route::post('board-update', [BoardController::class, 'update'])->name('board.update');
    Route::post('board-delete', [BoardController::class, 'delete'])->name('board.delete');

    //class
    Route::get('class-list', [ClassController::class, 'list_class'])->name('class.list');
    Route::get('create/class-list', [ClassController::class, 'create_class'])->name('class.create');
    Route::post('class-list/save', [ClassController::class, 'class_list_save'])->name('class_list.save');
    Route::post('/class-list/edit', [ClassController::class, 'class_list_edit'])->name('class_list.edit');
    Route::post('class/update', [ClassController::class, 'class_update'])->name('class.update');
    Route::post('/class/delete', [ClassController::class, 'class_delete'])->name('class.delete');

    Route::post('/class/get_standard', [ClassController::class, 'get_standard'])->name('get_standard.list');
    Route::post('/class/get_stream', [ClassController::class, 'get_stream'])->name('get_stream.list');

    //medium
    Route::get('medium-list', [MediumController::class, 'list_medium'])->name('medium.list');
    Route::get('create/medium', [MediumController::class, 'create_medium'])->name('medium.create');
    Route::post('medium-list/save', [MediumController::class, 'medium_list_save'])->name('medium_list.save');
    Route::post('/medium/edit', [MediumController::class, 'medium_list_edit'])->name('medium_list.edit');
    Route::post('medium/update', [MediumController::class, 'medium_update'])->name('medium.update');
    Route::post('/medium/delete', [MediumController::class, 'medium_delete'])->name('medium.delete');

    //standard
    Route::get('standard-list', [StandardController::class, 'list_standard'])->name('standard.list');
    Route::get('create/standard-list', [StandardController::class, 'create_standard'])->name('standard.create');
    Route::post('standard-list/save', [StandardController::class, 'standard_list_save'])->name('standard_list.save');
    Route::post('/standard-list/edit', [StandardController::class, 'standard_list_edit'])->name('standard_list.edit');
    Route::post('standard/update', [StandardController::class, 'standard_update'])->name('standard.update');
    Route::post('/standard/delete', [StandardController::class, 'standard_delete'])->name('standard.delete');

    //stream
    Route::get('stream-list', [StreamController::class, 'list_stream'])->name('stream.list');
    Route::get('stream-create', [StreamController::class, 'create_stream'])->name('stream.create');
    Route::post('stream-save', [StreamController::class, 'stream_list_save'])->name('stream_list.save');
    Route::post('stream-edit', [StreamController::class, 'stream_list_edit'])->name('stream_list.edit');
    Route::post('stream-update', [StreamController::class, 'stream_update'])->name('stream.update');
    Route::post('stream-delete', [StreamController::class, 'stream_delete'])->name('stream.delete');

    //subject
    Route::get('subject-list', [SubjectController::class, 'list_subject'])->name('subject.list');
    Route::get('create/subject-list', [SubjectController::class, 'create_subject'])->name('subject.create');
    Route::POST('get/standard_wise_stream', [SubjectController::class, 'standard_wise_stream'])->name('standard_wise_stream.list');
    Route::post('subject-save', [SubjectController::class, 'subject_list_save'])->name('subject_list.save');
    Route::post('/subject/delete', [SubjectController::class, 'subject_delete'])->name('subject.delete');
    Route::post('/subject/edit', [SubjectController::class, 'subject_edit'])->name('subject.edit');
    Route::post('subject/update', [SubjectController::class, 'subject_update'])->name('subject.update');

    //chapter
    Route::get('add-lists', [ChapterController::class, 'add_lists'])->name('chapter.list');
    Route::post('chapter/get-subject', [ChapterController::class, 'get_subjects']);
    Route::post('chapter-save', [ChapterController::class, 'chapter_save'])->name('chapter.save');
    Route::post('chapter-list', [ChapterController::class, 'chapter_lists']);

    //topic
    Route::get('add-topic', [TopicController::class, 'index'])->name('add.topic');
    Route::post('chapter/get-chapter', [TopicController::class, 'get_chapter']);
    Route::post('topic-save', [TopicController::class, 'topic_save'])->name('topic.save');
    Route::post('topic-list', [TopicController::class, 'topic_list']);

    Route::get('video-time-limit', [Videotimelimitcontroller::class, 'list'])->name('videolimit.list'); //uploded video time limit
    Route::post('video-timelimit-save', [Videotimelimitcontroller::class, 'save'])->name('videolimit.save');
    Route::post('video-timelimit-edit', [Videotimelimitcontroller::class, 'edit']);
    Route::post('video-timelimit-update', [Videotimelimitcontroller::class, 'update']);
    Route::post('video-timelimit-delete', [Videotimelimitcontroller::class, 'destroy']);


    //do-business-with
    Route::get('do-business-with-list', [DoBusinessWithController::class, 'list'])->name('do_business_with.list');
    Route::get('create/do-business-with', [DoBusinessWithController::class, 'create'])->name('do_business_with.create');
    Route::post('do-business-with/save', [DoBusinessWithController::class, 'save'])->name('do_business_with.save');
    Route::post('/do-business-with/edit', [DoBusinessWithController::class, 'edit'])->name('do_business_with.edit');
    Route::post('do-business-with/update', [DoBusinessWithController::class, 'update'])->name('do_business_with.update');
    Route::post('/do-business-with/delete', [DoBusinessWithController::class, 'delete'])->name('do_business_with.delete');

    //video category
    Route::get('video-category-list', [VideoCategoryController::class, 'index'])->name('videocategory.list');
    Route::post('video-category-save', [VideoCategoryController::class, 'save'])->name('videocategory.save');
    Route::post('video-category-edit', [VideoCategoryController::class, 'edit'])->name('videocategory.edit');
    Route::post('video-category-update', [VideoCategoryController::class, 'update'])->name('videocategory.update');
    Route::post('video-category-delete', [VideoCategoryController::class, 'delete'])->name('videocategory.delete');

    //student
    Route::post('/student/list', [StudentsController::class, 'list_student'])->name('student.list');
    Route::post('/student/create', [StudentsController::class, 'create_student'])->name('student.create');
    Route::post('/student/save', [StudentsController::class, 'save_student'])->name('student.save');
    Route::post('/student/edit', [StudentsController::class, 'edit_student'])->name('student.edit');
    Route::post('/student/update', [StudentsController::class, 'update_student'])->name('student.update');
    Route::post('/student/view', [StudentsController::class, 'view_student'])->name('create_form_data.view');
    Route::get('/student/create-form-data', [StudentsController::class, 'createformdata'])->name('student.view');
    //banner
    Route::get('banner-list', [BannerController::class, 'list_banner'])->name('banner.list');
    Route::get('create/banner-list', [BannerController::class, 'create_banner'])->name('banner_list.create');
    Route::post('banner/save', [BannerController::class, 'save_banner'])->name('banner.save');
    Route::post('/banner/edit', [BannerController::class, 'edit_banner'])->name('banner.edit');
    Route::post('/banner/update', [BannerController::class, 'update_banner'])->name('banner.update');
    Route::post('/banner/delete', [BannerController::class, 'banner_delete'])->name('banner.delete');

    Route::post('institute/get-board', [InstituteController::class, 'get_board'])->name('institute.get.board');

    //email varification
    //Route::get('/update-value/{token}', [StudentController::class, 'verifyEmail'])->name('verifyEmail.get');
    //Route::get('/update-value/{token}', 'StudentController@verifyEmail');
    Route::get('announcement-create', [AnnouncementController::class, 'announcement_create']);
    Route::post('announcement/save', [AnnouncementController::class, 'save']);
    Route::post('/announcement/edit', [AnnouncementController::class, 'edit']);
    Route::post('announcement/update', [AnnouncementController::class, 'update']);
    Route::post('announcement/delete', [AnnouncementController::class, 'destroy']);
    Route::resource('banner-sizes', 'App\Http\Controllers\Admin\BannerSizeController');
    Route::post('/banner-sizes/edit', [BannerSizeController::class, 'edit']);
    Route::post('/banner-sizes/update', [BannerSizeController::class, 'update']);
    Route::post('banner-sizes/destroy', [BannerSizeController::class, 'destroy']);
});

Route::get('/update-value/{token}', [StudentController::class, 'verifyEmail'])->name('verifyEmail.get');


// Route::patch('/permissions/{user_id}', [PermissionController::class, 'update'])->middleware(['auth', 'verified'])->name('permissions.update');
Route::get('/push-notificaiton', [WebNotificationController::class, 'index'])->name('push-notificaiton');
Route::post('/store-token', [WebNotificationController::class, 'storeToken'])->name('store.token');
Route::post('/send-web-notification', [WebNotificationController::class, 'sendWebNotification'])->name('send.web-notification');
require __DIR__ . '/auth.php';
