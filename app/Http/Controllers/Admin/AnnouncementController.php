<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common_announcement;
use App\Models\User;
use App\Models\Institute_detail;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function announcement_create()
    {
        $institute_list = Institute_detail::get()->toArray();
        $teachers = User::where('role_type', 4)->get();
        $announcement = Common_announcement::get()->toarray();
        foreach ($announcement as $value) {
            $institute_response = Institute_detail::whereIn('id', explode(',', $value['institute_id']))->get()->toarray();
            $teacher_response = User::whereIn('id', explode(',', $value['teacher_id']))->get()->toarray();
            $response[] = [
                'id' => $value['id'],
                'announcement' => $value['announcement'],
                'institute_show' => $institute_response,
                'teacher_show' => $teacher_response,
            ];
        }




        return view('announcement/create', compact('institute_list', 'teachers', 'response'));
    }

    public function create()
    {
    }

    public function save(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'teacher_id' => 'required',
            'announcement' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validation passed, create a new announcement
        $announcement = Common_announcement::create([
            'institute_id' => implode(",", $request->institute_id),
            'teacher_id' => implode(",", $request->teacher_id),
            'announcement' => $request->announcement,
        ]);

        // Optionally, you can return a response indicating success
        return redirect('announcement-create')->with('success', 'Announcement created successfully.');
    }

    public function show(string $id)
    {
    }

    public function edit(Request $request)
    {
        $announcement = Common_announcement::where('id', $request->anouncement_id)->get()->toarray();

        return response()->json(['announcement' => $announcement]);
    }

    public function update(Request $request, string $id)
    {
    }

    public function destroy(string $id)
    {
    }
}
