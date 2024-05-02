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
        $Standard = Standard_model::join('base_table', 'standard.id', '=', 'base_table.standard')
            ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
            ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
            ->leftjoin('board', 'board.id', '=', 'base_table.board')
            ->select(
                'stream.name as sname',
                'standard.*',
                'medium.name as medium',
                'board.name as board',
                'base_table.id as base_id'
            )
            ->where('standard.status', 'active')->get();

        // $Standards = Standard_model::join('base_table', 'standard.id', '=', 'base_table.standard')
        //     ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
        //     ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
        //     ->leftjoin('board', 'board.id', '=', 'base_table.board')
        //     ->select(
        //         'stream.name as sname',
        //         'standard.*',
        //         'medium.name as medium',
        //         'board.name as board',
        //         'base_table.id as base_id'
        //     )
        //     ->where('standard.status', 'active')->paginate(10);

        $subjects = Subject_model::get();
        // return view('chapter.create',compact('Standard','Standards','subjects'));

        return view('chapter.create', compact('Standard'));
    }
    public function chapter_list(Request $request)
    {

        $Standards = Chapter::leftjoin('base_table', 'chapters.base_table_id', '=', 'base_table.id')
            ->leftjoin('standard', 'standard.id', '=', 'base_table.standard')
            ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
            ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
            ->leftjoin('board', 'board.id', '=', 'base_table.board')
            ->leftjoin('subject', 'subject.id', '=', 'chapters.subject_id')
            ->select(
                'stream.name as sname',
                'standard.*',
                'medium.name as medium',
                'board.name as board',
                'base_table.id as base_id',
                'chapters.chapter_name',
                'chapters.chapter_no',
                'chapters.chapter_image',
                'subject.name as subject_name',
                'chapters.id as chapter_id'
            )
            ->where('standard.status', 'active')->paginate(10);

        return view('chapter.list', compact('Standards'));
    }
    //strandard wise data
    public function get_subjects(Request $request)
    {
        $bas_id = $request->standard_id;
        $subject = Subject_model::where('base_table_id', $bas_id)->get();
        return response()->json(['subject' => $subject]);
    }

    //chapter_save
    public function chapter_save(Request $request)
    {

        $request->validate([
            'standard_id' => 'required',
            'subject' => 'required',
            'chapter_no' => 'required|array', // Ensuring chapter_no is an array
            'chapter_no.*' => 'required', // Validating each chapter_no element
            'chapter_name' => 'required|array', // Ensuring chapter_name is an array
            'chapter_name.*' => 'required', // Validating each chapter_name element
            'chapter_image' => 'required|array', // Ensuring chapter_image is an array
            'chapter_image.*' => 'required|mimes:svg,jpeg,png,pdf|max:2048', // Validating each chapter_image element
        ], [
            'chapter_no.*.required' => 'Chapter number is required.',
            'chapter_name.*.required' => 'Chapter name is required.',
            'chapter_image.*.required' => 'Chapter image is required.',
            'chapter_image.*.mimes' => 'Chapter image must be a valid SVG, JPEG, PNG, or PDF file.',
            'chapter_image.*.max' => 'Chapter image may not be greater than 2048 kilobytes in size.',
        ]);


        foreach ($request->chapter_name as $i => $chapterName) {
            $chapter_imageFile = $request->file('chapter_image')[$i];
            $imagePath = $chapter_imageFile->store('chapter', 'public');

            $base_table = Chapter::create([
                'subject_id' => $request->input('subject'),
                'base_table_id' => $request->input('standard_id'),
                'chapter_no' => $request->input('chapter_no')[$i],
                'chapter_name' => $chapterName,
                'chapter_image' => $imagePath,
                //'status' => $request->input('status'),
            ]);
        }
        return redirect()->route('chapter.list')->with('success', 'Chapter Created Successfully');
    }

    //chapter_lists
    public function chapter_lists(Request $request)
    {
        $subject_id = $request->subject_id;
        $base_id = $request->base_id;

        $chapters = Chapter::where('subject_id', $subject_id)
            ->where('base_table_id', $base_id)->get();
        return response()->json(['chapters' => $chapters]);
    }
    function chapter_edit(Request $request, $id)
    {
        $Standard = Standard_model::join('base_table', 'standard.id', '=', 'base_table.standard')
            ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
            ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
            ->leftjoin('board', 'board.id', '=', 'base_table.board')
            ->select(
                'stream.name as sname',
                'standard.*',
                'medium.name as medium',
                'board.name as board',
                'base_table.id as base_id'
            )
            ->where('standard.status', 'active')->get();


        $subjects = Subject_model::get();

        return view('chapter.edit', compact('Standard'));
    }
}
