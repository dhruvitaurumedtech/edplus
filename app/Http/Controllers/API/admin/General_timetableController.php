<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Class_room_model;
use App\Models\General_timetable_model;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class General_timetableController extends Controller
{
    use ApiTrait;
    function create_general_timetable(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'class_room_id' => 'required|exists:class_room,id',
            'batch_id' => 'required|exists:batches,id',
            'standard_id' => 'required|exists:standard,id',
            'subject_id' => 'required|exists:subject,id',
            'lecture_type' => 'required|exists:lecture_type,id',
            'day' => 'required|integer',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $existingTimetable = General_timetable_model::where([
                'day' => $request->day,
            ])->first();
            if ($existingTimetable) {
                return $this->response([], 'Timetable entry already exists for this day.', false, 400);
            } else {

                $timetable = General_timetable_model::create([
                    'class_room_id' => $request->class_room_id,
                    'batch_id' => $request->batch_id,
                    'standard_id' => $request->standard_id,
                    'subject_id' => $request->subject_id,
                    'lecture_type' => $request->lecture_type,
                    'day' => $request->day,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ]);
                return $this->response([], 'Created general timetable successfully.');
            }
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    function display_general_timetable()
    {
        $timetables = General_timetable_model::all();
        return $this->response($timetables, 'Display general timetable successfully.');
    }
    function edit_general_timetable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_room_id' => 'required|exists:class_room,id',
            'batch_id' => 'required|exists:batches,id',
            'standard_id' => 'required|exists:standard,id',
            'subject_id' => 'required|exists:subject,id',
            'lecture_type' => 'required|exists:lecture_type,id',
            'day' => 'required|integer',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $existingTimetable = General_timetable_model::where('id', $request->id)->first();
            if ($existingTimetable) {
                $existingTimetable->update([
                    'class_room_id' => $request->class_room_id,
                    'batch_id' => $request->batch_id,
                    'standard_id' => $request->standard_id,
                    'subject_id' => $request->subject_id,
                    'lecture_type' => $request->lecture_type,
                    'day' => $request->day,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,

                ]);
                return $this->response([], 'Updated general timetable successfully.');
            }
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    function delete_general_timetable(Request $request)
    {
        $timetable = General_timetable_model::findOrFail($request->id);
        $timetable->delete();
        return $this->response([], 'Timetable entry soft deleted successfully.');
    }
    function institute_day_filter_general_timetable(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|exists:institute_detail,id',
            'day' => 'required|exists:batches,id',

        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        $institute_id = $request->institute_id;
        $day = $request->day;

        try {
            $class_room_id = Class_room_model::where('institute_id', $institute_id)->pluck('id');

            if ($class_room_id) {
                $General_timetable_model = General_timetable_model::join('class_room', 'class_room.id', '=', 'general_timetable.class_room_id')
                    ->join('batches', 'batches.id', '=', 'general_timetable.batch_id')
                    ->join('standard', 'standard.id', '=', 'general_timetable.standard_id')
                    ->join('subject', 'subject.id', '=', 'general_timetable.subject_id')
                    ->join('lecture_type', 'lecture_type.id', '=', 'general_timetable.lecture_type')
                    ->select(
                        'class_room.name as class_room_name',
                        'batches.batch_name',
                        'standard.name as standard_name',
                        'subject.name as subject_name',
                        'lecture_type.name as lecture_name',
                        'general_timetable.*'
                    )
                    ->where('general_timetable.class_room_id', $class_room_id)
                    ->where('general_timetable.day', $day)
                    ->get()->toarray();
                $data = [];
                foreach ($General_timetable_model as $value) {

                    $dayName = '';

                    switch ($value['day']) {
                        case 1:
                            $dayName = 'Monday';
                            break;
                        case 2:
                            $dayName = 'Tuesday';
                            break;
                        case 3:
                            $dayName = 'Wednesday';
                            break;
                        case 4:
                            $dayName = 'Thursday';
                            break;
                        case 5:
                            $dayName = 'Friday';
                            break;
                        case 6:
                            $dayName = 'Saturday';
                            break;
                        case 7:
                            $dayName = 'Sunday';
                            break;
                        default:
                            $dayName = 'Invalid day number';
                            break;
                    }

                    $data[] = array(
                        'id' => $value['id'],
                        'class_room_name' => $value['class_room_name'],
                        'batch_name' => $value['batch_name'],
                        'standard_name' => $value['standard_name'],
                        'subject_name' => $value['subject_name'],
                        'lecture_name' => $value['lecture_name'],
                        'day' => $dayName,
                        'start_time' => $value['start_time'],
                        'end_time' => $value['end_time'],
                    );
                }


                return $this->response($data, 'Fetch Successfully.');
            } else {
                return $this->response([], 'institute not exist', false, 400);
            }
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    function batch_standard_filter_general_timetable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'standard_id' => 'required|exists:standard,id',
            'batch_id' => 'required|exists:batches,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {


            $General_timetable_model = General_timetable_model::join('class_room', 'class_room.id', '=', 'general_timetable.class_room_id')
                ->join('batches', 'batches.id', '=', 'general_timetable.batch_id')
                ->join('standard', 'standard.id', '=', 'general_timetable.standard_id')
                ->join('subject', 'subject.id', '=', 'general_timetable.subject_id')
                ->join('lecture_type', 'lecture_type.id', '=', 'general_timetable.lecture_type')
                ->select(
                    'class_room.name as class_room_name',
                    'batches.batch_name',
                    'standard.name as standard_name',
                    'subject.name as subject_name',
                    'lecture_type.name as lecture_name',
                    'general_timetable.*'
                )
                ->where('general_timetable.standard_id', $request->standard_id)
                ->where('general_timetable.batch_id', $request->batch_id)
                ->get()->toarray();
            foreach ($General_timetable_model as $value) {

                $dayName = '';

                switch ($value['day']) {
                    case 1:
                        $dayName = 'Monday';
                        break;
                    case 2:
                        $dayName = 'Tuesday';
                        break;
                    case 3:
                        $dayName = 'Wednesday';
                        break;
                    case 4:
                        $dayName = 'Thursday';
                        break;
                    case 5:
                        $dayName = 'Friday';
                        break;
                    case 6:
                        $dayName = 'Saturday';
                        break;
                    case 7:
                        $dayName = 'Sunday';
                        break;
                    default:
                        $dayName = 'Invalid day number';
                        break;
                }

                $data[] = array(
                    'id' => $value['id'],
                    'class_room_name' => $value['class_room_name'],
                    'batch_name' => $value['batch_name'],
                    'standard_name' => $value['standard_name'],
                    'subject_name' => $value['subject_name'],
                    'lecture_name' => $value['lecture_name'],
                    'day' => $dayName,
                    'start_time' => $value['start_time'],
                    'end_time' => $value['end_time'],
                );
            }


            return $this->response($data, 'Fetch Successfully.');
        } catch (\Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
}
