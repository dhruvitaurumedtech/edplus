<?php

namespace App\Http\Controllers;

use App\Models\PdfAssignToBatch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiTrait;

class PdfController extends Controller
{
    use ApiTrait;
    /**
     * Display a listing of the resource.
     */

    public function pdfAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'batch_id' => 'required|exists:batches,id',
            'standard_id' => 'required',
            'chapter_id' => 'required',
            'subject_id' => 'required',
            'user_id'  => 'required',
            'pdf_id' => 'required',
            'assign_status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $batch_ids = explode(",", $request->batch_id);

            foreach ($batch_ids as $batch_id_value) {
                if (
                    PdfAssignToBatch::where('batch_id', $batch_id_value)
                    ->where('subject_id', $request->subject_id)
                    ->where('pdf_id', $request->pdf_id)
                    ->count() > 0
                ) {
                    return $this->response([], "Already Assign PDF This Batch!", false, 400);
                }

                if (
                    PdfAssignToBatch::where('batch_id', $batch_id_value)
                    ->where('subject_id', $request->subject_id)
                    ->count() >= 4
                ) {
                    return $this->response([], "Four records with the same Batch and Subject already exist", false, 400);
                }
            }

            foreach ($batch_ids as $value) {
                PdfAssignToBatch::create([
                    'pdf_id' => $request->pdf_id,
                    'batch_id' => $value,
                    'standard_id' => $request->standard_id,
                    'chapter_id' => $request->chapter_id,
                    'subject_id' => $request->subject_id,
                    'assign_status' => $request->assign_status,
                ]);
            }
            return $this->response([], "PDF Assign Batch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


}
