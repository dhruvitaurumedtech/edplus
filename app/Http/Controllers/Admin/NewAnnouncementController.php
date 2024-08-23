<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common_announcement;
use App\Models\Institute_detail;
use App\Models\Parents;
use App\Models\Student_detail;
use App\Models\Teacher_model;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\SendPushNotification;
use Illuminate\Support\Facades\Notification;


class NewAnnouncementController extends Controller
{
    public function announcement_create()
    {
        $institute_list = Institute_detail::get()->toArray();
        $UserRoleBy = User::whereIn('role_type', [3, 4, 5, 6])->get();
        $announcement = Common_announcement::get()->toarray();
        $response = [];
        foreach ($announcement as $value) {
            $institute_response = Institute_detail::whereIn('id', explode(',', $value['institute_id']))->get()->toarray();
            $teacher_response = User::whereIn('id', explode(',', $value['teacher_id']))->where('role_type', 4)->get()->toarray();
            $parent_response = User::whereIn('id', explode(',', $value['teacher_id']))->where('role_type', 5)->get()->toarray();
            $student_response = User::whereIn('id', explode(',', $value['teacher_id']))->where('role_type', 6)->get()->toarray();
            $response[] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'announcement' => $value['announcement'],
                'institute_show' => $institute_response,
                'teacher_show' => $teacher_response,
                'parent_show' => $parent_response,
                'student_show' => $student_response
            ];
        }
        return view('announcementnew/create', compact('institute_list', 'UserRoleBy', 'response'));
    }
    public function fetchUsers(Request $request)
    {
        $instituteIds = $request->input('institute_ids');
        $userIds = [];
        // Fetch user ids based on institute ids
        $instituteUserIds = Institute_detail::whereIn('id', $instituteIds)->pluck('user_id')->toArray();
        $studentIds = Student_detail::whereIn('institute_id', $instituteIds)->pluck('student_id')->toArray();
        $parentIds = Parents::whereIn('student_id', $studentIds)->pluck('parent_id')->toArray();
        $teacherIds = Teacher_model::whereIn('institute_id', $instituteIds)->pluck('teacher_id')->toArray();
        // Merge all user ids
        $userIds = array_merge($instituteUserIds, $studentIds, $parentIds, $teacherIds);
        // Remove duplicates
        $userIds = array_unique($userIds);
        // Fetch users based on user ids
        $users = User::whereIn('id', $userIds)->get();
        // Prepare data for JSON response
        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'role_type' => $user->role_type
            ];
        }
        return response()->json($userData);
    }
    public function saveAnnouncement(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'institute_id' => 'required',
            'selected_users' => 'required',
            'announcement' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $announcement = Common_announcement::create([
            'institute_id' => implode(",", $request->institute_id),
            'teacher_id' => implode(",", $request->selected_users),
            'title' => (!empty($request->title))? $request->title : '',
            'announcement' => $request->announcement,
        ]);
        // Get device keys of selected users
        $users = User::whereIn('id', $request->selected_users)
            ->where('device_key', '!=', null)
            ->get();
        $serverKey = "AAAAw2iOsiU:APA91bFvtJsovqQESQi_Tzf0rPxBxcCXHTASZBVScK_MZBrg-Tbx5sBFfTVu9ryq-bJx-mr3GdPqFfX-PNJhBuEea5sz9XT7ytSoHFwg45jX_oS60IxFiYxLXJCA38ZL2HSb3WBuBENa";
        $url = "https://fcm.googleapis.com/fcm/send";
        $registrationIds = $users->pluck('device_key')->toArray();
        $notificationTitle = (!empty($request->title))? $request->title : '';
        $notificationBody = $request->announcement;
        $data = [
            'registration_ids' => $registrationIds,
            'notification' => [
                'title' => $notificationTitle,
                'body' => $notificationBody,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // Adjust this if needed
            ],
        ];
        if ($users->isNotEmpty()) {
            $json = json_encode($data);
            $headers = [
                'Content-Type: application/json',
                'Authorization: key=' . $serverKey
            ];
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_HTTPHEADER => $headers,
            ]);
            $result = curl_exec($ch);
            if ($result === FALSE) {
            }
            curl_close($ch);
        }
        return redirect('announcement-create-new')->with('success', 'Announcement created successfully.');
    }
}
