<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Standard_model;
use App\Models\Subject_model;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
        return view('topic.list',compact('Standard','Standards','subjects'));
    }
    public function get_chapter(Request $request){
        echo $subject_id = $request->subject_id;
        $chapter = Chapter::where('subject_id',$subject_id)->get();
        return response()->json(['chapter'=>$chapter]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
