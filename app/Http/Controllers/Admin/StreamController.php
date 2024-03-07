<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stream_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StreamController extends Controller
{
    function list_stream(){
        $straemlist =Stream_model::whereNull('deleted_at')->paginate(10);
        return view('stream.list',compact('straemlist'));
    }
    public function create_stream(){
        return view('stream.create');
    }
    public function stream_list_save(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => 'required',
    ]);

    Stream_model::create([
        'name'=>$request->input('name'),
        'status'=>$request->input('status'),
    ]);
        $straemlist =Stream_model::whereNull('deleted_at')->paginate(10);
       return redirect()->route('stream.list')->with('success', 'Stream Created Successfully','straemlist');

    }
    public function stream_list_edit(Request $request){
        $id = $request->input('stream_id');
        $straemlist = Stream_model::find($id);
        return response()->json(['straemlist'=>$straemlist]);
    }
    public function stream_update(Request $request){
        $id=$request->input('stream_id');
        $class = Stream_model::find($id);
        $request->validate([
            'name'=>['required','string','max:255',Rule::unique('stream','name')->ignore($id)],
            'status'=>'required',
       ]);
      
        $class->update([
            'name'=>$request->input('name'),
            'status'=>$request->input('status'),
        ]);
        return redirect()->route('stream.list')->with('success', 'Stream Updated successfully');
    
    }
    function stream_delete(Request $request){
        $stream_id=$request->input('stream_id');
        // dd($request->all());exit;
        $streamlist = Stream_model::find($stream_id);

        if (!$streamlist) {
            return redirect()->route('stream.list')->with('error', 'Class not found');
        }
        
        $streamlist->delete();

        return redirect()->route('stream.list')->with('success', 'Class deleted successfully');
  
    }
}
