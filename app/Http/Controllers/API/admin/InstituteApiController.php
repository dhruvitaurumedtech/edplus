<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\announcements_model;
use App\Models\Banner_model;
use App\Models\board;
use App\Models\Class_model;
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
use App\Models\Subject_model;
use App\Models\Subject_sub;
use App\Models\Student_detail;
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
use App\Models\Batch_assign_teacher_model;
use App\Models\Parents;
use App\Models\RoleHasPermission;
use App\Models\Student_fees_model;
use App\Models\Teacher_model;
use App\Models\UserHasRole;
use Exception;
use Illuminate\Support\Facades\Auth;

class InstituteApiController extends Controller
{

    use ApiTrait;

    private function array_symmetric_diff(array $array1, array $array2)
    {
        $diff1 = array_diff($array1, $array2);
        $diff2 = array_diff($array2, $array1);
        return array_merge($diff1, $diff2);
    }
    // function get_institute_reponse(Request $request)
    // {
    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }


    //     $existingUser = User::where('token', $token)->first();
    //     if ($existingUser) {

    //         $basinstitute = Base_table::where('status', 'active')
    //         ->select('institute_for')->groupby('institute_for')->get();
    //         $institute_for_id = '';
    //         foreach ($basinstitute as $insvalue) {
    //             $institute_for_id .= $insvalue->institute_for;
    //         }
    //         $institute_for_id .= 0;

    //         $institute_for_id = $basinstitute->pluck('institute_for')->toArray();

    //         $institute_for_array = DB::table('institute_for')
    //             ->whereIN('id', $institute_for_id)->get();

    //         $institute_for = [];
    //         foreach ($institute_for_array as $institute_for_array_value) {

    //             $onlyboardfrombase = base_table::where('institute_for', $institute_for_array_value->id)
    //                 ->select('board')
    //                 ->groupby('board')
    //                 ->get();
    //             $boardsids = '';
    //             foreach ($onlyboardfrombase as $boardsval) {
    //                 $boardsids .= $boardsval->board . ',';
    //             }
    //             $boardsids .= 0;

    //             $boardsids = $onlyboardfrombase->pluck('board')->toArray();
    //             $board_array = board::whereIN('id', $boardsids)
    //                 ->get();


    //             $board = [];
    //             foreach ($board_array as $board_array_value) {
    //                 $mediumsidget = base_table::where('board', $board_array_value->id)
    //                     ->where('institute_for', $institute_for_array_value->id)
    //                     ->select('medium')
    //                     ->groupby('medium')
    //                     ->get();
    //                 $mediumids = '';
    //                 foreach ($mediumsidget as $mediumsids) {
    //                     $mediumids .= $mediumsids->medium;
    //                 }
    //                 $mediumids .= 0;

    //                 $mediumids = $mediumsidget->pluck('medium')->toArray();
    //                 $medium_array = Medium_model::whereIN('id', $mediumids)->get();
    //                 $medium = [];

    //                 foreach ($medium_array as $medium_array_value) {

    //                     $classesidget = base_table::where('medium', $medium_array_value->id)
    //                         ->where('board', $board_array_value->id)
    //                         ->where('institute_for', $institute_for_array_value->id)
    //                         ->select('institute_for_class')
    //                         ->groupby('institute_for_class')
    //                         ->get();
    //                     $institute_for_classids = '';
    //                     foreach ($classesidget as $classesids) {
    //                         $institute_for_classids .= $classesids->institute_for_class;
    //                     }
    //                     $institute_for_classids .= 0;
    //                     $institute_for_classids = $classesidget->pluck('institute_for_class')->toArray();
    //                     $class_array = Class_model::whereIN('id', $institute_for_classids)
    //                         ->get();

    //                     $class = [];
    //                     foreach ($class_array as $class_array_value) {

    //                         $standardidget = base_table::where('institute_for_class', $class_array_value->id)
    //                             ->where('medium', $medium_array_value->id)
    //                             ->where('board', $board_array_value->id)
    //                             ->where('institute_for', $institute_for_array_value->id)
    //                             ->select('standard', 'id')
    //                             ->get();
    //                         $standardids = '';
    //                         foreach ($standardidget as $standardidsv) {
    //                             $standardids .= $standardidsv->standard;
    //                         }
    //                         $standardids .= 0;
    //                         $standardids = $standardidget->pluck('standard')->toArray();
    //                         $standard_array = Standard_model::whereIN('id', $standardids)
    //                             ->get();


    //                         $standard = [];
    //                         foreach ($standard_array as $standard_array_value) {

    //                             $stream_array = DB::table('base_table')
    //                                 ->leftJoin('stream', 'stream.id', '=', 'base_table.stream')
    //                                 ->select('stream.name as stream_name', 'base_table.id', 'stream.id as stream_id')
    //                                 ->whereNull('base_table.deleted_at')
    //                                 ->where('base_table.standard', $standard_array_value->id)
    //                                 // ->where('base_table.institute_for_class',$class_array_value->id)
    //                                 //->where('base_table.medium',$medium_array_value->id)
    //                                 //->where('base_table.board',$board_array_value->id)
    //                                 // ->where('base_table.institute_for',$institute_for_array_value->id)
    //                                 ->get();
    //                             $stream = [];

    //                             foreach ($stream_array as $stream_array_value) {

    //                                 $forsubdidget = base_table::where('institute_for_class', $class_array_value->id)
    //                                     ->where('institute_for', $institute_for_array_value->id)
    //                                     ->where('standard', $standard_array_value->id)
    //                                     ->where('board', $board_array_value->id)
    //                                     ->where('medium', $medium_array_value->id)
    //                                     ->orwhere('stream', $stream_array_value->id)
    //                                     ->select('standard', 'id')
    //                                     ->get();
    //                                 $baseidsfosubj = '';
    //                                 foreach ($forsubdidget as $forsubval) {
    //                                     $baseidsfosubj .= $forsubval->id . ',';
    //                                 }
    //                                 $baseidsfosubj .= 0;
    //                                 $baseidsfosubj = $forsubdidget->pluck('id')->toArray();

    //                                 // $subject_array = Subject_model::whereIN('base_table_id', $baseidsfosubj)
    //                                 //     ->get();

    //                                 if (!empty($stream_array_value->stream_id)) {
    //                                     $stream[] = [
    //                                         'stream_id' => $stream_array_value->stream_id . '',
    //                                         'stream' => $stream_array_value->stream_name . '',
    //                                         // 'subject' => $subject_array
    //                                     ];
    //                                 }
    //                             }

    //                             $subject_array = Subject_model::join('base_table', 'base_table.id', '=', 'subject.base_table_id')
    //                                 ->whereIN('subject.base_table_id', $baseidsfosubj)
    //                                 ->select('subject.*', 'base_table.stream')
    //                                 ->get();

    //                             $subject = [];
    //                             foreach ($subject_array as $value) {
    //                                 if ($value->stream != null) {
    //                                     $sstream = $value->stream;
    //                                 } else {
    //                                     $sstream = 0;
    //                                 }
    //                                 $subject[] = [
    //                                     'subject_id' => $value->id,
    //                                     'subject' => $value->name,
    //                                     'stream_id' => $sstream
    //                                 ];
    //                             }
    //                             $standard[] = [
    //                                 'standard_id' => $standard_array_value->id,
    //                                 'standard' => $standard_array_value->name,
    //                                 'stream' => $stream,
    //                                 'subject' => $subject
    //                             ];
    //                         }

    //                         $class[] = [
    //                             'class_id' => $class_array_value->id,
    //                             'class_icon' => asset($class_array_value->icon),
    //                             'class' => $class_array_value->name,
    //                             'standard' => $standard,
    //                         ];
    //                     }

    //                     $medium[] = [
    //                         'medium_id' => $medium_array_value->id,
    //                         'medium_icon' => asset($medium_array_value->icon),
    //                         'medium' => $medium_array_value->name,
    //                         'class' => $class,
    //                     ];
    //                 }

    //                 $board[] = [
    //                     'board_id' => $board_array_value->id,
    //                     'board_icon' => asset($board_array_value->icon),
    //                     'board' => $board_array_value->name,
    //                     'medium' => $medium,
    //                 ];
    //             }


    //             // $institute_for[] = [
    //             //     'institute_id'=>$institute_for_array_value->id,
    //             //     'institute_for' => $institute_for_array_value->institute_for_name,
    //             //     'board_detail' => $board,
    //             // ];
    //             $institute_for_name = $institute_for_array_value->name;

    //             if (!isset($institute_for[$institute_for_name])) {
    //                 $institute_for[] = [
    //                     'institute_id' => $institute_for_array_value->id,
    //                     'institute_icon' => asset($institute_for_array_value->icon),
    //                     'institute_for' => $institute_for_name,
    //                     'board_details' => $board,
    //                 ];
    //             } else {
    //                 $institute_for['board_details'][] = $board;
    //             }
    //             $institute_for = array_values($institute_for);
    //         }
    //         $dobusiness_with = Dobusinesswith_Model::where('status', 'active')
    //             ->where(function ($query) {
    //                 $query->whereNull('created_by')
    //                     ->orWhere('created_by', 1);
    //             })->get();
    //         $do_business_with = [];
    //         foreach ($dobusiness_with as $dobusinesswith_val) {
    //             $do_business_with[] = array(
    //                 'id' => $dobusinesswith_val->id,
    //                 'name' => $dobusinesswith_val->name
    //             );
    //         }
    //         $data = array(
    //             'do_business_with' => $do_business_with,
    //             'institute_details' => $institute_for
    //         );
    //         //    echo "<pre>";print_r($institute_for);exit;
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Fetch Data Successfully',
    //             'data' => $data,
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ], 400);
    //     }
    // }

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

                                        // $subject_array = Subject_model::whereIN('base_table_id', $baseidsfosubj)
                                        //     ->get();

                                        if (!empty($stream_array_value->stream_id)) {
                                            $stream[] = [
                                                'stream_id' => $stream_array_value->stream_id . '',
                                                'stream' => $stream_array_value->stream_name . '',
                                                // 'subject' => $subject_array
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
            return $this->response([], "Something want Wrong!!", false, 400);
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
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
            'email' => 'required|email|unique:institute_detail,email',
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
            // echo "<pre>";print_r($imagePath);exit;
            $currentDate = date("d-m-Y");
            $nextYearDate = date("d-m-Y", strtotime("+1 year"));
            $nextYear = date("d-m-Y", strtotime($nextYearDate));
            $dateString = $currentDate . " / " . $nextYear;
            $instituteDetail = Institute_detail::create([
                'unique_id' => $unique_id,
                'logo' => $imagePath,
                'about_us' => $request->about_us,
                'user_id' => $request->input('user_id'),
                'institute_name' => $request->input('institute_name'),
                'address' => $request->input('address'),
                'country_code' => $request->input('country_code'),
                'contact_no' => $request->input('contact_no'),
                'email' => $request->input('email'),
                'country' => $request->input('country'),
                'state' => $request->input('state'),
                'city' => $request->input('city'),
                'pincode' => $request->input('pincode'),
                'cover_photo' =>$imagePath2,
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
            return $this->response($e, "Invalid token.", false, 400);
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
            // print_r($institute_subjects);
            // print_r($institute_subject_ids);
            
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
                        $boarddif = $this->array_symmetric_diff($boardtewArray,$boardIds);
                    if(!empty($boarddif)){
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
                        // if (!in_array($board, $boardtewArray)) {
                        //     $student_check = Student_detail::where('board_id', $board)->where('institute_id', $institute->id)->first();
                        //     $teacher_check = Teacher_model::where('board_id', $board)->where('institute_id', $institute->id)->first();
                        //     if (!empty($student_check) || !empty($teacher_check)) {
                        //         return $this->response([], "Cannot remove institute_board. Already exist student and teacher this institute_board.", false, 400);
                        //     } else {
                        //         $delete_sub = Institute_board_sub::where('board_id', $board)
                        //             ->where('institute_id', $institute->id)->get();
                        //         if (!empty($delete_sub)) {
                        //             foreach ($delete_sub as $did) {
                        //                 $did->delete();
                        //             }
                        //         }
                        //     }
                        // }
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

            // $institute_for = Institute_for_sub::where('institute_id', $institute->id)->pluck('institute_for_id')->toArray();
            // $institute_for_ids = explode(',', $request->institute_for_id);
            // $differenceInstituteforArray = $this->array_symmetric_diff($institute_for, $institute_for_ids);
            // if (!empty($differenceInstituteforArray)) {
            //     $institute_for_check = Institute_for_sub::where('institute_id', $institute->id)->whereIn('institute_for_id', $differenceInstituteforArray)->pluck('institute_for_id');
            //     if ($institute_for_check->isEmpty()) {
            //         foreach ($differenceInstituteforArray as $difinstitute_for_ids) {
            //             $sub_instfor_exists = Institute_for_sub::where('institute_id', $institute->id)->where('institute_for_id', $difinstitute_for_ids)->first();
            //             if (!$sub_instfor_exists) {
            //                 Institute_for_sub::create([
            //                     'user_id' => $institute->user_id,
            //                     'institute_id' => $institute->id,
            //                     'institute_for_id' => $difinstitute_for_ids
            //                 ]);
            //             }
            //         }
            //     }else{
            //         $student_check = Student_detail::whereIn('institute_for_id', $institute_for_check)->where('institute_id', $institute->id)->first();
            //         $teacher_check = Teacher_model::whereIn('institute_for_id', $institute_for_check)->where('institute_id', $institute->id)->first();
            //         if (!empty($student_check) && !empty($teacher_check)) {
            //             return $this->response([], "Cannot remove institute_for. Already exist student and teacher this institute_for.", false, 400);
            //         } else {
            //             $delete_sub = Institute_for_sub::whereIn('institute_for_id', $institute_for_check)->where('institute_id', $institute->id)->get();
            //             if (!empty($delete_sub)) {
            //                 foreach ($delete_sub as $did) {
            //                     $did->delete();
            //                 }
            //             }
            //         }
            //     }
            // }

            // $institute_board = Institute_board_sub::where('institute_id', $institute->id)->pluck('board_id')->toArray();
            // $institute_board_ids = explode(',', $request->institute_board_id);
            // $differenceInstituteboardArray = $this->array_symmetric_diff($institute_board,$institute_board_ids);
            // if (!empty($differenceInstituteboardArray)) {
            //     $institute_board_check = Institute_board_sub::where('institute_id', $institute->id)->whereIn('board_id', $differenceInstituteboardArray)->pluck('board_id');
            //     if ($institute_board_check->isEmpty()) {
            //         foreach ($institute_board_ids as $institute_board_id) {
            //             foreach ($institute_for_ids as $institute_for_id) {
            //                 $sub_instboard_exists = Institute_board_sub::where('institute_id', $institute->id)->where('board_id', $institute_board_id)->where('institute_for_id', $institute_for_id)->first();
            //                 if (!$sub_instboard_exists) {
            //                     Institute_board_sub::create([
            //                         'user_id' => $institute->user_id,
            //                         'institute_id' => $institute->id,
            //                         'board_id' => $institute_board_id,
            //                         'institute_for_id' => $institute_for_id
            //                     ]);
            //                 }
            //             }
            //         }
            //     } else {
            //         $student_check = Student_detail::whereIn('board_id', $institute_board_check)->where('institute_id', $institute->id)->first();
            //         $teacher_check = Teacher_model::whereIn('board_id', $institute_board_check)->where('institute_id', $institute->id)->first();
            //         if (!empty($student_check) || !empty($teacher_check)) {
            //             return $this->response([], "Cannot remove institute_board. Already exist student and teacher this institute_board.", false, 400);
            //         } else {
            //             $delete_sub = Institute_board_sub::whereIn('board_id', $institute_board_check)->where('institute_id', $institute->id)->get();
            //             if (!empty($delete_sub)) {
            //                 foreach ($delete_sub as $did) {
            //                     $did->delete();
            //                 }
            //             }
            //         }
            //     }
            // } 

            // $institute_medium = Medium_sub::where('institute_id', $institute->id)->pluck('medium_id')->toArray();
            // $institute_medium_ids = explode(',', $request->institute_medium_id);
            // $differenceInstitutemediumArray = $this->array_symmetric_diff($institute_medium, $institute_medium_ids);
            // if (!empty($differenceInstitutemediumArray)) {
            //     $institute_medium_check = Medium_sub::where('institute_id', $institute->id)->whereIn('medium_id', $differenceInstitutemediumArray)->pluck('medium_id');
            //     if ($institute_medium_check->isEmpty()) {
            //         foreach ($institute_medium_ids as $institute_medium_id) {
            //             foreach ($institute_for_ids as $institute_for_id) {
            //                 foreach ($institute_board_ids as $institute_board_id) {
            //                     $sub_instmedium_exists = Medium_sub::where('institute_id', $institute->id)->where('medium_id', $institute_medium_id)->where('institute_for_id', $institute_for_id)->where('board_id', $institute_board_id)->tosql();
            //                     if (!$sub_instmedium_exists) {
            //                         Medium_sub::create([
            //                             'user_id' => $institute->user_id,
            //                             'institute_id' => $institute->id,
            //                             'medium_id' => $institute_medium_id,
            //                             'institute_for_id' => $institute_for_id,
            //                             'board_id' => $institute_board_id
            //                         ]);
            //                     }
            //                 }
            //             }
            //         }
            //     }else{
            //         $student_check = Student_detail::whereIn('medium_id', $institute_medium_check)->where('institute_id', $institute->id)->first();
            //         $teacher_check = Teacher_model::whereIn('medium_id', $institute_medium_check)->where('institute_id', $institute->id)->first();
            //         if (!empty($student_check) || !empty($teacher_check)) {
            //             return $this->response([], "Cannot remove institute_medium. Already exist student or teacher in this institute_medium.", false, 400);
            //         } else {
            //             $delete_sub = Medium_sub::whereIn('medium_id', $institute_medium_check)->where('institute_id', $institute->id)->get();
            //             if (!empty($delete_sub)) {
            //                 foreach ($delete_sub as $did) {
            //                     $did->delete();
            //                 }
            //             }
            //         }
            //     }
            // }

            // $class_medium = Class_sub::where('institute_id', $institute->id)->pluck('class_id')->toArray();
            // $class_medium_ids = explode(',', $request->institute_for_class_id);
            // $differenceClassmediumArray = $this->array_symmetric_diff($class_medium, $class_medium_ids);

            // if (!empty($differenceClassmediumArray)) {
            //     $class_medium_check = Class_sub::where('institute_id', $institute->id)->whereIn('class_id', $differenceClassmediumArray)->pluck('class_id');
            //     if ($class_medium_check->isEmpty()) {
            //         foreach ($class_medium_ids as $class_medium_id) {
            //             foreach ($institute_for_ids as $institute_for_id) {
            //                 foreach ($institute_board_ids as $institute_board_id) {
            //                     foreach ($institute_medium_ids as $institute_medium_id) {
            //                         $sub_classmedium_exists = Class_sub::where('institute_id', $institute->id)
            //                             ->where('class_id', $class_medium_id)
            //                             ->where('institute_for_id', $institute_for_id)
            //                             ->where('board_id', $institute_board_id)
            //                             ->where('medium_id', $institute_medium_id)
            //                             ->first();
            //                         if (!$sub_classmedium_exists) {
            //                             Class_sub::create([
            //                                 'user_id' => $institute->user_id,
            //                                 'institute_id' => $institute->id,
            //                                 'class_id' => $class_medium_id,
            //                                 'institute_for_id' => $institute_for_id,
            //                                 'board_id' => $institute_board_id,
            //                                 'medium_id' => $institute_medium_id
            //                             ]);
            //                         }
            //                     }
            //                 }
            //             }
            //         }
            //     }else{
            //         $student_check = Student_detail::whereIn('class_id', $class_medium_check)->where('institute_id', $institute->id)->first();
            //         $teacher_check = Teacher_model::whereIn('class_id', $class_medium_check)->where('institute_id', $institute->id)->first();
            //         if (!empty($student_check) || !empty($teacher_check)) {
            //             return $this->response([], "Cannot remove class_medium. Already exist student or teacher in this class_medium.", false, 400);
            //         } else {
            //             $delete_sub = Class_sub::whereIn('class_id', $class_medium_check)->where('institute_id', $institute->id)->get();
            //             if (!empty($delete_sub)) {
            //                 foreach ($delete_sub as $did) {
            //                     $did->delete();
            //                 }
            //             }
            //         }
            //     }
            // }


            // $standard_medium = Standard_sub::where('institute_id', $institute->id)->pluck('standard_id')->toArray();
            // $standard_medium_ids = explode(',', $request->standard_id);

            // $differenceStandardmediumArray = $this->array_symmetric_diff($standard_medium, $standard_medium_ids);

            // if (!empty($differenceStandardmediumArray)) {
            //     $standard_medium_check = Standard_sub::where('institute_id', $institute->id)->whereIn('standard_id', $differenceStandardmediumArray)->pluck('standard_id');
            //     if ($standard_medium_check->isEmpty()) {
            //         foreach ($standard_medium_ids as $standard_medium_id) {
            //             foreach ($institute_for_ids as $institute_for_id) {
            //                 foreach ($institute_board_ids as $institute_board_id) {
            //                     foreach ($institute_medium_ids as $institute_medium_id) {
            //                         foreach ($class_medium_ids as $class_medium_id) {
            //                             $sub_standardmedium_exists = Standard_sub::where('institute_id', $institute->id)
            //                                 ->where('standard_id', $standard_medium_id)
            //                                 ->where('institute_for_id', $institute_for_id)
            //                                 ->where('board_id', $institute_board_id)
            //                                 ->where('medium_id', $institute_medium_id)
            //                                 ->where('class_id', $class_medium_id)
            //                                 ->first();
            //                             if (!$sub_standardmedium_exists) {
            //                                 Standard_sub::create([
            //                                     'user_id' => $institute->user_id,
            //                                     'institute_id' => $institute->id,
            //                                     'standard_id' => $standard_medium_id,
            //                                     'institute_for_id' => $institute_for_id,
            //                                     'board_id' => $institute_board_id,
            //                                     'medium_id' => $institute_medium_id,
            //                                     'class_id' => $class_medium_id
            //                                 ]);
            //                             }
            //                         }
            //                     }
            //                 }
            //             }
            //         }
            //     }else{
            //         $student_check = Student_detail::whereIn('standard_id', $standard_medium_check)->where('institute_id', $institute->id)->first();
            //         $teacher_check = Teacher_model::whereIn('standard_id', $standard_medium_check)->where('institute_id', $institute->id)->first();
            //         if (!empty($student_check) || !empty($teacher_check)) {
            //             return $this->response([], "Cannot remove standard_medium. Already exist student or teacher in this standard_medium.", false, 400);
            //         } else {
            //             $delete_sub = Standard_sub::whereIn('standard_id', $standard_medium_check)->where('institute_id', $institute->id)->get();
            //             if (!empty($delete_sub)) {
            //                 foreach ($delete_sub as $did) {
            //                     $did->delete();
            //                 }    
            //             }
            //         }
            //     }
            // }

            // $stream_medium = Stream_sub::where('institute_id', $institute->id)->pluck('stream_id')->toArray();
            // $stream_medium_ids = explode(',', $request->stream_id);
            // $stream_standards_ids = explode(',', $request->standard_id);
            // $differenceStreammediumArray = $this->array_symmetric_diff($stream_medium, $stream_medium_ids);

            // if (!empty($differenceStreammediumArray[0])) {
            //     $stream_medium_check = Stream_sub::where('institute_id', $institute->id)->whereIn('stream_id', $differenceStreammediumArray)->pluck('stream_id');
            //     if ($stream_medium_check->isEmpty()) {
            //         foreach ($stream_medium_ids as $stream_medium_id) {
            //             foreach ($institute_for_ids as $institute_for_id) {
            //                 foreach ($institute_board_ids as $institute_board_id) {
            //                     foreach ($institute_medium_ids as $institute_medium_id) {
            //                         foreach ($class_medium_ids as $class_medium_id) {
            //                             foreach ($stream_standards_ids as $standard_id) {
            //                                 $sub_streammedium_exists = Stream_sub::where('institute_id', $institute->id)
            //                                     ->where('stream_id', $stream_medium_id)
            //                                     ->where('institute_for_id', $institute_for_id)
            //                                     ->where('board_id', $institute_board_id)
            //                                     ->where('medium_id', $institute_medium_id)
            //                                     ->where('class_id', $class_medium_id)
            //                                     ->where('standard_id', $standard_id)
            //                                     ->first();
            //                                 if (!$sub_streammedium_exists) {
            //                                     Stream_sub::create([
            //                                         'user_id' => $institute->user_id,
            //                                         'institute_id' => $institute->id,
            //                                         'stream_id' => $stream_medium_id,
            //                                         'institute_for_id' => $institute_for_id,
            //                                         'board_id' => $institute_board_id,
            //                                         'medium_id' => $institute_medium_id,
            //                                         'class_id' => $class_medium_id,
            //                                         'standard_id' => $standard_id
            //                                     ]);
            //                                 }
            //                             }
            //                         }
            //                     }
            //                 }
            //             }
            //         }
            //     }else{

            //         $student_check = Student_detail::whereIn('stream_id', $stream_medium_check)->where('institute_id', $institute->id)->first();
            //         $teacher_check = Teacher_model::whereIn('stream_id', $stream_medium_check)->where('institute_id', $institute->id)->first();
            //         if (!empty($student_check) || !empty($teacher_check)) {
            //             return $this->response([], "Cannot remove stream_medium. Already exist student or teacher in this stream_medium.", false, 400);
            //         } else {
            //             $delete_sub = Stream_sub::whereIn('stream_id', $stream_medium_check)->where('institute_id', $institute->id)->get();
            //             if (!empty($delete_sub)) {
            //                 foreach ($delete_sub as $did) {
            //                     $did->delete();
            //                 }
            //             }
            //         }
            //     }
            // } 

            // $institute_subjects = Subject_sub::where('institute_id', $institute->id)->pluck('subject_id')->toArray();
            // $institute_subject_ids = explode(',', $request->subject_id);
            // $differenceInstituteSubjectArray = $this->array_symmetric_diff($institute_subjects, $institute_subject_ids);
            // if (!empty($differenceInstituteSubjectArray)) {
            //     $institute_subject_check = Subject_sub::where('institute_id', $institute->id)->whereIn('subject_id', $differenceInstituteSubjectArray)->pluck('subject_id');
            //     if ($institute_subject_check->isEmpty()) {
            //         foreach ($institute_subject_ids as $institute_subject_id) {
            //             $sub_instboard_exists = Subject_sub::where('institute_id', $institute->id)->where('subject_id', $institute_subject_id)->first();
            //             if (!$sub_instboard_exists) {
            //                 Subject_sub::create([
            //                     'user_id' => $institute->user_id,
            //                     'institute_id' => $institute->id,
            //                     'subject_id' => $institute_subject_id,
            //                 ]);
            //             }
            //         }
            //     }else{
            //         $student_check = Student_detail::whereIn('subject_id', $institute_subject_check)->where('institute_id', $institute->id)->first();
            //         $teacher_check = Teacher_model::whereIn('subject_id', $institute_subject_check)->where('institute_id', $institute->id)->first();
            //         if (!empty($student_check) || !empty($teacher_check)) {
            //             return $this->response([], "Cannot remove institute_subject. Already exist student and teacher for this institute_subject.", false, 400);
            //         } else {
            //             $delete_sub = Subject_sub::whereIn('subject_id', $institute_subject_check)->where('institute_id', $institute->id)->get();
            //             if (!empty($delete_sub)) {
            //                 foreach ($delete_sub as $did) {
            //                     $did->delete();
            //                 }
            //             }
            //         }
            //     }
            // }

            return $this->response([], "institute Update Successfully");
        } catch (Exception $e) {
            //return $e->getMessage();
            return $this->response([], $e->getMessage(), false, 400);
        }
    }



    // public function register_institute123(Request $request)
    // {

    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required|integer',
    //         'institute_for_id' => 'required',
    //         'institute_board_id' => 'required',
    //         'institute_for_class_id' => 'required',
    //         'institute_medium_id' => 'required',
    //         'institute_work_id' => 'required',
    //         'standard_id' => 'required',
    //         'subject_id' => 'required',
    //         'institute_name' => 'required|string',
    //         'address' => 'required|string',
    //         'contact_no' => 'required|integer|min:10',
    //         'email' => 'required|email|unique:institute_detail,email',
    //         'logo' => 'required',
    //         'country' => 'required',
    //         'state' => 'required',
    //         'city' => 'required',
    //         'pincode' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }
    //     try {
    //         $subadminPrefix = 'ist_';
    //         $startNumber = 101;

    //         $lastInsertedId = DB::table('institute_detail')->orderBy('id', 'desc')->value('unique_id');
    //         // echo $lastInsertedId;exit;
    //         if (!is_null($lastInsertedId)) {
    //             $number = substr($lastInsertedId, 3);
    //             $numbers = str_replace('_', '', $number);

    //             $newID = $numbers + 1;
    //         } else {
    //             $newID = $startNumber;
    //         }

    //         $paddedNumber = str_pad($newID, 3, '0', STR_PAD_LEFT);

    //         $unique_id = $subadminPrefix . $paddedNumber;

    //         $iconFile = $request->file('logo');
    //         $imagePath = $iconFile->store('icon', 'public');
    //         //institute_detail
    //         //acedamic year
    //         $currentDate = date("d-m-Y");
    //         $nextYearDate = date("d-m-Y", strtotime("+1 year"));
    //         $nextYear = date("d-m-Y", strtotime($nextYearDate));
    //         $dateString = $currentDate . " / " . $nextYear;
    //         $instituteDetail = Institute_detail::create([
    //             'unique_id' => $unique_id,
    //             // 'youtube_link' => $request->input('youtube_link'),
    //             // 'whatsaap_link' => $request->input('whatsaap_link'),
    //             // 'facebook_link' => $request->input('facebook_link'),
    //             // 'instagram_link' => $request->input('instagram_link'),
    //             // 'website_link' => $request->input('website_link'),
    //             // 'gst_slab' => $request->input('gst_slab'),
    //             // 'gst_number' => $request->input('gst_number'),
    //             // 'close_time' => $request->input('close_time'),
    //             // 'open_time' => $request->input('open_time'),
    //             'logo' => $imagePath,
    //             'about_us' => $request->about_us,
    //             'user_id' => $request->input('user_id'),
    //             'institute_name' => $request->input('institute_name'),
    //             'address' => $request->input('address'),
    //             'contact_no' => $request->input('contact_no'),
    //             'email' => $request->input('email'),
    //             'country' => $request->input('country'),
    //             'state' => $request->input('state'),
    //             'city' => $request->input('city'),
    //             'pincode' => $request->input('pincode'),
    //             'status' => 'active',
    //             'start_academic_year' => $currentDate,
    //             'end_academic_year' => $nextYear
    //         ]);
    //         $lastInsertedId = $instituteDetail->id;
    //         $institute_name = $instituteDetail->institute_name;

    //         $subjectid = explode(',', $request->input('subject_id'));
    //         $sectsbbsiqy = Subject_model::whereIN('id', $subjectid)->pluck('base_table_id')->toArray();

    //         $uniqueArray = array_unique($sectsbbsiqy);
    //         $basedtqy = Base_table::whereIN('id', $uniqueArray)->get();
    //         foreach ($basedtqy as $svaluee) {
    //             $institute_for = $svaluee->institute_for;
    //             $board = $svaluee->board;
    //             $medium = $svaluee->medium;
    //             $institute_for_class = $svaluee->institute_for_class;
    //             $standard = $svaluee->standard;
    //             $stream = $svaluee->stream;

    //             $insfor = Institute_for_sub::where('institute_id', $lastInsertedId)
    //                 ->where('institute_for_id', $institute_for)->first();
    //             if (empty($insfor)) {

    //                 $createinstitutefor = Institute_for_sub::create([
    //                     'user_id' => $request->input('user_id'),
    //                     'institute_id' => $lastInsertedId,
    //                     'institute_for_id' => $institute_for,
    //                 ]);

    //                 if (!$createinstitutefor) {
    //                     $instituteFordet = Institute_detail::where('id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->first();
    //                     $delt = $instituteFordet->delete();
    //                 }
    //             }

    //             $bordsubr = Institute_board_sub::where('institute_id', $lastInsertedId)
    //                 ->where('institute_for_id', $institute_for)
    //                 ->where('board_id', $board)->first();

    //             if (empty($bordsubr)) {
    //                 $createboard = Institute_board_sub::create([
    //                     'user_id' => $request->input('user_id'),
    //                     'institute_id' => $lastInsertedId,
    //                     'institute_for_id' => $institute_for,
    //                     'board_id' => $board,
    //                 ]);

    //                 if (!$createboard) {
    //                     $instituteFordet = Institute_detail::where('id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->first();
    //                     $instituteFordet->delete();

    //                     $instituteForSub = Institute_for_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();
    //                 }
    //             }

    //             $medadded = Medium_sub::where('institute_id', $lastInsertedId)
    //                 ->where('institute_for_id', $institute_for)
    //                 ->where('board_id', $board)
    //                 ->where('medium_id', $medium)
    //                 ->first();
    //             if (empty($medadded)) {
    //                 $createmedium = Medium_sub::create([
    //                     'user_id' => $request->input('user_id'),
    //                     'institute_id' => $lastInsertedId,
    //                     'institute_for_id' => $institute_for,
    //                     'board_id' => $board,
    //                     'medium_id' => $medium,
    //                 ]);

    //                 if (!$createmedium) {
    //                     $instituteFordet = Institute_detail::where('id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->first();
    //                     $instituteFordet->delete();

    //                     Institute_for_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();

    //                     Institute_board_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();
    //                 }
    //             }

    //             $addedclas = Class_sub::where('institute_id', $lastInsertedId)
    //                 ->where('institute_for_id', $institute_for)
    //                 ->where('board_id', $board)
    //                 ->where('medium_id', $medium)
    //                 ->where('class_id', $institute_for_class)
    //                 ->first();
    //             if (empty($addedclas)) {
    //                 $createclass = Class_sub::create([
    //                     'user_id' => $request->input('user_id'),
    //                     'institute_id' => $lastInsertedId,
    //                     'institute_for_id' => $institute_for,
    //                     'board_id' => $board,
    //                     'medium_id' => $medium,
    //                     'class_id' => $institute_for_class,
    //                 ]);

    //                 if (!$createclass) {
    //                     $instituteFordet = Institute_detail::where('id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->first();
    //                     $instituteFordet->delete();

    //                     Institute_for_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();

    //                     Institute_board_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();

    //                     Medium_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();
    //                 }
    //             }

    //             $stndsubd = Standard_sub::where('institute_id', $lastInsertedId)
    //                 ->where('institute_for_id', $institute_for)
    //                 ->where('board_id', $board)
    //                 ->where('medium_id', $medium)
    //                 ->where('class_id', $institute_for_class)
    //                 ->where('standard_id', $standard)
    //                 ->first();
    //             if (empty($stndsubd)) {
    //                 $createstnd = Standard_sub::create([
    //                     'user_id' => $request->input('user_id'),
    //                     'institute_id' => $lastInsertedId,
    //                     'institute_for_id' => $institute_for,
    //                     'board_id' => $board,
    //                     'medium_id' => $medium,
    //                     'class_id' => $institute_for_class,
    //                     'standard_id' => $standard,
    //                 ]);

    //                 if (!$createstnd) {
    //                     $instituteFordet = Institute_detail::where('id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->first();
    //                     $instituteFordet->delete();

    //                     Institute_for_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();

    //                     Institute_board_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();

    //                     Medium_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();

    //                     Class_sub::where('institute_id', $lastInsertedId)
    //                         ->where('user_id', $request->input('user_id'))->delete();
    //                 }
    //             }


    //             if ($stream != null) {

    //                 $addedsrm = Stream_sub::where('institute_id', $lastInsertedId)
    //                     ->where('institute_for_id', $institute_for)
    //                     ->where('board_id', $board)
    //                     ->where('medium_id', $medium)
    //                     ->where('class_id', $institute_for_class)
    //                     ->where('standard_id', $standard)
    //                     ->where('stream_id', $stream)
    //                     ->first();

    //                 if (empty($addedsrm)) {
    //                     $createstrem = Stream_sub::create([
    //                         'user_id' => $request->input('user_id'),
    //                         'institute_id' => $lastInsertedId,
    //                         'institute_for_id' => $institute_for,
    //                         'board_id' => $board,
    //                         'medium_id' => $medium,
    //                         'class_id' => $institute_for_class,
    //                         'standard_id' => $standard,
    //                         'stream_id' => $stream,
    //                     ]);

    //                     if (!$createstrem) {
    //                         $instituteFordet = Institute_detail::where('id', $lastInsertedId)
    //                             ->where('user_id', $request->input('user_id'))->first();
    //                         $instituteFordet->delete();

    //                         Institute_for_sub::where('institute_id', $lastInsertedId)
    //                             ->where('user_id', $request->input('user_id'))->delete();

    //                         Institute_board_sub::where('institute_id', $lastInsertedId)
    //                             ->where('user_id', $request->input('user_id'))->delete();

    //                         Medium_sub::where('institute_id', $lastInsertedId)
    //                             ->where('user_id', $request->input('user_id'))->delete();

    //                         Class_sub::where('institute_id', $lastInsertedId)
    //                             ->where('user_id', $request->input('user_id'))->delete();

    //                         Standard_sub::where('institute_id', $lastInsertedId)
    //                             ->where('user_id', $request->input('user_id'))->delete();
    //                     }
    //                 }
    //             }
    //         }

    //         //end new code

    //         //dobusiness
    //         try {
    //             $institute_work_id = explode(',', $request->input('institute_work_id'));
    //             foreach ($institute_work_id as $value) {
    //                 if ($value == 'other') {
    //                     $instituteforadd = Dobusinesswith_Model::create([
    //                         'name' => $request->input('do_businesswith_name'),
    //                         'category_id' => $request->input('category_id'), //video category table id
    //                         'created_by' => $request->input('user_id'),
    //                         'status' => 'active',
    //                     ]);
    //                     $dobusinesswith_id = $instituteforadd->id;
    //                 } else {
    //                     $dobusinesswith_id = $value;
    //                 }

    //                 $addeddobusn = Dobusinesswith_sub::where('institute_id', $lastInsertedId)
    //                     ->where('do_business_with_id', $dobusinesswith_id)
    //                     ->first();

    //                 if (empty($addeddobusn)) {
    //                     Dobusinesswith_sub::create([
    //                         'user_id' => $request->input('user_id'),
    //                         'institute_id' => $lastInsertedId,
    //                         'do_business_with_id' => $dobusinesswith_id,
    //                     ]);
    //                 }
    //             }
    //         } catch (\Exception $e) {

    //             Subject_sub::where('institute_id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->delete();

    //             Standard_sub::where('institute_id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->delete();

    //             Stream_sub::where('institute_id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->delete();

    //             Standard_sub::where('institute_id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->delete();

    //             Class_sub::where('institute_id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->delete();

    //             Medium_sub::where('institute_id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->delete();

    //             Institute_board_sub::where('institute_id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->delete();

    //             Institute_for_sub::where('institute_id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->delete();

    //             Dobusinesswith_sub::where('institute_id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->delete();

    //             $indel = Institute_detail::where('id', $lastInsertedId)
    //                 ->where('user_id', $request->input('user_id'))->forceDelete();


    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }

    //         //institute_for_sub
    //         $intitute_for_id = explode(',', $request->input('institute_for_id'));
    //         foreach ($intitute_for_id as $value) {
    //             if ($value == 5) {
    //                 $instituteforadd = institute_for_model::create([
    //                     'name' => $request->input('institute_for'),
    //                     'status' => 'active',
    //                 ]);
    //                 $institute_for_id = $instituteforadd->id;
    //                 Institute_for_sub::create([
    //                     'user_id' => $request->input('user_id'),
    //                     'institute_id' => $lastInsertedId,
    //                     'institute_for_id' => $institute_for_id,
    //                 ]);
    //             } else {
    //                 $institute_for_id = $value;
    //             }
    //             // Institute_for_sub::create([
    //             //     'user_id' => $request->input('user_id'),
    //             //     'institute_id' => $lastInsertedId,
    //             //     'institute_for_id' => $institute_for_id,
    //             // ]);

    //         }

    //         //board_sub
    //         $institute_board_id = explode(',', $request->input('institute_board_id'));
    //         foreach ($institute_board_id as $value) {
    //             //other
    //             if ($value == 4) {
    //                 $instituteboardadd = board::create([
    //                     'name' => $request->input('institute_board'),
    //                     'status' => 'active',
    //                 ]);
    //                 $instituteboard_id = $instituteboardadd->id;
    //                 Institute_board_sub::create([
    //                     'user_id' => $request->input('user_id'),
    //                     'institute_id' => $lastInsertedId,
    //                     'board_id' => $instituteboard_id,
    //                 ]);
    //             } else {
    //                 $instituteboard_id = $value;
    //             }
    //             //end other

    //             // Institute_board_sub::create([
    //             //     'user_id' => $request->input('user_id'),
    //             //     'institute_id' => $lastInsertedId,
    //             //     'board_id' => $instituteboard_id,
    //             // ]);
    //         }

    //         // class
    //         // $institute_for_class_id = explode(',', $request->input('institute_for_class_id'));
    //         // foreach ($institute_for_class_id as $value) {

    //         //     Class_sub::create([
    //         //         'user_id' => $request->input('user_id'),
    //         //         'institute_id' => $lastInsertedId,
    //         //         'class_id' => $value,
    //         //     ]);
    //         // }

    //         //medium
    //         // $institute_medium_id = explode(',', $request->input('institute_medium_id'));
    //         // foreach ($institute_medium_id as $value) {
    //         //     Medium_sub::create([
    //         //         'user_id' => $request->input('user_id'),
    //         //         'institute_id' => $lastInsertedId,
    //         //         'medium_id' => $value,
    //         //     ]);
    //         // }

    //         //standard

    //         //$standard_id = explode(',', $request->input('standard_id'));
    //         // foreach ($standard_id as $value) {
    //         //     Standard_sub::create([
    //         //         'user_id' => $request->input('user_id'),
    //         //         'institute_id' => $lastInsertedId,
    //         //         'standard_id' => $value,
    //         //     ]);
    //         // }

    //         //stream

    //         // if ($request->input('stream_id')) {
    //         //     $stream = explode(',', $request->input('stream_id'));
    //         //     foreach ($stream as $value) {
    //         //         Stream_sub::create([
    //         //             'user_id' => $request->input('user_id'),
    //         //             'institute_id' => $lastInsertedId,
    //         //             'stream_id' => $value,
    //         //         ]);
    //         //     }
    //         // }

    //         //subject

    //         $subject_id = explode(',', $request->input('subject_id'));

    //         foreach ($subject_id as $value) {
    //             try {
    //                 $suadeed = Subject_sub::where('institute_id', $lastInsertedId)
    //                     ->where('subject_id', $value)->get();
    //                 if (empty($suadeed)) {
    //                     Subject_sub::create([
    //                         'user_id' => $request->input('user_id'),
    //                         'institute_id' => $lastInsertedId,
    //                         'subject_id' => $value,
    //                     ]);
    //                 }
    //             } catch (\Exception $e) {

    //                 Subject_sub::where('institute_id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->delete();

    //                 Standard_sub::where('institute_id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->delete();

    //                 Stream_sub::where('institute_id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->delete();

    //                 Standard_sub::where('institute_id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->delete();

    //                 Class_sub::where('institute_id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->delete();

    //                 Medium_sub::where('institute_id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->delete();

    //                 Institute_board_sub::where('institute_id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->delete();

    //                 Institute_for_sub::where('institute_id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->delete();

    //                 Dobusinesswith_sub::where('institute_id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->delete();

    //                 $indel = Institute_detail::where('id', $lastInsertedId)
    //                     ->where('user_id', $request->input('user_id'))->forceDelete();


    //                 return response()->json([
    //                     'success' => 500,
    //                     'message' => 'Server Error',
    //                     'error' => $e->getMessage(),
    //                 ], 500);
    //             }
    //         }

    //         return response()->json([
    //             'success' => 200,
    //             'message' => 'institute create Successfully',
    //             'data' => [
    //                 'institute_id' => $lastInsertedId,
    //                 'institute_name' => $institute_name,
    //                 'logo' => asset($imagePath)
    //             ]
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'Error creating institute',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // function get_board(Request $request)
    // {
    //     $institute_id = $request->input('institute_id');
    //     $user_id = $request->input('user_id');
    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $boards = Board::whereHas('boardSub', function ($query) use ($institute_id) {
    //         $query->where('institute_id', $institute_id);
    //     })
    //         ->with(['classes.standards.subjects'])
    //         ->paginate(10);

    //     $board_array = [];

    //     foreach ($boards as $board) {
    //         $class_array = [];

    //         foreach ($board->classes as $class) {
    //             $standard_array = [];

    //             foreach ($class->standards as $standard) {
    //                 $subject_array = $standard->subjects->map(function ($subject) {
    //                     return [
    //                         'subject_id' => $subject->id,
    //                         'standard_id' => $subject->standard_id,
    //                         'subject_name' => $subject->name,
    //                     ];
    //                 })->all();

    //                 $standard_array[] = [
    //                     'standard_id' => $standard->id,
    //                     'class_id' => $standard->class_id,
    //                     'standard_name' => $standard->name,
    //                     'subject_array' => $subject_array,
    //                 ];
    //             }

    //             $class_array[] = [
    //                 'class_id' => $class->id,
    //                 'board_id' => $class->board_id,
    //                 'class_name' => $class->name,
    //                 'standard_array' => $standard_array,
    //             ];
    //         }

    //         $board_array[] = [
    //             'board_id' => $board->id,
    //             'board_name' => $board->name,
    //             'class_array' => $class_array,
    //         ];
    //     }
    //     $existingUser = User::where('token', $token)->first();
    //     if ($existingUser) {

    //         $bannerlist = Banner_model::where('user_id', $user_id)->get();
    //         if ($bannerlist) {
    //             foreach ($bannerlist as $value) {
    //                 $banner_array[] = array(
    //                     'banner_url' => asset($value->banner_image),

    //                 );
    //             }
    //         }

    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Successfully fetch data.',
    //             'institute_for' => '',
    //             'banner_array' => $banner_array,
    //             'board_array' => $board_array


    //         ], 200, [], JSON_NUMERIC_CHECK);
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ], 400);
    //     }
    // }

    // function get_class(Request $request)
    // {
    //     $institute_id = $request->input('institute_id');
    //     $user_id = $request->input('user_id');
    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }


    //     $existingUser = User::where('token', $token)->where('id', $request->input('user_id'))->first();

    //     if ($existingUser) {

    //         $classlist = DB::table('class')
    //             ->join('class_sub', 'class_sub.class_id', '=', 'class.id')
    //             ->where('class_sub.institute_id', $institute_id)
    //             ->where('class_sub.user_id', $user_id)
    //             ->select('class.*')
    //             ->paginate(10);

    //         $class_array = [];

    //         foreach ($classlist as $classItem) {
    //             $standardlist = DB::table('standard')
    //                 ->join('standard_sub', 'standard.id', '=', 'standard_sub.standard_id')
    //                 ->where('standard.class_id', $classItem->id)
    //                 ->where('standard_sub.institute_id', $institute_id)
    //                 ->where('standard_sub.user_id', $user_id)
    //                 ->select('standard.*')
    //                 ->get();


    //             $standard_array = [];

    //             foreach ($standardlist as $standardItem) {
    //                 $streamlist = DB::table('stream')
    //                     ->join('stream_sub', 'stream_sub.stream_id', '=', 'stream.id')
    //                     ->where('stream.standard_id', $standardItem->id)
    //                     ->where('stream_sub.institute_id', $institute_id)
    //                     ->where('stream_sub.user_id', $user_id)
    //                     ->select('stream.*')
    //                     ->get();

    //                 $subjectlist = DB::table('subject')
    //                     ->join('subject_sub', 'subject_sub.subject_id', '=', 'subject.id', 'left')
    //                     ->where('subject.standard_id', $standardItem->id)
    //                     ->select('subject.*')
    //                     ->paginate(10);
    //                 $subject_array = [];
    //                 $stream_array = [];
    //                 foreach ($streamlist as $streamItem) {
    //                     $stream_array[] = [
    //                         'stream_id' => $streamItem->id,
    //                         'stream_name' => $streamItem->name,
    //                         'subject' => $subject_array,
    //                     ];
    //                     foreach ($subjectlist as $subjectItem) {

    //                         $subject_array[] = [
    //                             'subject_id' => $subjectItem->id,
    //                             'subject_name' => $subjectItem->name,

    //                         ];
    //                     }
    //                 }
    //                 $standard_array[] = [
    //                     'standard_id' => $standardItem->id,
    //                     'standard_name' => $standardItem->name,
    //                     'stream' => $stream_array,
    //                 ];
    //             }
    //             $class_array[] = [
    //                 'class_id' => $classItem->id,
    //                 'class_name' => $classItem->name,
    //                 'standard' => $standard_array,
    //             ];
    //         }
    //         return response()->json([
    //             'success' => 200,
    //             'message' => 'Fetch Class successfully',
    //             'data' =>  $class_array,

    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'success' => 500,
    //             'message' => 'No found data.',
    //         ], 500);
    //     }
    // }
    // function get_homescreen_first(Request $request)
    // {

    //     $institute_id = $request->input('institute_id');
    //     $user_id = $request->input('user_id');

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $perPage = $request->input('per_page', 10);

    //     $existingUser = User::where('token', $token)->where('id', $request->input('user_id'))->first();

    //     if ($existingUser) {
    //         if (empty($institute_id)) {
    //             $institute_id = Institute_detail::where('user_id', $user_id)->select('id')->first();
    //         }
    //         // Institute_detail::where();
    //         $boarids = Institute_board_sub::where('user_id', $user_id)
    //             ->where('institute_id', $institute_id)->pluck('board_id')->toArray();
    //         $uniqueBoardIds = array_unique($boarids);

    //         $board_list = DB::table('board')
    //             ->whereIN('id', $uniqueBoardIds)
    //             ->get();

    //         $board_array = [];
    //         foreach ($board_list as $board_value) {

    //             $medium_sublist = DB::table('medium_sub')
    //                 ->where('user_id', $user_id)
    //                 ->where('board_id', $board_value->id)
    //                 ->where('institute_id', $institute_id)
    //                 ->pluck('medium_id')->toArray();
    //             $uniquemediumds = array_unique($medium_sublist);

    //             $medium_list = Medium_model::whereIN('id', $uniquemediumds)->get();

    //             $medium_array = [];
    //             foreach ($medium_list as $medium_value) {
    //                 $medium_array[] = [
    //                     'id' => $medium_value->id,
    //                     'medium_name' => $medium_value->name,
    //                     'medium_icon' => asset($medium_value->icon)
    //                 ];
    //             }
    //             $board_array[] = [
    //                 'id' => $board_value->id,
    //                 'board_name' => $board_value->name,
    //                 'board_icon' => asset($board_value->icon),
    //                 'medium' => $medium_array,

    //                 // Include banner_array inside board_array
    //             ];
    //         }
    //         $banner_list = Banner_model::where('user_id', $user_id)
    //             ->where('institute_id', $institute_id)
    //             ->get();
    //         if ($banner_list->isEmpty()) {
    //             $banner_list = Banner_model::where('status', 'active')
    //                 ->where('user_id', '1')
    //                 ->get();
    //         }
    //         $banner_array = [];

    //         foreach ($banner_list as $value) {
    //             $banner_array[] = [
    //                 'id' => $value->id,
    //                 'banner_image' => asset($value->banner_image),
    //                 'url' => $value->url . '',
    //             ];
    //         }

    //         //announcement
    //         $announcement = [];
    //         $fifteenDaysAgo = Carbon::now()->subDays(15);

    //         // $announcement_list = announcements_model::where('institute_id', $institute_id)->where('created_at', '>=', $fifteenDaysAgo)->orderBy('created_at', 'desc')->get()->toarray();
    //         // foreach ($announcement_list as $value) {
    //         //     $announcement = [
    //         //         'title' => $value['title'],
    //         //         'message' => $value['detail']
    //         //     ];
    //         // }

    //         $announcement_list = Common_announcement::whereRaw("FIND_IN_SET($institute_id, institute_id)")
    //             ->where('created_at', '>=', $fifteenDaysAgo)
    //             ->orderBy('created_at', 'desc')->paginate($perPage);
    //         foreach ($announcement_list as $value) {
    //             $announcement[] = array(
    //                 'title' => $value['title'],
    //                 'announcement' => $value['announcement'],
    //                 'created_at' => $value['created_at']
    //             );
    //         }

    //         $response = [
    //             'banner' => $banner_array,
    //             'board' => $board_array,
    //             'announcement' => $announcement
    //         ];
    //         return response()->json([
    //             'success' => 200,
    //             'message' => 'Fetch Board successfully',
    //             // 'banner' => $banner_array,
    //             'data' => $response,
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ], 400);
    //     }
    // }

    public function get_homescreen_first(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $user_id = Auth::id();
            $perPage = $request->input('per_page', 10);
            // Fetch institute_id if empty
            $institute_id = $request->institute_id ?: Institute_detail::where('user_id', $user_id)->value('id');

            // Fetch unique board ids
            $uniqueBoardIds = Institute_board_sub::where('user_id', $user_id)
                ->where('institute_id', $institute_id)
                ->distinct()
                ->pluck('board_id')
                ->toArray();

            // Fetch board details
            $board_list = Board::whereIn('id', $uniqueBoardIds)->get(['id', 'name', 'icon']);

            $board_array = [];
            foreach ($board_list as $board) {
                $medium_list = Medium_model::whereIn('id', function ($query) use ($user_id, $institute_id, $board) {
                    $query->select('medium_id')
                        ->from('medium_sub')
                        ->where('user_id', $user_id)
                        ->where('board_id', $board->id)
                        ->where('institute_id', $institute_id);
                })->get(['id', 'name', 'icon']);
                $medium_array = $medium_list->map(function ($medium) {
                    return [
                        'id' => $medium->id,
                        'medium_name' => $medium->name,
                        'medium_icon' => asset($medium->icon)
                    ];
                })
                    ->toArray();
                $board_array[] = [
                    'id' => $board->id,
                    'board_name' => $board->name,
                    'board_icon' => asset($board->icon),
                    'medium' => $medium_array,
                    // Include banner_array inside board_array
                ];
            }

            // Fetch banners
            $banner_list = Banner_model::where(function ($query) use ($user_id, $institute_id) {
                $query->where('user_id', $user_id)
                    ->where('institute_id', $institute_id);
            })
                ->orWhere('status', 'active')
                ->where('user_id', 1)
                ->get(['id', 'banner_image', 'url']);

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
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    // public function get_homescreen_second(Request $request)
    // {
    //     $institute_id = $request->input('institute_id');
    //     $user_id = $request->input('user_id');
    //     $board_id = $request->input('board_id');
    //     $medium_id = $request->input('medium_id');

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }


    //     $existingUser = User::where('token', $token)->where('id', $request->input('user_id'))->first();
    //     // echo "<pre>";print_r($existingUser);exit;
    //     if ($existingUser) {
    //         if (empty($institute_id)) {
    //             $institute_id = Institute_detail::where('user_id', $user_id)->first();
    //         }
    //         // Institute_detail::where();
    //         $standard_list = DB::table('standard_sub')
    //             ->join('standard', 'standard_sub.standard_id', '=', 'standard.id')
    //             ->select('standard.*')
    //             ->where('standard_sub.user_id', $user_id)
    //             ->where('standard_sub.institute_id', $institute_id)
    //             ->where('standard_sub.board_id', $board_id)
    //             ->where('standard_sub.medium_id', $medium_id)
    //             ->get();
    //         // print_r($standard_list);exit;

    //         $standard_array = [];
    //         foreach ($standard_list as $standard_value) {

    //             $getbsiqy = Base_table::where('board', $board_id)
    //                 ->where('medium', $medium_id)
    //                 ->where('standard', $standard_value->id)
    //                 ->pluck('id')
    //                 ->toArray();

    //             $subject_list = DB::table('subject_sub')
    //                 ->join('subject', 'subject_sub.subject_id', '=', 'subject.id')
    //                 ->select('subject.*')
    //                 ->where('subject_sub.user_id', $user_id)
    //                 ->where('subject_sub.institute_id', $institute_id)
    //                 ->whereIN('subject.base_table_id', $getbsiqy)
    //                 ->get();

    //             $subject_array = [];
    //             foreach ($subject_list as $subject_value) {
    //                 $subject_array[] = [
    //                     'id' => $subject_value->id,
    //                     'subject_value' => $subject_value->name,
    //                     'image' => asset($subject_value->image),
    //                 ];
    //             }

    //             //batch list
    //             $batchqY = Batches_model::join('board', 'board.id', '=', 'batches.board_id')
    //                 ->join('medium', 'medium.id', '=', 'batches.medium_id')
    //                 ->leftjoin('stream', 'stream.id', '=', 'batches.stream_id')
    //                 ->where('batches.institute_id', $institute_id)
    //                 ->where('batches.standard_id', $standard_value->id)
    //                 ->where('batches.user_id', $user_id)
    //                 ->select('batches.*', 'board.name as board', 'medium.name as medium', 'stream.name as stream')->get();
    //             $batchesDT = [];
    //             foreach ($batchqY as $batDT) {
    //                 $subids = explode(",", $batDT->subjects);
    //                 $batSubQY = Subject_model::whereIN('id', $subids)->get();
    //                 $subects = [];
    //                 foreach ($batSubQY as $batDt) {
    //                     $subects[] = array('id' => $batDt->id, 'subject_name' => $batDt->name);
    //                 }

    //                 $batchesDT[] = array(
    //                     'id' => $batDT->id,
    //                     'batch_name' => $batDT->batch_name,
    //                     'board' => $batDT->board,
    //                     'medium' => $batDT->medium,
    //                     'stream' => $batDT->stream,
    //                     'subjects' => $subects
    //                 );
    //             }

    //             $standard_array[] = [
    //                 'id' => $standard_value->id,
    //                 'standard_name' => $standard_value->name,
    //                 'subject' => $subject_array,
    //                 'batches' => $batchesDT
    //                 // Include banner_array inside board_array
    //             ];
    //         }
    //         return response()->json([
    //             'success' => 200,
    //             'message' => 'Fetch Standard successfully',
    //             'data' => $standard_array,
    //         ], 200);
    //     } else {

    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ], 400);
    //     }
    // }


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
            $user_id = Auth::id();
            if (empty($institute_id)) {
                $institute_id = Institute_detail::where('user_id', $user_id)->first();
            }
            // Institute_detail::where();
            $standard_list = DB::table('standard_sub')
                ->join('standard', 'standard_sub.standard_id', '=', 'standard.id')
                ->select('standard.*')
                ->where('standard_sub.user_id', $user_id)
                ->where('standard_sub.institute_id', $institute_id)
                ->where('standard_sub.board_id',  $request->board_id)
                ->where('standard_sub.medium_id', $request->medium_id)
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
                    ->where('subject_sub.user_id', $user_id)
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
                    ->where('batches.user_id', $user_id)
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
            return $this->response($e, "Invalid token.", false, 400);
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
            return $this->response([], "Invalid token.", false, 400);
        }
    }

    // public function get_request_list(Request $request)
    // {
    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();

    //     if ($existingUser) {
    //         $institute_id = $request->institute_id;
    //         $request_list = Student_detail::where('institute_id', $institute_id)
    //             ->where('status', '0')
    //             ->get()
    //             ->toArray();
    //         $response = []; // Initialize the response array outside the loop
    //         foreach ($request_list as $value) {
    //             $user_data = User::where('id', $value['student_id'])->first();
    //             if ($user_data) {
    //                 if (!empty($user_data->image)) {
    //                     $image = asset($user_data->image);
    //                 } else {
    //                     $image = asset('default.jpg');
    //                 }
    //                 $response[] = [
    //                     'student_id' => $user_data->id,
    //                     'name' => $user_data->firstname . ' ' . $user_data->lastname,
    //                     'photo' => $image,
    //                 ];
    //             }
    //         }

    //         if (!empty($response)) {
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Fetch student request list.',
    //                 'data' => $response,
    //             ], 200, [], JSON_NUMERIC_CHECK);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'No data found.',
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }

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
            return $this->response([], "Invalid token.", false, 400);
        }
    }

    // public function get_reject_request_list(Request $request)
    // {
    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     // echo "<pre>";print_r($existingUser);exit;
    //     if ($existingUser) {

    //         $institute_id = $request->institute_id;
    //         $student_id = Student_detail::where('institute_id', $institute_id)
    //             ->where('status', '2')
    //             ->where('created_at', '>=', Carbon::now()->subDays(15))
    //             ->pluck('student_id');

    //         if (!empty($student_id)) {

    //             $user_data = User::whereIN('id', $student_id)->get();

    //             $response = [];
    //             foreach ($user_data as $value2) {
    //                 if (!empty($value2['image'])) {
    //                     $image = asset($value2['image']);
    //                 } else {
    //                     $image = asset('default.jpg');
    //                 }
    //                 $response[] = [
    //                     'student_id' => $value2['id'],
    //                     'name' => $value2['firstname'] . ' ' . $value2['lastname'],
    //                     'photo' => $image,
    //                 ];
    //             }

    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Fetch student Reject list.',
    //                 'data' => $response,
    //             ], 200, [], JSON_NUMERIC_CHECK);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'No data Found.',
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


    public function get_reject_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'student_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $response = Student_detail::where('institute_id', $request->institute_id)->where('student_id', $request->student_id)->update(['status' => '2']);
            $serverKey = env('SERVER_KEY');

            $url = "https://fcm.googleapis.com/fcm/send";
            $users = User::where('id', $request->student_id)->pluck('device_key');

            $notificationTitle = "Your Request Rejected successfully!!";
            $notificationBody = "Your Teacher Request Rejected successfully!!";

            $data = [
                'registration_ids' => $users,
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
                }

                curl_close($ch);
            }
            return $this->response([], "Successfully Reject Request.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }

    // public function get_reject_request(Request $request)
    // {
    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         $response = Student_detail::where('institute_id', $request->institute_id)->where('student_id', $request->student_id)->first();
    //         $reject_list = Student_detail::find($response->id);
    //         $data = $reject_list->update(['status' => '2']);
    //         if (!empty($data)) {
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Successfully Reject Request.',
    //             ], 200, [], JSON_NUMERIC_CHECK);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


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
                ->where('students_details.user_id', Auth::id())
                ->where('students_details.institute_id', $request->institute_id)
                ->select(
                    'students_details.*',
                    'users.firstname',
                    'users.lastname',
                    'users.dob',
                    'users.address',
                    'users.email',
                    'users.country_code',
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
                    'date_of_birth' => date('d-m-Y', strtotime($user_list->dob)),
                    'address' => $user_list->address,
                    'email' => $user_list->email,
                    'country_code' => $user_list->country_code,
                    'mobile_no' => $user_list->mobile,
                    //'institute_for' => $institute_for_list,
                    'board' => $user_list->board,
                    'board_id' => $user_list->board_id,
                    'medium' => $user_list->medium,
                    'medium_id' => $user_list->medium_id,
                    //'class_list' => $class_list,
                    'standard' => $user_list->standard,
                    'standard_id' => $user_list->standard_id,
                    'stream' => $user_list->stream,
                    'stream_id' => $user_list->stream_id,
                    'subject_list' => $subjectslist,
                ];
                return $this->response($response_data, "Successfully Fetch data.");
            } else {
                return $this->response([], "Successfully Fetch data.");
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }

    // public function fetch_student_detail(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required|integer',
    //         'student_id' => 'required|integer',
    //         'institute_id' => 'required|integer',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'data' => array('errors' => $errorMessages),
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }
    //     $institute_id = $request->institute_id;
    //     $user_id = $request->user_id;
    //     $student_id = $request->student_id;

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         $user_list = Student_detail::join('users', 'users.id', '=', 'students_details.student_id')
    //             ->join('board', 'board.id', '=', 'students_details.board_id')
    //             ->join('medium', 'medium.id', '=', 'students_details.medium_id')
    //             ->join('standard', 'standard.id', '=', 'students_details.standard_id')
    //             ->leftjoin('stream', 'stream.id', '=', 'students_details.stream_id')
    //             ->where('students_details.student_id', $student_id)
    //             ->where('students_details.user_id', $user_id)
    //             ->where('students_details.institute_id', $institute_id)
    //             ->select(
    //                 'students_details.*',
    //                 'users.firstname',
    //                 'users.lastname',
    //                 'users.dob',
    //                 'users.address',
    //                 'users.email',
    //                 'users.mobile',
    //                 'board.name as board',
    //                 'medium.name as medium',
    //                 'standard.name as standard',
    //                 'stream.name as stream'
    //             )
    //             ->first();
    //         if ($user_list) {
    //             $subjids = explode(',', $user_list->subject_id);
    //             $subjcts = Subject_model::whereIN('id', $subjids)->get();
    //             $subjectslist = [];
    //             foreach ($subjcts as $subDT) {
    //                 $subjectslist[] = array(
    //                     'id' => $subDT->id,
    //                     'name' => $subDT->name,
    //                     'image' => asset($subDT->image)
    //                 );
    //             }

    //             $response_data = [
    //                 'student_id' => $user_list->student_id,
    //                 'institute_id' => $user_list->institute_id,
    //                 'first_name' => $user_list->firstname,
    //                 'last_name' => $user_list->lastname,
    //                 'date_of_birth' => date('d-m-Y', strtotime($user_list->dob)),
    //                 'address' => $user_list->address,
    //                 'email' => $user_list->email,
    //                 'mobile_no' => $user_list->mobile,
    //                 //'institute_for' => $institute_for_list,
    //                 'board' => $user_list->board,
    //                 'board_id' => $user_list->board_id,
    //                 'medium' => $user_list->medium,
    //                 'medium_id' => $user_list->medium_id,
    //                 //'class_list' => $class_list,
    //                 'standard' => $user_list->standard,
    //                 'standard_id' => $user_list->standard_id,
    //                 'stream' => $user_list->stream,
    //                 'stream_id' => $user_list->stream_id,
    //                 'subject_list' => $subjectslist,

    //             ];
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Successfully Fetch data.',
    //                 'data' => $response_data
    //             ], 200, [], JSON_NUMERIC_CHECK);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'No Data Found.',
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


    // public function add_student123(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'institute_id' => 'required',
    //         'user_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->response([], $validator->errors()->first(), false, 400);
    //     }

    //     try {
    //         DB::beginTransaction();
    //         $institute_id = $request->institute_id;
    //         $user_id = $request->user_id;
    //         $existingUser = User::where('id', $request->user_id)->first();
    //         if ($existingUser->role_type == 6) {
    //             $student_id = $request->user_id;
    //             $institute_id = $request->institute_id;
    //             $getuidfins = Institute_detail::where('id', $institute_id)->first();
    //             $user_id = $getuidfins->user_id;
    //         } else {
    //             $student_id = $request->student_id;
    //             $institute_id = $request->institute_id;
    //             $user_id = $request->user_id;
    //         }

    //         $batch_id = $request->batch_id;
    //         $studentdtls = Student_detail::where('student_id', $student_id)
    //             ->where('institute_id', $institute_id)->first();

    //         $insdelQY = Standard_sub::where('board_id', $request->board_id)
    //             ->where('medium_id', $request->medium_id)
    //             ->where('standard_id', $request->standard_id)
    //             ->where('institute_id', $institute_id)
    //             ->first();

    //         if (empty($insdelQY)) {
    //             return $this->response([], 'institute are not working for this standard Please Select Currect Data.', false, 400);
    //         }
    //         if (!empty($studentdtls)) {

    //             $studentupdetail = [
    //                 'user_id' => $user_id,
    //                 'institute_id' => $request->institute_id,
    //                 'student_id' => $student_id,
    //                 'institute_for_id' => $insdelQY->institute_for_id,
    //                 'board_id' =>  $request->board_id,
    //                 'medium_id' => $request->medium_id,
    //                 'class_id' => $insdelQY->class_id,
    //                 'standard_id' => $request->standard_id,
    //                 'stream_id' => $request->stream_id,
    //                 'subject_id' => $request->subject_id,
    //                 'batch_id' => $batch_id,
    //                 'status' => '1',
    //             ];

    //             if ($request->stream_id == 'null' || $request->stream_id == '') {
    //                 $studentupdetail['stream_id'] = null;
    //             }

    //             $studentdetail = Student_detail::where('student_id', $student_id)
    //                 ->where('institute_id', $institute_id)
    //                 ->update($studentupdetail);

    //             if (!empty($studentdetail) && !empty($request->first_name)) {
    //                 //student detail update
    //                 $student_details = User::find($student_id);
    //                 $data = $student_details->update([
    //                     'firstname' => $request->first_name,
    //                     'lastname' => $request->last_name,
    //                     'dob' => $request->date_of_birth,
    //                     'address' => $request->address,
    //                     'email' => $request->email_id,
    //                     'mobile' => $request->mobile_no,
    //                 ]);

    //                 $response = Student_detail::where('institute_id', $institute_id)
    //                     ->where('student_id', $student_id)->first();

    //                 $reject_list = Student_detail::find($response->id);
    //                 $data = $reject_list->update(['status' => '1']);


    //                 $prnts = Parents::join('users', 'users.id', 'parents.parent_id')
    //                     ->where('parents.student_id', $student_id)
    //                     ->select('users.firstname', 'users.lastname', 'users.email', 'parents.id')
    //                     ->get();
    //                 foreach ($prnts as $prdetail) {
    //                     $parDT = [
    //                         'name' => $prdetail['firstname'] . ' ' . $prdetail['lastname'],
    //                         'email' => $prdetail,
    //                         'id' => $prdetail->id
    //                     ];
    //                     Mail::to($prdetail->email)->send(new WelcomeMail($parDT));
    //                 }
    //                 return $this->response([], 'Successfully Update Student.');
    //             } else {
    //                 return $this->response([], 'Not Inserted.', false, 400);
    //             }
    //         } else {

    //             if ($existingUser->role_type != 6 && empty($request->student_id)) {
    //                 $data = user::create([
    //                     'firstname' => $request->first_name,
    //                     'lastname' => $request->last_name,
    //                     'dob' => $request->date_of_birth,
    //                     'address' => $request->address,
    //                     'email' => $request->email_id,
    //                     'mobile' => $request->mobile_no,
    //                 ]);
    //                 $student_id = $data->id;
    //             } else {
    //                 $student_id = $student_id;
    //             }

    //             if (!empty($student_id)) {
    //                 $studentdetail = [
    //                     'user_id' => $user_id,
    //                     'institute_id' => $request->institute_id,
    //                     'student_id' => $student_id,
    //                     'institute_for_id' => $insdelQY->institute_for_id,
    //                     'board_id' =>  $request->board_id,
    //                     'medium_id' => $request->medium_id,
    //                     'class_id' => $insdelQY->class_id,
    //                     'standard_id' => $request->standard_id,
    //                     'batch_id' => $batch_id,
    //                     'stream_id' => $request->stream_id,
    //                     'subject_id' => $request->subject_id,
    //                     'status' => '0',
    //                 ];


    //                 if ($request->stream_id == 'null' || $request->stream_id == '') {
    //                     $studentdetail['stream_id'] = null;
    //                 }

    //                 $studentdetailadd = Student_detail::create($studentdetail);

    //                 return $this->response([], 'Successfully Insert Student.');
    //             } else {
    //                 return $this->response([], 'Not Inserted.', false, 400);
    //             }
    //         }
    //         DB::commit();
    //         return $this->response([], "Banner created Successfully");
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         return $this->response($e, "Invalid token.", false, 400);
    //     }
    // }

    public function add_student(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'user_id' => 'required',
            'country_code' => 'required',
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
                            'dob' => $request->date_of_birth,
                            'address' => $request->address,
                            'email' => $request->email_id,
                            'country_code' => $request->country_code,
                            'mobile' => $request->mobile_no,
                        ]);

                        $response = Student_detail::where('institute_id', $institute_id)
                            ->where('student_id', $student_id)->first();

                        $reject_list = Student_detail::find($response->id);
                        $data = $reject_list->update(['status' => '1']);


                        $prnts = Parents::join('users', 'users.id', 'parents.parent_id')
                            ->join('institute_detail', 'institute_detail.id', 'parents.institute_id')
                            ->where('parents.student_id', $student_id)
                            ->select('users.firstname', 'users.lastname', 'users.email', 'parents.id', 'institute_detail.institute_name')
                            ->get();
                        foreach ($prnts as $prdetail) {
                            $parDT = [
                                'name' => $prdetail['firstname'] . ' ' . $prdetail['lastname'],
                                'email' => $prdetail,
                                'id' => $prdetail->id,
                                'institute' => $prdetail->institute_name,
                            ];
                            Mail::to($prdetail->email)->send(new WelcomeMail($parDT));
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
                            'dob' => $request->date_of_birth,
                            'address' => $request->address,
                            'email' => $request->email_id,
                            'country_code' => $request->country_code,
                            'mobile' => $request->mobile_no,
                        ]);
                        $student_id = $data->id;
                    } else {
                        $student_id = $student_id;
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
                        // print_r($request->subject_id);exit;
                        $subject_amount = Subject_sub::where('institute_id', $institute_id)
                            ->whereIn('subject_id', explode(',', $request->subject_id))
                            ->select('amount')
                            ->get();
                        $amount = 0;
                        foreach ($subject_amount as $value) {
                            $amount += $value->amount;
                        }
                        
                        Student_fees_model::create([
                            'user_id' => $user_id,
                            'institute_id' => $request->institute_id,
                            'student_id' => $student_id,
                            'subject_id' => $request->subject_id,
                            //'total_fees' => (!empty($amount)) ? $amount : '',
                            'total_fees' => (!empty($amount)) ? (float)$amount : null,
                        ]);
                        print_r($amount);exit;
                        $parets = Parents::where('student_id', $student_id)->where('verify', '0')->get();
                        if (!empty($parets)) {
                            foreach ($parets as $prdtl) {
                                $parnsad = Parents::where('id', $prdtl->id)->update([
                                    'institute_id' => $request->institute_id
                                ]);
                            }
                        } else {
                            $pare = Parents::where('student_id', $student_id)
                                ->where('institute_id', $request->institute_id)->get();
                            if (empty($pare)) {
                                foreach ($parets as $prdtl) {
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
                        //
                        return $this->response([], 'Successfully Insert Student.');
                    } else {
                        return $this->response([], 'Not Inserted.', false, 400);
                    }
                }
            }
            // DB::commit();
        } catch (\Exception $e) {
            // DB::rollback();
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
                // 'boards' => $boards,
                // 'mediums' => $mediums,
                // 'classs' => $classs,
                // 'streams' => $streams,
                // 'subjects' => $subjects,
                // 'standards' => $standards
            );

            return $this->response($alldata, 'Successfully fetch Data.');
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
    //student list for add exam marks
    // public function student_list_for_add_marks123(Request $request)
    // {

    //     $validator = \Validator::make($request->all(), [
    //         'institute_id' => 'required',
    //         'user_id' => 'required',
    //         'exam_id' => 'required',
    //         'batch_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         $institute_id = $request->institute_id;
    //         $user_id = $request->user_id;
    //         $exam_id = $request->exam_id;
    //         $batch_id = $request->batch_id;
    //         $examdt = Exam_Model::where('id', $exam_id)->first();

    //         if (!empty($examdt)) {

    //             $studentDT = Student_detail::join('users', 'users.id', '=', 'students_details.student_id')
    //                 ->join('standard', 'standard.id', '=', 'students_details.standard_id')
    //                 ->where('students_details.institute_id', $institute_id)
    //                 ->where('students_details.user_id', $user_id)
    //                 ->where('students_details.board_id', $examdt->board_id)
    //                 ->where('students_details.medium_id', $examdt->medium_id)
    //                 ->where('students_details.batch_id', $examdt->batch_id)
    //                 //->where('students_details.class_id', $examdt->class_id)
    //                 ->where('students_details.standard_id', $examdt->standard_id)
    //                 //->where('students_details.stream_id', $examdt->stream_id)
    //                 ->when($examdt->stream_id, function ($query, $stream_id) {
    //                     return $query->where('students_details.stream_id', $stream_id);
    //                 })
    //                 ->whereRaw("FIND_IN_SET($examdt->subject_id, students_details.subject_id)")
    //                 ->select('students_details.*', 'users.firstname', 'users.lastname', 'standard.name as standardname')->get();

    //             $studentsDET = [];
    //             foreach ($studentDT as $stddt) {
    //                 $subjectqy = Subject_model::where('id', $examdt->subject_id)->first();
    //                 $marksofstd = Marks_model::where('student_id', $stddt->student_id)->where('exam_id', $request->exam_id)->first();
    //                 $studentsDET[] = array(
    //                     'student_id' => $stddt->student_id,
    //                     'exam_id' => $request->exam_id,
    //                     'batch_id' => $request->batch_id,
    //                     'marks' => !empty($marksofstd->mark) ? (float)$marksofstd->mark : 0,
    //                     'firstname' => $stddt->firstname,
    //                     'lastname' => $stddt->lastname,
    //                     'total_mark' => $examdt->total_mark,
    //                     'standard' => $stddt->standardname,
    //                     'subject' => $subjectqy->name
    //                 );
    //             }
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Successfully fetch Data.',
    //                 'data' => $studentsDET
    //             ], 200, [], JSON_NUMERIC_CHECK);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Exam not found.',
    //                 'data' => []
    //             ], 400, [], JSON_NUMERIC_CHECK);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


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
            // echo $examdt;exit;
            $studentDT = Student_detail::join('users', 'users.id', '=', 'students_details.student_id')
                ->join('standard', 'standard.id', '=', 'students_details.standard_id')
                ->where('students_details.institute_id', $institute_id)
                ->where('students_details.user_id', $user_id)
                ->where('students_details.board_id', $examdt->board_id)
                ->where('students_details.medium_id', $examdt->medium_id)
                ->where('students_details.batch_id', $examdt->batch_id)
                //->where('students_details.class_id', $examdt->class_id)
                ->where('students_details.standard_id', $examdt->standard_id)
                //->where('students_details.stream_id', $examdt->stream_id)
                ->when($examdt->stream_id, function ($query, $stream_id) {
                    return $query->where('students_details.stream_id', $stream_id);
                })
                ->whereRaw("FIND_IN_SET($examdt->subject_id, students_details.subject_id)")
                ->select('students_details.*', 'users.firstname', 'users.lastname', 'standard.name as standardname')->get();

            $studentsDET = [];
            foreach ($studentDT as $stddt) {
                $subjectqy = Subject_model::where('id', $examdt->subject_id)->first();
                $marksofstd = Marks_model::where('student_id', $stddt->student_id)->where('exam_id', $request->exam_id)->first();
                $studentsDET[] = array(
                    'student_id' => $stddt->student_id,
                    'exam_id' => $request->exam_id,
                    'batch_id' => $request->batch_id,
                    'marks' => !empty($marksofstd->mark) ? (float)$marksofstd->mark : 0,
                    'firstname' => $stddt->firstname,
                    'lastname' => $stddt->lastname,
                    'total_mark' => $examdt->total_mark,
                    'standard' => $stddt->standardname,
                    'subject' => $subjectqy->name
                );
            }
            return $this->response($studentsDET, "Successfully fetch Data.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }



    // student list with marks
    // public function student_list_with_marks(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'exam_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         //$student_id = $request->student_id;
    //         $user_id = $request->user_id;
    //         $examid = $request->exam_id;
    //         $examdtr = Exam_Model::where('id', $examid)->first();

    //         if (!empty($examdtr)) {
    //             $marksdt = Marks_model::join('users', 'users.id', '=', 'marks.student_id')
    //                 ->where('marks.exam_id', $examid)
    //                 ->select('marks.*', 'users.firstname', 'users.lastname')->get();
    //             $studentsDET = [];
    //             foreach ($marksdt as $markses) {
    //                 $subjectq = Subject_model::where('id', $examdtr->subject_id)->first();
    //                 $standardtq = Standard_model::where('id', $examdtr->standard_id)->first();
    //                 $studentsDET[] = array(
    //                     'student_id' => $markses->student_id,
    //                     'exam_id' => $request->exam_id,
    //                     'firstname' => $markses->firstname,
    //                     'lastname' => $markses->lastname,
    //                     'total_mark' => $examdtr->total_mark,
    //                     'mark' => $markses->mark,
    //                     'standard' => $standardtq->name,
    //                     'subject' => $subjectq->name
    //                 );
    //             }
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Successfully fetch Data.',
    //                 'data' => $studentsDET
    //             ], 200, [], JSON_NUMERIC_CHECK);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Exam not found.',
    //                 'data' => []
    //             ], 400, [], JSON_NUMERIC_CHECK);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


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
            return $this->response([], "Invalid token.", false, 400);
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
            return $this->response([], "Invalid token.", false, 400);
        }
    }

    //add exam marks
    // public function add_marks(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'institute_id' => 'required|integer',
    //         'user_id' => 'required',
    //         'student_id' => 'required',
    //         'exam_id' => 'required',
    //         'mark' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         $institute_id = $request->institute_id;
    //         $user_id = $request->user_id;
    //         $student_id = $request->student_id;
    //         $exam_id = $request->exam_id;
    //         $mark = $request->mark;

    //         $addesmarks = Marks_model::where('student_id', $student_id)->where('exam_id', $exam_id)->first();
    //         if ($addesmarks) {
    //             $admarks = Marks_model::where('id', $addesmarks->id)->update([
    //                 'student_id' => $student_id,
    //                 'exam_id' => $exam_id,
    //                 'mark' => $mark,
    //             ]);
    //         } else {
    //             $admarks = Marks_model::create([
    //                 'student_id' => $student_id,
    //                 'exam_id' => $exam_id,
    //                 'mark' => $mark,
    //             ]);
    //         }


    //         if ($admarks) {
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Added.',
    //                 'data' => []
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Data not added.',
    //                 'data' => []
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //             'data' => []
    //         ]);
    //     }
    // }

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
                        ->where('subject_id', $announcement->subject_id)
                        ->pluck('teacher_id');
                    $combinedIds = array_merge($combinedIds, $teachersId->toArray());
                }

                // Check for role type 5
                if (in_array('5', $roleTypes)) {
                    $studentId = Student_detail::where('institute_id', $announcement->institute_id)
                        ->where('board_id', $announcement->board_id)
                        ->where('medium_id', $announcement->medium_id)
                        ->where('subject_id', $announcement->subject_id)
                        ->pluck('student_id');
                    $parent = Parents::whereIn('student_id', $studentId)->pluck('parent_id');
                    $combinedIds = array_merge($combinedIds, $parent->toArray());
                }

                // Check for role type 6
                if (in_array('6', $roleTypes)) {
                    $studentId = Student_detail::where('institute_id', $request->institute_id)
                        ->where('board_id', $request->board_id)
                        ->where('medium_id', $request->medium_id)
                        ->where('subject_id', $request->subject_id)->pluck('student_id');
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
                return $this->response([], "Announcement added successfully.");
            }
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }

    //add announcements
    // public function add_announcements(Request $request)
    // {
    //     // echo "<pre>";print_r($request->all());exit;
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'institute_id' => 'required',
    //         'board_id' => 'required',
    //         'medium_id' => 'required',
    //         'batch_id' => 'required',
    //         //'institute_for_id' => 'required',
    //         //'class_id' => 'required',
    //         //'stream_id' => 'required',
    //         'subject_id' => 'required',
    //         'role_type' => 'required',
    //         'title' => 'required',
    //         'detail' => 'required',
    //         'standard_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();

    //     if ($existingUser) {
    //         $user_id = $request->user_id;
    //         $institute_id = $request->institute_id;
    //         $board_id = $request->board_id;
    //         $medium_id = $request->medium_id;
    //         // $institute_for_id = $request->institute_for_id;
    //         //$class_id = $request->class_id;
    //         $stream_id = $request->stream_id;
    //         $subject_id = $request->subject_id;
    //         $role_type = $request->role_type;
    //         $title = $request->title;
    //         $detail = $request->detail;
    //         $standard_id = $request->standard_id;
    //         $batch_id = $request->batch_id;

    //         if ($stream_id == 'null') {
    //             $stream_idd = null;
    //         } else {
    //             $stream_idd = $request->stream_id;
    //         }

    //         $addannounc = announcements_model::create([
    //             'user_id' => $user_id,
    //             'institute_id' => $institute_id,
    //             'batch_id' => $batch_id,
    //             'board_id' => $board_id,
    //             'medium_id' => $medium_id,
    //             //'institute_for_id' => $institute_for_id,
    //             //'class_id' => $class_id,
    //             'stream_id' => $stream_idd,
    //             'subject_id' => $subject_id,
    //             'role_type' => $role_type,
    //             'title' => $title,
    //             'detail' => $detail,
    //             'standard_id' => $standard_id
    //         ]);

    //         if ($addannounc) {
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Announcement added successfully.',
    //                 'data' => []
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Data not added.',
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }

    //announcement list
    // public function announcements_list(Request $request)
    // {

    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'institute_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {
    //             //$student_id = $request->student_id;
    //             $user_id = $request->user_id;
    //             $institute_id = $request->institute_id;
    //             $board_id = $request->board_id;
    //             $standard_id = $request->standard_id;
    //             $searchData = $request->searchData;

    //             $anoouncmntdt = announcements_model::where('user_id', $user_id)
    //                 ->where('institute_id', $institute_id)
    //                 ->when($searchData, function ($query, $searchData) {
    //                     return $query->where(function ($query) use ($searchData) {
    //                         $query->where('title', 'like', '%' . $searchData . '%')
    //                             ->orWhere('detail', 'like', '%' . $searchData . '%');
    //                     });
    //                 })
    //                 ->when($board_id, function ($query, $board_id) {
    //                     return $query->where(function ($query) use ($board_id) {
    //                         $query->where('board_id', $board_id);
    //                     });
    //                 })
    //                 ->when($standard_id, function ($query, $standard_id) {
    //                     return $query->where(function ($query) use ($standard_id) {
    //                         $query->where('standard_id', $standard_id);
    //                     });
    //                 })
    //                 ->orderByDesc('created_at')
    //                 ->get();

    //             if (!empty($anoouncmntdt)) {

    //                 $announcementDT = [];
    //                 foreach ($anoouncmntdt as $anoouncmnt) {

    //                     $subary = explode(",", $anoouncmnt->subject_id);
    //                     $batinsd = explode(",", $anoouncmnt->batch_id);
    //                     $subjectq = Subject_model::whereIN('id', $subary)->get();
    //                     $standardtq = Standard_model::where('id', $anoouncmnt->standard_id)->first();
    //                     $boarddt = board::where('id', $anoouncmnt->board_id)->first();
    //                     $batchnm = Batches_model::whereIN('id', $batinsd)->get();

    //                     $subjctslist = [];
    //                     foreach ($subjectq as $subnms) {
    //                         $subjctslist[] = array('id' => $subnms->id, 'name' => $subnms->name);
    //                     }

    //                     $batchslist = [];
    //                     foreach ($batchnm as $btcnmms) {
    //                         $batchslist[] = array('id' => $btcnmms->id, 'name' => $btcnmms->batch_name);
    //                     }

    //                     $roles = [];
    //                     $roledsid = explode(",", $anoouncmnt->role_type);
    //                     $roqy = Role::whereIN('id', $roledsid)->get();
    //                     foreach ($roqy as $rolDT) {
    //                         $roles[] = array(
    //                             'id' => $rolDT->id,
    //                             'name' => $rolDT->role_name
    //                         );
    //                     }

    //                     $announcementDT[] = array(
    //                         'id' => $anoouncmnt->id,
    //                         'date' => $anoouncmnt->created_at,
    //                         'title' => $anoouncmnt->title,
    //                         'detail' => $anoouncmnt->detail,
    //                         //'subject_id' => $subjectq->id,

    //                         //'batch_id' => !empty($batchnm->id) ? $batchnm->id : 0,
    //                         //'batch_name' => !empty($batchnm->batch_name) ? $batchnm->batch_name : '',

    //                         'standard_id' => $standardtq->id,
    //                         'standard' => $standardtq->name,
    //                         'board_id' => $boarddt->id,
    //                         'board' => $boarddt->name,
    //                         'role' => $roles,
    //                         'batches' => $batchslist,
    //                         'subject' => $subjctslist,

    //                     );
    //                 }
    //                 return response()->json([
    //                     'status' => 200,
    //                     'message' => 'Successfully fetch Data.',
    //                     'data' => $announcementDT
    //                 ], 200, [], JSON_NUMERIC_CHECK);
    //             } else {
    //                 return response()->json([
    //                     'status' => 400,
    //                     'message' => 'Data not found.',
    //                 ], 400, [], JSON_NUMERIC_CHECK);
    //             }
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }

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
            $user_id = $request->user_id;
            $institute_id = $request->institute_id;
            $board_id = $request->board_id;
            $standard_id = $request->standard_id;
            $searchData = $request->searchData;

            $announcements = announcements_model::where('user_id', $user_id)
                ->where('institute_id', $institute_id)
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
            return $this->response([], "Invalid token.", false, 400);
        }
    }


    public function delete_account(Request $request)
    {
        try {
            $user = Auth::user();
            $user->delete();
            return $this->response([], "Delete Account Successfully!");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }


    // public function delete_account123(Request $request)
    // {

    //     $userId = $request->user_id;
    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $userId)->first();
    //     if ($existingUser) {

    //         user::where('id', $userId)->delete();
    //         return response()->json([
    //             'status' => '200',
    //             'message' => 'Delete Account Successfully!',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ], 400);
    //     }
    // }


    public function  roles(Request $request)
    {
        try {
            $rolesDT = [];
            $suad = [1, 2, 3];
            $roleqry = Role::whereNull('deleted_at')->whereNotIN('id', $suad)->get();
            foreach ($roleqry as $roldel) {
                $rolesDT[] = array(
                    'id' => $roldel->id,
                    'role_name' => $roldel->role_name
                );
            }
            return $this->response($rolesDT, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }

    //roles list
    // public function roles(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();

    //     if ($existingUser) {
    //         try {
    //             $rolesDT = [];
    //             $suad = [1, 2, 3];
    //             $roleqry = Role::whereNull('deleted_at')->whereNotIN('id', $suad)->get();
    //             foreach ($roleqry as $roldel) {
    //                 $rolesDT[] = array(
    //                     'id' => $roldel->id,
    //                     'role_name' => $roldel->role_name
    //                 );
    //             }
    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Data Fetch Successfully',
    //                 'data' => $rolesDT
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Something went wrong',
    //                 'data' => []
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ], 400);
    //     }
    // }


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
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    //student list all and filter wise
    // public function institute_students(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'institute_id' => 'required',

    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {
    //             $user_id = $request->user_id;
    //             $institute_id = $request->institute_id;

    //             //filter fields
    //             $board = $request->board_id;
    //             $medium = $request->medium_id;
    //             $standard = $request->standard_id;
    //             $batch_id = $request->batch_id;
    //             $searchkeyword = $request->searchkeyword;
    //             $perPage = $request->input('per_page', 10);

    //             $students = Student_detail::join('users', 'users.id', 'students_details.student_id')
    //                 ->join('board', 'board.id', 'students_details.board_id')
    //                 ->join('medium', 'medium.id', 'students_details.medium_id')
    //                 ->join('standard', 'standard.id', 'students_details.standard_id')
    //                 ->leftjoin('batches', 'batches.id', 'students_details.batch_id')
    //                 ->where('students_details.user_id', $user_id)
    //                 ->where('students_details.institute_id', $institute_id)
    //                 ->when($board, function ($query, $board) {
    //                     return $query->where('students_details.board_id', $board);
    //                 })
    //                 ->when($medium, function ($query, $medium) {
    //                     return $query->where('students_details.medium_id', $medium);
    //                 })
    //                 ->when($standard, function ($query, $standard) {
    //                     return $query->where('students_details.standard_id', $standard);
    //                 })
    //                 ->when($batch_id, function ($query, $batch_id) {
    //                     return $query->where('students_details.batch_id', $batch_id);
    //                 })
    //                 ->when($searchkeyword, function ($query, $searchkeyword) {
    //                     return $query->where(function ($query) use ($searchkeyword) {
    //                         $query->where('users.firstname', 'like', '%' . $searchkeyword . '%')
    //                             ->orWhere('users.lastname', 'like', '%' . $searchkeyword . '%')
    //                             ->orWhere('users.unique_id', 'like', '%' . $searchkeyword . '%');
    //                     });
    //                 })
    //                 ->select(
    //                     'students_details.*',
    //                     'batches.batch_name',
    //                     'users.firstname',
    //                     'users.lastname',
    //                     'users.image',
    //                     'board.name as board',
    //                     'medium.name as medium',
    //                     'standard.name as standard'
    //                 )
    //                 ->orderByDesc('students_details.created_at')
    //                 ->paginate($perPage);

    //             $stulist = [];
    //             foreach ($students as $stdDT) {
    //                 $stulist[] = array(
    //                     'id' => $stdDT->student_id,
    //                     'name' => $stdDT->firstname . ' ' . $stdDT->lastname,
    //                     'image' => asset($stdDT->image),
    //                     'board_id' => $stdDT->board_id,
    //                     'board' => $stdDT->board . '(' . $stdDT->medium . ')',
    //                     'standard_id' => $stdDT->standard_id,
    //                     'standard' => $stdDT->standard,
    //                     'batch_id' => $stdDT->batch_id,
    //                     'batch_name' => $stdDT->batch_name,
    //                 );
    //             }

    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Data Fetch Successfully',
    //                 'data' => $stulist
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


    public function  filters_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        try {
            $boarids = Institute_board_sub::where('user_id', auth()->user()->id)
                ->where('institute_id', $request->institute_id)->pluck('board_id')->toArray();
            $uniqueBoardIds = array_unique($boarids);
            // echo "<pre>";print_r($uniqueBoardIds);exit;

            $board_list = DB::table('board')
                ->whereIN('id', $uniqueBoardIds)
                ->get();

            $board_array = [];
            foreach ($board_list as $board_value) {

                $medium_sublist = DB::table('medium_sub')
                    ->where('user_id', auth()->user()->id)
                    ->where('board_id', $board_value->id)
                    ->where('institute_id', $request->institute_id)
                    ->pluck('medium_id')->toArray();

                $uniquemediumds = array_unique($medium_sublist);

                $medium_list = Medium_model::whereIN('id', $uniquemediumds)->get();

                $medium_array = [];
                foreach ($medium_list as $medium_value) {

                    $stndQY = Standard_sub::join('standard', 'standard.id', 'standard_sub.standard_id')
                        ->where('standard_sub.user_id', auth()->user()->id)
                        ->where('standard_sub.institute_id', $request->institute_id)
                        ->where('standard_sub.board_id', $board_value->id)
                        ->where('standard_sub.medium_id', $medium_value->id)
                        ->select('standard.id as std_id', 'standard.name as std_name')->distinct()->get();
                    $stddata = [];
                    foreach ($stndQY as $stndDT) {
                        $forcounstd = Student_detail::whereNull('deleted_at')
                            ->where('user_id', auth()->user()->id)
                            ->where('institute_id', $request->institute_id)
                            ->where('board_id', $board_value->id)
                            ->where('medium_id', $medium_value->id)
                            ->get();
                        $stdCount = $forcounstd->count();

                        $stddata[] = [
                            'id' => $stndDT->std_id,
                            'name' => $stndDT->std_name,
                            'no_of_std' => $stdCount,
                        ];
                    }


                    $medium_array[] = [
                        'id' => $medium_value->id,
                        'medium_name' => $medium_value->name,

                        'standard' => $stddata
                    ];
                }
                $concatenated_name = $board_value->name . ' - ' . $medium_value->name;
                $board_array[] = [
                    'board_id' => $board_value->id,
                    'medium_id' => $medium_value->id,
                    'board_medium_name' => $concatenated_name,
                    'standard' => $stddata
                ];
            }
            return $this->response($board_array, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    // public function filters_data(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'institute_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {

    //             $user_id = $request->user_id;
    //             $institute_id = $request->institute_id;


    //             $boarids = Institute_board_sub::where('user_id', $user_id)
    //                 ->where('institute_id', $institute_id)->pluck('board_id')->toArray();
    //             $uniqueBoardIds = array_unique($boarids);

    //             $board_list = DB::table('board')
    //                 ->whereIN('id', $uniqueBoardIds)
    //                 ->get();

    //             $board_array = [];
    //             foreach ($board_list as $board_value) {

    //                 $medium_sublist = DB::table('medium_sub')
    //                     ->where('user_id', $user_id)
    //                     ->where('board_id', $board_value->id)
    //                     ->where('institute_id', $institute_id)
    //                     ->pluck('medium_id')->toArray();
    //                 $uniquemediumds = array_unique($medium_sublist);

    //                 $medium_list = Medium_model::whereIN('id', $uniquemediumds)->get();

    //                 $medium_array = [];
    //                 foreach ($medium_list as $medium_value) {

    //                     $stndQY = Standard_sub::join('standard', 'standard.id', 'standard_sub.standard_id')
    //                         ->where('standard_sub.user_id', $user_id)
    //                         ->where('standard_sub.institute_id', $institute_id)
    //                         ->where('standard_sub.board_id', $board_value->id)
    //                         ->where('standard_sub.medium_id', $medium_value->id)->select('standard.id as std_id', 'standard.name as std_name')->get();
    //                     $stddata = [];
    //                     foreach ($stndQY as $stndDT) {
    //                         $forcounstd = Student_detail::whereNull('deleted_at')
    //                             ->where('user_id', $user_id)
    //                             ->where('institute_id', $institute_id)
    //                             ->where('board_id', $board_value->id)
    //                             ->where('medium_id', $medium_value->id)
    //                             ->get();
    //                         $stdCount = $forcounstd->count();

    //                         $stddata[] = array(
    //                             'id' => $stndDT->std_id,
    //                             'name' => $stndDT->std_name,
    //                             'no_of_std' => $stdCount
    //                         );
    //                     }

    //                     $medium_array[] = [
    //                         'id' => $medium_value->id,
    //                         'medium_name' => $medium_value->name,
    //                         'standard' => $stddata
    //                     ];
    //                 }

    //                 $board_array[] = [
    //                     'id' => $board_value->id,
    //                     'board_name' => $board_value->name,
    //                     'medium' => $medium_array,

    //                     // Include banner_array inside board_array
    //                 ];
    //             }

    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Data Fetch Successfully',
    //                 'data' => $board_array
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


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
            $boarids = Institute_board_sub::where('user_id', Auth::id())
                ->where('institute_id', $institute_id)->pluck('board_id')->toArray();
            $uniqueBoardIds = array_unique($boarids);

            $board_list = DB::table('board')
                ->whereIN('id', $uniqueBoardIds)
                ->get();

            $board_array = [];
            foreach ($board_list as $board_value) {

                $medium_sublist = DB::table('medium_sub')
                    ->where('user_id', Auth::id())
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
                    'contact_no' => $value['contact_no'] . '',
                    'email' => $value['email'] . '',
                    'about_us' => $value['about_us'] . '',
                    'board_name' => $board_array,
                    'website_link' => $value['website_link'] . '',
                    'instagram_link' => $value['instagram_link'] . '',
                    'facebook_link' => $value['facebook_link'] . '',
                    'whatsaap_link' => $value['whatsaap_link'] . '',
                    'youtube_link' => $value['youtube_link'] . '',
                    'logo' => url($value['logo']),
                    'cover_photo' => ($value['cover_photo'] ? url($value['cover_photo']) : url('cover_blank_image.png')),
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
            return $this->response($e, "Invalid token.", false, 400);
        }
    }

    // public function institute_profile(Request $request)
    // {
    //     $institute_id = $request->institute_id;
    //     $user_id = $request->user_id;
    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $user_id)->first();
    //     if ($existingUser) {
    //         $institute_detail = Institute_detail::where('institute_detail.id', $institute_id)
    //             ->select('institute_detail.*')
    //             ->get()->toarray();


    //         if (empty($institute_id)) {
    //             $institute_id = Institute_detail::where('user_id', $user_id)->select('id')->first();
    //         }
    //         // Institute_detail::where();
    //         $boarids = Institute_board_sub::where('user_id', $user_id)
    //             ->where('institute_id', $institute_id)->pluck('board_id')->toArray();
    //         $uniqueBoardIds = array_unique($boarids);

    //         $board_list = DB::table('board')
    //             ->whereIN('id', $uniqueBoardIds)
    //             ->get();

    //         $board_array = [];
    //         foreach ($board_list as $board_value) {

    //             $medium_sublist = DB::table('medium_sub')
    //                 ->where('user_id', $user_id)
    //                 ->where('board_id', $board_value->id)
    //                 ->where('institute_id', $institute_id)
    //                 ->pluck('medium_id')->toArray();
    //             $uniquemediumds = array_unique($medium_sublist);

    //             $medium_list = Medium_model::whereIN('id', $uniquemediumds)->get();

    //             $medium_array = [];
    //             foreach ($medium_list as $medium_value) {
    //                 $medium_array[] = [
    //                     'id' => $medium_value->id,
    //                     'medium_name' => $medium_value->name,
    //                 ];
    //             }
    //             $board_array[] = [
    //                 'id' => $board_value->id,
    //                 'board_name' => $board_value->name,
    //                 'icon' => $board_value->icon,
    //                 'medium' => $medium_array,

    //                 // Include banner_array inside board_array
    //             ];
    //         }
    //         $institute_response = [];
    //         foreach ($institute_detail as $value) {
    //             $institute_response[] = [
    //                 'institute_name' => $value['institute_name'],
    //                 'address' => $value['address'] . '',
    //                 'contact_no' => $value['contact_no'] . '',
    //                 'email' => $value['email'] . '',
    //                 'about_us' => $value['about_us'] . '',
    //                 'board_name' => $board_array,
    //                 'website_link' => $value['website_link'] . '',
    //                 'instagram_link' => $value['instagram_link'] . '',
    //                 'facebook_link' => $value['facebook_link'] . '',
    //                 'whatsaap_link' => $value['whatsaap_link'] . '',
    //                 'youtube_link' => $value['youtube_link'] . '',
    //                 'logo' => url($value['logo']),
    //                 'cover_photo' => ($value['cover_photo'] ? url($value['cover_photo']) : url('cover_blank_image.png')),
    //                 'country' => $value['country'] . '',
    //                 'state' => $value['state'] . '',
    //                 'city' => $value['city'] . '',
    //                 'pincode' => $value['pincode'] . '',
    //                 'open_time' => $value['open_time'] . '',
    //                 'close_time' => $value['close_time'] . '',
    //                 'gst_number' => $value['gst_number'] . '',
    //                 'gst_slab' => $value['gst_slab'] . '',
    //                 'start_academic_year' => $value['start_academic_year'] . '',
    //                 'end_academic_year' => $value['end_academic_year'] . '',

    //             ];
    //         }
    //         return response()->json([
    //             'status' => '200',
    //             'message' => 'Institute Fetch Successfully',
    //             'data' => $institute_response
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }
    //institute profile edit
    // public function institute_profile_edit(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'institute_id' => 'required',
    //         'institute_name' => 'required',
    //         'email' => 'required',
    //         'address' => 'required',
    //         'contact_no' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {
    //             $user_id = $request->user_id;
    //             $institute_id = $request->institute_id;
    //             $institutedt = Institute_detail::find($institute_id);
    //             if ($institutedt) {
    //                 $institutedt->institute_name = $request->institute_name;
    //                 $institutedt->address = $request->address;
    //                 $institutedt->contact_no = $request->contact_no;
    //                 $institutedt->email = $request->email;
    //                 $institutedt->about_us = $request->about_us;
    //                 //$institutedt->about_us = $request->country;
    //                 //$institutedt->state = $request->state;
    //                 //$institutedt->city = $request->city;
    //                 //$institutedt->pincode = $request->pincode;                 
    //                 $institutedt->open_time = $request->open_time;
    //                 $institutedt->close_time = $request->close_time;
    //                 $institutedt->gst_number = $request->gst_number;
    //                 $institutedt->gst_slab = $request->gst_slab;
    //                 $institutedt->website_link = $request->website_link;
    //                 $institutedt->instagram_link = $request->instagram_link;
    //                 $institutedt->facebook_link = $request->facebook_link;
    //                 $institutedt->whatsaap_link = $request->whatsaap_link;
    //                 $institutedt->youtube_link = $request->youtube_link;
    //                 $institutedt->start_academic_year = $request->start_academic_year;
    //                 $institutedt->end_academic_year = $request->end_academic_year;

    //                 $imagePath = null;
    //                 if ($request->hasFile('logo')) {
    //                     $logo_image = $request->file('logo');
    //                     $imagePath = $logo_image->store('logo', 'public');
    //                 }
    //                 if ($imagePath !== null) {
    //                     $institutedt->logo = $imagePath;
    //                 }
    //                 $imagePath2 = null;
    //                 if ($request->hasFile('cover_photo')) {
    //                     $logo_image = $request->file('cover_photo');
    //                     $imagePath2 = $logo_image->store('cover_photo', 'public');
    //                 }
    //                 if ($imagePath2 !== null) {
    //                     $institutedt->cover_photo = $imagePath2;
    //                 }
    //                 $institutedt->save();

    //                 return response()->json([
    //                     'status' => 200,
    //                     'message' => 'Record Update Successfully!.',
    //                     'data' => []
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'status' => 400,
    //                     'message' => 'Record not found.',
    //                     'data' => []
    //                 ]);
    //             }
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


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
            $institutedt->start_academic_year = $request->start_academic_year;
            $institutedt->end_academic_year = $request->end_academic_year;
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
            return $this->response([], "Invalid token.", false, 400);
        }
    }


    // category list for add do business with 
    // public function category_list(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {

    //             $vcategory = VideoCategory::where('status', 'active')->get();

    //             $cat_array = [];
    //             foreach ($vcategory as $cat_value) {
    //                 $cat_array[] = array(
    //                     'id' => $cat_value->id,
    //                     'name' => $cat_value->name
    //                 );
    //             }
    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Data Fetch Successfully',
    //                 'data' => $cat_array
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }

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
            return $this->response($e, "Invalid token.", false, 400);
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
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    //create batch
    // public function create_batch(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'institute_id' => 'required',
    //         'board_id' => 'required',
    //         'medium_id' => 'required',
    //         'standard_id' => 'required',
    //         'batch_name' => 'required',
    //         'subjects' => 'required',
    //         'student_capacity' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }

    //     $token = $request->header('Authorization');

    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }

    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {

    //             $addbatch = Batches_model::create([
    //                 'user_id' => $request->user_id,
    //                 'institute_id' => $request->institute_id,
    //                 'board_id' => $request->board_id,
    //                 'medium_id' => $request->medium_id,
    //                 'stream_id' => $request->stream_id, //nullable
    //                 'standard_id' => $request->standard_id,
    //                 'batch_name' => $request->batch_name,
    //                 'subjects' => $request->subjects,
    //                 'student_capacity' => $request->student_capacity
    //             ]);

    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Batch Added Successfully',
    //                 'data' => []
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


    public function batch_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'board_id' => 'required',
            'standard_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $batchlist = Batches_model::where([
                ['user_id', Auth::id()],
                ['institute_id', $request->institute_id],
                ['board_id', $request->board_id],
                ['standard_id', $request->standard_id]
            ])->get(['id', 'batch_name'])->toArray();
            return $this->response($batchlist, "Batch Fetch Successfully");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }


    // public function batch_list(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'institute_id' => 'required',
    //         'board_id' => 'required',
    //         'standard_id' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }
    //     $token = $request->header('Authorization');
    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }
    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {
    //             $batchlist = Batches_model::where('user_id', $request->user_id)
    //                 ->where('institute_id', $request->institute_id)
    //                 ->where('board_id', $request->board_id)
    //                 ->where('standard_id', $request->standard_id)->get()->toarray();
    //             $batch_response = [];
    //             foreach ($batchlist as $value) {
    //                 $batch_response[] = [
    //                     'id' => $value['id'],
    //                     'batch_name' => $value['batch_name']
    //                 ];
    //             }
    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Batch Fetch Successfully',
    //                 'data' => $batch_response
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }


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
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }



    // public function subjectList(Request $request)
    // {

    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'institute_id' => 'required',
    //         'board_id' => 'required',
    //         'medium_id' => 'required',
    //         'standard_id' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }
    //     $token = $request->header('Authorization');
    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }
    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {
    //             $subjctlist = Subject_sub::join('subject', 'subject.id', '=', 'subject_sub.subject_id')
    //                 ->join('base_table', 'base_table.id', '=', 'subject.base_table_id')
    //                 ->where('subject_sub.user_id', $request->user_id)
    //                 ->where('subject_sub.institute_id', $request->institute_id)
    //                 ->where('base_table.board', $request->board_id)
    //                 ->where('base_table.standard', $request->standard_id)->get()->toarray();
    //             $batch_response = [];
    //             foreach ($subjctlist as $svalue) {
    //                 $batch_response[] = [
    //                     'id' => $svalue['subject_id'],
    //                     'name' => $svalue['name']
    //                 ];
    //             }
    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Data Fetch Successfully',
    //                 'data' => $batch_response
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }

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
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    // public function allsubjectList(Request $request)
    // {

    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         //'institute_id' => 'required',
    //         'board_id' => 'required',
    //         'medium_id' => 'required',
    //         'standard_id' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }
    //     $token = $request->header('Authorization');
    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }
    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {
    //             $subjctslist = Subject_model::join('base_table', 'base_table.id', '=', 'subject.base_table_id')
    //                 ->where('base_table.board', $request->board_id)
    //                 ->where('base_table.medium', $request->medium_id)
    //                 ->where('base_table.standard', $request->standard_id)
    //                 ->select('subject.*')->get();
    //             $allsub_response = [];
    //             foreach ($subjctslist as $svalue) {
    //                 $allsub_response[] = [
    //                     'id' => $svalue->id,
    //                     'name' => $svalue->name,
    //                     'image' => asset($svalue->image),
    //                 ];
    //             }
    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Data Fetch Successfully',
    //                 'data' => $allsub_response
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }




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
            $subsub = Subject_sub::where('user_id', Auth::id())
                ->where('institute_id', $request->institute_id)
                ->delete();
            if ($subsub) {

                $subjectsids = explode(",", $request->subject_id);

                foreach ($subjectsids as $subjids) {
                    $subcts = Subject_sub::create([
                        'user_id' => Auth::id(),
                        'institute_id' => $request->institute_id,
                        'subject_id' => $subjids
                    ]);
                }
            }
            return $this->response([], "Updated Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }

    // public function edit_subject(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'institute_id' => 'required',
    //         'medium' => 'required',
    //         'board_id' => 'required',
    //         'standard_id' => 'required',
    //         'subject_id' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         $errorMessages = array_values($validator->errors()->all());
    //         return response()->json([
    //             'success' => 400,
    //             'message' => 'Validation error',
    //             'errors' => $errorMessages,
    //         ], 400);
    //     }
    //     $token = $request->header('Authorization');
    //     if (strpos($token, 'Bearer ') === 0) {
    //         $token = substr($token, 7);
    //     }
    //     $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
    //     if ($existingUser) {
    //         try {
    //             $subsub = Subject_sub::where('user_id', $request->user_id)
    //                 ->where('institute_id', $request->institute_id)
    //                 //->whereRow("FIND_IN_SET($request->subject_id, subject_id)")
    //                 ->delete();
    //             if ($subsub) {

    //                 $subjectsids = explode(",", $request->subject_id);

    //                 foreach ($subjectsids as $subjids) {
    //                     $subcts = Subject_sub::create([
    //                         'user_id' => $request->user_id,
    //                         'institute_id' => $request->institute_id,
    //                         'subject_id' => $subjids
    //                     ]);
    //                 }
    //             }

    //             return response()->json([
    //                 'status' => '200',
    //                 'message' => 'Updated Successfully',
    //                 'data' => []
    //             ]);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => 500,
    //                 'message' => 'Server Error',
    //                 'error' => $e->getMessage(),
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Invalid token.',
    //         ]);
    //     }
    // }
    public function do_business_with()
    {
        try {
            $data = Dobusinesswith_Model::all();
            if (!empty($data)) {
                foreach ($data as $value) {
                    $response[] = ['id' => $value->id, 'name' => $value->name];
                }
                return $this->response($response, "Successfully fetch Data.");
            } else {
                return $this->response([], "No data found.");
            }
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function approve_teacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'mobile' => 'required',
            'email' => 'required',
            'qualification' => 'required',
            'employee_type' => 'required',
            'board_id' => 'required|integer',
            'medium_id' => 'required|integer',
            'standard_id' => 'required|integer',
            'batch_id' => 'required|integer',
            // 'stream_id' => 'required',
            'subject_id' => 'required|integer',
            'teacher_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $subject = Subject_model::whereIn('id', explode(',', $request->subject_id))->get();

            foreach ($subject as $value) {
                $batch_list = Batches_model::whereRaw("FIND_IN_SET($value->id, subjects)")
                    ->select('*')->get()->toarray();
                foreach (explode(',', $request->batch_id) as $batchId) {
                    Batch_assign_teacher_model::create([
                        'teacher_id' => $request->teacher_id,
                        'batch_id' => $batchId,
                    ]);
                }
                $base_table_response = Base_table::where('id', $value->base_table_id)->get()->toarray();
                foreach ($base_table_response as $value2) {
                    if (is_array($request->subject_id)) {
                        $subject = implode(',', $request->subject_id);
                    } else {
                        $subject = $request->subject_id;
                    }

                    $teacher_detail = Teacher_model::where('teacher_id', $request->teacher_id);
                    $teacher_detail->update([
                        'institute_id' => $request->institute_id,
                        'teacher_id' => $request->teacher_id,
                        'institute_for_id' => $value2['institute_for'],
                        'board_id' => $value2['board'],
                        'medium_id' => $value2['medium'],
                        'class_id' => $value2['institute_for_class'],
                        'standard_id' => $value2['standard'],
                        'stream_id' => $value2['stream'],
                        'subject_id' => $subject,
                        'status' => '1',
                    ]);
                }
            }
            User::where('id', $request->teacher_id)->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                // 'address' => $request->address,
                'email' => $request->email,
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
                }

                curl_close($ch);
            }
            return $this->response([], "Teacher Assign successfully");
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
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
                ->select('users.id', 'users.firstname', 'users.lastname', 'teacher_detail.teacher_id', 'users.qualification')
                ->groupBy('users.id', 'users.firstname', 'users.lastname', 'teacher_detail.teacher_id', 'users.qualification');


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

                    $response[] = [
                        'teacher_id' => $value['teacher_id'],
                        'name' => $value['firstname'] . ' ' . $value['lastname'],
                        'qualification' => $value['qualification'],
                        'standard' => $standard_array
                    ];
                }
            }

            return $this->response($response, "Data Fetch Successfully");
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
}
