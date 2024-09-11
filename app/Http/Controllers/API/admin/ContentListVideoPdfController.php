<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Base_table;
use App\Models\Topic_model;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PDF;
use Illuminate\Support\Facades\Validator;
use Exception;

class ContentListVideoPdfController extends Controller
{
    use ApiTrait;
    function content_list_video_pdf(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
            $base_table_response=Base_table::leftjoin('subject','subject.base_table_id','=','base_table.id')
                         ->leftjoin('subject_sub','subject_sub.subject_id','=','subject.id')
                         ->leftjoin('standard','standard.id','=','base_table.standard')
                         ->leftjoin('board','board.id','=','base_table.board')
                         ->leftjoin('medium','medium.id','=','base_table.medium')
                         ->select('subject.id','board.name as board_name','standard.name as standard_name','medium.name as medium_name','subject.name as subject_name')
                         ->when(!empty($request->institute_id), function ($query) use ($request) {
                            return $query->where('subject_sub.institute_id', $request->institute_id);
                            })
                         ->groupBy(
                            'subject.id',
                            'board.name', 
                            'standard.name', 
                            'medium.name', 
                            'subject.name'
                         )
                         ->get()->toarray();
                        //  print_r($base_table_response);exit;
                        
                         $topic_response=Topic_model::leftjoin('chapters','chapters.id','=','topic.chapter_id')->when(!empty($request->institute_id), function ($query) use ($request) {
                            return $query->where('institute_id', $request->institute_id);
                            })
                         ->select('topic.*','chapters.chapter_name','chapters.chapter_no','chapters.chapter_image')
                         ->get()->toarray();
                         $data = ['base_table_response'=>$base_table_response,'topic_response'=>$topic_response];
                         
                $pdf = PDF::loadView('pdf.topicvideopdf', ['data' => $data])->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);;
                $folderPath = public_path('pdfs');

                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0755, true);
                }

                $baseFileName = 'topicvideopdf.pdf';
                $pdfPath = $folderPath . '/' . $baseFileName;
                $counter = 1;
                while (File::exists($pdfPath)) {
                    $pdfPath = $folderPath . '/topicvideopdf' . $counter . '.pdf'; 
                    $counter++;
                }
                file_put_contents($pdfPath, $pdf->output());
                $pdfUrl = asset('pdfs/' . basename($pdfPath));
                
                return $this->response($pdfUrl);
        } catch (Exception $e) {
            return $this->response([], "Something want wrong!.", false, 400);
        }
    }
}
