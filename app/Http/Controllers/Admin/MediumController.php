<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medium_model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MediumController extends Controller
{
    function list_medium(){
        $mediumlist = Medium_model::paginate(10);
        return view('medium.list', compact('mediumlist'));  
    }
    function create_medium(){
        return view('medium.create');
    }
    function medium_list_save(Request $request){
        $request->validate([
            'icon' => 'required|image|mimes:svg|max:2048',
            'name'=>['required','string','max:255',Rule::unique('medium', 'name')],
            'status'=>'required',
    ]);
    $iconFile = $request->file('icon');
    $imagePath = $iconFile->store('icon', 'public');

    Medium_model::create([
        'name'=>$request->input('name'),
        'icon'=>$imagePath,
        'status'=>$request->input('status'),
    ]);

    return redirect()->route('medium.create')->with('success', 'Medium  Created Successfully');

    }
    function medium_list_edit(Request $request){
        $id = $request->input('medium_id');
        $medium_list = Medium_model::find($id);
        return response()->json(['medium_list'=>$medium_list]);
        
    }
    function medium_update(Request $request){
        $id=$request->input('medium_id');
        $medium = Medium_model::find($id);
        $request->validate([
            'name'=>['required','string','max:255',Rule::unique('medium', 'name')->ignore($id)],
            'status'=>'required',
       ]);
       $iconFile = $request->file('icon');
        if(!empty($iconFile)){
            $imagePath = $iconFile->store('icon', 'public');
        }else{
            $imagePath=$request->input('old_icon');
        }
        $medium->update([
            'name'=>$request->input('name'),
            'icon'=>$imagePath,
            'status'=>$request->input('status'),
        ]);
        return redirect()->route('medium.list')->with('success', 'Medium Updated successfully');
    }
    function medium_delete(Request $request){
        $medium_id=$request->input('medium_id');
        $medium_list = Medium_model::find($medium_id);
        if (!$medium_list) {
            return redirect()->route('medium.list')->with('error', 'Medium not found');
        }
        $medium_list->delete();
        return redirect()->route('medium.list')->with('success', 'Medium deleted successfully');
  
    }
}
