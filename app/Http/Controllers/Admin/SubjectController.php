<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institute_for_model;
use App\Models\board;
use App\Models\Medium_model;
use App\Models\Class_model;
use App\Models\Standard_model;
use App\Models\Stream_model;
use App\Models\Base_table;
use App\Models\Subject_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    function list_subject()
    {
        $addsubstandard = Standard_model::join('base_table', 'standard.id', '=', 'base_table.standard')
            ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
            ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
            ->leftjoin('board', 'board.id', '=', 'base_table.board')
            ->select(
                'stream.name as sname',
                'standard.name as standard',
                'medium.name as medium',
                'board.name as board',
                'base_table.id as base_id',
                'base_table.status'
            )
            ->where('standard.status', 'active')->orderBy('base_table.id', 'desc')->paginate(10);

        $subject_list = Base_table::join('subject', 'subject.base_table_id', '=', 'base_table.id')
            ->select('subject.*', 'base_table.standard', 'base_table.id as baset_id')
            ->where('base_table.status', 'active')->get();




        return view('subject.list', compact('subject_list', 'addsubstandard'));
    }
    function subject_list_save(Request $request)
    {

        $request->validate([
            'institute_for' => 'required',
            'board' => 'required',
            'medium' => 'required',
            'institute_for_class' => 'required',
            'standard' => 'required',
            //'stream' => 'required',
            //'subject' => 'required',
            'status' => 'required',


        ]);

        // Build the query

        // Subquery to check for existence of a record in the subject table
        $subquery = DB::table('subject')
            ->select(DB::raw(1))
            ->whereColumn('subject.base_table_id', 'base_table.id')
            ->where('subject.name', request('subject'));

        // Build the main query
        $query = DB::table('base_table')
            ->where('institute_for', request('institute_for'))
            ->where('board', request('board'))
            ->where('medium', request('medium'))
            ->where('institute_for_class', request('institute_for_class'))
            ->where('standard', request('standard'))
            ->where('stream', request('stream'))
            ->where('status', request('status'))
            ->whereExists($subquery);

        // Print the generated SQL query

        // Execute the query and check if any results are returned
        $exists = $query->count();
        if ($exists <= 0) {


            $base_table = Base_table::create([
                'institute_for' => $request->input('institute_for'),
                'board' => $request->input('board'),
                'medium' => $request->input('medium'),
                'institute_for_class' => $request->input('institute_for_class'),
                'standard' => $request->input('standard'),
                'stream' => $request->input('stream'),
                // 'subject' => $request->input('subject'),
                'status' => $request->input('status'),
                'created_by' => Auth::id(),
            ]);

            $base_table_id = $base_table->id;

            $subjects = $request->input('subject');
            $subject_images = $request->file('subject_image');
            // print_r($subject_images = $request->file('subject_image'));exit;
            if ($subjects && $subject_images) {
                foreach ($subjects as $i => $subject) {
                    if (isset($subject_images[$i])) {
                        $subject_image = $subject_images[$i];
                        $name = $subject_image->getClientOriginalName();
                        $subject_image->move(public_path() . '/subject/', $name);
                        Subject_model::create([
                            'base_table_id' => $base_table_id,
                            'name' => $subject,
                            'image' => '/subject/' . $name,
                            'status' => 'active',
                            'created_by' => Auth::id(),
                        ]);
                    } else {
                    }
                }
            }


            return redirect()->route('subject.list')->with('success', 'Subject Created Successfully');
        } else {
            return redirect()->route('subject.list')->with('error', 'Already Exist Record!');
        }
    }

    function create_subject()
    {
        $institute_for = Institute_for_model::where('status', 'active')->get();
        $board = board::where('status', 'active')->get();
        $medium = Medium_model::where('status', 'active')->get();
        $class = Class_model::where('status', 'active')->get();
        $standard = Standard_model::where('status', 'active')->get();
        $stream = Stream_model::where('status', 'active')->get();
        $addsubstandard = Standard_model::join('base_table', 'standard.id', '=', 'base_table.standard')
            ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
            ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
            ->leftjoin('board', 'board.id', '=', 'base_table.board')
            ->select(
                'stream.name as sname',
                'standard.*',
                'medium.name as medium',
                'board.name as board',
                'base_table.id as base_id'
            )
            ->where('standard.status', 'active')->paginate(10);

        $subject_list = Base_table::join('subject', 'subject.base_table_id', '=', 'base_table.id')
            ->select('subject.*', 'base_table.standard', 'base_table.id as baset_id')
            ->where('base_table.status', 'active')->get();
        return view('subject.create', compact('addsubstandard', 'subject_list', 'institute_for', 'board', 'medium', 'class', 'standard', 'stream'));
    }
    function standard_wise_stream(Request $request)
    {
        $id = $request->input('standard_id');
        $streamlist = Stream_model::where('standard_id', $id)->get()->toArray();
        return response()->json(['streamlist' => $streamlist]);
    }

    function subject_edit(Request $request, $id)
    {
        $id = $id;
        $basetable_list = Base_table::where('id', $id)->first()->toarray();


        $selected_subject_list = Subject_model::where('base_table_id', $id)->get()->toarray();

        $institute_for = Institute_for_model::where('status', 'active')->get();
        $board = board::where('status', 'active')->get();
        $medium = Medium_model::where('status', 'active')->get();
        $class = Class_model::where('status', 'active')->get();
        $standard = Standard_model::where('status', 'active')->get();
        $stream = Stream_model::where('status', 'active')->get();
        $addsubstandard = Standard_model::join('base_table', 'standard.id', '=', 'base_table.standard')
            ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
            ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
            ->leftjoin('board', 'board.id', '=', 'base_table.board')
            ->select(
                'stream.name as sname',
                'standard.*',
                'medium.name as medium',
                'board.name as board',
                'base_table.id as base_id'
            )
            ->where('standard.status', 'active')->paginate(10);

        $subject_list = Base_table::join('subject', 'subject.base_table_id', '=', 'base_table.id')
            ->select('subject.*', 'base_table.standard', 'base_table.id as baset_id')
            ->where('base_table.status', 'active')->get();

        return view('subject.edit', compact('addsubstandard', 'id', 'basetable_list', 'selected_subject_list', 'subject_list', 'institute_for', 'board', 'medium', 'class', 'standard', 'stream'));
    }
    function subject_update(Request $request)
    {

        $request->validate([
            'institute_for' => 'required',
            'board' => 'required',
            'medium' => 'required',
            'institute_for_class' => 'required',
            'standard' => 'required',
            //'stream' => 'required',
            //'subject' => 'required',
            'status' => 'required',


        ]);

        // $subquery = DB::table('subject')
        //     ->select(DB::raw(1))
        //     ->whereColumn('subject.base_table_id', 'base_table.id')
        //     ->where('subject.name', request('subject'));

        // $query = DB::table('base_table')
        //     ->where('institute_for', request('institute_for'))
        //     ->where('board', request('board'))
        //     ->where('medium', request('medium'))
        //     ->where('institute_for_class', request('institute_for_class'))
        //     ->where('standard', request('standard'))
        //     ->where('stream', request('stream'))
        //     ->where('status', request('status'))
        //     ->whereExists($subquery);

        // $exists = $query->count();

        // if ($exists <= 0) {
        $subject_model = Base_table::where('id', $request->id)
            ->update([
                'institute_for' => $request->institute_for,
                'board' => $request->board,
                'medium' => $request->medium,
                'institute_for_class' => $request->institute_for_class,
                'standard' => $request->standard,
                'stream' => $request->stream,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);
        $base_table_id = $request->id;
        $subjects = $request->input('subject');
        //if (!empty($subjects) && !empty($subject_images)) {
        foreach ($subjects as $i => $subject) {
            if (empty($request->file('subject_image')[$i])) {
                $subject_image = $request->old_subject_image[$i];
            } else {
                $subject_images = $request->file('subject_image')[$i];
                $subject_image = $subject_images->getClientOriginalName();

                $subject_images->move(public_path() . '/subject/', $subject_image);
            }
            $subject_id = $request->subject_id;
            $subject_id_value = isset($subject_id[$i]) ? $subject_id[$i] : null;
            Subject_model::updateOrCreate(
                ['id' => $subject_id_value],
                [
                    'base_table_id' => $base_table_id,
                    'name' => $subject,
                    'image' => '/subject/' . str_replace("/subject/", "", $subject_image),
                    'status' => 'active',
                    'created_by' => Auth::id()
                ]
            );
        }
        return redirect()->route('subject.list')->with('success', 'Subject Updated successfully');
    }
    public function subject_delete(Request $request)
    {
        $subject_id = $request->input('subject_id');
        $subjectlist = Subject_model::find($subject_id);

        if (!$subjectlist) {
            return redirect()->route('subject.list')->with('error', 'Subject not found');
        }

        $subjectlist->delete();

        return redirect()->route('subject.list')->with('success', 'Subject deleted successfully');
    }
    function unique_subject_delete(Request $request)
    {
        $subject_id = $request->input('subject_id');
        $subjectlist = Subject_model::find($subject_id);

        if (!$subjectlist) {
            return response()->json(['data' => 'subject not found']);
        }

        $subjectlist->delete();
        return response()->json(['data' => 'Subject deleted successfully']);
    }
}
