<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Base_table;
use App\Models\board;
use App\Models\Class_model;
use App\Models\Institute_detail;
use App\Models\Institute_for_model;
use App\Models\Medium_model;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InstituteController extends Controller
{
    public function list_admin() {
        $users = User::where('role_type',[2,3])->paginate(10); 
        return view('admin.list', compact('users'));
    }
    public function list_institute(){
        $institute_list = Institute_detail::paginate(10); 
        return view('institute/list_institute',compact('institute_list'));

    }
    public function create_institute(){
        $institute_for_array = DB::table('base_table')
                    ->leftJoin('institute_for', 'institute_for.id', '=', 'base_table.institute_for')
                    ->select(
                        'institute_for.name as institute_for_name',
                        DB::raw('ANY_VALUE(base_table.id) as id'), // Use ANY_VALUE for non-aggregated columns
                        'institute_for.id as institute_for_id'
                    )
                    ->groupBy('institute_for.name', 'base_table.institute_for', 'institute_for.id')
                    ->whereNull('base_table.deleted_at')
                    ->get();
        $board_array = DB::table('base_table')
                    ->leftJoin('board', 'board.id', '=', 'base_table.board')
                    ->select('board.name as board_name', 'base_table.id', 'board.id as board_id')
                    ->whereNull('base_table.deleted_at')
                    ->whereRaw('base_table.id = (SELECT id FROM base_table b WHERE b.board = base_table.board ORDER BY b.id LIMIT 1)')
                    ->get();   
        $medium_array = Base_table::leftJoin('medium', 'medium.id', '=', 'base_table.medium')
                    ->select('base_table.id', DB::raw('GROUP_CONCAT(DISTINCT medium.name) as medium_name'))
                    ->whereNull('base_table.deleted_at')
                    ->whereRaw('base_table.id = (SELECT m.id FROM base_table m WHERE m.medium = base_table.medium ORDER BY m.id LIMIT 1)')
                    ->groupBy('base_table.id')
                    ->get()
                    ->toArray();
                
                
               
        $class_array = DB::table('base_table')
                    ->leftJoin('class', 'class.id', '=', 'base_table.institute_for_class')
                    ->select('base_table.id', DB::raw('GROUP_CONCAT(DISTINCT class.name) as class_name'))
                    ->whereNull('base_table.deleted_at')
                    ->whereRaw('base_table.id = (SELECT m.id FROM base_table m WHERE m.institute_for_class = base_table.institute_for_class ORDER BY m.id LIMIT 1)')
                    ->groupBy('base_table.id')
                    ->get()
                    ->toArray();
                
                            
                
                // dd($medium_array);
                
                
                                
        // echo "<pre>";print_r($medium_array );exit;
        return view('institute/create_institute',compact('institute_for_array','board_array','medium_array','class_array'));
    }
    public function create_institute_for(){
        $institute_for = Institute_for_model::paginate(10); 
        return view('institute/create_institute_for',compact('institute_for'));
    
    }
    public function list_institute_for(){
        $institute_for = Institute_for_model::paginate(10); 
        return view('institute/list_institute_for',compact('institute_for'));

    }
    public function institute_for_save(Request $request){
        // dd($request->all());exit;
        $request->validate([
                'icon' =>'required|image|mimes:svg|max:2048',
                'name'=>['required','string','max:255',Rule::unique('institute_for', 'name')],
                'status'=>'required',
        ]);
        $iconFile = $request->file('icon');
        $imagePath = $iconFile->store('icon', 'public');


        Institute_for_model::create([
            'name'=>$request->input('name'),
            'icon'=>$imagePath,
            'status'=>$request->input('status'),
        ]);
      return redirect()->route('institute_for.list')->with('success', 'Institute For Created Successfully');
   
    }
    public function institute_for_edit(Request $request){
        $id = $request->input('institute_id');
        $Institute_for_model = Institute_for_model::find($id);
        return response()->json(['Institute_for_model'=>$Institute_for_model]);
    }
    public function institute_for_update(Request $request){
        $id=$request->input('institute_id');
        $role = Institute_for_model::find($id);
        $request->validate([
            'name'=>['required','string','max:255',Rule::unique('institute_for', 'name')->ignore($id)],
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
        return redirect()->route('institute_for.list')->with('success', 'Institute For Updated successfully');
    }
    public function institute_for_delete(Request $request){
        $institute_id=$request->input('institute_id');
        $institute_for = Institute_for_model::find($institute_id);

        if (!$institute_for) {
            return redirect()->route('institute_for.list')->with('error', 'Institute for not found');
        }

        $institute_for->delete();

        return redirect()->route('institute_for.list')->with('success', 'Institute for deleted successfully');
  }
  function institute_register(Request $request){
    echo "<pre>";print_r($request->All());exit;
  }
}