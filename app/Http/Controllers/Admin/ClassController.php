<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\board;
use App\Models\Class_model;
use App\Models\Standard_model;
use App\Models\Stream_model;
use App\Models\Subject_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    function list_class(){
        $classlist = Class_model::paginate(10);
        // $classlist =DB::table('class')
        // ->join('board', 'class.board_id', '=', 'board.id')
        // ->select('class.*', 'board.name as board_name')
        // ->whereNull('class.deleted_at')
        // ->paginate(10);
        // $boardlist = board::get()->toArray(); 
        return view('class.list', compact('classlist'));
    }
    function create_class(){
        $classlist = Class_model::paginate(10);
        return view('class.create',compact('classlist'));
    }
    function class_list_save(Request $request){
        $request->validate([
            'icon' => 'required|image|mimes:svg|max:2048',
            'name' => 'required|unique:class,name',
            'status' => 'required',
    ]);
    $iconFile = $request->file('icon');
    $imagePath = $iconFile->store('icon', 'public');
    Class_model::create([
        'name'=>$request->input('name'),
        'icon'=>$imagePath,
        'status'=>$request->input('status'),
    ]);

    return redirect()->route('class.create')->with('success', 'Class Created Successfully');

    }
    function class_list_edit(Request $request){
        $id = $request->input('class_id');
        // $board_list = board::get()->toArray();
        $class_list = Class_model::find($id);
        return response()->json(['class_list'=>$class_list]);
        
    }
    function class_update(Request $request){
        $id=$request->input('class_id');
        $class = Class_model::find($id);
        $request->validate([
            'name'=>'required',
            'status'=>'required',
       ]);
       $iconFile = $request->file('icon');
        if(!empty($iconFile)){
            $imagePath = $iconFile->store('icon', 'public');
        }else{
            $imagePath=$request->input('old_icon');
        }
        $class->update([
            'board_id'=>$request->input('board_id'),
            'name'=>$request->input('name'),
            'icon'=>$imagePath,
            'status'=>$request->input('status'),
        ]);
        return redirect()->route('class.list')->with('success', 'Class Updated successfully');
    

    }
    function class_delete(Request $request){
        $class_id=$request->input('class_id');
        // dd($request->all());exit;
        $class_list = Class_model::find($class_id);

        if (!$class_list) {
            return redirect()->route('class.list')->with('error', 'Class not found');
        }

        $class_list->delete();

        return redirect()->route('class.list')->with('success', 'Class deleted successfully');
  
    }
    function get_standard(Request $request){
        $class_id=$request->input('classId');
        $standard_list = Standard_model::where('class_id',$class_id)->get();
        return response()->json(['standard_list'=>$standard_list]);
    }
    function get_stream(Request $request){
        $standard_id=$request->input('standard_id');
        $stream_list = Stream_model::where('standard_id',$standard_id)->get();
        if(sizeof($stream_list) == 0){
            $subject_list = Subject_model::where('standard_id',$standard_id)->get();
        }
        else{
            $subject_list = '';
        }
        return response()->json(['stream_list'=>$stream_list,'subject_list'=>$subject_list]);
    }
}
