<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
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
            $topic_response=Topic_model::leftjoin('base_table','base_table.id','=','topic.base_table_id')
                         ->leftjoin('subject','subject.id','=','topic.subject_id')
                         ->leftjoin('standard','standard.id','=','topic.standard_id')
                         ->leftjoin('board','board.id','=','base_table.board')
                         ->leftjoin('medium','medium.id','=','base_table.medium')
                         ->select('topic.*','board.name as board_name','standard.name as standard_name','medium.name as medium_name','subject.name as subject_name')
                         ->when(!empty($request->institute_id), function ($query) use ($request) {
                            return $query->where('topic.institute_id', $request->institute_id);
                            })
                         ->where('topic.institute_id',$request->institute_id)  
                         ->get()->toarray();
                $pdf = PDF::loadView('pdf.topicvideopdf', ['data' => $topic_response])->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);;
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
