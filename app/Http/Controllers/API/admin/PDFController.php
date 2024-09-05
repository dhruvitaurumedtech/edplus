<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Student_detail;
use App\Traits\ApiTrait;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class PDFController extends Controller
{
    use ApiTrait;
    public function index(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'institute_id' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->response([], $validator->errors()->first(), false, 400);
            }
        
        $data=Student_detail::join('users','users.id','=','students_details.student_id')
                      ->join('standard','standard.id','=','students_details.standard_id')
                      ->join('board','board.id','=','students_details.board_id')
                      ->join('medium','medium.id','=','students_details.medium_id')
                      ->select('users.*','board.name as board_name','standard.name as standard_name','medium.name as medium_name')
                      ->where('institute_id',$request->institute_id)
                      ->get()->toarray();
        
        $pdf = PDF::loadView('pdf.studentlist', ['data' => $data]);

        $folderPath = public_path('pdfs');

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $baseFileName = 'studentlist.pdf';
        $pdfPath = $folderPath . '/' . $baseFileName;

        $counter = 1;
        while (File::exists($pdfPath)) {
            $pdfPath = $folderPath . '/studentlist' . $counter . '.pdf'; 
            $counter++;
        }
        
        file_put_contents($pdfPath, $pdf->output());
        $pdfUrl = asset('pdfs/' . basename($pdfPath));
        return $this->response($pdfUrl);
        } catch (Exception $e) {
            return $this->response([], "Something want wrong!.", false, 400);
        }
    }
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
