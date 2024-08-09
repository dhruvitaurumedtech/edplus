<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Base_table;
use App\Models\Chapter;
use App\Models\Institute_detail;
use App\Models\Standard_model;
use App\Models\Subject_model;
use App\Models\Topic_model;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    public function index()
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
      $topics = Topic_model::join('base_table', 'topic.base_table_id', '=', 'base_table.id')
            ->join('chapters', 'topic.chapter_id', '=', 'chapters.id')
            ->join('subject', 'topic.subject_id', '=', 'subject.id')
            ->join('standard', 'topic.standard_id', '=', 'standard.id')
            ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
            ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
            ->leftjoin('board', 'board.id', '=', 'base_table.board')
            ->select(
                'subject.name as subject',
                'chapters.chapter_name',
                'stream.name as sname',
                'standard.*',
                'medium.name as medium',
                'board.name as board',
                'base_table.id as base_id',
                'chapters.id as chapter_id',
                'subject.id as subject_id'
            )
            ->where('standard.status', 'active')->paginate(10);
        $subjects = Subject_model::get();
        $videolist = VideoCategory::get();
        $institute_list = Institute_detail::where('user_id', Auth::user()->id)->get();
        return view('topic.list', compact('Standard', 'topics', 'subjects', 'videolist', 'institute_list'));
    }
    function list_topic(Request $request)
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
        $institute_list = Institute_detail::where('user_id', Auth::user()->id)->get();
        $subjects = Subject_model::get();
        $videolist = VideoCategory::get();

        return view('topic.create', compact('Standard', 'institute_list', 'subjects', 'videolist'));
    }
    public function get_chapter(Request $request)
    {
        $subject_id = $request->subject_id;
        $chapter = Chapter::where('subject_id', $subject_id)->get();
        return response()->json(['chapter' => $chapter]);
    }
    public function topic_save(Request $request)
    {
        $request->validate([
            'standard_id' => 'required',
            'subject' => 'required',
            'chapter_id' => 'required',
            'topic_no.*' => 'required',
            'topic_name' => 'required',
            'topic_name.*' => 'required',
            'video_category_id.*' => 'required',
            'video_upload.*' => 'required|mimes:mp4,mov,avi|max:10240',
        ]);
        for ($i = 0; $i < count($request->input('topic_no')); $i++) {
            $base_table = Base_table::where('id', $request->input('standard_id'))->first();
            $topic_video = $request->file('topic_video')[$i];
            $topic_model = Topic_model::create([
                'user_id' => Auth::user()->id,
                'institute_id' => $request->input('institute_id'),
                'base_table_id' => $request->input('standard_id'),
                'standard_id' => $base_table->standard,
                'subject_id' => $request->input('subject'),
                'chapter_id' => $request->input('chapter_id'),
                'topic_no' => $request->input('topic_no')[$i],
                'topic_name' => $request->input('topic_name')[$i],
                'topic_video' => $topic_video->getClientOriginalName(),
                'video_category_id' => $request->input('video_category')[$i],
            ]);
        }
        if ($request->hasfile('topic_video')) {
            foreach ($request->file('topic_video') as $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path() . '/videos/', $name);
                $data[] = $name;
            }
            return back()->with('Success!', 'Data Added!');
        }
        return redirect()->route('list.topic')->with('success', 'Topic Created Successfully');
    }
    public function topic_list(Request $request)
    {
        $subject_id = $request->subject_id;
        $base_id = $request->base_id;
        $chapter_id = $request->chapter_id;

        $topic_list = Topic_model::where('subject_id', $subject_id)
            ->where('base_table_id', $base_id)
            ->where('chapter_id', $chapter_id)
            ->get();
        return response()->json(['topic_list' => $topic_list]);
    }
}
