<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\VideoCategory;

class VideoCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $board_list = VideoCategory::whereNull('deleted_at')->paginate(10);
        return view('videocategory.list', compact('board_list'));
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
    public function save(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255', // You can adjust the max length according to your needs
            'status' => 'required|in:active,inactive',
        ]);
    
        $video = VideoCategory::create($validatedData);
            return redirect()->route('videocategory.list')->with('success', 'Video created successfully!');
    
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
    public function edit(Request $request)
    {
        $id = $request->input('video_category_id');
        $video_category_list = VideoCategory::find($id);
        return response()->json(['video_category_list'=>$video_category_list]);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id=$request->input('video_category_id');
        $class = VideoCategory::find($id);
        $request->validate([
            'name' => 'required|max:255',
            'status' => 'required|in:active,inactive',
      ]);
      
        $class->update([
            'name'=>$request->input('name'),
            'status'=>$request->input('status'),
        ]);
        return redirect()->route('videocategory.list')->with('success', 'Video Category Updated successfully');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $video_category_id=$request->input('video_category_id');
        $videocategorylist = VideoCategory::find($video_category_id);

        if (!$videocategorylist) {
            return redirect()->route('videocategory.list')->with('error', 'Subject not found');
        }

        $videocategorylist->delete();

        return redirect()->route('videocategory.list')->with('success', 'Video Category deleted successfully');
  
    }
}
