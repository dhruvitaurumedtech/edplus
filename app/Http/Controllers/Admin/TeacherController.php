<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{

    public function homescreen_teacher(Request $request)
    {
        $token = $request->header('Authorization');


        if (strpos($token, 'Bearer') === 0) {
            $token = substr($token, 7);
        }

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'per_page' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'data' => array('errors' => $errorMessages),
            ], 400);
        }

        try {

            $user_id = $request->user_id;
            $search_keyword = $request->search;
            $perPage = $request->input('per_page', 10);
            $existingUser = User::where('token', $token)->where('id', $user_id)->first();
            if ($existingUser) {

                //banner

                $banners = Banner_model::where('status', 'active')
                    ->whereIn('user_id', explode(',', '1'))
                    ->paginate($perPage);
                $banners_data = [];

                foreach ($banners as $value) {
                    $imgpath = asset($value->banner_image);
                    $banners_data[] = array(
                        'id' => $value->id,
                        'banner_image' => $imgpath,
                    );
                }
                $perPage = 10;
                //student searched response 
                $allinstitute = Institute_detail::where('status', 'active')
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('unique_id', 'like', '%' . $search_keyword . '%')
                            ->orWhere('institute_name', 'like', '%' . $search_keyword . '%');
                    })->paginate($perPage);

                $search_list = [];
                foreach ($allinstitute as $value) {
                    $search_list[] = array(
                        'id' => $value->id,
                        'institute_name' => $value->institute_name,
                        'address' => $value->address,
                        'logo' => asset($value->logo),
                    );
                }

                //student search history
                $searchhistory = Search_history::where('user_id', $user_id)->paginate($perPage);
                $searchhistory_list = [];
                foreach ($searchhistory as $value) {
                    // Check if the title already exists in the $searchhistory_list array
                    $existingTitles = array_column($searchhistory_list, 'title');
                    if (!in_array($value->title, $existingTitles)) {

                        $searchhistory_list[] = [
                            'id' => $value->id,
                            'institute_id' => $value->institute_id,
                            'user_id' => $value->user_id,
                            'title' => $value->title,
                        ];
                    }
                }

                //requested institute
                $requestnstitute = Student_detail::join('institute_detail', 'institute_detail.id', '=', 'students_details.institute_id')->where('students_details.status', '!=', '1')
                    ->where('students_details.student_id', $user_id)
                    ->select('institute_detail.*', 'students_details.status as sstatus', 'students_details.student_id')->paginate($perPage);

                $requested_institute = [];
                foreach ($requestnstitute as $value) {
                    $requested_institute[] = array(
                        'id' => $value->id,
                        'institute_name' => $value->institute_name,
                        'address' => $value->address,
                        'logo' => asset($value->logo),
                        'status' => $value->sstatus,
                    );
                }

                //join with

                $joininstitute = Institute_detail::where('status', 'active')
                    ->whereIn('id', function ($query) use ($user_id) {
                        $query->select('institute_id')
                            ->where('student_id', $user_id)
                            ->where('status', '=', '1')
                            ->from('students_details')
                            ->whereNull('deleted_at');
                    })
                    ->where('end_academic_year', '>=', now())
                    ->paginate($perPage); // ->where('end_academic_year', '>=', now())
                $join_with = [];
                foreach ($joininstitute as $value) {
                    $join_with[] = array(
                        'id' => $value->id,
                        'institute_name' => $value->institute_name . '(' . $value->unique_id . ')',
                        'address' => $value->address,
                        'logo' => asset($value->logo),
                    );
                }

                $parentsdt = Parents::where('student_id', $user_id)->get();

                $veryfy = [];
                foreach ($parentsdt as $checkvery) {
                    $veryfy[] = array('relation' => $checkvery->relation, 'verify' => $checkvery->verify);
                }
                if ($parentsdt->isEmpty()) {

                    $studentparents = '0';
                } else {
                    $studentparents = '1';
                }
                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully fetch data.',
                    'data' => array(
                        'banner' => $banners_data,
                        'search_list' => $search_list,
                        'searchhistory_list' => $searchhistory_list,
                        'requested_institute' => $requested_institute,
                        'join_with' => $join_with,
                        'parents_detail' => $studentparents,
                        'parents_verification' => $veryfy
                    ),
                ], 200, [], JSON_NUMERIC_CHECK);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token.',
                    'data' => []
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'data' => array('error' => $e->getMessage()),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function teaher_add_institute_request(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'teacher_id' => 'required|integer',
            'institute_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errorMessages = array_values($validator->errors()->all());
            return response()->json([
                'success' => 400,
                'message' => 'Validation error',
                'data' => array('errors' => $errorMessages),
            ], 400);
        }

        try {
            $token = $request->header('Authorization');

            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $user_id = $request->input('user_id');
            $existingUser = User::where('token', $token)->where('id', $user_id)->first();
            if ($existingUser) {
                $instituteid = $request->institute_id;
                $getsid = Student_detail::where('student_id', $request->user_id)
                    ->where('institute_id', $instituteid)->first();
                if ($getsid) {
                } else {
                    $getuid = Institute_detail::where('id', $instituteid)->select('user_id')->first();

                    $search_add = Student_detail::create([
                        'user_id' => $getuid->user_id,
                        'institute_id' => $request->input('institute_id'),
                        'student_id' => $request->input('user_id'),
                        'status' => '0',
                    ]);
                }

                return response()->json([
                    'success' => 200,
                    'message' => 'Request added successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => 500,
                'message' => 'Something went wrong',
                'data' => array('error' => $e->getMessage()),
            ], 500);
        }
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
