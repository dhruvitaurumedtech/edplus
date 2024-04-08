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
                'standard.*',
                'medium.name as medium',
                'board.name as board',
                'base_table.id as base_id'
            )
            ->where('standard.status', 'active')->paginate(10);

        $subject_list = Base_table::join('subject', 'subject.base_table_id', '=', 'base_table.id')
            ->select('subject.*', 'base_table.standard', 'base_table.id as baset_id')
            ->where('base_table.status', 'active')->get();



        $institute_for = Institute_for_model::where('status', 'active')->get();
        $board = board::where('status', 'active')->get();
        $medium = Medium_model::where('status', 'active')->get();
        $class = Class_model::where('status', 'active')->get();
        $standard = Standard_model::where('status', 'active')->get();
        $stream = Stream_model::where('status', 'active')->get();
        return view('subject.list', compact('institute_for', 'board', 'medium', 'class', 'standard', 'stream', 'subject_list', 'addsubstandard'));
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

        $base_table = Base_table::create([
            'institute_for' => $request->input('institute_for'),
            'board' => $request->input('board'),
            'medium' => $request->input('medium'),
            'institute_for_class' => $request->input('institute_for_class'),
            'standard' => $request->input('standard'),
            'stream' => $request->input('stream'),
            'subject' => $request->input('subject'),
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
        } else {
        }


        return redirect()->route('subject.list')->with('success', 'Subject Created Successfully');
    }

    function create_subject()
    {
        $standardlist = Standard_model::get()->toArray();
        $streamlist = Stream_model::get()->toArray();
        return view('subject.create', compact('streamlist', 'standardlist'));
    }
    function standard_wise_stream(Request $request)
    {
        $id = $request->input('standard_id');
        $streamlist = Stream_model::where('standard_id', $id)->get()->toArray();
        return response()->json(['streamlist' => $streamlist]);
    }

    function subject_edit(Request $request)
    {
        $id = $request->input('subject_id');
        $standardlist = Standard_model::get()->toArray();
        $streamlist = Stream_model::get()->toArray();
        $subjectlist = Subject_model::find($id);
        return response()->json([
            'standardlist' => $standardlist, 'streamlist' => $streamlist,
            'subjectlist' => $subjectlist
        ]);
    }
    function subject_update(Request $request)
    {
        $id = $request->input('subject_id');
        $class = Subject_model::find($id);
        $request->validate([
            'standard_id' => 'required',
            'stream_id' => 'required',
            'name' => ['required', 'string', 'max:255'],
            'status' => 'required',
        ]);

        $class->update([
            'standard_id' => $request->input('standard_id'),
            'stream_id' => $request->input('stream_id'),
            'name' => $request->input('name'),
            'status' => $request->input('status'),
        ]);
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
}
