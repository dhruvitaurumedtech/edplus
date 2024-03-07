<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Class_model;
use App\Models\Standard_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StandardController extends Controller
{
    function list_standard(){
        $standardlist = Standard_model::paginate(10); 
        // $standardlist =DB::table('standard')
        //     ->join('class', 'standard.class_id', '=', 'class.id')
        //     ->select('standard.*', 'class.name as class_name')
        //     ->whereNull('standard.deleted_at')
        //     ->paginate(10);
        // $class_list = Class_model::get()->toArray();
        return view('standard.list', compact('standardlist'));
    }
    function create_standard(){
        $class_list = Class_model::get()->toArray();
        return view('standard.create',compact('class_list'));
    }
    function standard_list_save(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('standard', 'name')],
            'status' => 'required',
    ]);

    Standard_model::create([
        'name'=>$request->input('name'),
        'status'=>$request->input('status'),
    ]);

    return redirect()->route('standard.create')->with('success', 'Standard Created Successfully');
    }
    function standard_list_edit(Request $request){
        $id = $request->input('standard_id');
        $class_list = Class_model::get()->toArray();
        $standard_list = Standard_model::find($id);
        return response()->json(['standard_list'=>$standard_list,'class_list'=>$class_list]);
        
    }
    function standard_update(Request $request){
        $id=$request->input('standard_id');
        $standard = Standard_model::find($id);
        $request->validate([
            'name'=>['required','string','max:255',Rule::unique('standard', 'name')->ignore($id)],
            'status'=>'required',
       ]);
      
        $standard->update([
            'name'=>$request->input('name'),
            'status'=>$request->input('status'),
        ]);
        return redirect()->route('standard.list')->with('success', 'Standard Updated successfully');
    
    }
    function standard_delete(Request $request){
        $standard_id=$request->input('standard_id');
        $standard_list = Standard_model::find($standard_id);

        if (!$standard_list) {
            return redirect()->route('standard.list')->with('error', 'Standard not found');
        }

        $standard_list->delete();

        return redirect()->route('standard.list')->with('success', 'Standard deleted successfully');
  
    }
}
