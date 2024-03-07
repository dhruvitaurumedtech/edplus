<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\board;
use App\Models\Institute_for_model;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    public function list(){
        $board_list = board::whereNull('deleted_at')->paginate(10);
        return view('board.list', compact('board_list'));
    }

    public function create(){
        return view('board/create');
    }

    public function save(Request $request){
        $request->validate([
            'icon' => 'required|image|mimes:svg|max:2048',
            'name' => ['required', 'string', 'max:255', Rule::unique('board', 'name')],
            'status' => 'required',
        ]);
        $iconFile = $request->file('icon');
        $imagePath = $iconFile->store('icon', 'public');

        board::create([
            'name'=>$request->input('name'),
            'icon'=>$imagePath,
            'status'=>$request->input('status'),
        ]);

        $board_list = board::whereNull('deleted_at')->paginate(10);
        return redirect()->route('board.list')->with('success', 'Board Created Successfully','board_list');

    }

    public function edit(Request $request){
        $id = $request->input('board_id');
        $board_list = board::find($id);
        return response()->json(['board_list'=>$board_list]);
        
    }

    public function update(Request $request){
        // dd($request->all());exit;
        $id=$request->input('board_id');
        $role = board::find($id);
        $request->validate([
            'name'=>['required','string','max:255',Rule::unique('board', 'name')->ignore($id)],
            'status'=>'required',
       ]);
        $iconFile = $request->file('icon');
        if(!empty($iconFile)){
            $imagePath = $iconFile->store('icon', 'public');
        }else{
            $imagePath=$request->input('old_icon');
        }
        $role->update([
            'name'=>$request->input('name'),
            'icon'=>$imagePath,
            'status'=>$request->input('status'),
        ]);
        return redirect()->route('board.list')->with('success', 'Board Updated successfully');
    

    }

    public function delete(Request $request){
        $board_id=$request->input('board_id');
        $board_list = board::find($board_id);

        if (!$board_list) {
            return redirect()->route('board.list')->with('error', 'Board not found');
        }

        $board_list->delete();

        return redirect()->route('board.list')->with('success', 'Board deleted successfully');
  
      
    }
}
