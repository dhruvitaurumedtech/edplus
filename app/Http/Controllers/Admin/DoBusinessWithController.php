<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dobusinesswith_Model;
use Illuminate\Http\Request;
use App\Models\VideoCategory;
use Illuminate\Validation\Rule;

class DoBusinessWithController extends Controller
{
    function list(){
        $do_business_with = Dobusinesswith_Model::
        join('video_categories','video_categories.id','=','do_business_with.category_id')
        ->select('video_categories.name as category','do_business_with.*')->paginate(10);
        $category = VideoCategory::where('status','active')->get();
        return view('do_business_with.list',compact('do_business_with','category'));
    }
    function create(){
        $categories = VideoCategory::where('status','active')->get();
        return view('do_business_with.create',compact('categories'));
    }
    function save(Request $request){
        $request->validate([
            'name'=>['required','string','max:255',Rule::unique('do_business_with', 'name')],
            'category'=>'required',
            'status'=>'required',
    ]);

    Dobusinesswith_Model::create([
        'name'=>$request->input('name'),
        'category_id'=>$request->input('category'),
        'status'=>$request->input('status'),
    ]);

    return redirect()->route('do_business_with.create')->with('success', 'Do Business With Created Successfully');

    }
    function edit(Request $request){
        $id = $request->input('id');
        $Dobusinesswith_Model = Dobusinesswith_Model::find($id);
        return response()->json(['Dobusinesswith_Model'=>$Dobusinesswith_Model]);
    }
    function update(Request $request){
        $id=$request->input('id');
        $role = Dobusinesswith_Model::find($id);
        $request->validate([
            'name'=>['required','string','max:255',Rule::unique('do_business_with', 'name')->ignore($id)],
            'category'=>'required',
            'status'=>'required',
       ]);
      
        $role->update([
            'category_id'=>$request->input('category'),
            'name'=>$request->input('name'),
            'status'=>$request->input('status'),
        ]);
        return redirect()->route('do_business_with.list')->with('success', 'Do Business With Updated successfully');
 
    }
    function delete(Request $request){
        $id=$request->input('id');
        $DoBusinessWith = Dobusinesswith_Model::find($id);

        if (!$DoBusinessWith) {
            return redirect()->route('do_business_with.list')->with('error', 'Do Business With for not found');
        }

        $DoBusinessWith->delete();

        return redirect()->route('do_business_with.list')->with('success', 'Do Business With for deleted successfully');
 
    }
}
