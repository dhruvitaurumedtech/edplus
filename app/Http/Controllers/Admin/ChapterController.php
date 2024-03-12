<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Subject_model;
use App\Models\Standard_model;
use PHPOpenSourceSaver\JWTAuth\Claims\Subject;

class ChapterController extends Controller
{
    public function add_lists()
    {
        $Standard = Standard_model::
              join('base_table','standard.id','=','base_table.standard')
        ->leftjoin('stream','stream.id','=','base_table.stream')
        ->leftjoin('medium','medium.id','=','base_table.medium')
        ->leftjoin('board','board.id','=','base_table.board')
        ->select('stream.name as sname','standard.*','medium.name as medium',
        'board.name as board','base_table.id as base_id')
        ->where('standard.status','active')->get();

        $Standards = Standard_model::
              join('base_table','standard.id','=','base_table.standard')
        ->leftjoin('stream','stream.id','=','base_table.stream')
        ->leftjoin('medium','medium.id','=','base_table.medium')
        ->leftjoin('board','board.id','=','base_table.board')
        ->select('stream.name as sname','standard.*','medium.name as medium',
        'board.name as board','base_table.id as base_id')
        ->where('standard.status','active')->paginate(10);

        $subjects = Subject_model::get();
        return view('chapter.list',compact('Standard','Standards','subjects'));
    }

    //strandard wise data
    public function get_subjects(Request $request){
        $bas_id = $request->standard_id;
        $subject = Subject_model::where('base_table_id',$bas_id)->get();
        return response()->json(['subject'=>$subject]);
    }

    //chapter_save
    public function chapter_save(Request $request){
        $request->validate([
            'standard_id' => 'required',
            'subject' => 'required',
            'chapter_no.*' => 'required',
            'chapter_name.*' => 'required',
            'chapter_image.*' => 'required|mimes:svg,jpeg,png,pdf|max:2048',
        ]);

    foreach ($request->chapter_name as $i => $chapterName) {  
        
        $chapter_imageFile = $request->file('chapter_image')[$i];
        $imagePath = $chapter_imageFile->store('chapter', 'public');

        $base_table = Chapter::create([
            'subject_id' => $request->input('subject'),
            'base_table_id' => $request->input('standard_id'),
            'chapter_no'=>$request->input('chapter_no')[$i],
            'chapter_name' => $chapterName,
            'chapter_image' => $imagePath,
            //'status' => $request->input('status'),
        ]);
    }
        return redirect()->route('chapter.list')->with('success', 'Chapter Created Successfully');
    }

    //chapter_lists
    public function chapter_lists(Request $request){
        $subject_id = $request->subject_id;
        $base_id = $request->base_id;

        $chapters = Chapter::where('subject_id',$subject_id)
        ->where('base_table_id',$base_id)->get();
        return response()->json(['chapters'=>$chapters]);
    }

}
