<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Base_table;
use App\Models\board;
use App\Models\Class_model;
use App\Models\Class_sub;
use App\Models\Dobusinesswith_Model;
use App\Models\Dobusinesswith_sub;
use App\Models\Institute_board_sub;
use App\Models\Institute_detail;
use App\Models\Institute_for_model;
use App\Models\Institute_for_sub;
use App\Models\Medium_model;
use App\Models\Medium_sub;
use App\Models\Standard_sub;
use App\Models\Stream_sub;
use App\Models\Subject_sub;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InstituteController extends Controller
{
    public function list_admin()
    {
        $users = User::where('role_type', [2, 3])->paginate(10);
        return view('admin.list', compact('users'));
    }
    public function list_institute()
    {
        $institute_list = Institute_detail::paginate(10);
        return view('institute/list_institute', compact('institute_list'));
    }
    public function create_institute()
    {
        $institute_for_array = DB::table('base_table')
            ->leftJoin('institute_for', 'institute_for.id', '=', 'base_table.institute_for')
            ->select(
                'institute_for.name as institute_for_name',
                DB::raw('MAX(base_table.id) as id'),
                'institute_for.id as institute_for_id'
            )
            ->groupBy('institute_for.name', 'base_table.institute_for', 'institute_for.id')
            ->whereNull('base_table.deleted_at')
            ->get();
        // echo "<pre>";print_r($institute_for_array);exit;
        // $board_array = DB::table('base_table')
        //             ->leftJoin('board', 'board.id', '=', 'base_table.board')
        //             ->select('board.name as board_name', 'base_table.id', 'board.id')
        //             ->whereNull('base_table.deleted_at')
        //             ->whereRaw('base_table.id = (SELECT id FROM base_table b WHERE b.board = base_table.board ORDER BY b.id LIMIT 1)')
        //             ->get();   
        // $medium_array = Base_table::leftJoin('medium', 'medium.id', '=', 'base_table.medium')
        //             ->select('base_table.id',DB::raw('MAX(medium.id) as medium_id'), DB::raw('GROUP_CONCAT(DISTINCT medium.name) as medium_name'))
        //             ->whereNull('base_table.deleted_at')
        //             ->whereRaw('base_table.id = (SELECT m.id FROM base_table m WHERE m.medium = base_table.medium ORDER BY m.id LIMIT 1)')
        //             ->groupBy('base_table.id')
        //             ->get()
        //             ->toArray();



        // $class_array =Base_table::leftJoin('class', 'class.id', '=', 'base_table.institute_for_class')
        //         ->select('base_table.id',DB::raw('MAX(class.id) as class_id'), DB::raw('GROUP_CONCAT(DISTINCT class.name) as class_name'))
        //         ->whereNull('base_table.deleted_at')
        //         ->whereRaw('base_table.id = (SELECT m.id FROM base_table m WHERE m.institute_for_class = base_table.institute_for_class ORDER BY m.id LIMIT 1)')
        //         ->groupBy('base_table.id')
        //         ->get()
        //         ->toArray();

        // $standard_array = Base_table::leftJoin('standard', 'standard.id', '=', 'base_table.standard')
        //             ->select('base_table.id',DB::raw('MAX(standard.id) as standard_id'), DB::raw('GROUP_CONCAT(DISTINCT standard.name) as standard_name'))
        //             ->whereNull('base_table.deleted_at')
        //             ->whereRaw('base_table.id = (SELECT m.id FROM base_table m WHERE m.standard = base_table.standard ORDER BY m.id LIMIT 1)')
        //             ->groupBy('base_table.id')
        //             ->get()
        //             ->toArray();
        // $stream_array = Base_table::leftJoin('stream', 'stream.id', '=', 'base_table.stream')
        //             ->select('base_table.id',DB::raw('MAX(stream.id) as stream_id'), DB::raw('GROUP_CONCAT(DISTINCT stream.name) as stream_name'))
        //             ->whereNull('base_table.deleted_at')
        //             ->whereRaw('base_table.id = (SELECT m.id FROM base_table m WHERE m.stream = base_table.stream ORDER BY m.id LIMIT 1)')
        //             ->groupBy('base_table.id')
        //             ->get()
        //             ->toArray();

        // $subject_array = Base_table::leftJoin('subject', 'subject.base_table_id', '=', 'base_table.id')
        //             ->select('base_table.id', DB::raw('MAX(subject.base_table_id) as base_table_id'),DB::raw('GROUP_CONCAT(DISTINCT subject.name) as subject_name'))
        //             ->whereNull('base_table.deleted_at')
        //             ->whereRaw('base_table.id = (SELECT m.id FROM base_table m WHERE m.id = base_table.id ORDER BY m.id LIMIT 1)')
        //             ->groupBy('base_table.id')
        //             ->get()
        //             ->toArray();

        //   $do_business_with=Dobusinesswith_Model::get()->toarray();


        // echo "<pre>";print_r($stream_array );exit;
        // ,'board_array','medium_array','class_array',
        //                                                  'standard_array','stream_array','subject_array','do_business_with'
        return view('institute/create_institute', compact('institute_for_array'));
    }
    public function get_board(Request $request)
    {
        $institute_for = Base_table::where('institute_for', $request->input('institute_for_id'))->get()->toarray();
        $board = [];
        foreach ($institute_for as $value) {
            $board[] = $value['board'];
        }
        $board_list = Board::whereIn('id', $board)
            ->select('id', 'name')
            ->get()
            ->toArray();
        return response()->json(['board_list' => $board_list]);
    }
    public function create_institute_for()
    {
        $institute_for = Institute_for_model::paginate(10);
        return view('institute/create_institute_for', compact('institute_for'));
    }
    public function list_institute_for()
    {
        $institute_for = Institute_for_model::paginate(10);
        return view('institute/list_institute_for', compact('institute_for'));
    }
    public function institute_for_save(Request $request)
    {
        // dd($request->all());exit;
        $request->validate([
            'icon' => 'required|image|mimes:svg|max:2048',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('institute_for', 'name')
                    ->ignore($request->input('name'))
                    ->whereNull('deleted_at'),
            ],
            'status' => 'required',
        ]);
        $iconFile = $request->file('icon');
        $imagePath = $iconFile->store('icon', 'public');


        Institute_for_model::create([
            'name' => $request->input('name'),
            'icon' => $imagePath,
            'status' => $request->input('status'),
        ]);
        return redirect()->route('institute_for.list')->with('success', 'Institute For Created Successfully');
    }
    public function institute_for_edit(Request $request)
    {
        $id = $request->input('institute_id');
        $Institute_for_model = Institute_for_model::find($id);
        return response()->json(['Institute_for_model' => $Institute_for_model]);
    }
    public function institute_for_update(Request $request)
    {
        $id = $request->input('institute_id');
        $role = Institute_for_model::find($id);
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('institute_for', 'name')->ignore($id)],
            'status' => 'required',
        ]);

        $iconFile = $request->file('icon');
        if (!empty($iconFile)) {
            $imagePath = $iconFile->store('icon', 'public');
        } else {
            $imagePath = $request->input('old_icon');
        }
        $role->update([
            'name' => $request->input('name'),
            'icon' => $imagePath,
            'status' => $request->input('status'),
        ]);
        return redirect()->route('institute_for.list')->with('success', 'Institute For Updated successfully');
    }
    public function institute_for_delete(Request $request)
    {
        $institute_id = $request->input('institute_id');
        $institute_for = Institute_for_model::find($institute_id);

        if (!$institute_for) {
            return redirect()->route('institute_for.list')->with('error', 'Institute for not found');
        }

        $institute_for->delete();

        return redirect()->route('institute_for.list')->with('success', 'Institute for deleted successfully');
    }
    function institute_register(Request $request)
    {
        echo "<pre>";
        print_r($request->all());
        exit;
        $validator = \Validator::make($request->all(), [
            'institute_for_id' => 'required',
            'institute_board_id' => 'required',
            'institute_for_class_id' => 'required',
            'institute_medium_id' => 'required',
            'institute_work_id' => 'required',
            'standard_id' => 'required',
            'subject_id' => 'required',
            'institute_name' => 'required',
            'address' => 'required',
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


            //institute_detail
            $instituteDetail = Institute_detail::create([
                'unique_id' => $unique_id,

                'user_id' => Auth::user()->id,
                'institute_name' => $request->input('institute_name'),
                'address' => $request->input('address'),
                'contact_no' => $request->input('contact_no'),
                'email' => $request->input('email'),
                'status' => 'inactive'
            ]);
            $lastInsertedId = $instituteDetail->id;
            $institute_name = $instituteDetail->institute_name;

            //institute_for_sub
            // $intitute_for_id = explode(',', $request->input('institute_for_id'));
            foreach ($request->input('institute_for_id') as $value) {
                // if ($value == 5) {
                //     $instituteforadd = institute_for_model::create([
                //         'name' => $request->input('institute_for'),
                //         'status' => 'active',
                //     ]);
                //     $institute_for_id = $instituteforadd->id;
                // } else {
                $institute_for_id = $value;
                // }
                Institute_for_sub::create([
                    'user_id' => Auth::user()->id,
                    'institute_id' => $lastInsertedId,
                    'institute_for_id' => $institute_for_id,
                ]);
            }

            //board_sub
            // $institute_board_id = explode(',', );
            // echo "<pre>";print_r($request->input('institute_board_id'));exit;
            foreach ($request->input('institute_board_id') as $value) {
                //other
                // if ($value == 4) {
                //     $instituteboardadd = board::create([
                //         'name' => $request->input('institute_board'),
                //         'status' => 'active',
                //     ]);
                //     $instituteboard_id = $instituteboardadd->id;
                // } else {
                $instituteboard_id = $value;
                // }
                //end other

                Institute_board_sub::create([
                    'user_id' => Auth::user()->id,
                    'institute_id' => $lastInsertedId,
                    'board_id' => $instituteboard_id,
                ]);
            }

            // class
            // $institute_for_class_id = explode(',', $request->input('institute_for_class_id'));
            foreach ($request->input('institute_for_class_id') as $value) {

                Class_sub::create([
                    'user_id' => Auth::user()->id,
                    'institute_id' => $lastInsertedId,
                    'class_id' => $value,
                ]);
            }

            //medium
            // $institute_medium_id = explode(',', $request->input('institute_medium_id'));
            foreach ($request->input('institute_medium_id') as $value) {
                Medium_sub::create([
                    'user_id' => Auth::user()->id,
                    'institute_id' => $lastInsertedId,
                    'medium_id' => $value,
                ]);
            }

            //dobusiness
            // $institute_work_id = explode(',', $request->input('institute_work_id'));
            foreach ($request->input('institute_work_id') as $value) {
                Dobusinesswith_sub::create([
                    'user_id' => Auth::user()->id,
                    'institute_id' => $lastInsertedId,
                    'do_business_with_id' => $value,
                ]);
            }

            //standard
            // $standard_id = explode(',', $request->input('standard_id'));
            foreach ($request->input('standard_id') as $value) {
                Standard_sub::create([
                    'user_id' => Auth::user()->id,
                    'institute_id' => $lastInsertedId,
                    'standard_id' => $value,
                ]);
            }

            //stream
            if ($request->input('stream_id')) {
                // $stream = explode(',', $request->input('stream_id'));
                foreach ($request->input('stream_id') as $value) {
                    Stream_sub::create([
                        'user_id' => Auth::user()->id,
                        'institute_id' => $lastInsertedId,
                        'stream_id' => $value,
                    ]);
                }
            }
            //subject
            // $subject_id = explode(',', $request->input('subject_id'));
            foreach ($request->input('subject_id') as $value) {
                Subject_sub::create([
                    'user_id' => Auth::user()->id,
                    'institute_id' => $lastInsertedId,
                    'subject_id' => $value,
                ]);
            }

            // return response()->json([
            //     'success' => 200,
            //     'message' => 'institute create Successfully',
            //     'data' => [
            //         'institute_id' => $lastInsertedId,
            //         'institute_name' => $institute_name,
            //     ]
            // ], 200);
            return redirect()->route('institute.list')->with('success', 'Institute Created Successfully');
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Error creating institute',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
