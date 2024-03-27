<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
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
use PHPUnit\Framework\Attributes\Medium;

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

                                    $subject_array = Subject_model::whereIN('base_table_id', $baseidsfosubj)
                                        ->get();

                                    $subject = [];
                                    foreach ($subject_array as $value) {
                                        $subject[] = [
                                            'subject_id' => $value->id,
                                            'subject' => $value->name
                                        ];
                                    }

                                    if (!empty($stream_array_value->stream_id)) {
                                        $stream[] = [
                                            'stream_id' => $stream_array_value->stream_id . '',
                                            'stream' => $stream_array_value->stream_name . '',
                                            // 'subject' => $subject_array
                                        ];
                                    }
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
            $dobusiness_with = Dobusinesswith_Model::where('status', 'active')->get();
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
            'institute_for_id' => 'required|string',
            'institute_board_id' => 'required|string',
            'institute_for_class_id' => 'required|string',
            'institute_medium_id' => 'required|string',
            'institute_work_id' => 'required|string',
            'standard_id' => 'required|string',
            'subject_id' => 'required|string',
            'institute_name' => 'required|string',
            'address' => 'required|string',
            'contact_no' => 'required|integer|min:10',
            'email' => 'required|email|unique:institute_detail,email',
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
            $instituteDetail = Institute_detail::create([
                'unique_id' => $unique_id,
                'youtube_link' => $request->input('youtube_link'),
                'whatsaap_link' => $request->input('whatsaap_link'),
                'facebook_link' => $request->input('facebook_link'),
                'instagram_link' => $request->input('instagram_link'),
                'website_link' => $request->input('website_link'),
                'gst_slab' => $request->input('gst_slab'),
                'gst_number' => $request->input('gst_number'),
                'close_time' => $request->input('close_time'),
                'open_time' => $request->input('open_time'),
                'logo' => $imagePath,
                'user_id' => $request->input('user_id'),
                'institute_name' => $request->input('institute_name'),
                'address' => $request->input('address'),
                'contact_no' => $request->input('contact_no'),
                'email' => $request->input('email'),
                'status' => 'active'
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


                Institute_for_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                ]);

                Institute_board_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                    'board_id' => $board,
                ]);

                Medium_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                    'board_id' => $board,
                    'medium_id' => $medium,
                ]);

                Class_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                    'board_id' => $board,
                    'medium_id' => $medium,
                    'class_id' => $institute_for_class,
                ]);

                Standard_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for,
                    'board_id' => $board,
                    'medium_id' => $medium,
                    'class_id' => $institute_for_class,
                    'standard_id' => $standard,
                ]);

                if ($stream != null) {

                    Stream_sub::create([
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

            //end new code

            //dobusiness
            $institute_work_id = explode(',', $request->input('institute_work_id'));
            foreach ($institute_work_id as $value) {
                Dobusinesswith_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'do_business_with_id' => $value,
                ]);
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
                Subject_sub::create([
                    'user_id' => $request->input('user_id'),
                    'institute_id' => $lastInsertedId,
                    'subject_id' => $value,
                ]);
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
                    ];
                }
                $board_array[] = [
                    'id' => $board_value->id,
                    'board_name' => $board_value->name,
                    'medium' => $medium_array,

                    // Include banner_array inside board_array
                ];
            }
            $banner_list = Banner_model::where('user_id', $user_id)
                ->where('institute_id', $institute_id)
                ->get();
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
            $response = [
                'banner' => $banner_array,
                'board' => $board_array,
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
                $standard_array[] = [
                    'id' => $standard_value->id,
                    'standard_name' => $standard_value->name,
                    'subject' => $subject_array,
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
        // echo "<pre>";print_r($request->all());exit;
        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        $institute_id = $request->institute_id;
        $user_id = $request->user_id;
        $existingUser = User::where('token', $token)->where('id', $request->user_id)->first();
        if ($existingUser) {
            $user_id = $request->user_id;
            $institute_id = $request->institute_id;
            $student_id = $request->student_id;
            $studentdetail = Student_detail::create([
                'user_id' => $user_id,
                'institute_id' => $request->institute_id,
                'student_id' => $student_id,
                'institute_for_id' => $request->institute_for_id,
                'board_id' =>  $request->board_id,
                'medium_id' => $request->medium_id,
                'class_id' => $request->class_id,
                'standard_id' => $request->standard_id,
                'stream_id' => $request->stream_id,
                'subject_id' => $request->subject_id,
                'status' => '1',
            ]);

            if (!empty($studentdetail)) {
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


                $response = Student_detail::where('institute_id', $request->institute_id)->where('student_id', $request->student_id)->first();
                $reject_list = Student_detail::find($response->id);
                $data = $reject_list->update(['status' => '1']);
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
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
    }
}
