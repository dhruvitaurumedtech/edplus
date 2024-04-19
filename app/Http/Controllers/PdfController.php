<?php

namespace App\Http\Controllers;

use App\Models\PdfAssignToBatch;
use App\Models\User;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pdfAssign(Request $request)
    {
        $batch_id = $request->batch_id;
        $pdf_id = $request->pdf_id;
        $user_id = $request->user_id;
        $standard_id = $request->standard_id;
        $chapter_id = $request->chapter_id;
        $subject_id = $request->subject_id;

        $token = $request->header('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $existingUser = User::where('token', $token)->where('id', $user_id)->first();
        if ($existingUser) {
            $validator = \Validator::make($request->all(), [
                'batch_id' => 'required|exists:batches,id',
                'standard_id' => 'required',
                'chapter_id' => 'required',
                'subject_id' => 'required',
                'user_id'  => 'required',
                'pdf_id' => 'required',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'success' => 400,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }
            $batch_ids = explode(",", $batch_id);
            foreach ($batch_ids as $batch_id_value) {
                $record = PdfAssignToBatch::where('batch_id', $batch_id_value)
                    ->where('subject_id', $subject_id)
                    ->count();
                if ($record > 0) {
                    return response()->json([
                        'success' => 400,
                        'message' => 'Already Assign PDF This Batch!',
                    ], 400);
                }
                $existingRecordsCount = PdfAssignToBatch::where('batch_id', $batch_id_value)
                    ->where('subject_id', $subject_id)
                    ->count();
                if ($existingRecordsCount >= 4) {
                    return response()->json([
                        'success' => 400,
                        'message' => 'Four records with the same Batch and Subject already exist',
                    ], 400);
                }
            }
            // video_assignbatch::whereIn('b')
            foreach ($batch_ids as $value) {
                $VideoAssignToBatch = PdfAssignToBatch::create([
                    'pdf_id' => $pdf_id,
                    'batch_id' => $value,
                    'standard_id' => $standard_id,
                    'chapter_id' => $chapter_id,
                    'subject_id' => $subject_id
                ]);
            }

            return response()->json([
                'success' => 400,
                'message' => 'Video Assign Batch Successfully',
            ], 400);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token.',
            ]);
        }
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
