<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\announcements_model;
use App\Models\Banner_model;
use App\Models\board;
use App\Models\Class_model;
use App\Models\Class_room_model;
use App\Models\Class_sub;
use App\Models\Dobusinesswith_Model;
use App\Models\Dobusinesswith_sub;
use App\Models\Institute_for_model;
use App\Models\Institute_board_sub;
use App\Models\Institute_detail;
use App\Models\Institute_for_sub;
use App\Models\Medium_model;
use App\Models\Medium_sub;
use App\Models\Standard_model;
use App\Models\Standard_sub;
use App\Models\Stream_sub;
use App\Models\Stream_model;
use App\Models\UserRoleMapping;
use App\Models\Subject_model;
use App\Models\Subject_sub;
use App\Models\Student_detail;
use App\Models\VideoAssignToBatch;
use App\Models\User;
use App\Models\Insutitute_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Base_table;
use App\Models\Batches_model;
use App\Models\Common_announcement;
use App\Models\Exam_Model;
use App\Models\Marks_model;
use App\Models\VideoCategory;
use Carbon\Carbon;
use PDO;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Models\Attendance_model;
use App\Models\Batch_assign_teacher_model;
use App\Models\Home_work_model;
use App\Models\Parents;
use App\Models\Remainder_model;
use App\Models\RoleHasPermission;
use App\Models\Staff_detail_Model;
use App\Models\Student_fees_model;
use App\Models\Teacher_model;
use App\Models\Timetable;
use App\Models\Timetables;
use App\Models\UserHasRole;
use Exception;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\TryCatch;
use Tymon\JWTAuth\Facades\JWTAuth;

class InstituteApiController extends Controller
{

    use ApiTrait;

    private function array_symmetric_diff(array $array1, array $array2)
    {
        $diff1 = array_diff($array1, $array2);
        $diff2 = array_diff($array2, $array1);
        return array_merge($diff1, $diff2);
    }
    
    public function get_institute_reponse(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                $basinstitute = Base_table::where('status', 'active')->select('institute_for')->groupby('institute_for')->pluck('institute_for')->toArray();
                $institute_for_array = DB::table('institute_for')
                    ->whereIN('id', $basinstitute)->get();
                $institute_for = [];
                foreach ($institute_for_array as $institute_for_array_value) {
                    $onlyboardfrombase = base_table::where('institute_for', $institute_for_array_value->id)
                        ->select('board')
                        ->groupby('board')
                        ->pluck('board')->toArray();
                    $board_array = board::whereIN('id', $onlyboardfrombase)
                        ->get();
                    $board = [];
                    foreach ($board_array as $board_array_value) {
                        $mediumsidget = base_table::where('board', $board_array_value->id)
                            ->where('institute_for', $institute_for_array_value->id)
                            ->select('medium')
                            ->groupby('medium')
                            ->pluck('medium')->toArray();
                        $medium_array = Medium_model::whereIN('id', $mediumsidget)->get();
                        $medium = [];
                        foreach ($medium_array as $medium_array_value) {

                            $classesidget = base_table::where('medium', $medium_array_value->id)
                                ->where('board', $board_array_value->id)
                                ->where('institute_for', $institute_for_array_value->id)
                                ->select('institute_for_class')
                                ->groupby('institute_for_class')
                                ->pluck('institute_for_class')->toArray();
                            $class_array = Class_model::whereIN('id', $classesidget)
                                ->get();

                            $class = [];
                            foreach ($class_array as $class_array_value) {

                                $standardidget = base_table::where('institute_for_class', $class_array_value->id)
                                    ->where('medium', $medium_array_value->id)
                                    ->where('board', $board_array_value->id)
                                    ->where('institute_for', $institute_for_array_value->id)
                                    ->select('standard', 'id')
                                    ->pluck('standard')->toArray();
                                $standard_array = Standard_model::whereIN('id', $standardidget)
                                    ->get();


                                $standard = [];
                                foreach ($standard_array as $standard_array_value) {
                                    $stream_array = DB::table('base_table')
                                        ->leftJoin('stream', 'stream.id', '=', 'base_table.stream')
                                        ->select('stream.name as stream_name', 'base_table.id', 'stream.id as stream_id')
                                        ->whereNull('base_table.deleted_at')
                                        ->where('base_table.standard', $standard_array_value->id)
                                        ->get();
                                    $stream = [];

                                    foreach ($stream_array as $stream_array_value) {

                                        $forsubdidget = base_table::where('institute_for_class', $class_array_value->id)
                                            ->where('institute_for', $institute_for_array_value->id)
                                            ->where('standard', $standard_array_value->id)
                                            ->where('board', $board_array_value->id)
                                            ->where('medium', $medium_array_value->id)
                                            ->orwhere('stream', $stream_array_value->id)
                                            ->select('standard', 'id')
                                            ->get();
                                        $baseidsfosubj = $forsubdidget->pluck('id')->toArray();
                                        if (!empty($stream_array_value->stream_id)) {
                                            $stream[] = [
                                                'stream_id' => $stream_array_value->stream_id . '',
                                                'stream' => $stream_array_value->stream_name . '',
                                            ];
                                        }
                                    }

                                    $subject_array = Subject_model::join('base_table', 'base_table.id', '=', 'subject.base_table_id')
                                        ->whereIN('subject.base_table_id', $baseidsfosubj)
                                        ->select('subject.*', 'base_table.stream')
                                        ->get();

                                    $subject = [];
                                    foreach ($subject_array as $value) {
                                        if ($value->stream != null) {
                                            $sstream = $value->stream;
                                        } else {
                                            $sstream = 0;
                                        }
                                        $subject[] = [
                                            'subject_id' => $value->id,
                                            'subject' => $value->name,
                                            'stream_id' => $sstream
                                        ];
                                    }
                                    $standard[] = [
                                        'standard_id' => $standard_array_value->id,
                                        'standard' => $standard_array_value->name,
                                        'stream' => $stream,
                                        'subject' => $subject
                                    ];
                                }

                                $class[] = [
                                    'class_id' => $class_array_value->id,
                                    'class_icon' => asset($class_array_value->icon),
                                    'class' => $class_array_value->name,
                                    'standard' => $standard,
                                ];
                            }

                            $medium[] = [
                                'medium_id' => $medium_array_value->id,
                                'medium_icon' => asset($medium_array_value->icon),
                                'medium' => $medium_array_value->name,
                                'class' => $class,
                            ];
                        }

                        $board[] = [
                            'board_id' => $board_array_value->id,
                            'board_icon' => asset($board_array_value->icon),
                            'board' => $board_array_value->name,
                            'medium' => $medium,
                        ];
                    }
                    $institute_for_name = $institute_for_array_value->name;

                    if (!isset($institute_for[$institute_for_name])) {
                        $institute_for[] = [
                            'institute_id' => $institute_for_array_value->id,
                            'institute_icon' => asset($institute_for_array_value->icon),
                            'institute_for' => $institute_for_name,
                            'board_details' => $board,
                        ];
                    } else {
                        $institute_for['board_details'][] = $board;
                    }
                    $institute_for = array_values($institute_for);
                }
                $dobusiness_with = Dobusinesswith_Model::where('status', 'active')
                    ->where(function ($query) {
                        $query->whereNull('created_by')
                            ->orWhere('created_by', 1);
                    })->get();
                $do_business_with = [];
                foreach ($dobusiness_with as $dobusinesswith_val) {
                    $do_business_with[] = array(
                        'id' => $dobusinesswith_val->id,
                        'name' => $dobusinesswith_val->name
                    );
                }
                $data = array(
                    'do_business_with' => $do_business_with,
                    'institute_details' => $institute_for
                );
                return $this->response($data, "Fetch Data Successfully");
            }
            return $this->response([], "Something went wrong!!", false, 400);
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }

    public function register_institute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'institute_for_id' => 'required',
            'institute_board_id' => 'required',
            'institute_for_class_id' => 'required',
            'institute_medium_id' => 'required',
            'institute_work_id' => 'required',
            'standard_id' => 'required',
            'subject_id' => 'required',
            'institute_name' => 'required|string',
            'address' => 'required|string',
            'contact_no' => 'required|integer|min:10',
            'email' => 'required|email',
            'logo' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            DB::beginTransaction();
            $subadminPrefix = 'ist_';
            $startNumber = 101;
            $lastInsertedId = DB::table('institute_detail')->orderBy('id', 'desc')->value('unique_id');
            if (!is_null($lastInsertedId)) {
                $number = substr($lastInsertedId, 3);
                $numbers = str_replace('_', '', $number);
                $newID = $numbers + 1;
            } else {
                $newID = $startNumber;
            }
            $paddedNumber = str_pad($newID, 3, '0', STR_PAD_LEFT);
            $unique_id = $subadminPrefix . $paddedNumber;
            $iconFile = $request->file('logo');
            $imagePath = $iconFile->store('icon', 'public');
            if ($request->hasFile('cover_photo')) {
                $iconFile2 = $request->file('cover_photo');
                $imagePath2 = $iconFile2->store('cover_photo', 'public');
            } else {
                $imagePath2 = '';
            }
            $currentDate = date("Y-m-d");
            $nextYearDate = date("Y-m-d", strtotime("+1 year"));
            $nextYear = date("Y-m-d", strtotime($nextYearDate));
            $dateString = $currentDate . " / " . $nextYear;
            $instituteDetail = Institute_detail::create([
                'unique_id' => $unique_id,
                'logo' => $imagePath,
                'about_us' => $request->about_us,
                'user_id' => $request->input('user_id'),
                'institute_name' => $request->input('institute_name'),
                'address' => $request->input('address'),
                'country_code' => $request->input('country_code'),
                'country_code_name' => $request->input('country_code_name'),
                'contact_no' => $request->input('contact_no'),
                'email' => $request->input('email'),
                'country' => $request->input('country'),
                'state' => $request->input('state'),
                'city' => $request->input('city'),
                'pincode' => $request->input('pincode'),
                'cover_photo' => $imagePath2,
                'status' => 'active',
                'start_academic_year' => $currentDate,
                'end_academic_year' => $nextYear
            ]);
            $lastInsertedId = $instituteDetail->id;
            $institute_name = $instituteDetail->institute_name;
            $subjectid = explode(',', $request->input('subject_id'));
            $sectsbbsiqy = Subject_model::whereIN('id', $subjectid)->pluck('base_table_id')->toArray();
            $uniqueArray = array_unique($sectsbbsiqy);
            $basedtqy = Base_table::whereIN('id', $uniqueArray)->get();
            foreach ($basedtqy as $svaluee) {
                $institute_for = $svaluee->institute_for;
                $board = $svaluee->board;
                $medium = $svaluee->medium;
                $institute_for_class = $svaluee->institute_for_class;
                $standard = $svaluee->standard;
                $stream = $svaluee->stream;

                $insfor = Institute_for_sub::where('institute_id', $lastInsertedId)
                    ->where('institute_for_id', $institute_for)->first();
                if (empty($insfor)) {
                    $createinstitutefor = Institute_for_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'institute_for_id' => $institute_for,
                    ]);
                }

                $bordsubr = Institute_board_sub::where('institute_id', $lastInsertedId)
                    ->where('institute_for_id', $institute_for)
                    ->where('board_id', $board)->first();

                if (empty($bordsubr)) {
                    $createboard = Institute_board_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'institute_for_id' => $institute_for,
                        'board_id' => $board,
                    ]);
                }

                $medadded = Medium_sub::where('institute_id', $lastInsertedId)
                    ->where('institute_for_id', $institute_for)
                    ->where('board_id', $board)
                    ->where('medium_id', $medium)
                    ->first();
                if (empty($medadded)) {
                    $createmedium = Medium_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'institute_for_id' => $institute_for,
                        'board_id' => $board,
                        'medium_id' => $medium,
                    ]);
                }

                $addedclas = Class_sub::where('institute_id', $lastInsertedId)
                    ->where('institute_for_id', $institute_for)
                    ->where('board_id', $board)
                    ->where('medium_id', $medium)
                    ->where('class_id', $institute_for_class)
                    ->first();
                if (empty($addedclas)) {
                    $createclass = Class_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'institute_for_id' => $institute_for,
                        'board_id' => $board,
                        'medium_id' => $medium,
                        'class_id' => $institute_for_class,
                    ]);
                }

                $stndsubd = Standard_sub::where('institute_id', $lastInsertedId)
                    ->where('institute_for_id', $institute_for)
                    ->where('board_id', $board)
                    ->where('medium_id', $medium)
                    ->where('class_id', $institute_for_class)
                    ->where('standard_id', $standard)
                    ->first();
                if (empty($stndsubd)) {
                    $createstnd = Standard_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'institute_for_id' => $institute_for,
                        'board_id' => $board,
                        'medium_id' => $medium,
                        'class_id' => $institute_for_class,
                        'standard_id' => $standard,
                    ]);
                }
                if ($stream != null) {
                    $addedsrm = Stream_sub::where('institute_id', $lastInsertedId)
                        ->where('institute_for_id', $institute_for)
                        ->where('board_id', $board)
                        ->where('medium_id', $medium)
                        ->where('class_id', $institute_for_class)
                        ->where('standard_id', $standard)
                        ->where('stream_id', $stream)
                        ->first();

                    if (empty($addedsrm)) {
                        $createstrem = Stream_sub::create([
                            'user_id' => $request->input('user_id'),
                            'institute_id' => $lastInsertedId,
                            'institute_for_id' => $institute_for,
                            'board_id' => $board,
                            'medium_id' => $medium,
                            'class_id' => $institute_for_class,
                            'standard_id' => $standard,
                            'stream_id' => $stream,
                        ]);
                    }
                }
            }
            $institute_work_id = explode(',', $request->input('institute_work_id'));
            foreach ($institute_work_id as $value) {
                if ($value == 'other') {
                    $instituteforadd = Dobusinesswith_Model::create([
                        'name' => $request->input('do_businesswith_name'),
                        'category_id' => $request->input('category_id'),
                        'created_by' => $request->input('user_id'),
                        'status' => 'active',
                    ]);
                    $dobusinesswith_id = $instituteforadd->id;
                } else {
                    $dobusinesswith_id = $value;
                }

                $addeddobusn = Dobusinesswith_sub::where('institute_id', $lastInsertedId)
                    ->where('do_business_with_id', $dobusinesswith_id)
                    ->first();

                if (empty($addeddobusn)) {
                    Dobusinesswith_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'do_business_with_id' => $dobusinesswith_id,
                    ]);
                }
            }
            $intitute_for_id = explode(',', $request->input('institute_for_id'));
            foreach ($intitute_for_id as $value) {
                if ($value == 5) {
                    $instituteforadd = institute_for_model::create([
                        'name' => $request->input('institute_for'),
                        'status' => 'active',
                    ]);
                    $institute_for_id = $instituteforadd->id;
                    Institute_for_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'institute_for_id' => $institute_for_id,
                    ]);
                } else {
                    $institute_for_id = $value;
                }
            }
            //board_sub
            $institute_board_id = explode(',', $request->input('institute_board_id'));
            foreach ($institute_board_id as $value) {
                if ($value == 4) {
                    $instituteboardadd = board::create([
                        'name' => $request->input('institute_board'),
                        'status' => 'active',
                    ]);
                    $instituteboard_id = $instituteboardadd->id;
                    Institute_board_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'board_id' => $instituteboard_id,
                    ]);
                } else {
                    $instituteboard_id = $value;
                }
                //end other
            }

            $subject_id = explode(',', $request->input('subject_id'));
            foreach ($subject_id as $value) {
                $suadeed = Subject_sub::where('institute_id', $lastInsertedId)
                    ->where('subject_id', $value)->first();

                if (empty($suadeed)) {
                    Subject_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'subject_id' => $value,
                    ]);
                }
            }
            $roles = UserHasRole::whereIn('id', [3, 4, 5, 6])->get();
            foreach ($roles as $role) {
                $user_has_roles = new UserHasRole();
                $user_has_roles->role_id = $role->role_id;
                $user_has_roles->user_id = Auth::id();
                if ($user_has_roles->save()) {
                    $role_has_permissions = RoleHasPermission::where('user_has_role_id', $role->role_id)->get();
                    foreach ($role_has_permissions as $role_has_permission) {
                        $new_role_has_permission = new RoleHasPermission();
                        $new_role_has_permission->user_has_role_id = $user_has_roles->id;
                        $new_role_has_permission->feature_id = $role_has_permission->feature_id;
                        $new_role_has_permission->action_id = $role_has_permission->action_id;
                        $new_role_has_permission->save();
                    }
                }
            }
            $data = [
                'institute_id' => $lastInsertedId,
                'institute_name' => $institute_name,
                'logo' => asset($imagePath)
            ];
            DB::commit();
            return $this->response($data, "institute create Successfully");
        } catch (Exception $e) {
            DB::rollback();
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    public function update_institute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'institute_for_id' => ['required', function ($attribute, $value, $fail) {
                $ids = explode(',', $value);
                foreach ($ids as $id) {
                    if (!Institute_for_model::where('id', $id)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                }
            }],
            'institute_board_id' => ['required', function ($attribute, $value, $fail) {
                $ids = explode(',', $value);
                foreach ($ids as $id) {
                    if (!board::where('id', $id)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                }
            }],
            'institute_for_class_id' => ['required', function ($attribute, $value, $fail) {
                $ids = explode(',', $value);
                foreach ($ids as $id) {
                    if (!Class_model::where('id', $id)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                }
            }],
            'institute_medium_id' => ['required', function ($attribute, $value, $fail) {
                $ids = explode(',', $value);
                foreach ($ids as $id) {
                    if (!Medium_model::where('id', $id)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                }
            }],
            'standard_id' => ['required', function ($attribute, $value, $fail) {
                $ids = explode(',', $value);
                foreach ($ids as $id) {
                    if (!Standard_model::where('id', $id)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                }
            }],
            'subject_id' => ['required', function ($attribute, $value, $fail) {
                $ids = explode(',', $value);
                foreach ($ids as $id) {
                    if (!Subject_model::where('id', $id)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                }
            }],

            'institute_id' => 'required|exists:institute_detail,id',

        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute = Institute_detail::where('id', $request->institute_id)->first();

            //start by priyanka
            $institute_subjects = Subject_sub::where('institute_id', $institute->id)->pluck('subject_id')->toArray();
            $institute_subject_ids = explode(',', $request->subject_id);
            $differenceInstituteSubjectArray = $this->array_symmetric_diff($institute_subjects, $institute_subject_ids);
            if (!empty($differenceInstituteSubjectArray)) {

                $sectsbbsiqy = Subject_model::whereIN('id', $differenceInstituteSubjectArray)->pluck('base_table_id')->toArray();
                $uniqueArray = array_unique($sectsbbsiqy);
                $basedtqy = Base_table::whereIN('id', $uniqueArray)->get();
                foreach ($basedtqy as $svaluee) {

                    $institute_for = $svaluee->institute_for;
                    $board = $svaluee->board;
                    $medium = $svaluee->medium;
                    $institute_for_class = $svaluee->institute_for_class;
                    $standard = $svaluee->standard;
                    $stream = $svaluee->stream;


                    //institute

                    $institute_for_check = Institute_for_sub::where('institute_id', $institute->id)
                        ->where('institute_for_id', $institute_for)->get();
                    $instiyutewArray = explode(',', $request->institute_for_id);
                    if ($institute_for_check->isEmpty()) {
                        if (in_array($institute_for, $instiyutewArray)) {
                            Institute_for_sub::create([
                                'user_id' => $institute->user_id,
                                'institute_id' => $institute->id,
                                'institute_for_id' => $institute_for
                            ]);
                        }
                    } else {
                        if (!in_array($institute_for, $instiyutewArray)) {
                            $student_check = Student_detail::where('institute_for_id', $institute_for)->where('institute_id', $institute->id)->get();
                            $teacher_check = Teacher_model::where('institute_for_id', $institute_for)->where('institute_id', $institute->id)->get();
                            if (!empty($student_check) && !empty($teacher_check)) {
                                return $this->response([], "Cannot remove institute_for. Already exist student and teacher this institute_for.", false, 400);
                            } else {
                                $delete_sub = Institute_for_sub::where('institute_for_id', $institute_for)->where('institute_id', $institute->id)->get();
                                if (!empty($delete_sub)) {
                                    foreach ($delete_sub as $did) {
                                        $did->delete();
                                    }
                                }
                            }
                        }
                    }

                    //board
                    $boardtewArray = explode(',', $request->institute_board_id);
                    $institute_board_check = Institute_board_sub::where('institute_id', $institute->id)
                        ->where('board_id', $board)->get();
                    $institute_subjects = Subject_sub::where('institute_id', $institute->id)->pluck('subject_id')->toArray();

                    if ($institute_board_check->isEmpty()) {
                        if (in_array($board, $boardtewArray)) {
                            Institute_board_sub::create([
                                'user_id' => $institute->user_id,
                                'institute_id' => $institute->id,
                                'board_id' => $board,
                                'institute_for_id' => $institute_for
                            ]);
                        }
                    } else {
                        $boardIds = Institute_board_sub::where('institute_id', $institute->id)->pluck('board_id')->toArray();
                        $boarddif = $this->array_symmetric_diff($boardtewArray, $boardIds);
                        if (!empty($boarddif)) {
                            if (!in_array($boarddif, $boardtewArray)) {
                                $student_check = Student_detail::wherein('board_id', $boarddif)->where('institute_id', $institute->id)->first();
                                $teacher_check = Teacher_model::wherein('board_id', $boarddif)->where('institute_id', $institute->id)->first();
                                if (!empty($student_check) || !empty($teacher_check)) {
                                    return $this->response([], "Cannot remove institute_board. Already exist student and teacher this institute_board.", false, 400);
                                } else {
                                    $delete_sub = Institute_board_sub::wherein('board_id', $boarddif)
                                        ->where('institute_id', $institute->id)->get();
                                    if (!empty($delete_sub)) {
                                        foreach ($delete_sub as $did) {
                                            $did->delete();
                                        }
                                    }
                                }
                            }
                        }
                        
                    }

                    //medium
                    $institute_medium_check = Medium_sub::where('institute_id', $institute->id)->where('board_id', $board)->where('medium_id', $medium)->get();
                    $mediumtewArray = explode(',', $request->institute_medium_id);
                    if ($institute_medium_check->isEmpty()) {
                        if (in_array($medium, $mediumtewArray)) {
                            Medium_sub::create([
                                'user_id' => $institute->user_id,
                                'institute_id' => $institute->id,
                                'medium_id' => $medium,
                                'institute_for_id' => $institute_for,
                                'board_id' => $board
                            ]);
                        }
                    } else {
                        if (!in_array($medium, $mediumtewArray)) {
                            $student_check = Student_detail::where('medium_id', $medium)->where('institute_id', $institute->id)->first();
                            $teacher_check = Teacher_model::where('medium_id', $medium)->where('institute_id', $institute->id)->first();
                            if (!empty($student_check) || !empty($teacher_check)) {
                                return $this->response([], "Cannot remove institute_medium. Already exist student or teacher in this institute_medium.", false, 400);
                            } else {
                                $delete_sub = Medium_sub::where('medium_id', $medium)->where('institute_id', $institute->id)->get();
                                if (!empty($delete_sub)) {
                                    foreach ($delete_sub as $did) {
                                        $did->delete();
                                    }
                                }
                            }
                        }
                    }

                    //class
                    $class_medium_check = Class_sub::where('institute_id', $institute->id)
                        ->where('class_id', $institute_for_class)->get();
                    $classtewArray = explode(',', $request->institute_for_class_id);
                    if ($class_medium_check->isEmpty()) {
                        if (in_array($institute_for_class, $classtewArray)) {
                            Class_sub::create([
                                'user_id' => $institute->user_id,
                                'institute_id' => $institute->id,
                                'class_id' => $institute_for_class,
                                'institute_for_id' => $institute_for,
                                'board_id' => $board,
                                'medium_id' => $medium
                            ]);
                        }
                    } else {
                        if (!in_array($institute_for_class, $classtewArray)) {
                            $student_check = Student_detail::where('class_id', $institute_for_class)->where('institute_id', $institute->id)->first();
                            $teacher_check = Teacher_model::where('class_id', $institute_for_class)->where('institute_id', $institute->id)->first();
                            if (!empty($student_check) || !empty($teacher_check)) {
                                return $this->response([], "Cannot remove class_medium. Already exist student or teacher in this class_medium.", false, 400);
                            } else {
                                $delete_sub = Class_sub::where('class_id', $institute_for_class)->where('institute_id', $institute->id)->get();
                                if (!empty($delete_sub)) {
                                    foreach ($delete_sub as $did) {
                                        $did->delete();
                                    }
                                }
                            }
                        }
                    }

                    //standard
                    $standard_medium_check = Standard_sub::where('institute_id', $institute->id)
                        ->where('standard_id', $standard)
                        ->where('board_id', $board)
                        ->where('medium_id', $medium)->first();
                    $standardewArray = explode(',', $request->standard_id);
                    if (!$standard_medium_check) {
                        if (in_array($standard, $standardewArray)) {
                            Standard_sub::create([
                                'user_id' => $institute->user_id,
                                'institute_id' => $institute->id,
                                'standard_id' => $standard,
                                'institute_for_id' => $institute_for,
                                'board_id' => $board,
                                'medium_id' => $medium,
                                'class_id' => $institute_for_class
                            ]);
                        }
                    } else {
                        if (!in_array($standard, $standardewArray)) {
                            $student_check = Student_detail::where('standard_id', $standard)->where('institute_id', $institute->id)->first();
                            $teacher_check = Teacher_model::where('standard_id', $standard)->where('institute_id', $institute->id)->first();
                            if (!empty($student_check) || !empty($teacher_check)) {
                                return $this->response([], "Cannot remove standard. Already exist student or teacher in this standard_medium.", false, 400);
                            } else {
                                $delete_sub = Standard_sub::where('standard_id', $standard)
                                    ->where('institute_id', $institute->id)
                                    ->where('board_id', $board)
                                    ->where('medium_id', $medium)
                                    ->get();
                                if (!empty($delete_sub)) {
                                    foreach ($delete_sub as $did) {
                                        $did->delete();
                                    }
                                }
                            }
                        }
                    }

                    //stream
                    if (!empty($stream) || $stream != '') {
                        $stream_medium_check = Stream_sub::where('institute_id', $institute->id)->where('stream_id', $stream)->get();
                        if ($stream_medium_check->isEmpty()) {
                            Stream_sub::create([
                                'user_id' => $institute->user_id,
                                'institute_id' => $institute->id,
                                'stream_id' => $stream,
                                'institute_for_id' => $institute_for,
                                'board_id' => $board,
                                'medium_id' => $medium,
                                'class_id' => $institute_for_class,
                                'standard_id' => $standard
                            ]);
                        } else {
                            $streamewArray = explode(',', $request->stream_id);
                            if (!in_array($stream, $streamewArray)) {
                                $student_check = Student_detail::where('stream_id', $stream)->where('institute_id', $institute->id)->first();
                                $teacher_check = Teacher_model::where('stream_id', $stream)->where('institute_id', $institute->id)->first();
                                if (!empty($student_check) || !empty($teacher_check)) {
                                    return $this->response([], "Cannot remove stream_medium. Already exist student or teacher in this stream_medium.", false, 400);
                                } else {
                                    $delete_sub = Stream_sub::where('stream_id', $stream)->where('institute_id', $institute->id)->get();
                                    if (!empty($delete_sub)) {
                                        foreach ($delete_sub as $did) {
                                            $did->delete();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                //subject
                $institute_subject_check = Subject_sub::where('institute_id', $institute->id)->whereIn('subject_id', $differenceInstituteSubjectArray)->pluck('subject_id');
                if ($institute_subject_check->isEmpty()) {
                    foreach ($institute_subject_ids as $institute_subject_id) {
                        $sub_instboard_exists = Subject_sub::where('institute_id', $institute->id)->where('subject_id', $institute_subject_id)->first();
                        if (!$sub_instboard_exists) {
                            Subject_sub::create([
                                'user_id' => $institute->user_id,
                                'institute_id' => $institute->id,
                                'subject_id' => $institute_subject_id,
                            ]);
                        }
                    }
                } else {
                    $subjewArray = explode(',', $request->subject_id);
                    if (!in_array($institute_subject_check, $subjewArray)) {
                        $student_check = Student_detail::whereIn('subject_id', $institute_subject_check)->where('institute_id', $institute->id)->first();
                        $teacher_check = Teacher_model::whereIn('subject_id', $institute_subject_check)->where('institute_id', $institute->id)->first();
                        if (!empty($student_check) || !empty($teacher_check)) {
                            return $this->response([], "Cannot remove institute_subject. Already exist student and teacher for this institute_subject.", false, 400);
                        } else {
                            $delete_sub = Subject_sub::whereIn('subject_id', $institute_subject_check)->where('institute_id', $institute->id)->get();
                            if (!empty($delete_sub)) {
                                foreach ($delete_sub as $did) {
                                    $did->delete();
                                }
                            }
                        }
                    }
                }
            }

            //end
        return $this->response([], "institute Update Successfully");
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }


    public function get_homescreen_first(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $user_id = $request->user_id;
            $perPage = $request->input('per_page', 10);
            $institute_id = $request->institute_id;
            $uniqueBoardIds = Institute_board_sub::where('institute_id', $institute_id)
                ->distinct()
                ->pluck('board_id')
                ->toArray();

            // Fetch board details
            $board_list = Board::whereIn('id', $uniqueBoardIds)->get(['id', 'name', 'icon']);

            $board_array = [];
            foreach ($board_list as $board) {
                $medium_sublist = DB::table('medium_sub')
                    ->where('board_id', $board->id)
                    ->where('institute_id', $institute_id)
                    ->pluck('medium_id')->toArray();
                $uniquemediumds = array_unique($medium_sublist);

                $medium_list = Medium_model::whereIN('id', $uniquemediumds)->get();

                $medium_array = [];
                foreach ($medium_list as $medium_value) {
                    $medium_array[] = [
                        'id' => $medium_value->id,
                        'medium_name' => $medium_value->name,
                        'medium_icon' => asset($medium_value->icon)
                    ];
                }
                $board_array[] = [
                    'id' => $board->id,
                    'board_name' => $board->name,
                    'board_icon' => asset($board->icon),
                    'medium' => $medium_array,
                ];
            }

            // Fetch banners
            $banner_list = Banner_model::where(function ($query) use ($user_id, $institute_id) {
                $query
                    ->where('user_id', $user_id)
                    ->where('status', 'active')
                    ->where('institute_id', $institute_id);
            })
                ->get(['id', 'banner_image', 'url']);
            if ($banner_list->isEmpty()) {
                // If no results found, use the second condition
                $banner_list = Banner_model::where(function ($query) {
                    $query->where('status', 'active')
                        ->where('user_id', 1);
                })
                    ->get(['id', 'banner_image', 'url']);
            }

            $banner_array = $banner_list->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'banner_image' => asset($banner->banner_image),
                    'url' => $banner->url ?? ''
                ];
            })->toArray();

            // Fetch announcements
            $announcement_list = Common_announcement::whereRaw("FIND_IN_SET($institute_id, institute_id)")
                ->where('created_at', '>=', now()->subDays(15))
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['title', 'announcement', 'created_at']);

            $announcement = $announcement_list->toArray()['data'];
            $response = [
                'banner' => $banner_array,
                'board' => $board_array,
                'announcement' => $announcement,
            ];
            return $this->response($response, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }

    public function get_homescreen_second(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'board_id' => 'required|exists:board,id',
            'medium_id' => 'required|exists:medium,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $institute_id = $request->institute_id;

            if (empty($institute_id)) {
                $user_id = Auth::id();
                $institute_id = Institute_detail::where('user_id', $user_id)->first();
            }
            $standard_list = DB::table('standard_sub')
                ->join('standard', 'standard_sub.standard_id', '=', 'standard.id')
                ->select('standard.*')
                ->where('standard_sub.institute_id', $institute_id)
                ->where('standard_sub.board_id',  $request->board_id)
                ->where('standard_sub.medium_id', $request->medium_id)
                ->orderByRaw('CAST(standard.name AS UNSIGNED), standard.name')
                ->get();  

            $standard_array = [];
            foreach ($standard_list as $standard_value) {

                $getbsiqy = Base_table::where('board',  $request->board_id)
                    ->where('medium', $request->medium_id)
                    ->where('standard', $standard_value->id)
                    ->pluck('id')
                    ->toArray();
                $subject_list = DB::table('subject_sub')
                    ->join('subject', 'subject_sub.subject_id', '=', 'subject.id')
                    ->select('subject.*')
                    ->where('subject_sub.institute_id', $institute_id)
                    ->whereIN('subject.base_table_id', $getbsiqy)
                    ->get();

                $subject_array = [];
                foreach ($subject_list as $subject_value) {
                    $subject_array[] = [
                        'id' => $subject_value->id,
                        'subject_value' => $subject_value->name,
                        'image' => !empty($subject_value->image) ? asset($subject_value->image) : '',
                    ];
                }
                //batch list
                $batchqY = Batches_model::join('board', 'board.id', '=', 'batches.board_id')
                    ->join('medium', 'medium.id', '=', 'batches.medium_id')
                    ->leftjoin('stream', 'stream.id', '=', 'batches.stream_id')
                    ->where('batches.institute_id', $institute_id)
                    ->where('batches.standard_id', $standard_value->id)
                    ->select('batches.*', 'board.name as board', 'medium.name as medium', 'stream.name as stream')->get();
                $batchesDT = [];
                foreach ($batchqY as $batDT) {
                    $subids = explode(",", $batDT->subjects);
                    $batSubQY = Subject_model::whereIN('id', $subids)->get();
                    $subects = [];
                    foreach ($batSubQY as $batDt) {
                        $subects[] = array('id' => $batDt->id, 'subject_name' => $batDt->name);
                    }

                    $batchesDT[] = array(
                        'id' => $batDT->id,
                        'batch_name' => $batDT->batch_name,
                        'board' => $batDT->board,
                        'medium' => $batDT->medium,
                        'stream' => $batDT->stream,
                        'subjects' => $subects
                    );
                }

                $standard_array[] = [
                    'id' => $standard_value->id,
                    'standard_name' => $standard_value->name,
                    'subject' => $subject_array,
                    'batches' => $batchesDT
                ];
            }
            return $this->response($standard_array, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }


    public function get_request_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $user = Auth::user();
            $request_list = Student_detail::where('institute_id', $request->institute_id)
                ->where('status', '0')
                ->get();
            if (!empty($request_list)) {
                $response = $request_list->filter(function ($value) {
                    return $user_data = User::find($value->student_id);
                })->map(function ($value) {
                    $user_data = User::find($value->student_id);
                    return [
                        'student_id' => $user_data->id,
                        'name' => $user_data->firstname . ' ' . $user_data->lastname,
                        'photo' => $user_data->image,
                    ];
                })->toArray();
                return $this->response($response, "Fetch student request list.");
            } else {
                return $this->response([], "student not found.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }

    public function get_reject_request_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $student_id = Student_detail::where('institute_id', $request->institute_id)
                ->where('status', '2')
                ->where('created_at', '>=', Carbon::now()->subDays(15))
                ->pluck('student_id');
            if (!empty($student_id)) {
                $response = User::whereIn('id', $student_id)
                    ->get(['id', 'firstname', 'lastname', 'image'])
                    ->map(function ($user) {
                        return [
                            'student_id' => $user->id,
                            'name' => $user->firstname . ' ' . $user->lastname,
                            'photo' => $user->image,
                        ];
                    })->toArray();
                return $this->response($response, "Fetch student Reject list.");
            }
            return $this->response([], "Successfully Reject Request.");
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }
    public function get_accept_request_convert(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'student_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $response = Student_detail::where('institute_id', $request->institute_id)->where('student_id', $request->student_id)->update(['status' => '0']);
           
            return $this->response([], "Successfully Request Convert.");
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }


    public function get_reject_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'student_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            
            
            
            $response = Student_detail::where('institute_id', $request->institute_id)
            ->where('student_id', $request->student_id)
            ->update(['status' =>'2']);

            if ($response) {
                Student_detail::where('institute_id', $request->institute_id)
                    ->where('student_id', $request->student_id)
                    ->increment('reject_count', 1);
            }
            $totalrcount = Student_detail::where('institute_id', $request->institute_id)
                    ->where('student_id', $request->student_id)
                    ->select('reject_count')->first();
            if($totalrcount->reject_count >= 2){
                return $this->response([], "You already remove this student.");
            }else{    
            
            $serverKey = env('SERVER_KEY');

            $url = "https://fcm.googleapis.com/fcm/send";
            $users = User::where('id', $request->student_id)->pluck('device_key');

            $notificationTitle = "Your Request Rejected!!";
            $notificationBody = "Your Teacher Request Rejected!!";

            $data = [
                'registration_ids' => $users,
                'notification' => [
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
            ];

            if ($users->isNotEmpty()) {
                $json = json_encode($data);

                $headers = [
                    'Content-Type: application/json',
                    'Authorization: key=' . $serverKey
                ];

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $json,
                    CURLOPT_HTTPHEADER => $headers,
                ]);

                $result = curl_exec($ch);

                if ($result === FALSE) {
                }

                curl_close($ch);
            }
            return $this->response([], "Successfully Reject Request.");
        }
        } catch (Exception $e) {
            return $this->response([], "Somthing went wrong.", false, 400);
        }
    }

    public function fetch_student_detail(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'institute_id' => 'required|integer',
        ]);

        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $user_list = Student_detail::join('users', 'users.id', '=', 'students_details.student_id')
                ->join('board', 'board.id', '=', 'students_details.board_id')
                ->join('medium', 'medium.id', '=', 'students_details.medium_id')
                ->join('standard', 'standard.id', '=', 'students_details.standard_id')
                ->leftjoin('stream', 'stream.id', '=', 'students_details.stream_id')
                ->where('students_details.student_id', $request->student_id)
                ->where('students_details.institute_id', $request->institute_id)
                ->select(
                    'students_details.*',
                    'users.firstname',
                    'users.lastname',
                    'users.dob',
                    'users.address',
                    'users.email',
                    'users.country_code',
                    'users.country_code_name',
                    'users.mobile',
                    'board.name as board',
                    'medium.name as medium',
                    'standard.name as standard',
                    'stream.name as stream'
                )
                ->first();
            if ($user_list) {
                $subjids = explode(',', $user_list->subject_id);
                $subjcts = Subject_model::whereIN('id', $subjids)->get();
                $subjectslist = [];
                foreach ($subjcts as $subDT) {
                    $subjectslist[] = array(
                        'id' => $subDT->id,
                        'name' => $subDT->name,
                        'image' => asset($subDT->image)
                    );
                }

                $response_data = [
                    'student_id' => $user_list->student_id,
                    'institute_id' => $user_list->institute_id,
                    'first_name' => $user_list->firstname,
                    'last_name' => $user_list->lastname,
                    'date_of_birth' => (!empty($user_list->dob)) ? date('d-m-Y', strtotime($user_list->dob)) : '',
                    'address' => $user_list->address,
                    'email' => $user_list->email,
                    'country_code' => $user_list->country_code,
                    'country_code_name' => $user_list->country_code_name,
                    'mobile_no' => $user_list->mobile,
                    'board' => $user_list->board,
                    'board_id' => $user_list->board_id,
                    'medium' => $user_list->medium,
                    'medium_id' => $user_list->medium_id,
                    'standard' => $user_list->standard,
                    'standard_id' => $user_list->standard_id,
                    'stream' => $user_list->stream,
                    'stream_id' => $user_list->stream_id,
                    'subject_list' => $subjectslist,
                    'reject_count'=>$user_list->reject_count

                ];
                return $this->response($response_data, "Successfully Fetch data.");
            } else {
                return $this->response([], "Successfully Fetch data.");
            }
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }

    public function student_fees_calculation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'student_id' => 'required',
            'subject_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        $subject_amount = Subject_sub::join('subject', 'subject.id', '=', 'subject_sub.subject_id')
            ->where('institute_id', $request->institute_id)
            ->whereIn('subject_id', explode(',', $request->subject_id))
            ->select('subject_sub.amount', 'subject.name as subject_name')
            ->get();

        $amount = 0;
        $emptyAmountCount = 0;
        $emptyAmountSubjects = [];

        foreach ($subject_amount as $value) {

            if ($value->amount == 0) {
                $emptyAmountCount++;
                $emptyAmountSubjects[] = $value->subject_name;
            } else {
                $amount += $value->amount;
            }
        }
        if ($emptyAmountCount != 0) {
            return $this->response($emptyAmountSubjects, "Fees for the selected student's subjects are empty. Can you approve the student without fees? Otherwise, add the fees for the subjects.");
        }
        return $this->response([], 'Approve Screen.');
    }
    function student_fees_calculation2(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'student_id' => 'required|exists:users,id'

        ]);
          
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $selected_subject = Student_detail::where('institute_id', $request->institute_id)
            ->where('student_id', $request->student_id)
            ->first();

            $subject_ids = explode(',', $request->subject_id);
            
            $enter_subject = array_diff($subject_ids, explode(',',$selected_subject->subject_id));
            
            foreach($enter_subject as $subject_id){
                $subject_fees=Subject_sub::where('institute_id',$request->institute_id)->where('subject_id',$subject_id)->get();
                foreach($subject_fees as $value1){
                    if($value1->amount=='' ||  $value1->amount==0){
                       return $this->response([], "Fees for the selected student's subjects are empty. Can you approve the student without fees? Otherwise, add the fees for the subjects."); 
                    }else{
                        return $this->response([], 'Approve Screen.');
                    }
                }
            }
            if(empty($enter_subject)){
                        return $this->response([], 'Approve Screen.');

            }
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!.", false, 400);
        }  
    }
    public function add_student(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'user_id' => 'required',
            'country_code' => 'required',
            'country_code_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }


        try {
            // DB::beginTransaction();
            $institute_id = $request->institute_id;
            $existingUser = User::where('id', $request->user_id)->first();
            if ($existingUser->role_type == 6) {
                $student_id = $request->user_id;
                $institute_id = $request->institute_id;
                $getuidfins = Institute_detail::where('id', $institute_id)->first();
                $user_id = $getuidfins->user_id;
            } else {
                $student_id = $request->student_id;
                $institute_id = $request->institute_id;
                $user_id = $request->user_id;
            }

            $batch_id = $request->batch_id;
            $studentdtls = Student_detail::where('student_id', $student_id)
                ->where('institute_id', $institute_id)->first();
            
            $insdelQY = Standard_sub::where('board_id', $request->board_id)
                ->where('medium_id', $request->medium_id)
                ->where('standard_id', $request->standard_id)
                ->where('institute_id', $institute_id)
                ->first();
            if (empty($insdelQY)) {
                return $this->response([], 'institute are not working for this standard Please Select Currect Data.', false, 400);
            }

            if (!empty($studentdtls)) {
                if ($existingUser->role_type != 6) {
                    $studentupdetail = [
                        'user_id' => $user_id,
                        'institute_id' => $request->institute_id,
                        'student_id' => $student_id,
                        'institute_for_id' => $insdelQY->institute_for_id,
                        'board_id' =>  $request->board_id,
                        'medium_id' => $request->medium_id,
                        'class_id' => $insdelQY->class_id,
                        'standard_id' => $request->standard_id,
                        'stream_id' => $request->stream_id,
                        'subject_id' => $request->subject_id,
                        'batch_id' => $batch_id,
                        'status' => '1',
                    ];

                    if ($request->stream_id == 'null' || $request->stream_id == '') {
                        $studentupdetail['stream_id'] = null;
                    }

                    $studentdetail = Student_detail::where('student_id', $student_id)
                        ->where('institute_id', $institute_id)
                        ->update($studentupdetail);

                    if (!empty($studentdetail) && !empty($request->first_name)) {
                        //student detail update
                        $student_details = User::find($student_id);
                        $data = $student_details->update([
                            'firstname' => $request->first_name,
                            'lastname' => $request->last_name,
                            'dob' => date('d-m-Y', strtotime($request->date_of_birth)),
                            'address' => $request->address,
                            'email' => $request->email_id,
                            'country_code' => $request->country_code,
                            'country_code_name' => $request->country_code_name,
                            'mobile' => $request->mobile_no,
                            'status' => '1',
                        ]);
                        $subject_amount = Subject_sub::where('institute_id', $request->institute_id)
                            ->whereIn('subject_id', explode(',', $request->subject_id))
                            ->select('amount')
                            ->get();
                        // print_r($subject_amount);exit;    

                        $amount = 0;
                        foreach ($subject_amount as $value) {

                            $amount += $value->amount;
                        }
                        //echo $amount;exit;
                        $studentFee = Student_fees_model::where('student_id', $student_id);
                        if ($studentFee) {
                            $studentFee->create([
                                'user_id' => $user_id,
                                'institute_id' => $request->institute_id,
                                'student_id' => $student_id,
                                'subject_id' => $request->subject_id,
                                'total_fees' => (!empty($amount)) ? (float)$amount : 0.00,
                            ]);
                        }

                        $response = Student_detail::join('users', 'users.id', 'students_details.student_id')
                            ->join('standard', 'standard.id', 'students_details.standard_id')
                            ->where('students_details.institute_id', $institute_id)
                            ->where('students_details.student_id', $student_id)
                            ->select('students_details.*', 'users.firstname', 'users.lastname', 'standard.name as standardn')
                            ->first();
                        $subcts = Subject_model::whereIN('id', explode(",", $response->subject_id))->get();
                        $sujids = [];
                        foreach ($subcts as $subnames) {
                            $sujids[] = ['subname' => $subnames->name, 'image' => (!empty($subnames->image)) ? url($subnames->image) : asset('profile/no-image.png'),];
                        }

                        $reject_list = Student_detail::find($response->id);
                        $data = $reject_list->update(['status' => '1']);


                        $prnts = Parents::join('users', 'users.id', 'parents.parent_id')
                            ->join('institute_detail', 'institute_detail.id', 'parents.institute_id')
                            ->where('parents.student_id', $student_id)
                            ->where('parents.institute_id', $institute_id)
                            ->select(
                                'users.firstname',
                                'users.lastname',
                                'users.email',
                                'parents.id',
                                'institute_detail.institute_name',
                                'institute_detail.address',
                                'institute_detail.email as Iemail',
                                'institute_detail.contact_no',
                                'institute_detail.website_link',
                                'institute_detail.start_academic_year',
                                'institute_detail.end_academic_year'
                            )
                            ->get();
                        foreach ($prnts as $prdetail) {
                            $startAcademicYear = $prdetail->start_academic_year;
                            $startDate = Carbon::parse($startAcademicYear);
                            $syear = $startDate->year;

                            $endAcademicYear = $prdetail->end_academic_year;
                            $endtDate = Carbon::parse($endAcademicYear);
                            $eyear = $endtDate->year;

                            $parDT = [
                                'name' => $prdetail['firstname'] . ' ' . $prdetail['lastname'],
                                'sname' => $response['firstname'] . ' ' . $response['lastname'],
                                'email' => $prdetail->email,
                                'standard' => $response->standardn,
                                'id' => $prdetail->id,
                                'institute' => $prdetail->institute_name,
                                'address' => $prdetail->address,
                                'Iemail' => $prdetail->Iemail,
                                'contact_no' => $prdetail->contact_no,
                                'website_link' => $prdetail->website_link,
                                'year' => $syear . '-' . $eyear,
                                'subjects' => $sujids
                            ];

                            Mail::to($prdetail->email)->send(new WelcomeMail($parDT));
                        }

                        $serverKey = env('SERVER_KEY');

                        $url = "https://fcm.googleapis.com/fcm/send";
                        $users = User::where('id', $student_id)->pluck('device_key');

                        $notificationTitle = "Your Request Accepted successfully!!";
                        $notificationBody = "";

                        $data = [
                            'registration_ids' => $users,
                            'notification' => [
                                'title' => $notificationTitle,
                                'body' => $notificationBody,
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            ],
                        ];

                        if ($users->isNotEmpty()) {
                            $json = json_encode($data);

                            $headers = [
                                'Content-Type: application/json',
                                'Authorization: key=' . $serverKey
                            ];

                            $ch = curl_init();
                            curl_setopt_array($ch, [
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => $json,
                                CURLOPT_HTTPHEADER => $headers,
                            ]);

                            $result = curl_exec($ch);

                            if ($result === FALSE) {
                            }

                            curl_close($ch);
                        }
                        return $this->response([], 'Successfully Update Student.');
                    } else {
                        return $this->response([], 'Not Inserted.', false, 400);
                    }
                } else {
                    return $this->response([], "Something went wrong.", false, 400);
                }
            } else {
                $parets = Parents::where('student_id', $student_id)->get();

                if ($parets->isEmpty()) {
                    return $this->response([], 'Please add parents detail first.', false, 400);
                } else {
                    if ($existingUser->role_type != 6 && empty($request->student_id)) {
                        $data = user::create([
                            'firstname' => $request->first_name,
                            'lastname' => $request->last_name,
                            'dob' =>  date('d-m-Y', strtotime($request->date_of_birth)),
                            'address' => $request->address,
                            'email' => $request->email_id,
                            'country_code' => $request->country_code,
                            'country_code_name' => $request->country_code_name,
                            'mobile' => $request->mobile_no,
                        ]);
                        $student_id = $data->id;
                    } else {
                        $student_id = $student_id;
                        $student_details = User::find($student_id);
                        $data = $student_details->update([
                            'firstname' => $request->first_name,
                            'lastname' => $request->last_name,
                            'dob' => date('d-m-Y', strtotime($request->date_of_birth)),
                            'address' => $request->address,
                            'email' => $request->email_id,
                            'country_code' => $request->country_code,
                            'country_code_name' => $request->country_code_name,
                            'mobile' => $request->mobile_no,
                        ]);
                    }
                    if (!empty($student_id)) {
                        $studentdetail = [
                            'user_id' => $user_id,
                            'institute_id' => $request->institute_id,
                            'student_id' => $student_id,
                            'institute_for_id' => $insdelQY->institute_for_id,
                            'board_id' =>  $request->board_id,
                            'medium_id' => $request->medium_id,
                            'class_id' => $insdelQY->class_id,
                            'standard_id' => $request->standard_id,
                            'batch_id' => $batch_id,
                            'stream_id' => $request->stream_id,
                            'subject_id' => $request->subject_id,
                            'status' => '0',
                        ];
                        if ($request->stream_id == 'null' || $request->stream_id == '') {
                            $studentdetail['stream_id'] = null;
                        }

                        $studentdetailadd = Student_detail::create($studentdetail);
                        $parets = Parents::where('student_id', $student_id)->where('verify', '0')->get();

                        if (!$parets->isEmpty()) {

                            foreach ($parets as $prdtl) {
                                $parnsad = Parents::where('id', $prdtl->id)->update([
                                    'institute_id' => $request->institute_id
                                ]);
                            }
                        } else {
                            $pare = Parents::where('student_id', $student_id)
                                ->where('institute_id', $request->institute_id)->get();
                            if ($pare->isEmpty()) {
                                $paretsd = Parents::where('student_id', $student_id)->get();
                                foreach ($paretsd as $prdtl) {
                                    $parnsad = Parents::create([
                                        'student_id' =>  $student_id,
                                        'parent_id' => $prdtl->parent_id,
                                        'institute_id' => $request->institute_id,
                                        'relation' => $prdtl->relation,
                                        'verify' => '0',
                                    ]);
                                }
                            }
                        }

                        $serverKey = env('SERVER_KEY');

                        $url = "https://fcm.googleapis.com/fcm/send";
                        $user_detail = User::where('id', $student_id)->first();
                        $institute_user_id = institute_detail::where('id', $request->institute_id)->pluck('user_id');
                        $users = User::where('id', $institute_user_id)->pluck('device_key');
                        $notificationTitle = $user_detail->firstname . ' ' . $user_detail->lastname . " Send Request!!";
                        $notificationBody = "";

                        $data = [
                            'registration_ids' => $users,
                            'notification' => [
                                'title' => $notificationTitle,
                                'body' => $notificationBody,
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            ],
                        ];

                        if ($users->isNotEmpty()) {
                            $json = json_encode($data);

                            $headers = [
                                'Content-Type: application/json',
                                'Authorization: key=' . $serverKey
                            ];

                            $ch = curl_init();
                            curl_setopt_array($ch, [
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => $json,
                                CURLOPT_HTTPHEADER => $headers,
                            ]);

                            $result = curl_exec($ch);

                            if ($result === FALSE) {
                            }

                            curl_close($ch);
                        }
                        //
                        return $this->response([], 'Request sent Successfully!');
                    } else {
                        return $this->response([], 'Not Inserted.', false, 400);
                    }
                }
            }
        } catch (\Exception $e) {
            return $e;
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }

    //institute all detail
    public function institute_details(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_id = $request->institute_id;

            $instituteDTS = Institute_detail::where('id', $institute_id)->first();
            $user_id = $instituteDTS->user_id;

            $institute_for = Institute_for_model::join('institute_for_sub', 'institute_for.id', '=', 'institute_for_sub.institute_for_id')
                ->where('institute_for_sub.institute_id', $institute_id)
                ->where('institute_for_sub.user_id', $user_id)
                ->select('institute_for.*')
                ->distinct()->get();

            $institute_fors = [];
            foreach ($institute_for as $inst_forsd) {
                $board = Board::join('board_sub', function ($join) use ($institute_id, $user_id, $inst_forsd) {
                    $join->on('board.id', '=', 'board_sub.board_id')
                        ->where('board_sub.institute_id', $institute_id)
                        ->where('board_sub.user_id', $user_id)
                        ->where('board_sub.institute_for_id', $inst_forsd->id);
                })
                    ->whereNull('board.deleted_at')
                    ->select('board.*')
                    ->get();



                $boards = [];
                foreach ($board as $boardsdt) {
                    $medium = Medium_model::join('medium_sub', 'medium.id', '=', 'medium_sub.medium_id')
                        ->where('medium_sub.institute_id', $institute_id)
                        ->where('medium_sub.user_id', $user_id)
                        ->where('medium_sub.institute_for_id', $inst_forsd->id)
                        ->where('medium_sub.board_id', $boardsdt->id)
                        ->select('medium.*')
                        ->distinct()->get();
                    $mediums = [];
                    foreach ($medium as $mediumdt) {
                        $class = Class_model::join('class_sub', 'class.id', '=', 'class_sub.class_id')
                            ->where('class_sub.institute_id', $institute_id)
                            ->where('class_sub.user_id', $user_id)
                            ->where('class_sub.institute_for_id', $inst_forsd->id)
                            ->where('class_sub.board_id', $boardsdt->id)
                            ->where('class_sub.medium_id', $mediumdt->id)
                            ->select('class.*')
                            ->distinct()->get();
                        $classs = [];
                        foreach ($class as $classdt) {

                            $standard = Standard_model::join('standard_sub', 'standard.id', '=', 'standard_sub.standard_id')
                                ->where('standard_sub.institute_id', $institute_id)
                                ->where('standard_sub.user_id', $user_id)
                                ->where('standard_sub.institute_for_id', $inst_forsd->id)
                                ->where('standard_sub.board_id', $boardsdt->id)
                                ->where('standard_sub.medium_id', $mediumdt->id)
                                ->where('standard_sub.class_id', $classdt->id)
                                ->select('standard.*')
                                ->distinct()->get();

                            $standards = [];
                            foreach ($standard as $standarddt) {
                                //stream 
                                $stream = Stream_model::join('stream_sub', 'stream.id', '=', 'stream_sub.stream_id')
                                    ->where('stream_sub.institute_id', $institute_id)
                                    ->where('stream_sub.user_id', $user_id)
                                    ->where('stream_sub.institute_for_id', $inst_forsd->id)
                                    ->where('stream_sub.board_id', $boardsdt->id)
                                    ->where('stream_sub.medium_id', $mediumdt->id)
                                    ->where('stream_sub.class_id', $classdt->id)
                                    ->select('stream.*')
                                    ->distinct()->get();
                                $streams = [];
                                foreach ($stream as $streamdt) {
                                    $streams[] = array(
                                        'id' => $streamdt->id,
                                        'name' => $streamdt->name
                                    );
                                }

                                $batableids = Base_table::where('institute_for', $inst_forsd->id)
                                    ->where('board', $boardsdt->id)
                                    ->where('medium', $mediumdt->id)
                                    ->where('medium', $mediumdt->id)
                                    ->where('institute_for_class', $classdt->id)
                                    ->where('standard', $standarddt->id)->pluck('id')
                                    ->toArray();

                                $subject = Subject_model::join('subject_sub', 'subject.id', '=', 'subject_sub.subject_id')
                                    ->where('subject_sub.institute_id', $institute_id)
                                    ->where('subject_sub.user_id', $user_id)
                                    ->whereIN('subject.base_table_id', $batableids)
                                    ->select('subject.*')
                                    ->distinct()->get();
                                $subjects = [];

                                foreach ($subject as $subjectdt) {

                                    $subjects[] = array(
                                        'id' => $subjectdt->id,
                                        'name' => $subjectdt->name
                                    );
                                }

                                $standards[] = array(
                                    'id' => $standarddt->id,
                                    'name' => $standarddt->name,
                                    'stream' => $streams,
                                    'subject_id' => $subjects
                                );
                            }

                            $classs[] = array(
                                'id' => $classdt->id,
                                'name' => $classdt->name,
                                'standard' => $standards
                            );
                        }

                        $mediums[] = array(
                            'id' => $mediumdt->id,
                            'name' => $mediumdt->name, 'class' => $classs
                        );
                    }

                    $boards[] = array(
                        'id' => $boardsdt->id,
                        'name' => $boardsdt->name,
                        'medium' => $mediums
                    );
                }
                $institute_fors[] = array(
                    'id' => $inst_forsd->id,
                    'name' => $inst_forsd->name,
                    'boards' => $boards
                );
            }
            $alldata = array(
                'institute_fors' => $institute_fors,
            );

            return $this->response($alldata, 'Successfully fetch Data.');
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }
    //student list for add exam marks
       public function student_list_for_add_marks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'exam_id' => 'required',
            'batch_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_id = $request->institute_id;
            $user_id = Auth::id();
            $exam_id = $request->exam_id;
            $examdt = Exam_Model::where('id', $exam_id)->first();
            $stream = '';
            if ($examdt->stream_id == 'null') {
                $stream = null;
            } else {
                $stream = $examdt->stream;
            }
            $studentDT = Student_detail::join('users', 'users.id', '=', 'students_details.student_id')
                ->join('standard', 'standard.id', '=', 'students_details.standard_id')
                ->where('students_details.institute_id', $institute_id)
                ->where('students_details.board_id', $examdt->board_id)
                ->where('students_details.medium_id', $examdt->medium_id)
                ->where('students_details.batch_id', $examdt->batch_id)
                ->where('students_details.standard_id', $examdt->standard_id)
                ->where('students_details.stream_id', $stream)
                ->WhereRaw("FIND_IN_SET(?, students_details.subject_id)", [$examdt->subject_id])
                ->whereNull('students_details.deleted_at')
                ->whereNull('users.deleted_at')


                ->select('students_details.*', 'users.firstname', 'users.lastname', 'standard.name as standardname')->get();

            $studentsDET = [];
            foreach ($studentDT as $stddt) {

                $attdence =  Attendance_model::where('institute_id', $institute_id)
                    ->where('student_id', $stddt->student_id)
                    ->where('subject_id', $examdt->subject_id)
                    ->where('date', $examdt->exam_date)
                    ->first();

                $subjectqy = Subject_model::where('id', $examdt->subject_id)->first();
                $marksofstd = Marks_model::where('student_id', $stddt->student_id)
                    ->where('exam_id', $request->exam_id)->first();
                $studentsDET[] = array(
                    'student_id' => $stddt->student_id,
                    'exam_id' => $request->exam_id,
                    'batch_id' => $request->batch_id,
                    'marks' => !empty($marksofstd->mark) ? (float)$marksofstd->mark : 0,
                    'firstname' => $stddt->firstname,
                    'lastname' => $stddt->lastname,
                    'total_mark' => $examdt->total_mark,
                    'standard' => $stddt->standardname,
                    'subject' => $subjectqy->name,
                    'attendance' => (!empty($attdence)) ? $attdence->attendance : null,
                );
            }
            return $this->response($studentsDET, "Successfully fetch Data.");
        } catch (Exception $e) {

            return $this->response([], "Something went wrong.", false, 400);
        }
    }

    public function student_list_with_marks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $examdtr = Exam_Model::where('id', $request->exam_id)->first();
            if (!empty($examdtr)) {
                $marksdt = Marks_model::join('users', 'users.id', '=', 'marks.student_id')
                    ->where('marks.exam_id', $request->exam_id)
                    ->select('marks.*', 'users.firstname', 'users.lastname')->get();
                $studentsDET = [];
                foreach ($marksdt as $markses) {
                    $subjectq = Subject_model::where('id', $examdtr->subject_id)->first();
                    $standardtq = Standard_model::where('id', $examdtr->standard_id)->first();
                    $studentsDET[] = array(
                        'student_id' => $markses->student_id,
                        'exam_id' => $request->exam_id,
                        'firstname' => $markses->firstname,
                        'lastname' => $markses->lastname,
                        'total_mark' => $examdtr->total_mark,
                        'mark' => $markses->mark,
                        'standard' => $standardtq->name,
                        'subject' => $subjectq->name
                    );
                }
                return $this->response($studentsDET, "Successfully fetch Data.");
            } else {
                return $this->response([], "Exam not found.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }

    public function add_marks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'user_id' => 'required',
            'student_id' => 'required',
            'exam_id' => 'required',
            'mark' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $total_marks=Exam_Model::where('id',$request->exam_id)->first();
            if($total_marks->total_mark <  $request->mark){
                return $this->response([], "Enter marks less than total marks!", false, 400);
            }
            $addesmarks = Marks_model::where('student_id', $request->student_id)->where('exam_id', $request->exam_id)->first();
            if ($addesmarks) {
                $admarks = Marks_model::where('id', $addesmarks->id)->update([
                    'student_id' => $request->student_id,
                    'exam_id' => $request->exam_id,
                    'mark' => $request->mark,
                ]);
            } else {
                $admarks = Marks_model::create([
                    'student_id' => $request->student_id,
                    'exam_id' => $request->exam_id,
                    'mark' => $request->mark,
                ]);
            }
            return $this->response([], "Mark Added!!");
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }


    public function add_announcements(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'batch_id' => 'required',
            'subject_id' => 'required',
            'role_type' => 'required',
            'title' => 'required',
            'detail' => 'required',
            'standard_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $announcement = new announcements_model();
            $msg = 'added';
            if ($request->announcement_id) {
                $msg = 'updated';
                $announcement = $announcement->find($request->announcement_id);
                if (!$announcement) {
                    return $this->response([], 'Record not found', false, 400);
                }
            }

            $announcement->user_id = $request->user_id;
            $announcement->institute_id = $request->institute_id;
            $announcement->board_id = $request->board_id;
            $announcement->medium_id = $request->medium_id;
            if ($request->stream_id == 'null') {
                $stream_idd = null;
            } else {
                $stream_idd = $request->stream_id;
            }
            $announcement->stream_id = $stream_idd;
            $announcement->subject_id = $request->subject_id;
            $announcement->role_type = $request->role_type;
            $announcement->title = $request->title;
            $announcement->detail = $request->detail;
            $announcement->standard_id = $request->standard_id;
            $announcement->batch_id = $request->batch_id;
            if ($announcement->save()) {
                $roleTypes = explode(',', $announcement['role_type']);
                $combinedIds = [];
                if (in_array('4', $roleTypes)) {
                    $teachersId = Teacher_model::where('institute_id', $announcement->institute_id)
                        ->where('board_id', $announcement->board_id)
                        ->where('medium_id', $announcement->medium_id)
                        ->where('status', '1')
                        ->WhereRaw("FIND_IN_SET(?, subject_id)", [$request->subject_id])
                        ->pluck('teacher_id');
                    $combinedIds = array_merge($combinedIds, $teachersId->toArray());
                }
            
                // Check for role type 5
                if (in_array('5', $roleTypes)) {
                    $studentId = Student_detail::where('institute_id', $announcement->institute_id)
                        ->where('board_id', $announcement->board_id)
                        ->where('medium_id', $announcement->medium_id)
                        ->where('status', '1')
                        ->WhereRaw("FIND_IN_SET(?, subject_id)", [$request->subject_id])
                        ->pluck('student_id');
                    $parent = Parents::whereIn('student_id', $studentId)->pluck('parent_id');
                    $combinedIds = array_merge($combinedIds, $parent->toArray());
                }

                // Check for role type 6
                if (in_array('6', $roleTypes)) {
                    $subjectIds = explode(',', $request->subject_id);
                        $studentId = Student_detail::where('institute_id', $request->institute_id)
                        ->where('board_id', $request->board_id)
                        ->where('medium_id', $request->medium_id)
                        ->where('status', '1')
                        ->where(function($query) use ($subjectIds) {
                            foreach ($subjectIds as $subjectId) {
                                $query->orWhereRaw("FIND_IN_SET(?, subject_id)", [$subjectId]);
                            }
                        })
                        ->pluck('student_id');
                    
                
                    $combinedIds = array_merge($combinedIds, $studentId->toArray());
                }
                $serverKey = env('SERVER_KEY');
                $users = User::whereIn('id', $combinedIds)->where('device_key', '!=', null)->get();
                $url = "https://fcm.googleapis.com/fcm/send";
                $registrationIds = $users->pluck('device_key')->toArray();
               
                $notificationTitle = $announcement->title;
                $notificationBody = $announcement->detail;

                $data = [
                    'registration_ids' => $registrationIds,
                    'notification' => [
                        'title' => $notificationTitle,
                        'body' => $notificationBody,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // Adjust this if needed
                    ],
                ];

                if ($users->isNotEmpty()) {
                    $json = json_encode($data);

                    $headers = [
                        'Content-Type: application/json',
                        'Authorization: key=' . $serverKey
                    ];

                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $json,
                        CURLOPT_HTTPHEADER => $headers,
                    ]);

                    $result = curl_exec($ch);

                    if ($result === FALSE) {
                        // Handle error if needed
                    }

                    curl_close($ch);
                }
                return $this->response([], "Announcement $msg successfully.");
            }
        } catch (Exception $e) {
            return $this->response([], "Somthing went wrong.", false, 400);
        }
    }


    public function announcements_list(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_id = $request->institute_id;
            $board_id = $request->board_id;
            $standard_id = $request->standard_id;
            $batch_id = $request->batch_id;
            $subject_id = $request->subject_id;
            $searchData = $request->searchData;

                $announcements = announcements_model::where('institute_id', $institute_id)
                ->when($searchData, function ($query, $searchData) {
                    $query->where(function ($query) use ($searchData) {
                        $query->where('title', 'like', '%' . $searchData . '%')
                            ->orWhere('detail', 'like', '%' . $searchData . '%');
                    });
                })
                ->when($board_id, function ($query, $board_id) {
                    $query->where('board_id', $board_id);
                })
                ->when($standard_id, function ($query, $standard_id) {
                    $query->where('standard_id', $standard_id);
                })
                ->when($batch_id, function ($query) use ($batch_id) {
                    $batch_ids = explode(',', $batch_id);
                    $query->where(function ($query) use ($batch_ids) {
                        foreach ($batch_ids as $id) {
                            $query->orWhereRaw("FIND_IN_SET(?, batch_id)", [$id]);
                        }
                    });
                })
                ->when($subject_id, function ($query, $subject_id) {
                    $query->where('subject_id', $subject_id);
                })
                ->whereNull('deleted_at')
                ->orderByDesc('created_at')
                ->get();
             
            if ($announcements->isEmpty()) {
                return $this->response([], "Data not found.", false, 400);
            }

            $announcementDT = $announcements->map(function ($announcement) {
                $subjectIds = explode(",", $announcement->subject_id);
                $batchIds = explode(",", $announcement->batch_id);

                $subjects = Subject_model::whereIn('id', $subjectIds)->get()->map(function ($subject) {
                    return ['id' => $subject->id, 'name' => $subject->name];
                });

                $batches = Batches_model::whereIn('id', $batchIds)->get()->map(function ($batch) {
                    return ['id' => $batch->id, 'name' => $batch->batch_name];
                });

                $roles = Role::whereIn('id', explode(",", $announcement->role_type))->get()->map(function ($role) {
                    return ['id' => $role->id, 'name' => $role->role_name];
                });

                $standard = Standard_model::find($announcement->standard_id);
                $board = board::find($announcement->board_id);

                return [
                    'id' => $announcement->id,
                    'date' => $announcement->created_at,
                    'title' => $announcement->title,
                    'detail' => $announcement->detail,
                    'standard_id' => optional($standard)->id,
                    'standard' => optional($standard)->name,
                    'board_id' => optional($board)->id,
                    'board' => optional($board)->name,
                    'role' => $roles,
                    'batches' => $batches,
                    'subject' => $subjects,
                ];
            });
            return $this->response($announcementDT, "Successfully fetch Data.");
        } catch (Exception $e) {
            return $this->response([], "Somthing went wrong.", false, 400);
        }
    }

    public function delete_announcement(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'announcement_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $announc = announcements_model::where('id', $request->announcement_id);
            if (!empty($announc)) {
                $announc->delete();
                return $this->response([], "Announcement Delete Successfully.");
            } else {
                return $this->response([], "Record Not Found.");
            }
        } catch (Exception $e) {
            return $this->response($e, "Somthing went wrong.", false, 400);
        }
    }


    public function delete_account(Request $request)
    {
        try {
            $user = Auth::user();
            $user->delete();
            return $this->response([], "Delete Account Successfully!");
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }


    public function  roles(Request $request)
    {
        try {
            $rolesDT = [];
                $suad2 = ['teacher','student', 'parent'];
                $roleqry = Role::whereNull('deleted_at')->whereIN('role_name', $suad2)->get();
            foreach ($roleqry as $roldel) {
                $rolesDT[] = array(
                    'id' => $roldel->id,
                    'role_name' => $roldel->role_name
                );
            }


            return $this->response($rolesDT, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }

    public function institute_students(Request $request)
    {
        $validator = Validator::make($request->all(), ['institute_id' => 'required']);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $user_id = Auth::id();
            $query = Student_detail::join('users', 'users.id', 'students_details.student_id')
                ->join('board', 'board.id', 'students_details.board_id')
                ->join('medium', 'medium.id', 'students_details.medium_id')
                ->join('standard', 'standard.id', 'students_details.standard_id')
                ->leftjoin('batches', 'batches.id', 'students_details.batch_id')
                ->where('students_details.user_id', $user_id)
                ->where('students_details.institute_id', $request->institute_id)
                ->when($request->board_id, fn ($q, $board) => $q->where('students_details.board_id', $board))
                ->when($request->medium_id, fn ($q, $medium) => $q->where('students_details.medium_id', $medium))
                ->when($request->standard_id, fn ($q, $standard) => $q->where('students_details.standard_id', $standard))
                ->when($request->batch_id, fn ($q, $batch_id) => $q->where('students_details.batch_id', $batch_id))
                ->when($request->searchkeyword, function ($q, $searchkeyword) {
                    $q->where(function ($q) use ($searchkeyword) {
                        $q->where('users.firstname', 'like', '%' . $searchkeyword . '%')
                            ->orWhere('users.lastname', 'like', '%' . $searchkeyword . '%')
                            ->orWhere('users.unique_id', 'like', '%' . $searchkeyword . '%');
                    });
                });

            $perPage = $request->input('per_page', 10);
            $students = $query->select(
                'students_details.*',
                'batches.batch_name',
                'users.firstname',
                'users.lastname',
                'users.image',
                'board.name as board',
                'medium.name as medium',
                'standard.name as standard'
            )->orderByDesc('students_details.created_at')->paginate($perPage);

            return $this->response($students->map(fn ($stdDT) => [
                'id' => $stdDT->student_id,
                'name' => $stdDT->firstname . ' ' . $stdDT->lastname,
                'image' => asset($stdDT->image),
                'board_id' => $stdDT->board_id,
                'board' => $stdDT->board . '(' . $stdDT->medium . ')',
                'standard_id' => $stdDT->standard_id,
                'standard' => $stdDT->standard,
                'batch_id' => $stdDT->batch_id,
                'batch_name' => $stdDT->batch_name,
            ]), "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }


    public function  filters_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $boardIds = Institute_board_sub::where('institute_id', $request->institute_id)
                ->pluck('board_id')
                ->unique()
                ->toArray();

            $boardList = DB::table('board')
                ->whereIn('id', $boardIds)
                ->get();

            $boardArray = [];

            foreach ($boardList as $board) {
                $mediumIds = DB::table('medium_sub')
                    ->where('board_id', $board->id)
                    ->where('institute_id', $request->institute_id)
                    ->pluck('medium_id')
                    ->unique()
                    ->toArray();

                $mediumList = Medium_model::whereIn('id', $mediumIds)->get();

                foreach ($mediumList as $medium) {
                    $standards = Standard_sub::join('standard', 'standard.id', '=', 'standard_sub.standard_id')
                        ->where('standard_sub.institute_id', $request->institute_id)
                        ->where('standard_sub.board_id', $board->id)
                        ->where('standard_sub.medium_id', $medium->id)
                        ->select('standard.id as std_id', 'standard.name as std_name')
                        ->distinct()
                        ->get();

                    $standardArray = $standards->map(function ($standard) {
                        return [
                            'id' => $standard->std_id,
                            'name' => $standard->std_name,
                        ];
                    })->toArray();

                    $boardArray[] = [
                        'board_id' => $board->id,
                        'medium_id' => $medium->id,
                        'board_medium_name' => $board->name . ' - ' . $medium->name,
                        'standard' => $standardArray,
                    ];
                }
            }

            return $this->response($boardArray, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    public function institute_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_id = $request->institute_id;
            $institute_detail = Institute_detail::where('institute_detail.id', $institute_id)
                ->select('institute_detail.*')
                ->get()->toarray();
            if (empty($institute_id)) {
                $institute_id = Institute_detail::where('user_id', Auth::id())->select('id')->first();
            }
            $boarids = Institute_board_sub::where('institute_id', $institute_id)->pluck('board_id')->toArray();
            $uniqueBoardIds = array_unique($boarids);

            $board_list = DB::table('board')
                ->whereIN('id', $uniqueBoardIds)
                ->get();

            $board_array = [];
            foreach ($board_list as $board_value) {

                $medium_sublist = DB::table('medium_sub')
                    ->where('board_id', $board_value->id)
                    ->where('institute_id', $institute_id)
                    ->pluck('medium_id')->toArray();
                $uniquemediumds = array_unique($medium_sublist);

                $medium_list = Medium_model::whereIN('id', $uniquemediumds)->get();

                $medium_array = [];
                foreach ($medium_list as $medium_value) {
                    $medium_array[] = [
                        'id' => $medium_value->id,
                        'medium_name' => $medium_value->name,
                    ];
                }
                $board_array[] = [
                    'id' => $board_value->id,
                    'board_name' => $board_value->name,
                    'icon' => asset($board_value->icon),
                    'medium' => $medium_array,
                ];
            }

            $institute_response = [];
            foreach ($institute_detail as $value) {
                $institute_response[] = [
                    'institute_name' => $value['institute_name'],
                    'address' => $value['address'] . '',
                    'country_code' => $value['country_code'],
                    'country_code_name' => $value['country_code_name'],
                    'contact_no' => $value['contact_no'] . '',
                    'email' => $value['email'] . '',
                    'about_us' => $value['about_us'] . '',
                    'board_name' => $board_array,
                    'website_link' => $value['website_link'] . '',
                    'instagram_link' => $value['instagram_link'] . '',
                    'facebook_link' => $value['facebook_link'] . '',
                    'whatsaap_link' => $value['whatsaap_link'] . '',
                    'youtube_link' => $value['youtube_link'] . '',
                    'logo' => (!empty($value['logo'])) ? url($value['logo']) : asset('profile/no-image.png'),
                    'cover_photo' => ($value['cover_photo'] ? url($value['cover_photo']) : url('cover_photo/cover_image.png')),
                    'country' => $value['country'] . '',
                    'state' => $value['state'] . '',
                    'city' => $value['city'] . '',
                    'pincode' => $value['pincode'] . '',
                    'open_time' => $value['open_time'] . '',
                    'close_time' => $value['close_time'] . '',
                    'gst_number' => $value['gst_number'] . '',
                    'gst_slab' => $value['gst_slab'] . '',
                    'start_academic_year' => $value['start_academic_year'] . '',
                    'end_academic_year' => $value['end_academic_year'] . '',

                ];
            }
            return $this->response($institute_response, "Institute Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    public function institute_profile_edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'institute_name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'contact_no' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institutedt = Institute_detail::find($request->institute_id);
            $institutedt->institute_name = $request->institute_name;
            $institutedt->address = $request->address;
            $institutedt->country_code = $request->country_code;
            $institutedt->country_code_name = $request->country_code_name;
            $institutedt->contact_no = $request->contact_no;
            $institutedt->email = $request->email;
            $institutedt->about_us = $request->about_us;
            $institutedt->open_time = $request->open_time;
            $institutedt->close_time = $request->close_time;
            $institutedt->gst_number = $request->gst_number;
            $institutedt->gst_slab = $request->gst_slab;
            $institutedt->website_link = $request->website_link;
            $institutedt->instagram_link = $request->instagram_link;
            $institutedt->facebook_link = $request->facebook_link;
            $institutedt->whatsaap_link = $request->whatsaap_link;
            $institutedt->youtube_link = $request->youtube_link;
            $institutedt->start_academic_year = date('Y-m-d',strtotime($request->start_academic_year));
            $institutedt->end_academic_year = date('Y-m-d',strtotime($request->end_academic_year));
            $imagePath = null;
            if ($request->hasFile('logo')) {
                $logo_image = $request->file('logo');
                $imagePath = $logo_image->store('logo', 'public');
            }
            
            if ($imagePath !== null) {
                $institutedt->logo = $imagePath;
            }
            $imagePath2 = null;
            if ($request->hasFile('cover_photo')) {
                $logo_image = $request->file('cover_photo');
                $imagePath2 = $logo_image->store('cover_photo', 'public');
            }
            if ($imagePath2 !== null) {
                $institutedt->cover_photo = $imagePath2;
            }
            $institutedt->save();
            return $this->response([], "Institute Update Successfully!.");
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }


    // category list for add do business with 
    public function category_list(Request $request)
    {
        try {
            $user = Auth::user();
            $vcategory = VideoCategory::where('status', 'active')->get();
            $cat_array = [];
            foreach ($vcategory as $cat_value) {
                $cat_array[] = array(
                    'id' => $cat_value->id,
                    'name' => $cat_value->name
                );
            }
            return $this->response($cat_array, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }


    public function create_batch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'standard_id' => 'required',
            'batch_name' => 'required',
            'subjects' => 'required',
            'student_capacity' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {

            $batch = new Batches_model();
            $batch->user_id  = Auth::id();
            $batch->institute_id  = $request->institute_id;
            $batch->board_id  = $request->board_id;
            $batch->medium_id  = $request->medium_id;
            $batch->stream_id  = $request->stream_id;
            $batch->standard_id  = $request->standard_id;
            $batch->batch_name  = $request->batch_name;
            $batch->subjects  = $request->subjects;
            $batch->student_capacity  = $request->student_capacity;
            $batch->save();
            return $this->response([], "Batch Added Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    public function edit_batch(Request $request){

          $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'batch_id' =>'required',
            'batch_name' => 'required',
            'subjects' => 'required',
            'student_capacity' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {

            $batch = Batches_model::find($request->batch_id);
            if ($batch) {
                $batch->user_id = Auth::id(); 
                $batch->institute_id = $request->institute_id;
                $batch->batch_name = $request->batch_name;
                $batch->subjects = $request->subjects;
                $batch->student_capacity = $request->student_capacity;
                $batch->save();
                return $this->response([], "Batch update successfully.");
            } else {
                return $this->response([], "Batch not found.", false, 400);
            }
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    public function delete_batch(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'batch_id' =>'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $attendance=Attendance_model::where('institute_id',$request->institute_id)->where('batch_id',$request->batch_id)->whereNull('deleted_at')->count();
            if($attendance > 0){
                return $this->response([], "Already batch assign student.", false, 400);
            }
            $batch=Home_work_model::where('batch_id',$request->batch_id)->whereNull('deleted_at')->count();
            if($batch > 0){
                return $this->response([], "Already batch assign homework.", false, 400);
            }
            $student_detail=Student_detail::where('institute_id',$request->institute_id)->where('batch_id',$request->batch_id)->whereNull('deleted_at')->count();
            if($student_detail > 0){
                return $this->response([], "Already batch assign student.", false, 400);
            }
            $teacher_detail=Teacher_model::where('institute_id',$request->institute_id)->whereRaw('FIND_IN_SET(?, batch_id) > 0', [$request->batch_id])->whereNull('deleted_at')->count();
            if($teacher_detail > 0){
                return $this->response([], "Already batch assign teacher.", false, 400);
            }
            $timetable=Timetables::where('batch_id',$request->batch_id)->whereNull('deleted_at')->count();
            if($timetable > 0){
                return $this->response([], "Already batch assign timetable.", false, 400);
            }
            $batch_model=Batches_model::where('institute_id',$request->institute_id)->where('id',$request->batch_id)->whereNull('deleted_at');
            if(!$batch_model){
              return $this->response([], "Batch not found..", false, 400);
            }
            $batch_model->delete();
            return $this->response([], "Batch delete successfully.");


        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }

    }
    public function selected_fetch_batches(Request $request){
        $validator = Validator::make($request->all(), [
            'batch_id' =>'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
        $batch_list=Batches_model::where('id',$request->batch_id)->first();
        $subject_list=Subject_model::whereIn('id',explode(',',$batch_list->subjects))->get();
        $subject=[];
        foreach($subject_list as $value){
            $subject[] = ['subject_id'=>$value->id,'subject_name'=>$value->name];
        }
        $data = ['batch_id'=>$batch_list->id,'batch_name'=>$batch_list->batch_name,'subjects'=>$subject,'student_capacity'=>$batch_list->student_capacity]; 
        return $this->response($data, "Batch fetch successfully."); 
        } catch (Exception $e) {
              return $this->response($e, "Something went wrong.", false, 400);
        }
    }

    //create batch
    public function batch_list(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'board_id' => 'required|exists:board,id',
            'medium_id' => 'required|exists:medium,id',
            'standard_id' => 'required|exists:standard,id',

        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
           $batchlist = Batches_model::where([
                ['user_id', Auth::id()],
                ['institute_id', $request->institute_id],
                ['board_id', $request->board_id],
                ['medium_id', $request->medium_id],
                ['standard_id', $request->standard_id]
            ])->get(['id', 'batch_name'])->toArray();
            $batchlistQuery = Batches_model::
                 where('institute_id', $request->institute_id)
                ->where('board_id', $request->board_id)
                ->where('standard_id', $request->standard_id);

            if (!empty($request->medium_id)) {
                $batchlistQuery->where('medium_id', $request->medium_id);
            }

            $batchlist = $batchlistQuery->get(['id', 'batch_name'])->toArray();
            $allOption = [
                'id' => null,
                'batch_name' => 'All'
            ];
            
            $batchlist = array_merge([$allOption], $batchlist);
            return $this->response($batchlist, "Batch Fetch Successfully");
      
    }
    public function batch_list_get_subject(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $institute_id = $request->institute_id;
            if(empty($request->batch_id)){

            if (empty($institute_id)) {
                $user_id = Auth::id();
                $institute_id = Institute_detail::where('user_id', $user_id)->first();
            }
            $standard_list = DB::table('standard_sub')
                ->join('standard', 'standard_sub.standard_id', '=', 'standard.id')
                ->select('standard.*')
                ->where('standard_sub.institute_id', $institute_id)
                ->where('standard_sub.board_id',  $request->board_id)
                ->where('standard_sub.medium_id', $request->medium_id)
                ->where('standard_sub.standard_id', $request->standard_id)
                ->orderByRaw('CAST(standard.name AS UNSIGNED), standard.name')
                ->get();
            $standard_array = [];
            foreach ($standard_list as $standard_value) {

                $getbsiqy = Base_table::where('board',  $request->board_id)
                    ->where('medium', $request->medium_id)
                    ->where('standard', $standard_value->id)
                    ->pluck('id')
                    ->toArray();
                $subject_list = DB::table('subject_sub')
                    ->join('subject', 'subject_sub.subject_id', '=', 'subject.id')
                    ->select('subject.*')
                    ->where('subject_sub.institute_id', $institute_id)
                    ->whereIN('subject.base_table_id', $getbsiqy)
                    ->get();

                $subject_array = [];
                foreach ($subject_list as $subject_value) {
                    $subject_array[] = [
                        'id' => $subject_value->id,
                        'subject_name' => $subject_value->name,
                        'image' => !empty($subject_value->image) ? asset($subject_value->image) : '',
                    ];
                }
            }
            
          }else{
                //batch list
                $batchlist = Batches_model::where('id', $request->batch_id)->first();
                $subids = explode(",", $batchlist->subjects);
                $batSubQY = Subject_model::whereIN('id', $subids)->get();
                $subject_array = [];
                foreach ($batSubQY as $batDt) {
                    $subject_array[] = array('id' => $batDt->id, 'subject_name' => $batDt->name,'image' => !empty($batDt->image) ? asset($batDt->image) : '');
                }
               }
                $standard_array[] = [
                    'subject' => $subject_array,
                ];
            return $this->response($standard_array, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
        }


    public function subjectList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'standard_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $subjctlist = Subject_sub::join('subject', 'subject.id', '=', 'subject_sub.subject_id')
                ->join('base_table', 'base_table.id', '=', 'subject.base_table_id')
                ->where('subject_sub.user_id', Auth::id())
                ->where('subject_sub.institute_id', $request->institute_id)
                ->where('base_table.board', $request->board_id)
                ->where('base_table.standard', $request->standard_id)->get()->toarray();
            $batch_response = [];
            foreach ($subjctlist as $svalue) {
                $batch_response[] = [
                    'id' => $svalue['subject_id'],
                    'name' => $svalue['name']
                ];
            }
            return $this->response($batch_response, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }


    public function allsubjectList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'board_id' => 'required',
            'medium_id' => 'required',
            'standard_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $subjctslist = Subject_model::join('base_table', 'base_table.id', '=', 'subject.base_table_id')
                ->where('base_table.board', $request->board_id)
                ->where('base_table.medium', $request->medium_id)
                ->where('base_table.standard', $request->standard_id)
                ->select('subject.*')->get();
            $allsub_response = [];
            foreach ($subjctslist as $svalue) {
                $allsub_response[] = [
                    'id' => $svalue->id,
                    'name' => $svalue->name,
                    'image' => asset($svalue->image),
                ];
            }
            return $this->response($allsub_response, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }


    public function edit_subject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'medium' => 'required',
            'board_id' => 'required',
            'standard_id' => 'required',
            'subject_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $baseids = Base_table::where('board',$request->board_id)
            ->where('medium',$request->medium)
            ->where('standard',$request->standard_id)
            ->first();
            
            $subjcts = Subject_model::where('base_table_id',$baseids->id)->pluck('id');
            
            $subsub = Subject_sub::where('institute_id', $request->institute_id)
                ->whereIN('subject_id',$subjcts)->get()->toarray();
            
            $subsubSubjectIds = array_column($subsub, 'subject_id');
            $subjectsids = explode(",", $request->subject_id);
            
            $difference = array_diff($subsubSubjectIds, $subjectsids);
            $difference2 = array_diff($subjectsids,$subsubSubjectIds);
            $result = array_merge($difference, $difference2);

            foreach($result as $subid){

                $subsubget = Subject_sub::where('institute_id', $request->institute_id)
                ->where('subject_id',$subid)->first();
                
                if($subsubget){
                    Subject_sub::where('institute_id', $request->institute_id)
                    ->where('subject_id',$subid)
                    ->delete();
                }else{
                    Subject_sub::create([
                        'user_id' => Auth::id(),
                        'institute_id' => $request->institute_id,
                        'subject_id' => $subid,
                    ]);
                }
            }   
           
            return $this->response([], "Updated Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }

    public function do_business_with()
    {
        try {
            $data =  Dobusinesswith_Model::where('status', 'active')->get();
            if (!empty($data)) {
                foreach ($data as $value) {
                    $response[] = ['id' => $value->id, 'name' => $value->name];
                }
                return $this->response($response, "Successfully fetch Data.");
            } else {
                return $this->response([], "No data found.");
            }
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
        
    public function approve_teacher2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required',
            'institute_id' => 'required',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required',
            'country_code' => 'required',
            'qualification' => 'required|string',
            'employee_type' => 'required',
            'teacher_detail' => 'required',
            'teacher_detail.*.board_id' => 'required',
            'teacher_detail.*.medium_id' => 'required',
            'teacher_detail.*.standard_id' => 'required',
            'teacher_detail.*.stream_id' => 'nullable',
            'teacher_detail.*.batch_id' => 'required',
            'teacher_detail.*.subject_id' => 'required',
            'teacher_detail.*.teacher_detail_id' => 'required',
        ]);
        

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $teacher_detail = json_decode($request->teacher_detail, true);
            foreach($teacher_detail as $teacherDT){
                $teacherDetail = Teacher_model::where('id', $teacherDT['teacher_detail_id'])->first();
                
                if ($teacherDetail) {
                    $teacherDetail->update([
                        'board_id' => $teacherDT['board_id'],
                        'medium_id' => $teacherDT['medium_id'],
                        'standard_id' => $teacherDT['standard_id'],
                        'batch_id' => !empty($teacherDT['batch_id']) ? $teacherDT['batch_id'] : null,
                        'subject_id' => $teacherDT['subject_id'],
                        'teacher_id' => $request->teacher_id,
                        'status' => '1',
                    ]);
                }
                $teacherDetail = User::where('id', $request->teacher_id)->first();
                if ($teacherDetail) {
                    $teacherDetail->update([
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'mobile' => $request->mobile,
                        'country_code' => $request->country_code,
                        'email' => $request->email,
                        'qualification' => $request->qualification,
                        'employee_type' => $request->employee_type,
                    ]);
                }
            }

            User::where('id', $request->teacher_id)->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'mobile' => $request->mobile,
                'employee_type' => $request->employee_type,
                'qualification' => $request->qualification,
            ]);
             $serverKey = env('SERVER_KEY');

            $url = "https://fcm.googleapis.com/fcm/send";
            $users = User::where('id', $request->teacher_id)->pluck('device_key');

            $notificationTitle = "Your Request Approved successfully!!";
            $notificationBody = "Your Teacher Request Approved successfully!!";

            $data = [
                'registration_ids' => $users,
                'notification' => [
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 
                ],
            ];

            if ($users->isNotEmpty()) {
                $json = json_encode($data);

                $headers = [
                    'Content-Type: application/json',
                    'Authorization: key=' . $serverKey
                ];

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $json,
                    CURLOPT_HTTPHEADER => $headers,
                ]);

                $result = curl_exec($ch);

                if ($result === FALSE) {
                }

                curl_close($ch);
            }
            return $this->response([], "Teacher Add successfully");
        } catch (\Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }

    public function fetch_teacher_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $teacher_data = Teacher_model::join('users', 'users.id', '=', 'teacher_detail.teacher_id')
                ->where('teacher_detail.institute_id', $request->institute_id)
                ->where('teacher_detail.status', '1')
                ->select('users.id', 'users.firstname', 'users.lastname','users.image', 'teacher_detail.teacher_id', 'users.qualification')
                ->groupBy('users.id', 'users.firstname', 'users.lastname','users.image', 'teacher_detail.teacher_id', 'users.qualification');

            if (!empty($request->subject_id)) {
                $teacher_data->where(function ($query) use ($request) {
                    $query->where('teacher_detail.subject_id', $request->subject_id);
                });
            }

            if (!empty($request->search)) {
                $teacher_data->where(function ($query) use ($request) {
                    $query->where('users.firstname', 'like', "%{$request->search}%")
                        ->orWhere('users.lastname', 'like', "%{$request->search}%");
                });
            }

            $teacher_data = $teacher_data->get()->toArray();
            $response = [];
            if (!empty($teacher_data)) {

                foreach ($teacher_data as $value) {
                    $standard_list = Teacher_model::join('standard', 'standard.id', '=', 'teacher_detail.standard_id')
                        ->where('teacher_detail.institute_id', $request->institute_id)
                        ->where('teacher_detail.teacher_id', $value['teacher_id'])
                        ->where('teacher_detail.status', '1')
                        ->select('standard.name as standard_name')
                        ->get()
                        ->toArray();
                    $standard_array = [];
                    foreach ($standard_list as $standard_value) {
                        $standard_array[] = ['standard' => $standard_value['standard_name']];
                    }
                    $teacher_image=User::where('id',$value['teacher_id'])->first();
                    $response[] = [
                        'teacher_id' => $value['teacher_id'],
                        'teacher_image'=>!empty($value['image']) ? asset($value['image']) : asset('profile/no-image.png'),
                        'name' => $value['firstname'] . ' ' . $value['lastname'],
                        'qualification' => $value['qualification'],
                        'standard' => $standard_array,
                        'teacher_image' => (!empty($teacher_image->image)) ? asset($teacher_image->image) : asset('profile/no-image.png'),
                    ];
                    
                }
            }

            return $this->response($response, "Data Fetch Successfully");
        } catch (\Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }


    public function Add_classRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'name' => 'required|string|unique:class_room,name',
            'capacity' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        $nameu = explode(",",$request->name);
        $valueCounts = array_count_values($nameu);

       $repeatedValues = array_filter($valueCounts, function ($count) {
            return $count > 1;
        });

        if($repeatedValues){
            return $this->response([], "The name field contains duplicate values", false, 400);
        }

        $institute_id = $request->institute_id;
        $names = array_map('trim', explode(',', $request->name));
        $capacities = array_map('trim', explode(',', $request->capacity));

        if (count($names) !== count($capacities)) {
            
            return $this->response([], "The number of names and capacities must match.", false, 400);
        }
        
        try {
            foreach ($names as $index => $name) {
                
                $data=Class_room_model::where('institute_id',$request->institute_id)->where('name',$name)->first();
                if(!empty($data)){
                   return $this->response([], "The name already exists", false, 400);
                }
                $capacity = $capacities[$index];
                if (empty($request->edit_id)) {
                    $class = new Class_room_model();
                } else {
                    $class = Class_room_model::find($request->edit_id);
                }

                if (!$class) {
                    return $this->response([], "Class with id {$request->edit_id} not found.", false, 400);
                    continue;
                }

                $class->institute_id = $institute_id;
                $class->name = $name;
                $class->capacity = $capacity;

                $class->save();
            }
            if (!empty($request->edit_id)) {
                return $this->response([], "Class updated successfully:");
            } else {
                return $this->response([], "Class saved successfully:");
            }
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    public function view_classRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',

        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        $data = Class_room_model::where('institute_id', $request->institute_id)->get();
        $response = [];
        foreach ($data as $value) {
            $response[] = ['id' => $value->id, 'name' => $value->name, 'capacity' => $value->capacity];
        }
        return $this->response($response, "Classroom display successfully");
    }
    function delete_classRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:class_room,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $exam  = Class_room_model::where('id', $request->class_id)->delete();
            return $this->response([], "Successfully Deleted Classroom.");
        } catch (Exception $e) {
            return $this->response([], "Something went wrong.", false, 400);
        }
    }
    function create_remainder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'title' => 'required',
            'message' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $date = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
            $time = date('H:i', strtotime($request->input('time')));

            Remainder_model::create([
                'type_field' => '1',
                'role_type_id' => $request->input('role_type_id'),
                'student_id' => $request->input('student_id'),
                'date' => $date,
                'time' => $time,
                'title' => $request->input('title'),
                'message' => $request->input('message'),
            ]);
            return $this->response([], "Remainder set successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }

    function create_greeting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'title' => 'required',
            'message' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $date = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
            Remainder_model::create([
                'type_field' => '2',
                'role_type_id' => $request->input('role_type_id'),
                'student_id' => $request->input('student_id'),
                'date' => $date,
                'title' => $request->input('title'),
                'message' => $request->input('message'),
            ]);
            return $this->response([], "Greeting set successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    public function test(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $data = Remainder_model::get();
        foreach ($data as $value) {
            $givenDateString = $value->date;
            $timeString = $value->time;
            $dateTime = Carbon::createFromFormat('H:i:s', $timeString);

            $givenTimeString = $dateTime->format('H:i');
            $givenDateTime = Carbon::createFromFormat('Y-m-d H:i', "$givenDateString $givenTimeString");
            $currentDateTime = Carbon::now()->format('Y-m-d H:i');

            if ($givenDateTime->format('Y-m-d H:i') === $currentDateTime) {

                $serverKey = env('SERVER_KEY');

                $url = "https://fcm.googleapis.com/fcm/send";
                if (!empty($value->student_id)) {
                    $users = User::where('id', $value->student_id)->pluck('device_key');
                }
                if (!empty($value->role_type_id)) {
                    $users = User::where('role_type', $value->role_type_id)->pluck('device_key');
                }
                $notificationTitle = $value->title;
                $notificationBody = $value->message;

                $data = [
                    'registration_ids' => $users,
                    'notification' => [
                        'title' => $notificationTitle,
                        'body' => $notificationBody,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ];

                if ($users->isNotEmpty()) {
                    $json = json_encode($data);

                    $headers = [
                        'Content-Type: application/json',
                        'Authorization: key=' . $serverKey
                    ];

                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $json,
                        CURLOPT_HTTPHEADER => $headers,
                    ]);

                    $result = curl_exec($ch);

                    if ($result === FALSE) {
                    }
                    curl_close($ch);
                }
            }
        }
    }
    function get_subject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'batch_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $subjects = Batches_model::where('id', $request->batch_id)->first();
            if(empty($subjects)){
                return $this->response([], "Fetch successfully.");
            }
            $subjectids = explode(',', $subjects->subjects);
            if ($request->date) {
                $subjectids = Timetable::where('lecture_date', $request->date)
                    ->where('batch_id', $request->batch_id)
                    ->pluck('subject_id');
            }
            $subject_list = Subject_model::whereIn('id', $subjectids)->where('status','active')->get();
            $data = [];
            foreach ($subject_list as $value) {
                $data[] = [
                    'id' => $value->id,
                    'subject_name' => $value->name
                ];
            }
            return $this->response($data, "Fetch successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    public function user_list(Request $request)
    {
        try {
            $searchTerm = $request->input('search');
            $institute_id = $request->institute_id;
            // Query to fetch users based on institute_id and role_type conditions
            $user_list = User::leftJoin('staff_detail', 'staff_detail.user_id', '=', 'users.id')
                ->leftJoin('teacher_detail', 'teacher_detail.teacher_id', '=', 'users.id')
                ->leftJoin('roles', 'roles.id', '=', 'users.role_type')
                ->select('users.*', 'roles.role_name')
                ->where('teacher_detail.status','1')
                ->where(function ($query) use ($institute_id) {
                    $query->where('staff_detail.institute_id', $institute_id)
                        ->orWhere('teacher_detail.institute_id', $institute_id);
                })
                ->whereNotIn('roles.role_name', ['superadmin','institute','sub_admin','parent','student'])
                ->distinct('users.id');
             
            if (!empty($searchTerm)) {
                $searchParts = explode(' ', $searchTerm);

                if (count($searchParts) == 2) {
                    $firstname = $searchParts[0];
                    $lastname = $searchParts[1];

                    $user_list->where(function ($query) use ($firstname, $lastname) {
                        $query->where('users.firstname', 'like', '%' . $firstname . '%')
                            ->where('users.lastname', 'like', '%' . $lastname . '%')
                        ->orWhere(function ($query) use ($firstname) {
                            $query->where('roles.role_name', 'like', '%' . $firstname . '%');
                        });
                    });
                } else {
                    // If there's only one part, search in either firstname or lastname
                    $user_list->where(function ($query) use ($searchTerm) {
                        $query->where('users.firstname', 'like', '%' . $searchTerm . '%')
                            ->orWhere('users.lastname', 'like', '%' . $searchTerm . '%')
                            ->orWhere(function ($query) use ($searchTerm) {
                                $query->where('roles.role_name', 'like', '%' . $searchTerm . '%');
                            });
                    });
                }
            }

            $user_list = $user_list->get();
            $userRoleMappings = UserRoleMapping::join('users', 'users.id', '=', 'user_role_mapping.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'user_role_mapping.role_id')
                ->where('user_role_mapping.institute_id', $institute_id)
                ->whereNotIn('roles.role_name', ['superadmin','institute','sub_admin','parent','student'])
                ->select('users.*', 'roles.role_name');

            if (!empty($searchTerm)) {
                $searchParts = explode(' ', $searchTerm);

                if (count($searchParts) == 2) {
                    $firstname = $searchParts[0];
                    $lastname = $searchParts[1];

                    $userRoleMappings->where(function ($query) use ($firstname, $lastname) {
                        $query->where('users.firstname', 'like', '%' . $firstname . '%')
                            ->where('users.lastname', 'like', '%' . $lastname . '%')
                            ->orWhere(function ($query) use ($firstname) {
                                $query->where('roles.role_name', 'like', '%' . $firstname . '%');
                            });
                    });
                } else {
                    // If there's only one part, search in either firstname or lastname
                    $userRoleMappings->where(function ($query) use ($searchTerm) {
                        $query->where('users.firstname', 'like', '%' . $searchTerm . '%')
                            ->orWhere('users.lastname', 'like', '%' . $searchTerm . '%')
                            ->orWhere(function ($query) use ($searchTerm) {
                                $query->where('roles.role_name', 'like', '%' . $searchTerm . '%');
                            });
                    });
                }
            }

            $userRoleMappings = $userRoleMappings->get();

            $mergedUsers = $user_list->merge($userRoleMappings);

            $response = [];
            foreach ($mergedUsers as $value) {
                $response[] = [
                    'id' => $value->id,
                    'username' => $value->firstname . ' ' . $value->lastname,
                    'email' => $value->email,
                    'mobile' => $value->mobile,
                    'role_type' => $value->role_name,
                    'image' => (!empty($value->image)) ? asset($value->image) : asset('profile/no-image.png')
                ];
            }



            return $this->response($response, "Fetch successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    function role_list(Request $request){
        try{
            $institute_id = institute_Detail::where('user_id', Auth()->user()->id)->pluck('id')->first();
            $role_list=institute_detail::join('user_has_roles','user_has_roles.user_id','=','institute_detail.user_id')
                             ->join('roles','roles.created_by','=','institute_detail.user_id') 
                             ->select('roles.*')
                             ->where('institute_detail.id',$institute_id)
                             ->distinct('roles.id')
                             ->whereNotIn('role_name', ['institute','superadmin'])
                             ->get();  
                           
           return $this->response($role_list, "Fetch successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong.", false, 400);
        }
    }
    function testing(Request $request){
       $all_assign_video= VideoAssignToBatch::get();
       foreach($all_assign_video as $value){
        $now = Carbon::now();
        $createdAt = Carbon::parse($value->created_at);
        if ($now->diffInHours($createdAt) >= 24) {
            VideoAssignToBatch::where('id',$value->id)->delete();
        } 
       }

    }

    
    public function mobile_verify(Request $request){
        

        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'email'  => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
        $data= User::withTrashed()->where('mobile', $request->mobile)->where('email',$request->email)->first();
        if(!empty($data)){
            User::withTrashed()
            ->where('mobile', $request->mobile)
            ->restore();
           
            return $this->response([], "Activate User");
        }else{
            return $this->response([], "Invalid Mobile && email Number!!", false, 400);
        }
        }
        catch(Exception $e){
            return $this->response($e, "Something went wrong.", false, 400);

        }
    }
    public function teacher_subject_info(Request $request){
       
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'teacher_id'  => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $teacherDetails = Teacher_model::join('board', 'board.id', '=', 'teacher_detail.board_id')
            ->join('medium', 'medium.id', '=', 'teacher_detail.medium_id')
            ->join('standard', 'standard.id', '=', 'teacher_detail.standard_id')
            ->join('subject', 'subject.id', '=', 'teacher_detail.subject_id')
            ->select(
                'teacher_detail.board_id',
                'teacher_detail.medium_id',
                'teacher_detail.standard_id',
                DB::raw('MAX(board.name) as board_name'),
                DB::raw('MAX(medium.name) as medium_name'),
                DB::raw('MAX(standard.name) as standard_name'),
                DB::raw('GROUP_CONCAT(DISTINCT subject.id) as subject_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT subject.name) as subject_names'),
                DB::raw('GROUP_CONCAT(DISTINCT teacher_detail.id) as teacher_detail_id'),
                DB::raw('GROUP_CONCAT(subject.image) as subject_image')
            )
            ->where('teacher_detail.institute_id', $request->institute_id)
            ->where('teacher_detail.teacher_id', $request->teacher_id)
            ->groupBy('teacher_detail.board_id', 'teacher_detail.medium_id', 'teacher_detail.standard_id')
            ->get();
        $response = [];
        foreach ($teacherDetails as $detail) {

            $subjectIds = explode(',', $detail->subject_ids);
            $subjectNames = explode(',', $detail->subject_names);
            $subject_image =  explode(',',$detail->subject_image);
            $teacher_detail_ids = explode(',', $detail->teacher_detail_id);
            $subjectList = [];
            foreach ($subjectIds as $index => $subjectId) {
                $batchdt = Teacher_model::where('institute_id',$request->institute_id)
                ->where('teacher_id',$request->teacher_id)
                ->where('subject_id',$subjectId)->pluck('batch_id')->first();

                $batchlistdt = Batches_model::whereIN('id',explode(',',$batchdt))->get();
                $batchlist = [];
                foreach($batchlistdt as $batchnames){
                    $batchlist[] = ['batch_id'=>$batchnames->id,'batch_name'=>$batchnames->batch_name];
                }
               $subjectList[] = [
                    'teacher_detail_id'=>$teacher_detail_ids[$index],
                    'subject_id' => $subjectId,
                    'subject_name' => $subjectNames[$index],
                    'subject_image' => (!empty($subject_image[$index])) ? asset($subject_image[$index]) : asset('profile/no-image.png'),
                    'batch_list' => $batchlist,
                ];
            }
        
            $response[] = [
                'board_id' => $detail->board_id,
                'board_name' => $detail->board_name,
                'medium_id' => $detail->medium_id,
                'medium_name' => $detail->medium_name,
                'standard_id' => $detail->standard_id,
                'standard_name' => $detail->standard_name,
                'subject_list' => $subjectList,
            ];
        }
            return $this->response($response, "Fetch detail sucessfully.");
         }catch(Exception $e){
            return $this->response($e, "Something went wrong.", false, 400);

        }
    }
    function teacher_student_fetch_subject_selected_subject(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
           
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            if(!empty($request->teacher_id)){
                $teacherDetails = Teacher_model::join('board', 'board.id', '=', 'teacher_detail.board_id')
                ->join('medium', 'medium.id', '=', 'teacher_detail.medium_id')
                ->join('standard', 'standard.id', '=', 'teacher_detail.standard_id')
                ->join('subject', 'subject.id', '=', 'teacher_detail.subject_id')
                ->select(
                    'teacher_detail.id',
                    'teacher_detail.batch_id',
                    'teacher_detail.board_id',
                    'teacher_detail.medium_id',
                    'teacher_detail.standard_id',
                    DB::raw('MAX(board.name) as board_name'),
                    DB::raw('MAX(medium.name) as medium_name'),
                    DB::raw('MAX(standard.name) as standard_name'),
                    DB::raw('GROUP_CONCAT(DISTINCT subject.id) as subject_id'),
                    DB::raw('GROUP_CONCAT(DISTINCT subject.name) as subject_names'),
                    DB::raw('GROUP_CONCAT(DISTINCT teacher_detail.id) as teacher_detail_id')
                )
                ->where('teacher_detail.institute_id', $request->institute_id)
                ->where('teacher_detail.teacher_id', $request->teacher_id)
                ->where('teacher_detail.board_id', $request->board_id)
                ->where('teacher_detail.medium_id', $request->medium_id)
                ->where('teacher_detail.standard_id', $request->standard_id)
                ->groupBy('teacher_detail.batch_id','teacher_detail.id', 'teacher_detail.board_id', 'teacher_detail.medium_id', 'teacher_detail.standard_id')
                ->get();
            
           
                $base_table_ids = [];
                $selected_subject_ids = [];
                $selected_batch_ids = [];
                $batch_table_ids = [];

                foreach ($teacherDetails as $value) {
                $ids = Base_table::where('board', $value->board_id)
                    ->where('medium', $value->medium_id)
                    ->where('standard', $value->standard_id)
                    ->pluck('id')
                    ->toArray();
                $base_table_ids = array_merge($base_table_ids, $ids);

                $selected_subject_ids = array_merge($selected_subject_ids, explode(',', $value->subject_id));
                $selected_batch_ids = array_merge($selected_batch_ids, explode(',', $value->batch_id));

                $total_batch_ids = Batches_model::where('institute_id', $request->institute_id)
                                                ->where('board_id', $value->board_id)
                                                ->where('medium_id', $value->medium_id)
                                                ->where('standard_id', $value->standard_id)
                                                ->pluck('id')
                                                ->toArray();
                $batch_table_ids = array_merge($batch_table_ids, $total_batch_ids);
                }

                $base_table_ids = array_unique($base_table_ids);
                $selected_subject_ids = array_map('intval', array_unique($selected_subject_ids));
                $selected_batch_ids = array_map('intval', array_unique($selected_batch_ids));
                $batch_table_ids = array_unique($batch_table_ids);

                $subject_list = Subject_model::whereIn('base_table_id', $base_table_ids)->pluck('name', 'id')->toArray();
                $subject_results = [];
                foreach ($subject_list as $sid => $sname) {
                $subject_status = in_array($sid, $selected_subject_ids) ? 1 : 0;
                $batch_list = Batches_model::where('institute_id', $request->institute_id)
                
                 ->whereRaw('FIND_IN_SET(?, subjects) > 0', [$sid])
                ->pluck('batch_name', 'id')
                ->toArray();
             $all_batches_results = [];
                foreach ($batch_list as $id => $name) {
                $tdl = Teacher_model::where('subject_id',$sid)
                ->where('institute_id',$request->institute_id)
                ->where('teacher_id',$request->teacher_id)->first();
                $btchesids = explode(",",!empty($tdl->batch_id)?$tdl->batch_id:'');
                
                $all_batches_results[$id] = [
                    'batch_id' => $id,
                    'batch_name' => $name,
                   'status' => in_array($id,$btchesids) ? 1 : 0,
                ];
            }
            $subject_batches = array_values($all_batches_results);
                $subject_results[] = [
                    'subject_id' => $sid,
                    'subject_name' => $sname,
                    'status' => $subject_status,
                    'batches' => $subject_batches,
                ];
            }
                
                $response = ['subject_list'=>$subject_results];
                
                return $this->response($response, "Fetch data successfully.");
        }

        //student data
        if(!empty($request->student_id)){
            $selected_subject = Student_detail::where('institute_id', $request->institute_id)
            ->where('student_id', $request->student_id)
            ->first();
       
            $base_table_ids = [];
            $selected_subject_ids = [];
            $selected_batch_ids = [];
            $batch_table_ids = [];

            $ids = Base_table::where('board', $selected_subject->board_id)
                ->where('medium', $selected_subject->medium_id)
                ->where('standard', $selected_subject->standard_id)
                ->pluck('id')
                ->toArray();

            $base_table_ids = $ids;

            $subject_list = Subject_model::whereIn('base_table_id', $ids)
            ->pluck('name', 'id')->toArray();
            $subject_results = [];
            foreach ($subject_list as $sid => $sname) {
            $subject_status = in_array($sid, explode(",",$selected_subject->subject_id)) ? 1 : 0;
            $batch_list = Batches_model::where('institute_id', $request->institute_id)
            
             ->whereRaw('FIND_IN_SET(?, subjects) > 0', [$sid])
            ->pluck('batch_name', 'id')
            ->toArray();
            $all_batches_results = [];
            foreach ($batch_list as $id => $name) {
            $tdl = Student_detail::where('subject_id', 'LIKE', "%$sid%")
            ->where('institute_id',$request->institute_id)
            ->where('student_id',$request->student_id)->first();
            $btchesids = explode(",",!empty($tdl->batch_id)?$tdl->batch_id:'');
            
            $all_batches_results[$id] = [
                'batch_id' => $id,
                'batch_name' => $name,
               'status' => in_array($id,$btchesids) ? 1 : 0,
            ];
            }
            $subject_batches = array_values($all_batches_results);
            $subject_results[] = [
                'subject_id' => $sid,
                'subject_name' => $sname,
                'status' => $subject_status,
                'batches' => $subject_batches,
            ];
            }
            
            $response = ['subject_list'=>$subject_results];
            
            return $this->response($response, "Fetch data successfully.");
    }
        }catch(Exception $e){
            return $e;
            return $this->response($e, "Something went wrong.", false, 400);

        }
    }

    public function replacement_fetch_data(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
          
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            if ($request->child_id) {
                $student_id = $request->child_id;
            } else {
                $student_id = Auth::id();
            }
            $announcement = [];
            $getstdntdata = Student_detail::where('student_id', $student_id)
                ->where('institute_id', $request->institute_id)
                ->where('status', '=', '1')
                ->whereNull('deleted_at')
                ->first();
            if (!empty($getstdntdata)) {
                $announcQY = announcements_model::where('institute_id', $request->institute_id)
                    ->where('batch_id', $getstdntdata->batch_id)
                    ->whereRaw("FIND_IN_SET('6', role_type)")
                    ->when($request->child_id ,function($query){
                        $query->orwhereRaw("FIND_IN_SET('5', role_type)");
                    })
                    ->orderByDesc('created_at')
                    ->get();

                if (!empty($announcQY)) {
                    foreach ($announcQY as $announcDT) {
                        $announcement[] = array(
                            'title' => $announcDT->title,
                            'desc' => $announcDT->detail,
                            'time' => $announcDT->created_at
                        );
                    }
                }
            }

            return $this->response($announcement, "Announcement List");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!.", false, 400);
        }
    }
    
}
