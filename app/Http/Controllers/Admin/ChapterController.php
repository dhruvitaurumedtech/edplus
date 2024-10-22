<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Subject_model;
use App\Models\Standard_model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Claims\Subject;
use Illuminate\Pagination\Paginator;


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
        $subjects = Subject_model::get();
        return view('chapter.create', compact('Standard', 'subjects'));
    }
    public function chapter_list(Request $request)
    {
        $Standards = Chapter::join('base_table', 'chapters.base_table_id', '=', 'base_table.id')
        ->leftJoin('standard', 'standard.id', '=', 'base_table.standard')
        ->leftJoin('stream', 'stream.id', '=', 'base_table.stream')
        ->leftJoin('medium', 'medium.id', '=', 'base_table.medium')
        ->leftJoin('board', 'board.id', '=', 'base_table.board')
        ->leftJoin("subject", 'subject.id', '=', "chapters.subject_id")
        ->select(
            'subject.id as subject_id',
            'standard.name as standard_name',
            'medium.name as medium',
            'board.name as board',
            'base_table.id as base_id',
            'subject.name as subject_name',
            'chapters.*'
        )
        ->get(); // Fetch all without pagination
    
    // Group the data
    $StandardsGrouped = $Standards->groupBy(function ($item) {
        return $item->standard_name . ' ' . $item->sname . ' (' . $item->board . ', ' . $item->medium . ')';
    });
    
    // Prepare data for pagination after grouping
    $data = [];
    foreach ($StandardsGrouped as $key => $group) {
        $standardData = [
            'standard_name' => $key,
            'subjects' => []
        ];
    
        $groupBySubject = $group->groupBy('subject_id');
    
        foreach ($groupBySubject as $subjectId => $subjectItems) {
            $standardData['subjects'][] = [
                'subject_id' => $subjectId,
                'subject_name' => $subjectItems->first()->subject_name,
                'chapters' => $subjectItems
            ];
        }
    
        $data[] = $standardData;
    }
    
    $currentPage = Paginator::resolveCurrentPage();
    $perPage = 4;
    $currentPageItems = array_slice($data, ($currentPage - 1) * $perPage, $perPage);
    
    $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentPageItems,
        count($data), // Total items count
        $perPage, // Items per page
        $currentPage,
        ['path' => Paginator::resolveCurrentPath()] // Path for pagination links
    );
    
    // Return the paginated data
    $data = [
        'data' => $paginatedData->items(), // Paginated items
        'pagination' => [
            'total' => $paginatedData->total(),
            'current_page' => $paginatedData->currentPage(),
            'per_page' => $paginatedData->perPage(),
            'last_page' => $paginatedData->lastPage(),
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
        ]
    ];
    // echo "<pre>";print_r($data);exit;
    return view('chapter.list', compact('data'));
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
    //    echo "<pre>"; print_r($request->all());exit;
        $request->validate([
            'standard_id' => 'required',
            'subject' => 'required',
            'chapter_no' => 'required|array',
            'chapter_no.*' => 'required',
            'chapter_name' => 'required|array',
            'chapter_name.*' => 'required',
            'chapter_image' => 'required|array',
            'chapter_image.*' => 'required|mimes:svg,jpeg,png,pdf',
        ], [
            'chapter_no.*.required' => 'Chapter number is required.',
            'chapter_name.*.required' => 'Chapter name is required.',
            'chapter_image.*.required' => 'Chapter image is required.',
            'chapter_image.*.mimes' => 'Chapter image must be a valid SVG, JPEG, PNG, or PDF file.',
        ]);
        // $request->validate([
        //     'standard_id' => 'required|integer',
        //     'subject' => 'required',
        //     'chapter_no' => 'required|array',
        //     'chapter_name' => 'required|array',
        //     'chapter_image' => 'nullable|array',
        // ]);
       
            foreach ($request->chapter_name as $i => $chapterName) {

                $exists = Chapter::where('subject_id', $request->input('subject'))
                                  ->where('base_table_id', $request->input('standard_id'))
                                  ->where('chapter_name', $request->input('chapter_name')[$i])
                                  ->where('chapter_no', $request->input('chapter_no')[$i])
                                  
                                  ->exists();
                if ($exists) {
                    return redirect()->route('chapter.list')->with('error', 'Already Chapter Exists!');
                } 
                else 
                {
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
          
            // exit;
        
            return redirect()->route('chapter.list')->with('success', 'Chapter Created Successfully');
        }
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
        // $Standard = Chapter::join('base_table', 'chapters.base_table_id', '=', 'base_table.id')
        // ->leftJoin('standard', 'standard.id', '=', 'base_table.standard')
        // ->leftJoin('stream', 'stream.id', '=', 'base_table.stream')
        // ->leftJoin('medium', 'medium.id', '=', 'base_table.medium')
        // ->leftJoin('board', 'board.id', '=', 'base_table.board')
        // ->leftJoin("subject",'subject.id','=',"chapters.subject_id")
        // ->select(
        //     'subject.id as subject_id',
        //     'standard.name as standard_name',
        //     'medium.name as medium',
        //     'board.name as board',
        //     'base_table.id as base_id',
        //     'subject.name as subject_name',
        //     'chapters.*' 
        // )  
       
        // ->where('chapters.subject_id', $id)
        // ->paginate(10);
        // $Standard = $Standard->groupBy(function ($item) {
        //     return  $item->base_id;  
        // });
        

        // $formattedData = [];

        // foreach ($Standard as $key => $items) {  
        //     $createdObj = '{}'; 
        //     $tempObj = json_decode($createdObj);  
        //     $tempObj->base_id=$key;
        //     $tempObj->data=$items;
            
        //     array_push($formattedData,$tempObj);
           
        // } 
        // $Standard = Standard_model::join('base_table', 'standard.id', '=', 'base_table.standard')
        //     ->leftjoin('chapters', 'chapters.base_table_id', '=', 'base_table.id')
        //     ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
        //     ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
        //     ->leftjoin('board', 'board.id', '=', 'base_table.board')
        //     ->select(
        //         'stream.name as sname',
        //         'standard.*',
        //         'medium.name as medium',
        //         'board.name as board',
        //         'base_table.id as base_id',
        //         'chapters.subject_id',
        //         'chapters.id as chapter_id',
        //         'chapters.chapter_no',
        //         'chapters.chapter_name',
        //         'chapters.chapter_image'
        //     )
        //     ->where('standard.status', 'active')
        //     ->where('chapters.subject_id', $id)->get();
        // echo "<pre>";print_r($Standard);exit;
                $Standards = Chapter::join('base_table', 'chapters.base_table_id', '=', 'base_table.id')
                ->leftJoin('standard', 'standard.id', '=', 'base_table.standard')
                ->leftJoin('stream', 'stream.id', '=', 'base_table.stream')
                ->leftJoin('medium', 'medium.id', '=', 'base_table.medium')
                ->leftJoin('board', 'board.id', '=', 'base_table.board')
                ->leftJoin("subject", 'subject.id', '=', "chapters.subject_id")
                ->select(
                    'subject.id as subject_id',
                    'standard.name as standard_name',
                    'medium.name as medium',
                    'board.name as board',
                    'base_table.id as base_id',
                    'subject.name as subject_name',
                    'chapters.*'
                )
                ->where('chapters.id', $id)
                ->paginate(10);
            
            $Standards = $Standards->groupBy(function ($item) {
                return $item->standard_name . ' ' . $item->sname . ' (' . $item->board . ', ' . $item->medium . ')';
            });
            $data = [];

        foreach ($Standards as $key => $group) {
            // Create an entry for each standard group
            $standardData = [
                'standard_name' => $key,
                'subjects' => [] // Initialize an empty array for subjects
            ];

            $groupBySubject = $group->groupBy('subject_id');

            foreach ($groupBySubject as $subjectId => $subjectItems) {
                $standardData['subjects'][] = [
                    'subject_id' => $subjectId,
                    'subject_name' => $subjectItems->first()->subject_name, // Get subject name from the first item
                    'chapters' => $subjectItems // Attach the chapters for this subject
                ];
            }

            $data[] = $standardData; // Add the standard data to the final array
        }
        $Standard_list = Standard_model::join('base_table', 'standard.id', '=', 'base_table.standard')

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
            ->where('standard.status', 'active')
            ->get();
        $subject = Subject_model::get();
        // echo "<pre>";print_r($data);exit;
        return view('chapter.edit', compact('data', 'subject', 'Standard_list'));
    }
    public function chapter_update(Request $request)
    {
        // echo "<pre>";print_r($request->file('chapter_image'));exit;
        foreach ($request->chapter_name as $i => $chapterName) {
            $chapter_image = $request->file('chapter_image')[$i] ?? $request->input('old_chapter_image')[$i];
    
            if (!empty($request->chapter_id)) {
                $chapter = Chapter::findOrFail($request->chapter_id);
    
                $chapterQuery  = Chapter::where('subject_id', $request->input('subject'))
                    ->where('base_table_id', $request->input('standard_id'))
                    ->where('chapter_name', $chapterName)
                    ->where('chapter_no', $request->input('chapter_no')[$i]);
                    if (!empty($request->chapter_id)) {
                        $chapterQuery ->where('id', '!=', $request->chapter_id);
                    }
                    
                    if (!empty($request->file('chapter_image')[$i])) {
                        $chapterQuery ->where('chapter_image', 'chapter/'.$request->file('chapter_image')[$i]);
                    }
                    
                    $exists = $chapterQuery->exists();
    
                if ($exists) {
                    return redirect()->route('chapter.list')->with('error', 'Chapter already exists!');
                }
    
                if ($request->hasFile('chapter_image') && $request->file('chapter_image')[$i]) {
                    $chapter_imageFile = $request->file('chapter_image')[$i];
                    $imagePath = $chapter_imageFile->store('chapter', 'public');
                    $chapter->chapter_image = $imagePath; 
                }
            } else {
                $chapter = new Chapter();
                $exists = Chapter::where('subject_id', $request->input('subject'))
                    ->where('base_table_id', $request->input('standard_id'))
                    ->where('chapter_name', $chapterName)
                    ->where('chapter_no', $request->input('chapter_no')[$i])
                    ->exists();
    
                if ($exists) {
                    return redirect()->route('chapter.list')->with('error', 'Chapter already exists!');
                }
    
                if ($request->hasFile('chapter_image') && $request->file('chapter_image')[$i]) {
                    $chapter_imageFile = $request->file('chapter_image')[$i];
                    $imagePath = $chapter_imageFile->store('chapter', 'public');
                    $chapter->chapter_image = $imagePath; 
                }
            }
            
            $chapter->base_table_id = $request->input('standard_id');
            $chapter->subject_id = $request->input('subject');
            $chapter->chapter_no = $request->input('chapter_no')[$i];
            $chapter->chapter_name = $chapterName;
    
            $chapter->save();
        }
    
        return redirect()->route('chapter.list')->with('success', 'Chapters processed successfully.');
        
       
        
    }
    function chapter_delete(Request $request)
    {
        
        try{
        $chapter_id = $request->input('id');
        

        
        $class_list = Chapter::where('id', $chapter_id);
            if ($class_list) {
                $class_list->delete();
                return redirect()->route('chapter.list')->with('success', 'Chapters deleted successfully');
            
            }else{
                return redirect()->route('chapter.list')->with('error', 'Chapters not found');
            }
        }catch (QueryException $e) {
                if ($e->getCode() == '23000') { 
                    return redirect()->route('chapter.list')->with('error','Cannot delete chapter as it has related topics.');
                }

                return redirect()->route('chapter.list')->with('error','An error occurred while deleting the chapter.');
            }
                
    }
    public function chapter_subject_edit(Request $request){
         $chapter_id = $request->chapter_id;
        
        $chapter_subject = Chapter::leftjoin('subject','subject.id','=','chapters.subject_id')
                      ->select('chapters.*','subject.name','subject.id as subject_id')
                      ->where('chapters.id', $chapter_id)
                      ->get();
                      
                    
        $base_table_id = $request->base_table_id;
        $subject_list=Subject_model::where('base_table_id',$base_table_id)->select('*')->get();       
        return response()->json(['chapter_subject' => $chapter_subject,'subject_list'=>$subject_list]);
    }

    public function chapter_subject_update(Request $request){
        // print_r($request->all());exit;
        $chapter_id = $request->input('chapter_id');
        $subject_id  = $request->input('subject_id');
        $base_table_id  = $request->input('base_table_id');

        $exists = Chapter::where('subject_id', $subject_id)
        ->exists();
        if ($exists) {
            return redirect()->route('chapter.list')->with('error', 'Subject already assigned to this chapter!');
        } else {
       
        $chapter = Chapter::where('subject_id', $subject_id); 

        if ($chapter) {
         $chapter->update([
        'subject_id' => $subject_id,
        ]);
        return redirect()->route('chapter.list')->with('success', 'Chapter Subject Updated successfully');
        } else {
        return redirect()->route('chapter.list')->with('error', 'Chapter not found!');
        }
        }

     
    }
}
