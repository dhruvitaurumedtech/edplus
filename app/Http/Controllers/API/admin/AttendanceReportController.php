<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance_model;
use App\Models\Student_detail;
use App\Traits\ApiTrait;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceReportController extends Controller
{
    use ApiTrait;
    function attendance_report_pdf(Request $request){
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try{
           
            $pdf = PDF::loadView('pdf.attendance_report', ['data' => $data]);

            $folderPath = public_path('pdfs');

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $baseFileName = 'attendance_report.pdf';
            $pdfPath = $folderPath . '/' . $baseFileName;

            $counter = 1;
            while (File::exists($pdfPath)) {
                $pdfPath = $folderPath . '/attendance_report' . $counter . '.pdf'; 
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
