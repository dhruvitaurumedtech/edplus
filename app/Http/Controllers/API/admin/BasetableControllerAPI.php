<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Base_table;
use App\Models\board;
use App\Models\Class_model;
use App\Models\Class_sub;
use App\Models\Institute_board_sub;
use App\Models\Institute_for_model;
use App\Models\Institute_for_sub;
use App\Models\Medium_model;
use App\Models\Medium_sub;
use App\Models\Standard_model;
use App\Models\Standard_sub;
use App\Models\Stream_model;
use App\Models\Subject_model;
use App\Models\Subject_sub;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BasetableControllerAPI extends Controller
{
    use ApiTrait;
    public function institute_for(Request $request)
    {
        try {
            $base_institutfor = Institute_for_model::join('base_table', 'base_table.institute_for', '=', 'institute_for.id')
                ->select('institute_for.id', 'institute_for.name', 'institute_for.icon')
                ->distinct()
                ->get();
            $data = [];
            foreach ($base_institutfor as $basedata) {
                $data[] = array('id' => $basedata->id, 'name' => $basedata->name, 'icon' => url($basedata->icon));
            }
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }

    public function board(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'institute_for_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $getBoardsId  = Base_table::whereIn('institute_for', $institute_for_ids)->distinct()->pluck('board');
            $base_board = board::whereIn('id', $getBoardsId)->get();
            foreach ($base_board as $baseboard) {
                $data[] = array('id' => $baseboard->id,
                 'name' => $baseboard->name,
                 'institute_for_id'=>$baseboard->institute_for,
                 'icon' => url($baseboard->icon));
            }

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }

    public function medium(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $getBoardsId  = Base_table::whereIn('institute_for', $institute_for_ids)->whereIn('board', $board_ids)->distinct()->pluck('medium');
            $base_medium = Medium_model::whereIn('id', $getBoardsId)->get();
            $data = [];
            foreach ($base_medium as $basemedium) {
                $data[] = array(
                    'id' => $basemedium->id, 'name' => $basemedium->name,
                    'icon' => url($basemedium->icon)
                );
            }

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
    public function with_class_medium(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            // 'medium_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            // $medium_ids = explode(',', $request->medium_id);
            $getClassId  = Base_table::whereIn('institute_for', $institute_for_ids)->whereIn('board', $board_ids)->distinct()->pluck('institute_for_class');
            $base_class =  Class_model::whereIn('id', $getClassId)->get();
            $classData = [];
            foreach ($base_class as $baseclass) {
                $classData[] = array(
                    'id' => $baseclass->id, 'name' => $baseclass->name,
                    'icon' => (!empty($baseclass->icon))?url($baseclass->icon):asset('profile/no-image.png'),
                );
            }
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $getBoardsId  = Base_table::whereIn('institute_for', $institute_for_ids)->whereIn('board', $board_ids)->distinct()->pluck('medium');
            $base_medium = Medium_model::whereIn('id', $getBoardsId)->get();
            $mediumData = [];
            foreach ($base_medium as $basemedium) {
                $mediumData[] = array(
                    'id' => $basemedium->id, 
                    'name' => $basemedium->name,
                    'icon' => (!empty($basemedium->icon))?url($basemedium->icon):asset('profile/no-image.png'),
                    'classes' => $classData,
                );
            }
            
            

            return $this->response($mediumData, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
    public function with_class_medium_subject(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_for_id.*' => 'required',
            'board_id.*' => 'required',
            'class_id.*' => 'required',
            'medium_id.*' => 'required',
       ]);

       if ($validator->fails()) {
           return $this->response([], $validator->errors()->first(), false, 400);
       }

       try {
           
          // Initialize the result array
               $data = [];

               // Example data processing logic (assuming $request->data is an array of input parameters)
               foreach ($request->data as $datas) {
                   // Query to get the base standards
                   $base_standards = Base_table::join('standard', 'standard.id', '=', 'base_table.standard')
                       ->join('institute_for', 'institute_for.id', '=', 'base_table.institute_for')
                       ->join('class', 'class.id', '=', 'base_table.institute_for_class')
                       ->join('medium', 'medium.id', '=', 'base_table.medium')
                       ->join('board', 'board.id', '=', 'base_table.board')
                       ->join('subject', 'subject.base_table_id', '=', 'base_table.id')
                       ->where('base_table.institute_for', $datas['institute_for_id'])
                       ->where('base_table.board', $datas['board_id'])
                       ->where('base_table.medium', $datas['medium_id'])
                       ->whereIn('base_table.institute_for_class', $datas['class_id'])
                       ->select(
                           'standard.id',
                           'standard.name',
                           'institute_for.id as institute_for_id',
                           'institute_for.name as institute_for_name',
                           'class.id as class_id',
                           'medium.id as medium_id',
                           'board.id as board_id',
                           'class.name as class_name',
                           'medium.name as medium_name',
                           'board.name as board_name',
                           'subject.id as subject_id',
                           'subject.name as subject_name'
                       )
                       ->get();

                   foreach ($base_standards as $base_standard) {
                       $key = $base_standard->institute_for_id . '_' . $base_standard->medium_id . '_' . $base_standard->board_id;

                       // Check if the key already exists in the data array
                       if (!array_key_exists($key, $data)) {
                           $data[$key] = [
                               'institute_for_id' => $base_standard->institute_for_id,
                               'institute_for_name' => $base_standard->institute_for_name,
                               'medium_id' => $base_standard->medium_id,
                               'medium_name' => $base_standard->medium_name,
                               'board_id' => $base_standard->board_id,
                               'board_name' => $base_standard->board_name,
                               'class_data' => [],
                           ];
                       }

                       // Check if the class already exists in the class_data array
                       $classExists = false;
                       foreach ($data[$key]['class_data'] as &$class) {
                           if ($class['class_id'] === $base_standard->class_id) {
                               $classExists = true;
                               break;
                           }
                       }

                       // If the class does not exist, add it
                       if (!$classExists) {
                           $data[$key]['class_data'][] = [
                               'class_id' => $base_standard->class_id,
                               'class_name' => $base_standard->class_name,
                               'std_data' => [],
                           ];
                       }

                       // Find the class to add standard data
                       foreach ($data[$key]['class_data'] as &$class) {
                           if ($class['class_id'] === $base_standard->class_id) {
                               // Initialize an array to keep track of added standards
                               $added_standards = array_column($class['std_data'], 'id');

                               // Check if the standard already exists
                               if (!in_array($base_standard->id, $added_standards)) {
                                   $class['std_data'][] = [
                                       'id' => $base_standard->id,
                                       'standard_name' => $base_standard->name,
                                       'subject_data' => [], 
                                   ];
                               }

                               // No need to check further classes
                               foreach ($class['std_data'] as &$std) {
                                   if ($std['id'] === $base_standard->id) {
                                       // Initialize an array to keep track of added subjects
                                       $added_subjects = array_column($std['subject_data'], 'subject_id');

                                       // Check if the subject already exists
                                       if (!in_array($base_standard->subject_id, $added_subjects)) {
                                           $std['subject_data'][] = [
                                               'subject_id' => $base_standard->subject_id,
                                               'subject_name' => $base_standard->subject_name,
                                           ];
                                       }

                                       // No need to check further standards
                                       break;
                                   }
                               }

                               // No need to check further classes
                               break;
                           }
                       }
                   }
               }

           $data = array_values($data);
           return $this->response($data, "Fetch Data Successfully");
       } catch (Exception $e) {
           return $this->response($e, "Something went Wrong!!", false, 400);
       }
    }
    public function class(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);
            $getClassId  = Base_table::whereIn('institute_for', $institute_for_ids)->whereIn('board', $board_ids)->whereIn('medium', $medium_ids)->distinct()->pluck('institute_for_class');
            $base_class =  Class_model::whereIn('id', $getClassId)->get();
            $data = [];
            foreach ($base_class as $baseclass) {
                $data[] = array(
                    'id' => $baseclass->id, 'name' => $baseclass->name,
                    'icon' => url($baseclass->icon)
                );
            }

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }


    public function standard(Request $request)
    {
        $validator = Validator::make($request->all(), [
             'institute_for_id.*' => 'required',
             'board_id.*' => 'required',
             'class_id.*' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            
            $data = [];
            
            foreach ($request->data as $datas) {
                $base_standards = Standard_model::join('base_table', 'base_table.standard', '=', 'standard.id')
                ->join('class', 'base_table.institute_for_class', '=', 'class.id')
                ->join('medium', 'base_table.medium', '=', 'medium.id')
                ->join('board', 'base_table.board', '=', 'board.id')
                ->where('base_table.institute_for', $datas['institute_for_id'])
                ->where('base_table.board', $datas['board_id'])
                ->where('base_table.medium', $datas['medium_id'])
                ->whereIN('base_table.institute_for_class', $datas['class_id'])
                ->select('standard.id', 'standard.name', 'class.name as class_name', 'medium.name as medium_name', 'board.name as board_name')
                ->distinct()
                ->get();
                  
                foreach ($base_standards as $base_standard) {
                   
                $key = $base_standard->class_name . '_' . $base_standard->medium_name . '_' . $base_standard->board_name;
                if (!array_key_exists($key, $data)) {
                    $data[$key] = [
                        'class_name' => $base_standard->class_name,
                        'medium_name' => $base_standard->medium_name,
                        'board_name' => $base_standard->board_name,
                        'std_data' => [],
                    ];
                }
                $data[$key]['std_data'][] = [
                    'id' => $base_standard->id,
                    'standard_name' => $base_standard->name,
                ];
                }
            }
            $data = array_values($data);
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went Wrong!!", false, 400);
        }
    }

    public function stream(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'class_id' => 'required',
            'standard_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);
            $class_ids = explode(',', $request->class_id);
            $standard_ids = explode(',', $request->standard_id);

            $base_stream = Stream_model::join('base_table', 'base_table.stream', '=', 'stream.id')
                ->whereIN('base_table.institute_for', $institute_for_ids)
                ->whereIN('base_table.board', $board_ids)
                ->whereIN('base_table.medium', $medium_ids)
                ->whereIN('base_table.institute_for_class', $class_ids)
                ->whereIN('base_table.standard', $standard_ids)
                ->select('stream.id', 'stream.name')
                ->distinct()
                ->get();
            $data = [];
            foreach ($base_stream as $basestream) {
                $data[] = array('id' => $basestream->id, 'name' => $basestream->name);
            }

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }

    public function subject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'class_id' => 'required',
            'standard_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);
            $class_ids = explode(',', $request->class_id);
            $standard_ids = explode(',', $request->standard_id);
            $stream_ids = explode(',', trim($request->stream));

            $base_subject_query = Subject_model::join('base_table', 'base_table.id', '=', 'subject.base_table_id')
                ->leftjoin('stream', 'base_table.stream', '=', 'stream.id')
                ->whereIn('base_table.institute_for', $institute_for_ids)
                ->whereIn('base_table.board', $board_ids)
                ->whereIn('base_table.medium', $medium_ids)
                ->whereIn('base_table.institute_for_class', $class_ids)
                ->whereIn('base_table.standard', $standard_ids);

            if (!empty($stream_ids) && array_filter($stream_ids)) {
                $base_subject_query->whereIn('base_table.stream', $stream_ids);
            }
            $base_subject = $base_subject_query
                ->select('subject.id', 'subject.name', 'subject.image', 'stream.name as stream_name', 'base_table.stream as stream_id')
                ->distinct()
                ->get();

            $data = [];
            foreach ($base_subject as $basesubject) {
                $data[] = array(
                    'id' => $basesubject->id,
                    'name' => $basesubject->name,
                    'image' => !empty($basesubject->image) ? asset($basesubject->image) : '',
                    'stream_id' => $basesubject->stream_id,
                    'stream_name' => $basesubject->stream_name
                );
            }
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }


    public function get_edit_institute_for(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'institute_id' => 'required',
            'institute_id' => 'nullable|exists:institute_detail,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $base_institutfor = Institute_for_model::join('base_table', 'base_table.institute_for', '=', 'institute_for.id')
                ->select('institute_for.id', 'institute_for.name', 'institute_for.icon','institute_for.status')
                ->distinct()
                ->get();
            $institute_base_for_ids = Institute_for_sub::where('institute_id', $request->institute_id)->pluck('institute_for_id')->toArray();
            $data = [];
            foreach ($base_institutfor as $basedata) {
                $isAdded = in_array($basedata->id, $institute_base_for_ids);
                $data[] = array('id' => $basedata->id,
                'name' => $basedata->name,
                'icon' => url($basedata->icon),
                'is_active' => $basedata->status,
                'is_added' => $isAdded);
            }
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }

    public function get_edit_board(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'institute_id' => 'required',
            'institute_id' => 'nullable|exists:institute_detail,id',
            'institute_for_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $base_board = board::join('base_table','base_table.board','=','board.id')
            ->whereIn('base_table.institute_for', $institute_for_ids)
            ->select('board.id', 'board.name','board.status','board.icon', DB::raw('MAX(base_table.institute_for) as institute_for'))
            ->groupBy('board.id', 'board.name','board.status','board.icon')->get();
            
            $institute_base_board_id = Institute_board_sub::where('institute_id', $request->institute_id)->pluck('board_id')->toArray();
            foreach ($base_board as $baseboard) {
                $isAdded = in_array($baseboard->id, $institute_base_board_id);
                $data[] = array('id' => $baseboard->id,
                'name' => $baseboard->name,
                'icon' => url($baseboard->icon),
                'institute_for_id' => $baseboard->institute_for,
                'is_active' => $baseboard->status,
                'is_added' => $isAdded);
            }
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
    public function get_edit_medium(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'nullable|exists:institute_detail,id',
            'institute_for_id' => 'required',
            'board_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);

            $base_medium = Medium_model::join('base_table','base_table.medium','=','medium.id')
            ->leftjoin('board','board.id','=','base_table.board')
            ->leftjoin('institute_for','institute_for.id','=','base_table.institute_for')
            ->whereIn('base_table.institute_for', $institute_for_ids)
            ->whereIn('base_table.board', $board_ids)
            ->select('medium.id', 'medium.name','medium.status','medium.icon',
            'base_table.institute_for','base_table.board',
            'board.name as boardname','institute_for.name as institute_for_name')
            //  DB::raw('MAX(base_table.institute_for) as institute_for'),
            //  DB::raw('MAX(base_table.board) as board'))
            // ->groupBy('medium.id', 'medium.name','medium.status','medium.icon')
            ->distinct()
            ->get();
            $institute_base_medium_id = Medium_sub::where('institute_id', $request->institute_id)->pluck('medium_id')->toArray();
            $data = [];
            foreach ($base_medium as $basemedium) {
                $isAdded = in_array($basemedium->id, $institute_base_medium_id);

                //class
                

                $base_class = Class_model::join('base_table','base_table.institute_for_class','=','class.id')
                ->where('base_table.institute_for', $basemedium->institute_for)
                ->where('base_table.board', $basemedium->board)
                ->where('base_table.medium', $basemedium->id)
                ->select('class.id', 'class.name','class.status','class.icon')
                ->groupBy('class.id', 'class.name','class.status','class.icon')->get();

                $intitute_base_class_id = Class_sub::where('institute_id', $request->institute_id)
                ->where('board_id', $basemedium->board)
                ->where('medium_id', $basemedium->id)
                ->pluck('class_id')->toArray();

                $class = [];
                foreach ($base_class as $baseclass) {
                    $isAdded = in_array($baseclass->id, $intitute_base_class_id);
                    $class[] = array(
                        'id' => $baseclass->id,
                        'name' => $baseclass->name,
                        'icon' => url($baseclass->icon),
                        'is_active'=>$baseclass->status,
                        'is_added' => $isAdded
                    );
                }

                //end class
                $data[] = array('institute_for_id'=>$basemedium->institute_for,
                    'institute_for_name'=>$basemedium->institute_for_name,
                    'board_id'=>$basemedium->board,
                    'board_name'=>$basemedium->boardname,
                    'id' => $basemedium->id,
                    'name' => $basemedium->name,
                    'icon' => url($basemedium->icon),
                    'is_active' => $basemedium->status,
                    'is_added' => $isAdded,
                    'class'=>$class
                );
            }
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }

    public function get_edit_class(Request $request) // not in use
    {

        $validator = Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);
            $getClassId  = Base_table::whereIn('institute_for', $institute_for_ids)
            ->whereIn('board', $board_ids)
            ->whereIn('medium', $medium_ids)
            ->distinct()->pluck('institute_for_class');

            $base_class =  Class_model::whereIn('id', $getClassId)->get();
            $intitute_base_class_id = Class_sub::
            where('institute_id', $request->institute_id)
            ->where('board_id', $board_ids)
            ->where('medium_id', $medium_ids)
            ->pluck('class_id')->toArray();
            $data = [];
            foreach ($base_class as $baseclass) {
                $isAdded = in_array($baseclass->id, $intitute_base_class_id);
                $data[] = array(
                    'id' => $baseclass->id, 'name' => $baseclass->name,
                    'icon' => url($baseclass->icon),
                    'is_added' => $isAdded
                );
            }

            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
    // public function get_with_class_medium(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'institute_id' => 'required',
    //         'institute_for_id' => 'required',
    //         'board_id' => 'required',
    //         'medium_id' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return $this->response([], $validator->errors()->first(), false, 400);
    //     }
    //     try {
    //   // Extracting IDs from the request
    //         $institute_for_ids = explode(',', $request->institute_for_id);
    //         $board_ids = explode(',', $request->board_id);
    //         $medium_ids = explode(',', $request->medium_id);

    //         // Fetching distinct medium IDs based on provided criteria
    //         $getMediumIds = Base_table::join('board', 'board.id', '=', 'base_table.board')
    //             ->join('medium', 'medium.id', '=', 'base_table.medium')
    //             ->whereIn('base_table.institute_for', $institute_for_ids)
    //             ->whereIn('base_table.board', $board_ids)
    //             ->whereIN('base_table.medium', $medium_ids)
    //             ->select(
    //                 'board.name as boardname',
    //                 'medium.name as mediumname',
    //                 'medium.icon as mediumicon',
    //                 'medium.status as mediumstatus',
    //                 'base_table.board',
    //                 'base_table.medium'
    //             )
    //             ->distinct()
    //             ->get();

    //         // Fetching class IDs already associated with the institute
    //         // $institute_base_class_id = Class_sub::where('institute_id', $request->institute_id)
    //         //     ->whereIn('board_id', $board_ids)
    //         //     ->whereIn('medium_id', $medium_ids)
    //         //     ->pluck('class_id')
    //         //     ->toArray();

    //         // Initialize an array to store the final data
    //         $data = [];

    //         // Initialize a set to track processed mediums
    //         $processedMediums = [];

    //         // Loop through each medium
    //         foreach ($getMediumIds as $basemedium) {
    //             // Check if this medium has already been processed
    //             if (!isset($processedMediums[$basemedium->medium])) {

    //                 $institute_base_class_id = Class_sub::where('institute_id', $request->institute_id)
    //                 ->where('board_id', $basemedium->board)
    //                 ->where('medium_id', $basemedium->medium)
    //                 ->pluck('class_id')
    //                 ->toArray();

    //                 // Fetching distinct class IDs for this medium based on criteria
    //                 $getClassId = Base_table::whereIn('institute_for', $institute_for_ids)
    //                     ->where('board', $basemedium->board)
    //                     ->where('medium', $basemedium->medium)
    //                     ->distinct()
    //                     ->pluck('institute_for_class');

    //                 // Fetching classes based on the class IDs
    //                 $base_class = Class_model::whereIn('id', $getClassId)->get();

    //                 // Initialize an array to store the classes data for the current medium
    //                 $classes = [];

    //                 // Loop through each class to build the classes array for the medium
    //                 foreach ($base_class as $baseclass) {
    //                     $isAdded = in_array($baseclass->id, $institute_base_class_id);
    //                     $classes[] = [
    //                         'id' => $baseclass->id,
    //                         'name' => $baseclass->name,
    //                         'icon' => url($baseclass->icon),
    //                         'is_added' => $isAdded,
    //                         'is_active' => $baseclass->status, // Adding is_active field
    //                     ];
    //                 }

    //                 // Add medium and its associated classes to the final data array
    //                 $data[] = [
    //                     'id' => $basemedium->medium,
    //                     'name' => $basemedium->mediumname,
    //                     'icon' => url($basemedium->mediumicon),
    //                     'is_active' => $basemedium->mediumstatus, // Adding is_active field
    //                     'classes' => $classes, // Nested classes array
    //                 ];

    //                 // Mark this medium as processed
    //                 $processedMediums[$basemedium->medium] = true;
    //             }
    //         }




            
    //         return $this->response($data, "Fetch Data Successfully");
    //     } catch (Exception $e) {
    //         return $this->response($e, "Something went wrong!!", false, 400);
    //     }
    // }
    public function get_edit_standard(Request $request)
    {
        $validator = Validator::make($request->all(), [
             'institute_for_id.*' => 'required',
             'board_id.*' => 'required',
             'medium_id.*' => 'required',
             'class_id.*' => 'required',
             'institute_id' => 'nullable|exists:institute_detail,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        // try {
        //     $data = [];
        //     foreach ($request->data as $datas) {
        //     $base_standards = Standard_model::join('base_table', 'base_table.standard', '=', 'standard.id')
        //         ->join('institute_for', 'institute_for.id', '=', 'base_table.institute_for')
        //         ->join('class', 'base_table.institute_for_class', '=', 'class.id')
        //         ->join('medium', 'base_table.medium', '=', 'medium.id')
        //         ->join('board', 'base_table.board', '=', 'board.id')
        //         ->where('base_table.institute_for', $datas['institute_for_id'])
        //         ->where('base_table.board', $datas['board_id'])
        //         ->where('base_table.medium', $datas['medium_id'])
        //         ->whereIN('base_table.institute_for_class', $datas['class_id'])
        //         ->select('standard.id', 'standard.name','standard.status', 'class.name as class_name',
        //          'medium.name as medium_name', 'board.name as board_name'
        //          ,'base_table.institute_for_class','base_table.medium','base_table.board','base_table.institute_for','institute_for.name as institute_for_name')
        //         ->distinct()
        //         ->get();
                
        //     $institute_base_standard_id = Standard_sub::where('institute_id', $request->institute_id)->pluck('standard_id')->toArray();
            
        //     foreach ($base_standards as $base_standard) {

        //         $base_subject_query = Subject_model::join('base_table', 'base_table.id', '=', 'subject.base_table_id')
        //         //->leftjoin('stream', 'base_table.stream', '=', 'stream.id')
        //         ->where('base_table.institute_for', $datas['institute_for_id'])
        //         ->where('base_table.board', $datas['board_id'])
        //         ->where('base_table.medium', $datas['medium_id'])
        //         ->where('base_table.institute_for_class', $base_standard->institute_for_class)
        //         ->where('base_table.standard', $base_standard->id);

        //     // if (!empty($stream_ids) && array_filter($stream_ids)) {
        //     //     $base_subject_query->whereIn('base_table.stream', $stream_ids);
        //     // }
        //     $base_subject = $base_subject_query
        //         ->select('subject.id', 'subject.name', 'subject.image', 'subject.status')
        //         // 'stream.name as stream_name', 'base_table.stream as stream_id')
        //         ->distinct()
        //         ->get();
        //     $intitute_base_subject_id = Subject_sub::where('institute_id', $request->institute_id)->pluck('subject_id')->toArray();
        //     $subject = [];
        //     foreach ($base_subject as $basesubject) {
        //         $isAdded = in_array($basesubject->id, $intitute_base_subject_id);
        //         $subject[] = array(
        //             'id' => $basesubject->id,
        //             'name' => $basesubject->name,
        //             'image' => !empty($basesubject->image) ? asset($basesubject->image) : '',
        //             'is_active'=>$basesubject->status,
        //             'is_added' => $isAdded
        //         );
        //     }  

        //         $key = $base_standard->class_name . '_' . $base_standard->medium_name . '_' . $base_standard->board_name;
        //         if (!array_key_exists($key, $data)) {
        //             $data[$key] = [
        //                 'institute_for_id' => $base_standard->institute_for,
        //                 'institute_for_name' => $base_standard->institute_for_name,
        //                 'board_id' => $base_standard->board,
        //                 'board_name' => $base_standard->board_name,
        //                 'medium_id'=> $base_standard->medium,
        //                 'medium_name' => $base_standard->medium_name,
        //                 'class_id' => $base_standard->institute_for_class,
        //                 'class_name' => $base_standard->class_name,
        //                 'std_data' => [],
        //             ];
        //         }
                
        //         $isAdded = in_array($base_standard->id, $institute_base_standard_id);
        //         $data[$key]['std_data'][] = [
        //             'id' => $base_standard->id,
        //             'standard_name' => $base_standard->name,
        //             'is_active' =>$base_standard->status,
        //             'is_added' => $isAdded,
        //             'subject' => $subject
        //         ];
                
        //     }
        //     $data = array_values($data);
        // }

        try {
            $data = [];
            foreach ($request->data as $datas) {
                $base_standards = Standard_model::join('base_table', 'base_table.standard', '=', 'standard.id')
                    ->join('institute_for', 'institute_for.id', '=', 'base_table.institute_for')
                    ->join('class', 'base_table.institute_for_class', '=', 'class.id')
                    ->join('medium', 'base_table.medium', '=', 'medium.id')
                    ->join('board', 'base_table.board', '=', 'board.id')
                    ->where('base_table.institute_for', $datas['institute_for_id'])
                    ->where('base_table.board', $datas['board_id'])
                    ->where('base_table.medium', $datas['medium_id'])
                    ->whereIn('base_table.institute_for_class', $datas['class_id'])
                    ->select(
                        'standard.id', 
                        'standard.name',
                        'standard.status', 
                        'class.name as class_name',
                        'medium.name as medium_name', 
                        'board.name as board_name',
                        'base_table.institute_for_class',
                        'base_table.medium',
                        'base_table.board',
                        'base_table.institute_for',
                        'institute_for.name as institute_for_name'
                    )
                    ->distinct()
                    ->get();
        
                $institute_base_standard_id = Standard_sub::where('institute_id', $request->institute_id)
                    ->pluck('standard_id')->toArray();
        
                foreach ($base_standards as $base_standard) {
                    // Fetch subjects related to the standard
                    $base_subject_query = Subject_model::join('base_table', 'base_table.id', '=', 'subject.base_table_id')
                        ->where('base_table.institute_for', $datas['institute_for_id'])
                        ->where('base_table.board', $datas['board_id'])
                        ->where('base_table.medium', $datas['medium_id'])
                        ->where('base_table.institute_for_class', $base_standard->institute_for_class)
                        ->where('base_table.standard', $base_standard->id);
        
                    $base_subject = $base_subject_query
                        ->select('subject.id', 'subject.name', 'subject.image', 'subject.status')
                        ->distinct()
                        ->get();
        
                    $intitute_base_subject_id = Subject_sub::where('institute_id', $request->institute_id)
                        ->pluck('subject_id')->toArray();
        
                    $subject = [];
                    foreach ($base_subject as $basesubject) {
                        $isAdded = in_array($basesubject->id, $intitute_base_subject_id);
                        $subject[] = [
                            'id' => $basesubject->id,
                            'name' => $basesubject->name,
                            'image' => !empty($basesubject->image) ? asset($basesubject->image) : '',
                            'is_active' => $basesubject->status,
                            'is_added' => $isAdded
                        ];
                    }
        
                    // Key to group by institute_for_id, board_id, and medium_id
                    $key = $base_standard->institute_for . '_' . $base_standard->board . '_' . $base_standard->medium;
        
                    // Initialize if the key doesn't exist
                    if (!array_key_exists($key, $data)) {
                        $data[$key] = [
                            'institute_for_id' => $base_standard->institute_for,
                            'institute_for_name' => $base_standard->institute_for_name,
                            'board_id' => $base_standard->board,
                            'board_name' => $base_standard->board_name,
                            'medium_id' => $base_standard->medium,
                            'medium_name' => $base_standard->medium_name,
                            'classes' => [] // Will hold class-specific data
                        ];
                    }
        
                    // Check if the class already exists in the "classes" array
                    $classKey = $base_standard->institute_for_class;
        
                    if (!array_key_exists($classKey, $data[$key]['classes'])) {
                        $data[$key]['classes'][$classKey] = [
                            'class_id' => $base_standard->institute_for_class,
                            'class_name' => $base_standard->class_name,
                            'std_data' => []
                        ];
                    }
        
                    // Add standard data along with subjects to the class
                    $isAdded = in_array($base_standard->id, $institute_base_standard_id);
                    $data[$key]['classes'][$classKey]['std_data'][] = [
                        'id' => $base_standard->id,
                        'standard_name' => $base_standard->name,
                        'is_active' => $base_standard->status,
                        'is_added' => $isAdded,
                        'subject' => $subject
                    ];
                }
            }
        
            // Optionally flatten "classes" array to remove the classKey index
            foreach ($data as &$entry) {
                $entry['classes'] = array_values($entry['classes']);
            }
        
            $data = array_values($data); // Flatten final data
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went Wrong!!", false, 400);
        }
    }

    public function get_edit_subject(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
            'class_id' => 'required',
            'standard_id' => 'required',
            'institute_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);
            $class_ids = explode(',', $request->class_id);
            $standard_ids = explode(',', $request->standard_id);
            $stream_ids = explode(',', trim($request->stream));

            $base_subject_query = Subject_model::join('base_table', 'base_table.id', '=', 'subject.base_table_id')
                ->leftjoin('stream', 'base_table.stream', '=', 'stream.id')
                ->whereIn('base_table.institute_for', $institute_for_ids)
                ->whereIn('base_table.board', $board_ids)
                ->whereIn('base_table.medium', $medium_ids)
                ->whereIn('base_table.institute_for_class', $class_ids)
                ->whereIn('base_table.standard', $standard_ids);

            if (!empty($stream_ids) && array_filter($stream_ids)) {
                $base_subject_query->whereIn('base_table.stream', $stream_ids);
            }
            $base_subject = $base_subject_query
                ->select('subject.id', 'subject.name', 'subject.image', 'stream.name as stream_name', 'base_table.stream as stream_id')
                ->distinct()
                ->get();
            $intitute_base_subject_id = Subject_sub::where('institute_id', $request->institute_id)->pluck('subject_id')->toArray();
            $data = [];
            foreach ($base_subject as $basesubject) {
                $isAdded = in_array($basesubject->id, $intitute_base_subject_id);
                $data[] = array(
                    'id' => $basesubject->id,
                    'name' => $basesubject->name,
                    'image' => !empty($basesubject->image) ? asset($basesubject->image) : '',
                    'stream_id' => $basesubject->stream_id,
                    'stream_name' => $basesubject->stream_name,
                    'is_added' => $isAdded
                );
            }    
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
    public function get_with_class_medium(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'institute_for_id' => 'required',
            'board_id' => 'required',
            'medium_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
      // Extracting IDs from the request
            $institute_for_ids = explode(',', $request->institute_for_id);
            $board_ids = explode(',', $request->board_id);
            $medium_ids = explode(',', $request->medium_id);

            // Fetching distinct medium IDs based on provided criteria
            $getMediumIds = Base_table::join('board', 'board.id', '=', 'base_table.board')
                ->join('medium', 'medium.id', '=', 'base_table.medium')
                ->whereIn('base_table.institute_for', $institute_for_ids)
                ->whereIn('base_table.board', $board_ids)
                ->whereIN('base_table.medium', $medium_ids)
                ->select(
                    'board.name as boardname',
                    'medium.name as mediumname',
                    'medium.icon as mediumicon',
                    'medium.status as mediumstatus',
                    'base_table.board',
                    'base_table.medium',
                    'base_table.institute_for_class'
                )
                ->distinct()
                ->get();

           
            $data = [];

            // Initialize a set to track processed mediums
            $processedMediums = [];
            //   print_r($getMediumIds);exit;
            // Loop through each medium
            foreach ($getMediumIds as $basemedium) {
                // Check if this medium has already been processed
                if (!isset($processedMediums[$basemedium->medium])) {

                    $institute_base_class_id = Class_sub::where('institute_id', $request->institute_id)
                    ->where('board_id', $basemedium->board)
                    ->where('medium_id', $basemedium->medium)
                    // ->where('medium_id', $basemedium->institute_for_class)
                    ->pluck('class_id')
                    ->toArray();
                    // print_r($institute_base_class_id);exit;

                    // Fetching distinct class IDs for this medium based on criteria
                    $getClassId = Base_table::whereIn('institute_for', $institute_for_ids)
                        ->where('board', $basemedium->board)
                        ->where('medium', $basemedium->medium)
                        ->distinct()
                        ->pluck('institute_for_class');

                    // Fetching classes based on the class IDs
                    $base_class = Class_model::whereIn('id', $getClassId)->get();
                    // $base_class = Class_model::where('id', $getClassId)->get();
                    // print_r($base_class);exit;
                    // Initialize an array to store the classes data for the current medium
                    $classes = [];

                    // Loop through each class to build the classes array for the medium
                    foreach ($base_class as $baseclass) {
                        $isAdded = in_array($baseclass->id, $institute_base_class_id);
                        $classes[] = [
                            'id' => $baseclass->id,
                            'name' => $baseclass->name,
                            'icon' => url($baseclass->icon),
                            'is_added' => $isAdded,
                            'is_active' => $baseclass->status, // Adding is_active field
                        ];
                    }

                    // Add medium and its associated classes to the final data array
                    $data[] = [
                        'id' => $basemedium->medium,
                        'name' => $basemedium->mediumname,
                        'icon' => url($basemedium->mediumicon),
                        'is_active' => $basemedium->mediumstatus, // Adding is_active field
                        'classes' => $classes, // Nested classes array
                    ];

                    // Mark this medium as processed
                    $processedMediums[$basemedium->medium] = true;
                }
            }

           


            
            return $this->response($data, "Fetch Data Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Something went wrong!!", false, 400);
        }
    }
}
