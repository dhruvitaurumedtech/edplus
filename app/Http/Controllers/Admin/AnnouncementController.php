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
            $institute_list = Institute_detail::whereIn('id', explode(',', $value['institute_id']))->get()->toarray();
            $teacher_list = User::whereIn('id', explode(',', $value['teacher_id']))->get()->toarray();
            $response[] = [
                'announcement' => $value['announcement'],
                'institute_show' => $institute_list,
                'teacher_show' => $teacher_list,
            ];
        }



        return view('announcement/create', compact('institute_list', 'teachers', 'response'));
    }

    public function create()
    {
    }

    public function save(Request $request)
    {
        $rules = [
            'announcement' => 'required|string',
        ];

        // Validate the request
        $validator = \Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
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

    public function edit(string $id)
    {
    }

    public function update(Request $request, string $id)
    {
    }

    public function destroy(string $id)
    {
    }
}
