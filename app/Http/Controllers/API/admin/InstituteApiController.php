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
use App\Models\Exam_Model;
use App\Models\Marks_model;
use App\Models\VideoCategory;
use Carbon\Carbon;
use PDO;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Role;

class InstituteApiController extends Controller
{

    function get_institute_reponse(Request $request)
    {
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }


        $existingUser = User::where('token', $token)->first();
        if ($existingUser) {

            $basinstitute = Base_table::where('status', 'active')->select('institute_for')->groupby('institute_for')->get();
            $institute_for_id = '';
            foreach ($basinstitute as $insvalue) {
                $institute_for_id .= $insvalue->institute_for;
            }
            $institute_for_id .= 0;

            $institute_for_id = $basinstitute->pluck('institute_for')->toArray();

            $institute_for_array = DB::table('institute_for')
                ->whereIN('id', $institute_for_id)->get();

            $institute_for = [];
            foreach ($institute_for_array as $institute_for_array_value) {

                $onlyboardfrombase = base_table::where('institute_for', $institute_for_array_value->id)
                    ->select('board')
                    ->groupby('board')
                    ->get();
                $boardsids = '';
                foreach ($onlyboardfrombase as $boardsval) {
                    $boardsids .= $boardsval->board . ',';
                }
                $boardsids .= 0;

                $boardsids = $onlyboardfrombase->pluck('board')->toArray();
                $board_array = board::whereIN('id', $boardsids)
                    ->get();


                $board = [];
                foreach ($board_array as $board_array_value) {
                    $mediumsidget = base_table::where('board', $board_array_value->id)
                        ->where('institute_for', $institute_for_array_value->id)
                        ->select('medium')
                        ->groupby('medium')
                        ->get();
                    $mediumids = '';
                    foreach ($mediumsidget as $mediumsids) {
                        $mediumids .= $mediumsids->medium;
                    }
                    $mediumids .= 0;

                    $mediumids = $mediumsidget->pluck('medium')->toArray();
                    $medium_array = Medium_model::whereIN('id', $mediumids)->get();
                    $medium = [];

                    foreach ($medium_array as $medium_array_value) {

                        $classesidget = base_table::where('medium', $medium_array_value->id)
                            ->where('board', $board_array_value->id)
                            ->where('institute_for', $institute_for_array_value->id)
                            ->select('institute_for_class')
                            ->groupby('institute_for_class')
                            ->get();
                        $institute_for_classids = '';
                        foreach ($classesidget as $classesids) {
                            $institute_for_classids .= $classesids->institute_for_class;
                        }
                        $institute_for_classids .= 0;
                        $institute_for_classids = $classesidget->pluck('institute_for_class')->toArray();
                        $class_array = Class_model::whereIN('id', $institute_for_classids)
                            ->get();

                        $class = [];
                        foreach ($class_array as $class_array_value) {

                            $standardidget = base_table::where('institute_for_class', $class_array_value->id)
                                ->where('medium', $medium_array_value->id)
                                ->where('board', $board_array_value->id)
                                ->where('institute_for', $institute_for_array_value->id)
                                ->select('standard', 'id')
                                ->get();
                            $standardids = '';
                            foreach ($standardidget as $standardidsv) {
                                $standardids .= $standardidsv->standard;
                            }
                            $standardids .= 0;
                            $standardids = $standardidget->pluck('standard')->toArray();
                            $standard_array = Standard_model::whereIN('id', $standardids)
                                ->get();


                            $standard = [];
                            foreach ($standard_array as $standard_array_value) {

                                $stream_array = DB::table('base_table')
                                    ->leftJoin('stream', 'stream.id', '=', 'base_table.stream')
                                    ->select('stream.name as stream_name', 'base_table.id', 'stream.id as stream_id')
                                    ->whereNull('base_table.deleted_at')
                                    ->where('base_table.standard', $standard_array_value->id)
                                    // ->where('base_table.institute_for_class',$class_array_value->id)
                                    //->where('base_table.medium',$medium_array_value->id)
                                    //->where('base_table.board',$board_array_value->id)
                                    // ->where('base_table.institute_for',$institute_for_array_value->id)
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
                                    $baseidsfosubj = '';
                                    foreach ($forsubdidget as $forsubval) {
                                        $baseidsfosubj .= $forsubval->id . ',';
                                    }
                                    $baseidsfosubj .= 0;
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


                // $institute_for[] = [
                //     'institute_id'=>$institute_for_array_value->id,
                //     'institute_for' => $institute_for_array_value->institute_for_name,
                //     'board_detail' => $board,
                // ];
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
            //    echo "<pre>";print_r($institute_for);exit;
            return response()->json([
                'success' => true,
                'message' => 'Fetch Data Successfully',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }
    }

    public function register_institute(Request $request)
    {
        $validator = \Validator::make($request->all(), [
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
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }
        try {
            $subadminPrefix = 'ist_';
            $startNumber = 101;

            $lastInsertedId = DB::table('institute_detail')->orderBy('id', 'desc')->value('unique_id');
            // echo $lastInsertedId;exit;
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
            //institute_detail
            //acedamic year
            $currentDate = date("d-m-Y");
            $nextYearDate = date("d-m-Y", strtotime("+1 year"));
            $nextYear = date("d-m-Y", strtotime($nextYearDate));
            $dateString = $currentDate . " / " . $nextYear;
            $instituteDetail = Institute_detail::create([
                'unique_id' => $unique_id,
                // 'youtube_link' => $request->input('youtube_link'),
                // 'whatsaap_link' => $request->input('whatsaap_link'),
                // 'facebook_link' => $request->input('facebook_link'),
                // 'instagram_link' => $request->input('instagram_link'),
                // 'website_link' => $request->input('website_link'),
                // 'gst_slab' => $request->input('gst_slab'),
                // 'gst_number' => $request->input('gst_number'),
                // 'close_time' => $request->input('close_time'),
                // 'open_time' => $request->input('open_time'),
                'logo' => $imagePath,
                'about_us' => $request->about_us,
                'user_id' => $request->input('user_id'),
                'institute_name' => $request->input('institute_name'),
                'address' => $request->input('address'),
                'contact_no' => $request->input('contact_no'),
                'email' => $request->input('email'),
                'country' => $request->input('country'),
                'state' => $request->input('state'),
                'city' => $request->input('city'),
                'pincode' => $request->input('pincode'),
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


                $createinstitutefor = Institute_for_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                ]);

                if (!$createinstitutefor) {
                    $instituteFordet = Institute_detail::where('id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->first();
                    $delt = $instituteFordet->delete();
                }

                $createboard = Institute_board_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                    'board_id' => $board,
                ]);

                if (!$createboard) {
                    $instituteFordet = Institute_detail::where('id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->first();
                    $instituteFordet->delete();

                    $instituteForSub = Institute_for_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();
                }

                $createmedium = Medium_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                    'board_id' => $board,
                    'medium_id' => $medium,
                ]);

                if (!$createmedium) {
                    $instituteFordet = Institute_detail::where('id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->first();
                    $instituteFordet->delete();

                    Institute_for_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Institute_board_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();
                }

                $createclass = Class_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                    'board_id' => $board,
                    'medium_id' => $medium,
                    'class_id' => $institute_for_class,
                ]);

                if (!$createclass) {
                    $instituteFordet = Institute_detail::where('id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->first();
                    $instituteFordet->delete();

                    Institute_for_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Institute_board_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Medium_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();
                }

                $createstnd = Standard_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                    'board_id' => $board,
                    'medium_id' => $medium,
                    'class_id' => $institute_for_class,
                    'standard_id' => $standard,
                ]);

                if (!$createstnd) {
                    $instituteFordet = Institute_detail::where('id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->first();
                    $instituteFordet->delete();

                    Institute_for_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Institute_board_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Medium_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Class_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();
                }

                if ($stream != null) {

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

                    if (!$createstrem) {
                        $instituteFordet = Institute_detail::where('id', $lastInsertedId)
                            ->where('user_id', $request->input('user_id'))->first();
                        $instituteFordet->delete();

                        Institute_for_sub::where('institute_id', $lastInsertedId)
                            ->where('user_id', $request->input('user_id'))->delete();

                        Institute_board_sub::where('institute_id', $lastInsertedId)
                            ->where('user_id', $request->input('user_id'))->delete();

                        Medium_sub::where('institute_id', $lastInsertedId)
                            ->where('user_id', $request->input('user_id'))->delete();

                        Class_sub::where('institute_id', $lastInsertedId)
                            ->where('user_id', $request->input('user_id'))->delete();

                        Standard_sub::where('institute_id', $lastInsertedId)
                            ->where('user_id', $request->input('user_id'))->delete();
                    }
                }
            }

            //end new code

            //dobusiness
            try {
                $institute_work_id = explode(',', $request->input('institute_work_id'));
                foreach ($institute_work_id as $value) {
                    if ($value == 'other') {
                        $instituteforadd = Dobusinesswith_Model::create([
                            'name' => $request->input('do_businesswith_name'),
                            'category_id' => $request->input('category_id'), //video category table id
                            'created_by' => $request->input('user_id'),
                            'status' => 'active',
                        ]);
                        $dobusinesswith_id = $instituteforadd->id;
                    } else {
                        $dobusinesswith_id = $value;
                    }

                    Dobusinesswith_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'do_business_with_id' => $dobusinesswith_id,
                    ]);
                }
            } catch (\Exception $e) {

                Subject_sub::where('institute_id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->delete();

                Standard_sub::where('institute_id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->delete();

                Stream_sub::where('institute_id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->delete();

                Standard_sub::where('institute_id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->delete();

                Class_sub::where('institute_id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->delete();

                Medium_sub::where('institute_id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->delete();

                Institute_board_sub::where('institute_id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->delete();

                Institute_for_sub::where('institute_id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->delete();

                Dobusinesswith_sub::where('institute_id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->delete();

                $indel = Institute_detail::where('id', $lastInsertedId)
                    ->where('user_id', $request->input('user_id'))->forceDelete();


                return response()->json([
                    'success' => 500,
                    'message' => 'Server Error',
                    'error' => $e->getMessage(),
                ], 500);
            }

            //institute_for_sub
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
                // Institute_for_sub::create([
                //     'user_id' => $request->input('user_id'),
                //     'institute_id' => $lastInsertedId,
                //     'institute_for_id' => $institute_for_id,
                // ]);

            }

            //board_sub
            $institute_board_id = explode(',', $request->input('institute_board_id'));
            foreach ($institute_board_id as $value) {
                //other
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

                // Institute_board_sub::create([
                //     'user_id' => $request->input('user_id'),
                //     'institute_id' => $lastInsertedId,
                //     'board_id' => $instituteboard_id,
                // ]);
            }

            // class
            // $institute_for_class_id = explode(',', $request->input('institute_for_class_id'));
            // foreach ($institute_for_class_id as $value) {

            //     Class_sub::create([
            //         'user_id' => $request->input('user_id'),
            //         'institute_id' => $lastInsertedId,
            //         'class_id' => $value,
            //     ]);
            // }

            //medium
            // $institute_medium_id = explode(',', $request->input('institute_medium_id'));
            // foreach ($institute_medium_id as $value) {
            //     Medium_sub::create([
            //         'user_id' => $request->input('user_id'),
            //         'institute_id' => $lastInsertedId,
            //         'medium_id' => $value,
            //     ]);
            // }

            //standard

            //$standard_id = explode(',', $request->input('standard_id'));
            // foreach ($standard_id as $value) {
            //     Standard_sub::create([
            //         'user_id' => $request->input('user_id'),
            //         'institute_id' => $lastInsertedId,
            //         'standard_id' => $value,
            //     ]);
            // }

            //stream

            // if ($request->input('stream_id')) {
            //     $stream = explode(',', $request->input('stream_id'));
            //     foreach ($stream as $value) {
            //         Stream_sub::create([
            //             'user_id' => $request->input('user_id'),
            //             'institute_id' => $lastInsertedId,
            //             'stream_id' => $value,
            //         ]);
            //     }
            // }

            //subject

            $subject_id = explode(',', $request->input('subject_id'));

            foreach ($subject_id as $value) {
                try {
                    Subject_sub::create([
                        'user_id' => $request->input('user_id'),
                        'institute_id' => $lastInsertedId,
                        'subject_id' => $value,
                    ]);
                } catch (\Exception $e) {

                    Subject_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Standard_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Stream_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Standard_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Class_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Medium_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Institute_board_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Institute_for_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    Dobusinesswith_sub::where('institute_id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->delete();

                    $indel = Institute_detail::where('id', $lastInsertedId)
                        ->where('user_id', $request->input('user_id'))->forceDelete();


                    return response()->json([
                        'success' => 500,
                        'message' => 'Server Error',
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }

            return response()->json([
                'success' => 200,
                'message' => 'institute create Successfully',
                'data' => [
                    'institute_id' => $lastInsertedId,
                    'institute_name' => $institute_name,
                    'logo' => asset($imagePath)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Error creating institute',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    function get_board(Request $request)
    {
        $institute_id = $request->input('institute_id');
        $user_id = $request->input('user_id');
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $boards = Board::whereHas('boardSub', function ($query) use ($institute_id) {
            $query->where('institute_id', $institute_id);
        })
            ->with(['classes.standards.subjects'])
            ->paginate(10);

        $board_array = [];

        foreach ($boards as $board) {
            $class_array = [];

            foreach ($board->classes as $class) {
                $standard_array = [];

                foreach ($class->standards as $standard) {
                    $subject_array = $standard->subjects->map(function ($subject) {
                        return [
                            'subject_id' => $subject->id,
                            'standard_id' => $subject->standard_id,
                            'subject_name' => $subject->name,
                        ];
                    })->all();

                    $standard_array[] = [
                        'standard_id' => $standard->id,
                        'class_id' => $standard->class_id,
                        'standard_name' => $standard->name,
                        'subject_array' => $subject_array,
                    ];
                }

                $class_array[] = [
                    'class_id' => $class->id,
                    'board_id' => $class->board_id,
                    'class_name' => $class->name,
                    'standard_array' => $standard_array,
                ];
            }

            $board_array[] = [
                'board_id' => $board->id,
                'board_name' => $board->name,
                'class_array' => $class_array,
            ];
        }
        $existingUser = User::where('token', $token)->first();
        if ($existingUser) {

            $bannerlist = Banner_model::where('user_id', $user_id)->get();
            if ($bannerlist) {
                foreach ($bannerlist as $value) {
                    $banner_array[] = array(
                        'banner_url' => asset($value->banner_image),

                    );
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Successfully fetch data.',
                'institute_for' => '',
                'banner_array' => $banner_array,
                'board_array' => $board_array


            ], 200, [], JSON_NUMERIC_CHECK);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }
    }

    function get_class(Request $request)
    {
        $institute_id = $request->input('institute_id');
        $user_id = $request->input('user_id');
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }


        $existingUser = User::where('token', $token)->where('id', $request->input('user_id'))->first();

        if ($existingUser) {

            $classlist = DB::table('class')
                ->join('class_sub', 'class_sub.class_id', '=', 'class.id')
                ->where('class_sub.institute_id', $institute_id)
                ->where('class_sub.user_id', $user_id)
                ->select('class.*')
                ->paginate(10);

            $class_array = [];

            foreach ($classlist as $classItem) {
                $standardlist = DB::table('standard')
                    ->join('standard_sub', 'standard.id', '=', 'standard_sub.standard_id')
                    ->where('standard.class_id', $classItem->id)
                    ->where('standard_sub.institute_id', $institute_id)
                    ->where('standard_sub.user_id', $user_id)
                    ->select('standard.*')
                    ->get();


                $standard_array = [];

                foreach ($standardlist as $standardItem) {
                    $streamlist = DB::table('stream')
                        ->join('stream_sub', 'stream_sub.stream_id', '=', 'stream.id')
                        ->where('stream.standard_id', $standardItem->id)
                        ->where('stream_sub.institute_id', $institute_id)
                        ->where('stream_sub.user_id', $user_id)
                        ->select('stream.*')
                        ->get();

                    $subjectlist = DB::table('subject')
                        ->join('subject_sub', 'subject_sub.subject_id', '=', 'subject.id', 'left')
                        ->where('subject.standard_id', $standardItem->id)
                        ->select('subject.*')
                        ->paginate(10);
                    $subject_array = [];
                    $stream_array = [];
                    foreach ($streamlist as $streamItem) {
                        $stream_array[] = [
                            'stream_id' => $streamItem->id,
                            'stream_name' => $streamItem->name,
                            'subject' => $subject_array,
                        ];
                        foreach ($subjectlist as $subjectItem) {

                            $subject_array[] = [
                                'subject_id' => $subjectItem->id,
                                'subject_name' => $subjectItem->name,

                            ];
                        }
                    }
                    $standard_array[] = [
                        'standard_id' => $standardItem->id,
                        'standard_name' => $standardItem->name,
                        'stream' => $stream_array,
                    ];
                }
                $class_array[] = [
                    'class_id' => $classItem->id,
                    'class_name' => $classItem->name,
                    'standard' => $standard_array,
                ];
            }
            return response()->json([
                'success' => 200,
                'message' => 'Fetch Class successfully',
                'data' =>  $class_array,

            ], 200);
        } else {
            return response()->json([
                'success' => 500,
                'message' => 'No found data.',
            ], 500);
        }
    }
    function get_homescreen_first(Request $request)
    {

        $institute_id = $request->input('institute_id');
        $user_id = $request->input('user_id');

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }


        $existingUser = User::where('token', $token)->where('id', $request->input('user_id'))->first();

        if ($existingUser) {
            if (empty($institute_id)) {
                $institute_id = Institute_detail::where('user_id', $user_id)->select('id')->first();
            }
            // Institute_detail::where();
            $boarids = Institute_board_sub::where('user_id', $user_id)
                ->where('institute_id', $institute_id)->pluck('board_id')->toArray();
            $uniqueBoardIds = array_unique($boarids);

            $board_list = DB::table('board')
                ->whereIN('id', $uniqueBoardIds)
                ->get();

            $board_array = [];
            foreach ($board_list as $board_value) {

                $medium_sublist = DB::table('medium_sub')
                    ->where('user_id', $user_id)
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
                        'medium_icon' => asset($medium_value->icon)
                    ];
                }
                $board_array[] = [
                    'id' => $board_value->id,
                    'board_name' => $board_value->name,
                    'board_icon' => asset($board_value->icon),
                    'medium' => $medium_array,

                    // Include banner_array inside board_array
                ];
            }
            $banner_list = Banner_model::where('user_id', $user_id)
                ->where('institute_id', $institute_id)
                ->get();
            if ($banner_list->isEmpty()) {
                $banner_list = Banner_model::where('status', 'active')
                    ->where('user_id', '1')
                    ->get();
            }
            $banner_array = [];

            foreach ($banner_list as $value) {
                $banner_array[] = [
                    'id' => $value->id,
                    'banner_image' => asset($value->banner_image),
                    'url' => $value->url . '',
                ];
            }

            //announcement
            $announcement = [];
            $fifteenDaysAgo = Carbon::now()->subDays(15);

            $announcement_list = announcements_model::where('institute_id', $institute_id)->where('created_at', '>=', $fifteenDaysAgo)->orderBy('created_at', 'desc')->get()->toarray();
            foreach ($announcement_list as $value) {
                $announcement = [
                    'title' => $value['title'],
                    'message' => $value['detail']
                ];
            }

            $response = [
                'banner' => $banner_array,
                'board' => $board_array,
                'announcement' => $announcement
            ];
            return response()->json([
                'success' => 200,
                'message' => 'Fetch Board successfully',
                // 'banner' => $banner_array,
                'data' => $response,
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }
    }

    public function get_homescreen_second(Request $request)
    {
        $institute_id = $request->input('institute_id');
        $user_id = $request->input('user_id');
        $board_id = $request->input('board_id');
        $medium_id = $request->input('medium_id');

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }


        $existingUser = User::where('token', $token)->where('id', $request->input('user_id'))->first();
        // echo "<pre>";print_r($existingUser);exit;
        if ($existingUser) {
            if (empty($institute_id)) {
                $institute_id = Institute_detail::where('user_id', $user_id)->first();
            }
            // Institute_detail::where();
            $standard_list = DB::table('standard_sub')
                ->join('standard', 'standard_sub.standard_id', '=', 'standard.id')
                ->select('standard.*')
                ->where('standard_sub.user_id', $user_id)
                ->where('standard_sub.institute_id', $institute_id)
                ->where('standard_sub.board_id', $board_id)
                ->where('standard_sub.medium_id', $medium_id)
                ->get();
            // print_r($standard_list);exit;

            $standard_array = [];
            foreach ($standard_list as $standard_value) {

                $getbsiqy = Base_table::where('board', $board_id)
                    ->where('medium', $medium_id)
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
                    // Include banner_array inside board_array
                ];
            }
            return response()->json([
                'success' => 200,
                'message' => 'Fetch Standard successfully',
                'data' => $standard_array,
            ], 200);
        } else {

            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }
    }
    public function get_request_list(Request $request)
    {
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        // echo "<pre>";print_r($existingUser);exit;
        if ($existingUser) {
            $institute_id = $request->institute_id;
            $request_list = Student_detail::where('institute_id', $institute_id)
                ->where('status', '0')
                ->get()->toarray();
            if (!empty($request_list)) {
                foreach ($request_list as $value) {
                    $user_data = User::where('id', $value['student_id'])->get()->toarray();
                    $response = [];
                    foreach ($user_data as $value2) {
                        if (!empty($value2['image'])) {
                            $image = asset($value2['image']);
                        } else {
                            $image = asset('default.jpg');
                        }
                        $response[] = [
                            'student_id' => $value2['id'],
                            'name' => $value2['firstname'] . ' ' . $value2['lastname'],
                            'photo' => $image,
                        ];
                    }
                    return response()->json([
                        'status' => 200,
                        'message' => 'Fetch student request list.',
                        'data' => $response,
                    ], 200, [], JSON_NUMERIC_CHECK);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'No data Found.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    public function get_reject_request_list(Request $request)
    {
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        // echo "<pre>";print_r($existingUser);exit;
        if ($existingUser) {

            $institute_id = $request->institute_id;
            $request_list = Student_detail::where('institute_id', $institute_id)
                ->where('status', '2')
                ->pluck('student_id');

            if (!empty($request_list)) {

                $user_data = User::whereIN('id', $request_list)->get();
                $response = [];
                foreach ($user_data as $value2) {
                    if (!empty($value2['image'])) {
                        $image = asset($value2['image']);
                    } else {
                        $image = asset('default.jpg');
                    }
                    $response[] = [
                        'student_id' => $value2['id'],
                        'name' => $value2['firstname'] . ' ' . $value2['lastname'],
                        'photo' => $image,
                    ];
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Fetch student Reject list.',
                    'data' => $response,
                ], 200, [], JSON_NUMERIC_CHECK);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'No data Found.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    public function get_reject_request(Request $request)
    {
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            $response = Student_detail::where('institute_id', $request->institute_id)->where('student_id', $request->student_id)->first();
            $reject_list = Student_detail::find($response->id);
            $data = $reject_list->update(['status' => '2']);
            if (!empty($data)) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully Reject Request.',
                ], 200, [], JSON_NUMERIC_CHECK);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    public function fetch_student_detail(Request $request)
    {
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        $institute_id = $request->institute_id;
        $user_id = $request->user_id;
        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            $user_list = User::where('id', $request->student_id)->first();
            if ($user_list) {
                $institute_for = Institute_for_model::join('institute_for_sub', 'institute_for.id', '=', 'institute_for_sub.institute_for_id')
                    ->where('institute_for_sub.institute_id', $institute_id)
                    ->where('institute_for_sub.user_id', $user_id)
                    ->select('institute_for.*')->get();

                $institute_for_list = [];
                foreach ($institute_for as $institute_for_value) {
                    $institute_for_list[] = [
                        'id' => $institute_for_value['id'],
                        'name' => $institute_for_value['name'],
                    ];
                }
                $board = board::join('board_sub', 'board.id', '=', 'board_sub.board_id')
                    ->where('board_sub.institute_id', $institute_id)
                    ->where('board_sub.user_id', $user_id)
                    ->select('board.*')->get()->toarray();
                $board_list = [];
                foreach ($board as $board_value) {
                    $board_list[] = [
                        'id' => $board_value['id'],
                        'name' => $board_value['name'],
                    ];
                }
                $medium = Medium_model::join('medium_sub', 'medium.id', '=', 'medium_sub.medium_id')
                    ->where('medium_sub.institute_id', $institute_id)
                    ->where('medium_sub.user_id', $user_id)
                    ->select('medium.*')->get();
                $medium_list = [];
                foreach ($medium as $medium_value) {
                    $medium_list[] = [
                        'id' => $medium_value['id'],
                        'name' => $medium_value['name'],
                    ];
                }
                $class = Class_model::join('class_sub', 'class.id', '=', 'class_sub.class_id')
                    ->where('class_sub.institute_id', $institute_id)
                    ->where('class_sub.user_id', $user_id)
                    ->select('class.*')->get();

                $class_list = [];
                foreach ($class as $class_value) {
                    $class_list[] = [
                        'id' => $class_value['id'],
                        'name' => $class_value['name'],
                    ];
                }
                $standard = Standard_model::join('standard_sub', 'standard.id', '=', 'standard_sub.standard_id')
                    ->where('standard_sub.institute_id', $institute_id)
                    ->where('standard_sub.user_id', $user_id)
                    ->select('standard.*')->get();

                $standard_list = [];
                foreach ($standard as $standard_value) {
                    $standard_list[] = [
                        'id' => $standard_value['id'],
                        'name' => $standard_value['name'],
                    ];
                }
                $stream = Stream_model::join('stream_sub', 'stream.id', '=', 'stream_sub.stream_id')
                    ->where('stream_sub.institute_id', $institute_id)
                    ->where('stream_sub.user_id', $user_id)
                    ->select('stream.*')->get();
                $stream_list = [];
                foreach ($stream as $stream_value) {
                    $stream_list[] = [
                        'id' => $stream_value['id'],
                        'name' => $stream_value['name'],
                    ];
                }
                $subject = Subject_model::join('subject_sub', 'subject.id', '=', 'subject_sub.subject_id')
                    ->where('subject_sub.institute_id', $institute_id)
                    ->where('subject_sub.user_id', $user_id)
                    ->select('subject.*')->get();
                $subject_list = [];
                foreach ($subject as $subject_value) {
                    $subject_list[] = [
                        'id' => $subject_value['id'],
                        'name' => $subject_value['name'],
                    ];
                }

                $response_data = [
                    'id' => $user_list->id,
                    'first_name' => $user_list->firstname,
                    'last_name' => $user_list->lastname,
                    'date_of_birth' => date('d-m-Y', strtotime($user_list->dob)),
                    'address' => $user_list->address,
                    'email' => $user_list->email,
                    'mobile_no' => $user_list->mobile,
                    'institute_for' => $institute_for_list,
                    'board' => $board_list,
                    'medium' => $medium_list,
                    'class_list' => $class_list,
                    'standard_list' => $standard_list,
                    'stream_list' => $stream_list,
                    'subject_list' => $subject_list

                ];
                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully Fetch data.',
                    'data' => $response_data
                ], 200, [], JSON_NUMERIC_CHECK);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'No Data Found.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    public function add_student(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        // echo "<pre>";print_r($request->all());exit;
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        $institute_id = $request->institute_id;
        $user_id = $request->user_id;
        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            try {

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

                $studentdtls = Student_detail::where('student_id', $student_id)
                    ->where('institute_id', $institute_id)->first();

                // if ($request->stream_id != null) {
                //     $stream_id = $request->stream_id;
                // } else {
                //     $stream_id = null;
                // }

                $insdelQY = Standard_sub::where('board_id', $request->board_id)
                    ->where('medium_id', $request->medium_id)
                    ->where('standard_id', $request->standard_id)
                    ->where('institute_id', $institute_id)
                    ->first();

                if (!empty($studentdtls)) {

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

                    if ($request->stream_id == 'null') {
                        unset($studentupdetail['stream_id']);
                    }

                    $studentdetail = Student_detail::where('student_id', $student_id)
                        ->where('institute_id', $institute_id)->update([$studentupdetail]);

                    if (!empty($studentdetail) && !empty($request->first_name)) {
                        //student detail update
                        $student_details = User::find($student_id);
                        $data = $student_details->update([
                            'firstname' => $request->first_name,
                            'lastname' => $request->last_name,
                            'dob' => $request->date_of_birth,
                            'address' => $request->address,
                            'email' => $request->email_id,
                            'mobile' => $request->mobile_no,
                        ]);

                        $response = Student_detail::where('institute_id', $request->institute_id)
                            ->where('student_id', $request->student_id)->first();

                        $reject_list = Student_detail::find($response->id);
                        $data = $reject_list->update(['status' => '1']);

                        return response()->json([
                            'status' => 200,
                            'message' => 'Successfully Update Student.',
                        ], 200, [], JSON_NUMERIC_CHECK);
                    } else {
                        return response()->json([
                            'status' => 400,
                            'message' => 'Not Inserted.',
                        ]);
                    }
                } else {

                    if ($existingUser->role_type != 6 && empty($request->student_id)) {
                        $data = user::create([
                            'firstname' => $request->first_name,
                            'lastname' => $request->last_name,
                            'dob' => $request->date_of_birth,
                            'address' => $request->address,
                            'email' => $request->email_id,
                            'mobile' => $request->mobile_no,
                        ]);
                        $student_id = $data->id;
                    } else {
                        $student_id = $student_id;
                    }
                    $student_id = $request->user_id;
                    //print_r($student_id);exit;
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

                        if ($request->stream_id == 'null') {

                            unset($studentdetail['stream_id']);
                        }

                        $studentdetailadd = Student_detail::create($studentdetail);

                        return response()->json([
                            'status' => 200,
                            'message' => 'Successfully Insert Student.',
                        ], 200, [], JSON_NUMERIC_CHECK);
                    } else {
                        return response()->json([
                            'status' => 400,
                            'message' => 'Not Inserted.',
                        ]);
                    }
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 500,
                    'message' => 'Something went wrong',
                    'data' => array('error' => $e->getMessage()),
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }

    //institute all detail
    public function institute_details(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {

            $institute_id = $request->institute_id;

            $instituteDTS = Institute_detail::where('id', $institute_id)->first();
            $user_id = $instituteDTS->user_id;

            $institute_for = Institute_for_model::join('institute_for_sub', 'institute_for.id', '=', 'institute_for_sub.institute_for_id')
                ->where('institute_for_sub.institute_id', $institute_id)
                ->where('institute_for_sub.user_id', $user_id)
                ->select('institute_for.*')->get();
            $institute_fors = [];
            foreach ($institute_for as $inst_forsd) {
                $board = board::join('board_sub', 'board.id', '=', 'board_sub.board_id')
                    ->where('board_sub.institute_id', $institute_id)
                    ->where('board_sub.user_id', $user_id)
                    ->where('board_sub.institute_for_id', $inst_forsd->id)
                    ->select('board.*')->get();
                $boards = [];
                foreach ($board as $boardsdt) {
                    $medium = Medium_model::join('medium_sub', 'medium.id', '=', 'medium_sub.medium_id')
                        ->where('medium_sub.institute_id', $institute_id)
                        ->where('medium_sub.user_id', $user_id)
                        ->where('medium_sub.institute_for_id', $inst_forsd->id)
                        ->where('medium_sub.board_id', $boardsdt->id)
                        ->select('medium.*')->get();
                    $mediums = [];
                    foreach ($medium as $mediumdt) {
                        $class = Class_model::join('class_sub', 'class.id', '=', 'class_sub.class_id')
                            ->where('class_sub.institute_id', $institute_id)
                            ->where('class_sub.user_id', $user_id)
                            ->where('class_sub.institute_for_id', $inst_forsd->id)
                            ->where('class_sub.board_id', $boardsdt->id)
                            ->where('class_sub.medium_id', $mediumdt->id)
                            ->select('class.*')->get();
                        $classs = [];
                        foreach ($class as $classdt) {

                            $standard = Standard_model::join('standard_sub', 'standard.id', '=', 'standard_sub.standard_id')
                                ->where('standard_sub.institute_id', $institute_id)
                                ->where('standard_sub.user_id', $user_id)
                                ->where('standard_sub.institute_for_id', $inst_forsd->id)
                                ->where('standard_sub.board_id', $boardsdt->id)
                                ->where('standard_sub.medium_id', $mediumdt->id)
                                ->where('standard_sub.class_id', $classdt->id)
                                ->select('standard.*')->get();

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
                                    ->select('stream.*')->get();
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
                                    ->select('subject.*')->get();
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
                        'name' => $boardsdt->name, 'medium' => $mediums
                    );
                }
                $institute_fors[] = array(
                    'id' => $inst_forsd->id,
                    'name' => $inst_forsd->name, 'boards' => $boards
                );
            }



            $alldata = array(
                'institute_fors' => $institute_fors,
                'boards' => $boards,
                'mediums' => $mediums,
                'classs' => $classs,
                'streams' => $streams,
                'subjects' => $subjects,
                'standards' => $standards
            );

            return response()->json([
                'status' => 200,
                'message' => 'Successfully fetch Data.',
                'data' => $alldata
            ], 200, [], JSON_NUMERIC_CHECK);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    //student list for add exam marks
    public function student_list_for_add_marks(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'user_id' => 'required',
            'exam_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            $institute_id = $request->institute_id;
            $user_id = $request->user_id;
            $exam_id = $request->exam_id;
            $examdt = Exam_Model::where('id', $exam_id)->first();

            if (!empty($examdt)) {
                $studentDT = Student_detail::join('users', 'users.id', '=', 'students_details.student_id')
                    ->join('standard', 'standard.id', '=', 'students_details.standard_id')
                    ->where('students_details.institute_id', $institute_id)
                    ->where('students_details.user_id', $user_id)
                    ->where('students_details.board_id', $examdt->board_id)
                    ->where('students_details.medium_id', $examdt->medium_id)
                    ->where('students_details.class_id', $examdt->class_id)
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
                        'marks' => (float)$marksofstd->mark,
                        'firstname' => $stddt->firstname,
                        'lastname' => $stddt->lastname,
                        'total_mark' => $examdt->total_mark,
                        'standard' => $stddt->standardname,
                        'subject' => $subjectqy->name
                    );
                }
                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch Data.',
                    'data' => $studentsDET
                ], 200, [], JSON_NUMERIC_CHECK);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Exam not found.',
                    'data' => []
                ], 400, [], JSON_NUMERIC_CHECK);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    //student list with marks
    public function student_list_with_marks(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'exam_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            //$student_id = $request->student_id;
            $user_id = $request->user_id;
            $examid = $request->exam_id;
            $examdtr = Exam_Model::where('id', $examid)->first();

            if (!empty($examdtr)) {
                $marksdt = Marks_model::join('users', 'users.id', '=', 'marks.student_id')
                    ->where('marks.exam_id', $examid)
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
                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch Data.',
                    'data' => $studentsDET
                ], 200, [], JSON_NUMERIC_CHECK);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Exam not found.',
                    'data' => []
                ], 400, [], JSON_NUMERIC_CHECK);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    //add exam marks
    public function add_marks(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'user_id' => 'required',
            'student_id' => 'required',
            'exam_id' => 'required',
            'mark' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            $institute_id = $request->institute_id;
            $user_id = $request->user_id;
            $student_id = $request->student_id;
            $exam_id = $request->exam_id;
            $mark = $request->mark;

            $addesmarks = Marks_model::where('student_id', $student_id)->where('exam_id', $exam_id)->first();
            if ($addesmarks) {
                $admarks = Marks_model::where('id', $addesmarks->id)->update([
                    'student_id' => $student_id,
                    'exam_id' => $exam_id,
                    'mark' => $mark,
                ]);
            } else {
                $admarks = Marks_model::create([
                    'student_id' => $student_id,
                    'exam_id' => $exam_id,
                    'mark' => $mark,
                ]);
            }


            if ($admarks) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Added.',
                    'data' => []
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Data not added.',
                    'data' => []
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
                'data' => []
            ]);
        }
    }

    //add announcements
    public function add_announcements(Request $request)
    {
        // echo "<pre>";print_r($request->all());exit;
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            //'institute_for_id' => 'required',
            //'class_id' => 'required',
            //'stream_id' => 'required',
            'subject_id' => 'required',
            'role_type' => 'required',
            'title' => 'required',
            'detail' => 'required',
            'standard_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();

        if ($existingUser) {
            $user_id = $request->user_id;
            $institute_id = $request->institute_id;
            $board_id = $request->board_id;
            $medium_id = $request->medium_id;
            // $institute_for_id = $request->institute_for_id;
            //$class_id = $request->class_id;
            $stream_id = $request->stream_id;
            $subject_id = $request->subject_id;
            $role_type = $request->role_type;
            $title = $request->title;
            $detail = $request->detail;
            $standard_id = $request->standard_id;
            $batch_id = $request->batch_id;

            if ($stream_id == 'null') {
                $stream_idd = null;
            } else {
                $stream_idd = $request->stream_id;
            }
            $addannounc = announcements_model::create([
                'user_id' => $user_id,
                'institute_id' => $institute_id,
                'batch_id' => $batch_id,
                'board_id' => $board_id,
                'medium_id' => $medium_id,
                //'institute_for_id' => $institute_for_id,
                //'class_id' => $class_id,
                'stream_id' => $stream_idd,
                'subject_id' => $subject_id,
                'role_type' => $role_type,
                'title' => $title,
                'detail' => $detail,
                'standard_id' => $standard_id
            ]);

            if ($addannounc) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Announcement added successfully.',
                    'data' => []
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Data not added.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }

    //announcement list
    public function announcements_list(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            //$student_id = $request->student_id;
            $user_id = $request->user_id;
            $institute_id = $request->institute_id;
            $board_id = $request->board_id;
            $standard_id = $request->standard_id;
            $searchData = $request->searchData;

            $anoouncmntdt = announcements_model::where('user_id', $user_id)
                ->where('institute_id', $institute_id)
                ->when($searchData, function ($query, $searchData) {
                    return $query->where(function ($query) use ($searchData) {
                        $query->where('title', 'like', '%' . $searchData . '%')
                            ->orWhere('detail', 'like', '%' . $searchData . '%');
                    });
                })
                ->when($board_id, function ($query, $board_id) {
                    return $query->where(function ($query) use ($board_id) {
                        $query->where('board_id', $board_id);
                    });
                })
                ->when($standard_id, function ($query, $standard_id) {
                    return $query->where(function ($query) use ($standard_id) {
                        $query->where('standard_id', $standard_id);
                    });
                })
                ->orderByDesc('created_at')
                ->get();

            if (!empty($anoouncmntdt)) {

                $announcementDT = [];
                foreach ($anoouncmntdt as $anoouncmnt) {

                    $subjectq = Subject_model::where('id', $anoouncmnt->subject_id)->first();
                    $standardtq = Standard_model::where('id', $anoouncmnt->standard_id)->first();
                    $boarddt = board::where('id', $anoouncmnt->board_id)->first();
                    $batchnm = Batches_model::where('id', $anoouncmnt->batch_id)->first();

                    $roles = [];
                    $roledsid = explode(",", $anoouncmnt->role_type);
                    $roqy = Role::whereIN('id', $roledsid)->get();
                    foreach ($roqy as $rolDT) {
                        $roles[] = array(
                            'id' => $rolDT->id,
                            'name' => $rolDT->role_name
                        );
                    }
                       
                    $announcementDT[] = array(
                        'id' => $anoouncmnt->id,
                        'date' => $anoouncmnt->created_at,
                        'title' => $anoouncmnt->title,
                        'detail' => $anoouncmnt->detail,
                        'subject_id' => $subjectq->id,
                        'subject' => $subjectq->name,
                        'batch_id' => !empty($batchnm->id) ? $batchnm->id : 0,
                        'batch_name' => !empty($batchnm->batch_name) ? $batchnm->batch_name : '',
                        'standard_id' => $standardtq->id,
                        'standard' => $standardtq->name,
                        'board_id' => $boarddt->id,
                        'board' => $boarddt->name,
                        'role' => $roles
                    );
                }
                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch Data.',
                    'data' => $announcementDT
                ], 200, [], JSON_NUMERIC_CHECK);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Exam not found.',
                ], 400, [], JSON_NUMERIC_CHECK);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }

    //add time table
    public function add_time_table(Request $request)
    {
    }

    public function delete_account(Request $request)
    {

        $userId = $request->user_id;
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $userId)->first();
        if ($existingUser) {

            user::where('id', $userId)->delete();
            return response()->json([
                'status' => '200',
                'message' => 'Delete Account Successfully!',
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }
    }

    //roles list
    public function roles(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();

        if ($existingUser) {
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
                return response()->json([
                    'status' => '200',
                    'message' => 'Data Fetch Successfully',
                    'data' => $rolesDT
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => '200',
                    'message' => 'Something went wrong',
                    'data' => []
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ], 400);
        }
    }

    //student list all and filter wise
    public function institute_students(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
            
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            try {
                $user_id = $request->user_id;
                $institute_id = $request->institute_id;

                //filter fields
                $board = $request->board_id;
                $medium = $request->medium_id;
                $standard = $request->standard_id;
                $batch_id = $request->batch_id;
                $searchkeyword = $request->searchkeyword;
                $perPage = $request->input('per_page', 10);

                $students = Student_detail::join('users', 'users.id', 'students_details.student_id')
                    ->join('board', 'board.id', 'students_details.board_id')
                    ->join('medium', 'medium.id', 'students_details.medium_id')
                    ->join('standard', 'standard.id', 'students_details.standard_id')
                    ->leftjoin('batches', 'batches.id', 'students_details.batch_id')
                    ->where('students_details.user_id', $user_id)
                    ->where('students_details.institute_id', $institute_id)
                    ->when($board, function ($query, $board) {
                        return $query->where('students_details.board_id', $board);
                    })
                    ->when($medium, function ($query, $medium) {
                        return $query->where('students_details.medium_id', $medium);
                    })
                    ->when($standard, function ($query, $standard) {
                        return $query->where('students_details.standard_id', $standard);
                    })
                    ->when($batch_id, function ($query, $batch_id) {
                        return $query->where('students_details.batch_id', $batch_id);
                    })
                    ->when($searchkeyword, function ($query, $searchkeyword) {
                        return $query->where(function ($query) use ($searchkeyword) {
                            $query->where('users.firstname', 'like', '%' . $searchkeyword . '%')
                                ->orWhere('users.lastname', 'like', '%' . $searchkeyword . '%')
                                ->orWhere('users.unique_id', 'like', '%' . $searchkeyword . '%');
                        });
                    })
                    ->select('students_details.*','batches.batch_name', 'users.firstname', 'users.lastname', 'board.name as board', 'medium.name as medium', 'standard.name as standard')
                    ->orderByDesc('students_details.created_at')
                    ->paginate($perPage);

                $stulist = [];
                foreach ($students as $stdDT) {
                    $stulist[] = array(
                        'id' => $stdDT->student_id,
                        'name' => $stdDT->firstname . ' ' . $stdDT->lastname,
                        'board_id' => $stdDT->board_id,
                        'board' => $stdDT->board . '(' . $stdDT->medium . ')',
                        'standard_id' => $stdDT->standard_id,
                        'standard' => $stdDT->standard,
                        'batch_id'=>$stdDT->batch_id,
                        'batch_name'=>$stdDT->batch_name,
                    );
                }

                return response()->json([
                    'status' => '200',
                    'message' => 'Data Fetch Successfully',
                    'data' => $stulist
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 500,
                    'message' => 'Server Error',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }

    public function filters_data(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            try {

                $user_id = $request->user_id;
                $institute_id = $request->institute_id;


                $boarids = Institute_board_sub::where('user_id', $user_id)
                    ->where('institute_id', $institute_id)->pluck('board_id')->toArray();
                $uniqueBoardIds = array_unique($boarids);

                $board_list = DB::table('board')
                    ->whereIN('id', $uniqueBoardIds)
                    ->get();

                $board_array = [];
                foreach ($board_list as $board_value) {

                    $medium_sublist = DB::table('medium_sub')
                        ->where('user_id', $user_id)
                        ->where('board_id', $board_value->id)
                        ->where('institute_id', $institute_id)
                        ->pluck('medium_id')->toArray();
                    $uniquemediumds = array_unique($medium_sublist);

                    $medium_list = Medium_model::whereIN('id', $uniquemediumds)->get();

                    $medium_array = [];
                    foreach ($medium_list as $medium_value) {

                        $stndQY = Standard_sub::join('standard', 'standard.id', 'standard_sub.standard_id')
                            ->where('standard_sub.user_id', $user_id)
                            ->where('standard_sub.institute_id', $institute_id)
                            ->where('standard_sub.board_id', $board_value->id)
                            ->where('standard_sub.medium_id', $medium_value->id)->select('standard.id as std_id', 'standard.name as std_name')->get();
                        $stddata = [];
                        foreach ($stndQY as $stndDT) {
                            $forcounstd = Student_detail::whereNull('deleted_at')
                                ->where('user_id', $user_id)
                                ->where('institute_id', $institute_id)
                                ->where('board_id', $board_value->id)
                                ->where('medium_id', $medium_value->id)
                                ->get();
                            $stdCount = $forcounstd->count();

                            $stddata[] = array(
                                'id' => $stndDT->std_id,
                                'name' => $stndDT->std_name,
                                'no_of_std' => $stdCount
                            );
                        }

                        $medium_array[] = [
                            'id' => $medium_value->id,
                            'medium_name' => $medium_value->name,
                            'standard' => $stddata
                        ];
                    }

                    $board_array[] = [
                        'id' => $board_value->id,
                        'board_name' => $board_value->name,
                        'medium' => $medium_array,

                        // Include banner_array inside board_array
                    ];
                }

                return response()->json([
                    'status' => '200',
                    'message' => 'Data Fetch Successfully',
                    'data' => $board_array
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 500,
                    'message' => 'Server Error',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    public function institute_profile(Request $request)
    {
        $institute_id = $request->institute_id;
        $user_id = $request->user_id;
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $user_id)->first();
        if ($existingUser) {
            $institute_detail = Institute_detail::where('institute_detail.id', $institute_id)
                ->select('institute_detail.*')
                ->get()->toarray();


            if (empty($institute_id)) {
                $institute_id = Institute_detail::where('user_id', $user_id)->select('id')->first();
            }
            // Institute_detail::where();
            $boarids = Institute_board_sub::where('user_id', $user_id)
                ->where('institute_id', $institute_id)->pluck('board_id')->toArray();
            $uniqueBoardIds = array_unique($boarids);

            $board_list = DB::table('board')
                ->whereIN('id', $uniqueBoardIds)
                ->get();

            $board_array = [];
            foreach ($board_list as $board_value) {

                $medium_sublist = DB::table('medium_sub')
                    ->where('user_id', $user_id)
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
                    'medium' => $medium_array,

                    // Include banner_array inside board_array
                ];
            }
            $institute_response = [];
            foreach ($institute_detail as $value) {
                $institute_response[] = [
                    'institute_name' => $value['institute_name'],
                    'address' => $value['address'] . '',
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
                    'cover_photo' => ($value['cover_photo'] ? url($value['cover_photo']) : url('cover_blank_image.png'))
                ];
            }
            return response()->json([
                'status' => '200',
                'message' => 'Institute Fetch Successfully',
                'data' => $institute_response
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    //institute profile edit
    public function institute_profile_edit(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
            'institute_name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'contact_no' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            try {
                $user_id = $request->user_id;
                $institute_id = $request->institute_id;
                $institutedt = Institute_detail::find($institute_id);
                if ($institutedt) {
                    $institutedt->institute_name = $request->institute_name;
                    $institutedt->address = $request->address;
                    $institutedt->contact_no = $request->contact_no;
                    $institutedt->email = $request->email;
                    $institutedt->about_us = $request->about_us;
                    //$institutedt->about_us = $request->country;
                    //$institutedt->state = $request->state;
                    //$institutedt->city = $request->city;
                    //$institutedt->pincode = $request->pincode;                 
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

                    return response()->json([
                        'status' => 200,
                        'message' => 'Record Update Successfully!.',
                        'data' => []
                    ]);
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Record not found.',
                        'data' => []
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 500,
                    'message' => 'Server Error',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    //category list for add do business with 
    public function category_list(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            try {

                $vcategory = VideoCategory::where('status', 'active')->get();

                $cat_array = [];
                foreach ($vcategory as $cat_value) {
                    $cat_array[] = array('id' => $cat_value->id, 'name' => $cat_value->name);
                }
                return response()->json([
                    'status' => '200',
                    'message' => 'Data Fetch Successfully',
                    'data' => $cat_array
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 500,
                    'message' => 'Server Error',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }

    //create batch
    public function create_batch(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'standard_id' => 'required',
            'batch_name' => 'required',
            'subjects' => 'required',
            'student_capacity' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            try {

                $addbatch = Batches_model::create([
                    'user_id' => $request->user_id,
                    'institute_id' => $request->institute_id,
                    'board_id' => $request->board_id,
                    'medium_id' => $request->medium_id,
                    'stream_id' => $request->stream_id, //nullable
                    'standard_id' => $request->standard_id,
                    'batch_name' => $request->batch_name,
                    'subjects' => $request->subjects,
                    'student_capacity' => $request->student_capacity
                ]);

                return response()->json([
                    'status' => '200',
                    'message' => 'Batch Added Successfully',
                    'data' => []
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 500,
                    'message' => 'Server Error',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
    public function batch_list(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
            'board_id' => 'required',
            'standard_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'errors' => $errorMessages,
            ], 400);
        }
        $token = $request->header('Authorization');
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            try {
                $batchlist = Batches_model::where('user_id', $request->user_id)
                    ->where('institute_id', $request->institute_id)
                    ->where('board_id', $request->board_id)
                    ->where('standard_id', $request->standard_id)->get()->toarray();
                $batch_response = [];
                foreach ($batchlist as $value) {
                    $batch_response[] = [
                        'id' => $value['id'],
                        'batch_name' => $value['batch_name']
                    ];
                }
                return response()->json([
                    'status' => '200',
                    'message' => 'Batch Fetch Successfully',
                    'data' => $batch_response
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => 500,
                    'message' => 'Server Error',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
}
