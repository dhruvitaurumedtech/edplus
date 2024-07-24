<?php

namespace App\Http\Controllers\API\admin;

use App\Http\Controllers\Controller;
use App\Models\Discount_model;
use App\Models\Fees_colletion_model;
use App\Models\Fees_model;
use App\Models\Institute_detail;
use App\Models\Payment_type_model;
use App\Models\Student_detail;
use App\Models\Student_fees_model;
use App\Models\Subject_sub;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;




class FeesController extends Controller
{

    use ApiTrait;
    public function add_fees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
            'board_id' => 'required|exists:board,id',
            'medium_id' => 'required|exists:medium,id',
            'standard_id' => 'required|exists:standard,id',
            'subject_id' => 'required|exists:subject,id',
            'amount' => 'required',
            'stream_id' => 'nullable|integer|exists:stream,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $existingFee = Fees_model::where('user_id', Auth::user()->id)
                ->where('institute_id', $request->institute_id)
                ->where('board_id', $request->board_id)
                ->where('medium_id', $request->medium_id)
                ->where('standard_id', $request->standard_id)
                ->where('stream_id', $request->stream_id)
                ->where('subject_id', $request->subject_id)
                ->first();

            if ($existingFee) {
                return $this->response([], "Fees for the given criteria already exist.", false, 400);
            }

            $fee = new Fees_model;
            $fee->user_id = Auth::user()->id;
            $fee->institute_id = $request->institute_id;
            $fee->board_id = $request->board_id;
            $fee->medium_id = $request->medium_id;
            $fee->standard_id = $request->standard_id;
            $fee->stream_id = $request->stream_id;
            $fee->subject_id = $request->subject_id;
            $fee->amount = $request->amount;
            $fee->save();
            return $this->response([], "Fees inserted successfully.");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function view_fees_detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $fees = Fees_model::join('board', 'board.id', '=', 'fees.board_id')
                ->leftjoin('medium', 'medium.id', '=', 'fees.medium_id')
                ->leftjoin('standard', 'standard.id', '=', 'fees.standard_id')
                ->leftjoin('stream', 'stream.id', '=', 'fees.stream_id')
                ->leftjoin('subject', 'subject.id', '=', 'fees.subject_id')
                ->select(
                    'fees.amount',
                    'board.name as board_name',
                    'medium.name as medium_name',
                    'standard.name as standard_name',
                    'stream.name as stream_name',
                    'subject.name as subject_name'
                )
                ->where('fees.institute_id', $request->institute_id)
                ->get()->toarray();
            $data = [];
            if (!empty($fees)) {

                foreach ($fees as $value) {
                    $data[] = [
                        'board_name' => $value['board_name'],
                        'medium_name' => $value['medium_name'],
                        'standard_name' => $value['standard_name'],
                        'stream_name' => $value['stream_name'],
                        'subject_name' => $value['subject_name'],
                        'amount' => $value['amount'],
                    ];
                }
            } else {
                return $this->response([], "No record found", false, 400);
            }
            return $this->response($data, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function paid_fees_student(Request $request)
    {
        // echo"hi";exit;
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'batch_id' => 'required|exists:batches,id',
            'board_id' => 'required|exists:board,id',
            'medium_id' => 'required|exists:medium,id',
            'standard_id' => 'required|exists:standard,id',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $subjectIds = [];
            if (!empty($request->subject_id)) {
                $subjectIds = explode(',', $request->subject_id);
            }
            $query = Student_detail::join('board', 'board.id', '=', 'students_details.board_id')
                ->leftJoin('medium', 'medium.id', '=', 'students_details.medium_id')
                ->leftJoin('standard', 'standard.id', '=', 'students_details.standard_id')
                ->leftJoin('stream', 'stream.id', '=', 'students_details.stream_id')
                ->leftJoin('users', 'users.id', '=', 'students_details.student_id')
                // ->leftJoin('fees_colletion', 'fees_colletion.student_id', '=', 'students_details.student_id')
                ->select(
                    'users.id',
                    'users.firstname',
                    'users.lastname',
                    'users.image',
                    'students_details.student_id',
                    // DB::raw('SUM(fees_colletion.payment_amount) as total_payment_amount')
                )
                ->where('students_details.institute_id', $request->institute_id)
                ->where('students_details.board_id', $request->board_id)
                ->where('students_details.medium_id', $request->medium_id)
                ->where('students_details.standard_id', $request->standard_id)
                ->where('students_details.status', '1')
                ->groupBy(
                    'users.id',
                    'users.firstname',
                    'users.lastname',
                    'users.image',
                    'students_details.student_id'
                );

            if (!empty($request->batch_id)) {
                $query->where('students_details.batch_id', $request->batch_id);
            }

            $query->where(function ($query) use ($subjectIds) {
                foreach ($subjectIds as $subjectId) {
                    $query->orWhereRaw("FIND_IN_SET(?, students_details.subject_id)", [$subjectId]);
                }
            });

            $student_response = $query->get()->toArray();
            // print_r($student_response);exit;

            $students = [];

            foreach ($student_response as $value) {

                $fees_detail = Fees_colletion_model::where('student_id', $value['student_id'])
                        ->where('institute_id', $request->institute_id)
                        ->select(DB::raw('SUM(payment_amount) as total_payment_amount'))
                        ->first();

                    // Access the total payment amount
                     $total_payment_amount = $fees_detail->total_payment_amount;
                $student_fees = Student_fees_model::where('institute_id', $request->institute_id)
                    ->where('student_id', $value['student_id'])
                    ->first();


                $discount = Discount_model::where('institute_id', $request->institute_id)
                    ->where('student_id', $value['student_id'])
                    ->first();
                
                $total = null;
                if (!empty($student_fees) || !empty($discount)) {
                    $total = (!empty($student_fees->total_fees))?$student_fees->total_fees:0;
                    if ($discount) {
                        if ($discount->discount_by == 'Rupee') {
                            $total = $student_fees->total_fees - $discount->discount_amount;
                        } elseif ($discount->discount_by == 'Percentage') {
                            $total = $student_fees->total_fees - ($student_fees->total_fees * ($discount->discount_amount / 100));
                        }
                    }
                    
                    if (!empty($total) && !empty($total_payment_amount)) {
                        if ($total == $total_payment_amount) {
                            $students[] = [
                                'student_id' => $value['student_id'],
                                'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                                'profile' => !empty($value['image']) ? asset($value['image']) : asset('profile/no-image.png'),
                                'status' => 'paid'
                            ];
                        }
                    }
                    
                }
            }
            
            return $this->response($students, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function pending_fees_student(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'batch_id' => 'required|exists:batches,id',
            'board_id' => 'required|exists:board,id',
            'medium_id' => 'required|exists:medium,id',
            'standard_id' => 'required|exists:standard,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $subjectIds = [];
            if (!empty($request->subject_id)) {
                $subjectIds = explode(',', $request->subject_id);
            }
            $query = Student_detail::join('board', 'board.id', '=', 'students_details.board_id')
            ->leftJoin('medium', 'medium.id', '=', 'students_details.medium_id')
            ->leftJoin('standard', 'standard.id', '=', 'students_details.standard_id')
            ->leftJoin('users', 'users.id', '=', 'students_details.student_id')
            // ->leftJoin('fees_colletion', function($join) {
            //     $join->on('students_details.student_id', '=', 'fees_colletion.student_id')
            //          ->orOn('students_details.institute_id', '=', 'fees_colletion.institute_id');
            // })
            ->select(
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.image',
                'students_details.student_id'
                // DB::raw('SUM(fees_colletion.payment_amount) as total_payment_amount')
            )
            ->where('students_details.institute_id', $request->institute_id)
            // ->where('fees_colletion.institute_id', $request->institute_id)
            ->where('students_details.board_id', $request->board_id)
            ->where('students_details.medium_id', $request->medium_id)
            ->where('students_details.standard_id', $request->standard_id)
            ->where('students_details.status', '1')
            ->whereNull('users.deleted_at')
            ->whereNull('students_details.deleted_at')
            ->groupBy(
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.image',
                'students_details.student_id'
            );

            if (!empty($request->batch_id)) {
                $query->where('students_details.batch_id', $request->batch_id);
            }

            if (!empty($subjectIds)) {
                $query->where(function ($query) use ($subjectIds) {
                    foreach ($subjectIds as $subjectId) {
                        $query->orWhereRaw("FIND_IN_SET(?, students_details.subject_id)", [$subjectId]);
                    }
                });
            }

            $student_response = $query->get()->toArray();
            $students = [];

            foreach ($student_response as $value) {

                $fees_detail = Fees_colletion_model::where('student_id', $value['student_id'])
                        ->where('institute_id', $request->institute_id)
                        ->select(DB::raw('SUM(payment_amount) as total_payment_amount'))
                        ->first();

                    // Access the total payment amount
                     $total_payment_amount = $fees_detail->total_payment_amount;
                // $student_id = $value['student_id'];
                // echo $request->institute_id;
                $student_fees = Student_fees_model::where('student_id',  $value['id'])
                    ->where('institute_id', $request->institute_id)
                    ->first();

                // if (empty($student_fees)) {
                //     return $this->response([], "Fees are not included for the subject", false, 400);
                // }

                $discount = Discount_model::where('institute_id', $request->institute_id)
                    ->where('student_id', $value['id'])
                    ->first();

                $dis = !empty($discount) ? $discount->discount_amount : 0;
                $due_amount = 0;
                
                // echo $value['total_payment_amount'];exit;
                if (!empty($total_payment_amount) || !empty($student_fees->total_fees)) {
                    if ($student_fees->total_fees != $total_payment_amount) {
                        // echo $total_payment_amount;exit;
                        if (!empty($total_payment_amount)) {
                            
                            if ($discount && $discount->discount_by == 'Rupee') {
                                $due_amount = $student_fees->total_fees - $total_payment_amount - $dis;
                            } elseif ($discount && $discount->discount_by == 'Percentage') {
                                $revise_fees = $student_fees->total_fees * ($discount->discount_amount / 100);
                            //   echo $total_payment_amount;exit;
                                $due_amount = $student_fees->total_fees - $total_payment_amount - $revise_fees;
                                
                            } else {
                                $due_amount = $student_fees->total_fees - $total_payment_amount;
                            }
                        } else {
                            // $due_amount = $student_fees->total_fees - $dis;
                            if ($discount && $discount->discount_by == 'Rupee') {
                                $due_amount = $student_fees->total_fees - $total_payment_amount - $dis;
                            } elseif ($discount && $discount->discount_by == 'Percentage') {
                                $revise_fees = $student_fees->total_fees * ($discount->discount_amount / 100);
                                $due_amount = $student_fees->total_fees - $total_payment_amount - $revise_fees;
                               
                            } else {
                                $due_amount = $student_fees->total_fees - $total_payment_amount;
                            }
                        }
                    }
                }

                if ($due_amount != 0) {
                    $students[] = [
                        'student_id' => $value['id'],
                        'student_name' => $value['firstname'] . ' ' . $value['lastname'],
                        'profile' => !empty($value['image']) ? asset($value['image']) : asset('profile/no-image.png'),
                        'status' => 'pending',
                        'due_amount' => $due_amount
                    ];
                }
            }

            return $this->response($students, "Data fetched successfully", true, 200);
        } catch (\Exception $e) {
            return $this->response([], "Error: " . $e->getMessage(), false, 400);
        }
    }

    //invoice number mate student_id pass karvu padse
    public function payment_type(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'board_id' => 'required|integer|exists:board,id',
            'medium_id' => 'required|integer|exists:medium,id',
            'standard_id' => 'required|integer|exists:standard,id',
            'subject_id' => 'required|exists:subject,id',
            'stream_id' => 'nullable',
            'student_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $payment_mode = Payment_type_model::whereNull('deleted_at')->get();
            $data = [];
            foreach ($payment_mode as $value) {
                $data[] = ['id' => $value->id, 'name' => $value->name];
            }
            $fees_colletion = Fees_colletion_model::where('student_id', $request->student_id)->latest()->first();

            if (!empty($fees_colletion)) {
                $amount = $fees_colletion->total_amount - $fees_colletion->paid_amount;
                $parts = explode('-', $fees_colletion->invoice_no);

                if (count($parts) === 2) {
                    $number = $parts[1];
                }
                $invoice = $number + 1;
            } else {
                $invoice = 1;
            }

            $student = User::where('id', $request->student_id)->first();
            $student_name = $student->firstname . ' ' . $student->lastname;
            $invoiceNumber = 'INV' . $request->student_id . '-' . str_pad($invoice, 6, '0', STR_PAD_LEFT);
            $userId = Auth::user()->id;

            $Fee_amount = Fees_model::where('user_id', $userId)
                ->where('institute_id', $request->institute_id)
                ->where('board_id', $request->board_id)
                ->where('medium_id', $request->medium_id)
                ->where('standard_id', $request->standard_id)
                ->where('stream_id', $request->stream_id)
                ->where('subject_id', $request->subject_id)
                ->select('amount')
                ->first();
            //  echo "<pre>";print_r($Fee_amount);exit;

            $fees_colletion = Fees_colletion_model::where('student_id', $request->student_id)->get();
            $paid_amount = 0;
            foreach ($fees_colletion as $value) {
                $paid_amount += $value->paid_amount;
                $amount = $value->total_amount - $paid_amount;
                if ($amount == 0 || !empty($amount)) {
                    $total_amount = $amount;
                } else {
                    $total_amount = $Fee_amount->amount;
                }
            }
            $fees_colletion = Fees_colletion_model::where('student_id', $request->student_id)->count();
            if ($fees_colletion < 0) {
                $total_amount = $Fee_amount->amount;
            }
            $student_histroy = Fees_colletion_model::where('student_id', $request->student_id)->get();

            $histroy = [];
            foreach ($student_histroy as $value) {
                $histroy[] = [
                    'paid_amount' => $value->paid_amount,
                    'date' => $value->created_at,
                    'payment_mode' => $value->payment_type,
                    'invoice_no' => $value->invoice_no,
                    'transaction_id' => $value->transaction_id,

                ];
            }
            $data_final = [
                'payment_type' => $data,
                'invoice_number' => $invoiceNumber,
                'date' => date('Y-m-d'),
                'student_id' => $request->student_id,
                'student_name' => $student_name,
                'total_amount' => $total_amount,
                //    'paid_amount'=>(!empty($fees_colletion->paid_amount))?$fees_colletion->paid_amount:'',
                'histroy' => $histroy
            ];
            return $this->response($data_final, "Successfully Display PaymentType.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
    public function fees_collection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'student_id' => 'required|integer',
            'invoice_no' => 'required|string',
            'date' => 'required|date',
            'student_name' => 'required|string',
            'payment_amount' => 'required|integer',
            'payment_type' => 'required|string',
            'transaction_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            // Fetch student fees and discount details
            $student_fees = Student_fees_model::where('student_id', $request->student_id)
                ->where('institute_id', $request->institute_id)
                ->first();
                // print_r($student_fees);exit;

            $discount = Discount_model::where('institute_id', $request->institute_id)
                ->where('student_id', $request->student_id)
                ->first();

            
            $fees = null;

            if ($student_fees) {
                $discount_amount = 0;
                // if (!empty($discount->discount_amount)) {
                //     $discount_amount = $discount->discount_amount;
                // }
                if ($discount) {
                    if ($discount->discount_by == 'Rupee') {
                        $fees = $student_fees->total_fees - $discount->discount_amount;
                        $discount_amount = $discount->discount_amount;
                    } elseif ($discount->discount_by == 'Percentage') {
                       $fees = $student_fees->total_fees - ($student_fees->total_fees * ($discount->discount_amount / 100));
                       $discount_amount=$student_fees->total_fees * ($discount->discount_amount / 100);
                    }
                }
                // If no discount, fees remain the total fees
                $fees_data = Fees_colletion_model::where('student_id', $request->student_id)->where('institute_id', $request->institute_id)->get();
                $paid_amount = 0;
                if (!empty($fees_data)) {
                    foreach ($fees_data as $value) {
                        $paid_amount += $value->payment_amount;
                    }
                    // echo $paid_amount;exit;
                }
                 $fees = $student_fees->total_fees - $paid_amount - $discount_amount;
                if ($fees < $request->payment_amount) {
                    return $this->response([], "Amount is not matched", false, 400);
                }
                if($request->payment_amount < 0){
                    return $this->response([], "Invalid Amount!", false, 404);    
                }
            } else {
                return $this->response([], "Student fees record not found!", false, 400);
            }

            // Save the fee collection record
            $fee = new Fees_colletion_model;
            $fee->user_id = Auth::user()->id;
            $fee->institute_id = $request->institute_id;
            $fee->student_id = $request->student_id;
            $fee->student_name = $request->student_name;
            $fee->invoice_no = $request->invoice_no;
            $fee->date = $request->date;
            $fee->bank_name = $request->bank_name;
            $fee->payment_amount = $request->payment_amount;
            $fee->payment_type = $request->payment_type;
            $fee->transaction_id = $request->transaction_id ?? '';
            $fee->save();

            // Calculate the total paid amount
            $total_paid = Fees_colletion_model::where('student_id', $request->student_id)
                ->where('institute_id', $request->institute_id)
                ->sum('payment_amount');

            // Update the status of the latest fee collection record
            // Fees_colletion_model::where('id', $fee->id)->update([
            //     'status' => ($total_paid >= $student_fees->total_fees) ? 'paid' : 'pending'
            // ]);

            return $this->response([], "Fees paid successfully.");
        } catch (\Exception $e) {
            return $this->response([], "Error: " . $e->getMessage(), false, 400);
        }
    }
    public function display_subject_fees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',

        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $query = Subject_sub::leftjoin('subject', 'subject.id', '=', 'subject_sub.subject_id')
                ->leftjoin('base_table', 'base_table.id', '=', 'subject.base_table_id')
                ->leftjoin('board', 'board.id', '=', 'base_table.board')
                ->leftjoin('medium', 'medium.id', '=', 'base_table.medium')
                ->leftjoin('standard', 'standard.id', '=', 'base_table.standard')
                ->leftjoin('stream', 'stream.id', '=', 'base_table.stream')
                ->where('subject_sub.institute_id', $request->institute_id)
                ->select(
                    'subject.id',
                    'subject_sub.amount',
                    'subject.name as subject_name',
                    'board.id as board_id',
                    'board.name as board_name',
                    'medium.id as medium_id',
                    'medium.name as medium_name',
                    'standard.id as standard_id',
                    'standard.name as standard_name',
                    'stream.id as stream_id',
                    'stream.name as stream_name'
                );
            if (!empty($request->board_id)) {
                $query->whereIn('base_table.board', explode(',', $request->board_id));
            }
            if (!empty($request->standard_id)) {
                $query->whereIn('base_table.standard', explode(',', $request->standard_id));
            }
            if (!empty($request->medium_id)) {
                $query->whereIn('base_table.medium', explode(',', $request->medium_id));
            }
            if (!empty($request->stream_id)) {
                $query->whereIn('base_table.stream', explode(',', $request->stream_id));
            }
            if (!empty($request->subject_id)) {
                $query->whereIn('subject_sub.subject_id', explode(',', $request->subject_id));
            }
            if (!empty($request->sort_by)) {
                if ($request->sort_by == 'added') {
                    $query->whereNotNull('subject_sub.amount')->orderBy('subject_sub.amount', 'asc')->get()->toArray();
                } elseif ($request->sort_by == 'none') {
                    $query->whereNull('subject_sub.amount')->get()->toArray();
                }
            }

            $subject = $query->get()->toarray();

            $student = [];
            foreach ($subject as $value) {
                $student[] =  [
                    'subject_id' => $value['id'],
                    'subject_name' => $value['subject_name'],
                    'board_id' => $value['board_id'],
                    'board_name' => $value['board_name'],
                    'standard_id' => $value['standard_id'],
                    'standard_name' => $value['standard_name'],
                    'medium_id' => $value['medium_id'],
                    'medium_name' => $value['medium_name'],
                    'stream_id' => $value['stream_id'],
                    'stream_name' => $value['stream_name'],
                    'amount' => (!empty($value['amount'])) ? $value['amount'] . '.00' : '00.00',
                ];
            }
            return $this->response($student, "Data Fetch Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    function subject_amount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'amount' => 'required|integer',


        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $subject_sub = Subject_sub::where('institute_id', $request->institute_id)->where('subject_id', $request->subject_id);
            $subject_sub->update([
                'amount' => $request->amount
            ]);
            return $this->response([], "Fees Update Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function student_list_for_discount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $query = Student_detail::join('users', 'users.id', '=', 'students_details.student_id')
                ->join('standard', 'standard.id', '=', 'students_details.standard_id')
                ->leftjoin('stream', 'stream.id', '=', 'students_details.stream_id')
                ->where('students_details.institute_id', $request->institute_id)
                ->where('students_details.status', '1')
                ->whereNull('students_details.deleted_at')
                ->whereNull('users.deleted_at')
                    
                ->select('users.*', 'standard.name as standard_name', 'students_details.standard_id', 'students_details.stream_id', 'stream.name as streamname', 'students_details.subject_id');
            if (!empty($request->board_id)) {
                $query->whereIn('students_details.board_id', explode(',', $request->board_id));
            }
            if (!empty($request->standard_id)) {
                $query->whereIn('students_details.standard_id', explode(',', $request->standard_id));
            }
            if (!empty($request->medium_id)) {
                $query->whereIn('students_details.medium_id', explode(',', $request->medium_id));
            }
            if (!empty($request->stream_id)) {
                $query->whereIn('students_details.stream_id', explode(',', $request->stream_id));
            }
            if (!empty($request->subject_id)) {
                $query->whereIn('students_details.subject_id', explode(',', $request->subject_id));
            }
            if (!empty($request->search)) {
                $searchTerm = '%' . $request->search . '%';
                $query->where(function ($query) use ($searchTerm) {
                    $query->where(DB::raw("CONCAT(users.firstname, ' ', users.lastname)"), 'like', $searchTerm);
                });
            }
            $student_list = $query->get();
            // echo "<pre>";print_r($student_list);exit;


            $data = [];
            $revise_fee = '';
            $revise_fee = '';
            $discount_data = '';
            foreach ($student_list as $value) {

                $query = Student_detail::leftjoin('users', 'users.id', '=', 'students_details.student_id')
                    ->leftJoin('discount', function ($join) {
                        $join->on('discount.student_id', '=', 'students_details.student_id')
                            ->on('discount.institute_id', '=', 'students_details.institute_id');
                    })
                    ->leftjoin('standard', 'standard.id', '=', 'students_details.standard_id')
                    ->where('students_details.student_id', $value->id)
                    ->where('students_details.institute_id', $request->institute_id)
                    ->whereNull('students_details.deleted_at')
                    ->whereNull('users.deleted_at')
                    ->select('users.*', 'standard.name as standard_name', 'discount.discount_amount', 'discount.discount_by')

                    ->first();
                $amounts = 0;
                // print_r($student_list);exit;
                // foreach(explode(',',$value->subject_id) as $subject_id){whereIn('subject_id',explode(',', $value->subject_id))->
                // print_r(explode(',', $request->institute_id));exit;
                //    $subject_ids = $value->subject_id;
                $subject_sub = Student_fees_model::where('institute_id', $request->institute_id)
                    ->where('student_id', $value->id)
                    //    ->whereRaw("FIND_IN_SET(subject_id, '$subject_ids')")
                    ->sum('total_fees');

                // foreach($subject_sub as $values){
                //    $amounts +=$values->total_fees;
                // }
                $amounts = $subject_sub;

                // }

                if (empty($query->discount_by)) {
                    $revise_fee = 0;
                    $discount_data = '00.00';
                }
                if ($query->discount_by == 'Rupee') {
                    $revise_fee = $amounts - $query->discount_amount;
                    $discount_data = (!empty($query->discount_amount)) ? $query->discount_amount . '.00' : '00.00';
                }
                if ($query->discount_by == 'Percentage') {
                    $discountAmount =  $amounts * ($query->discount_amount / 100);
                    $revise_fee = $amounts - $discountAmount;
                    $discount_data = (!empty($query->discount_amount)) ? $query->discount_amount . '%' : '0%';
                }
                $paid_amount = Fees_colletion_model::where('institute_id', $request->institute_id)
                    ->where('student_id', $value->id)
                    ->sum('payment_amount');
                if (!empty($paid_amount)) {

                    $revise_fee = $revise_fee - $paid_amount;
                } else {
                    $revise_fee = $revise_fee;
                }
                $data[] = [
                    'student_id' => $value->id,
                    'student_name' => $value->firstname . ' ' . $value->lastname,
                    'profile' => (!empty($value->image)) ? asset($value->image) : asset('profile/no-image.png'),
                    'standard_id' => $value->standard_id,
                    'standard_name' => $value->standard_name,
                    'stream_id' => $value->stream_id,
                    'streamname' => $value->streamname,
                    'total_fees_amount' => !empty($amounts) ? $amounts . '.00' : '00.00',
                    'discount' => $discount_data,
                    'paid_amount' => !empty($paid_amount) ? $paid_amount . '.00' : '00.00',
                    'revise_fee' => !empty($revise_fee) ? $revise_fee . '.00' : '00.00',
                    'discount_by' => $query->discount_by
                ];
            }
            return $this->response($data, "fetch Student list Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function fetch_discount_for_student(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'institute_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
             $student_detail=  Student_detail::leftjoin('users', 'users.id', '=', 'students_details.student_id')
                              ->leftjoin('standard', 'standard.id', '=', 'students_details.standard_id')
                              ->select('users.*', 'standard.name as standard_name','students_details.subject_id')
                              ->where('students_details.student_id', $request->student_id)
                              ->where('students_details.institute_id', $request->institute_id)
                              ->whereNull('students_details.deleted_at')
                              ->first();
            // print_r($student_detail);exit;
            $subject_sum =   Subject_sub::where('institute_id',$request->institute_id)->whereIn('subject_id',explode(",",$student_detail->subject_id))->get();
             $total = 0;
            foreach($subject_sum as $value){
                  $total +=$value->amount;
            }
            // $query = Student_detail::leftjoin('users', 'users.id', '=', 'students_details.student_id')
            //                         ->leftjoin('standard', 'standard.id', '=', 'students_details.standard_id')
            //                         ->leftJoin('subject_sub', function ($join) {
            //                             $join->on('subject_sub.subject_id', '=', 'students_details.subject_id')
            //                                 ->on('subject_sub.institute_id', '=', 'students_details.institute_id');
            //                         })
            //                         ->where('students_details.student_id', $request->student_id)
            //                         ->where('students_details.institute_id', $request->institute_id)
            //                         ->select('users.*', 'standard.name as standard_name', 'subject_sub.amount')
            //                         ->get()
            //                         ->toarray();
                // print_r($query);exit;
                //  $total =0;
                // foreach($query as $value){
                //     $total +=$value->amount;
                // }
            //     print_r($total);exit;
            $data = [
                'student_id' => $student_detail->id,
                'student_name' => $student_detail->firstname . ' ' . $student_detail->lastname,
                'profile' => (!empty($student_detail->image)) ? asset($student_detail->image) : asset('profile/no-image.png'),
                'standard_name' => $student_detail->standard_name,
                'total_fees' => $total. '.00'
            ];
            return $this->response($data, "Fetch Student list Successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    function add_discount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'institute_id' => 'required|integer',
            'discount_amount' => 'required',
            'discount_by' => [
                'required',
                Rule::in(['Rupee', 'Percentage']),
            ],
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            $fees = Student_fees_model::where('institute_id', $request->institute_id)->where('student_id', $request->student_id)->select('total_fees')->first();
            // print_r($fees->total_fees);exit;
            
            if($fees->total_fees == 0) {
                return $this->response([], "Fees is zero; cannot apply discount!", false, 404);
            }
            if($fees->total_fees <= $request->discount_amount) {
                return $this->response([], "Discount amount is too large!", false, 404);
            }
            if($request->discount_by == 'Percentage') {
                if ($request->discount_amount <= 100) {
 
                    $paid_amount = Fees_colletion_model::where('institute_id', $request->institute_id)
                    ->where('student_id', $request->student_id)
                    ->sum('payment_amount');
                    if (!empty($paid_amount)) {
                        $remaing_fees = $fees->total_fees - $paid_amount;
                        if($fees->total_fees == $paid_amount){
                            return $this->response([], "Student Fees Already paid!", false, 404);    
                        }
                        else if($remaing_fees<=$request->discount_amount){
                            return $this->response([], "Discount amount is too large!", false, 404);
                        }else if($request->discount_amount < 0){
                            return $this->response([], "Invalid Amount!", false, 404);    
                        }if($fees->total_fees == $paid_amount){
                            return $this->response([], "Student Fees Already paid!", false, 404);    

                        }else
                        {

                            $discount_amount = $request->discount_amount;
                        } 
                    } else{
                        if($request->discount_amount < 0){
                            return $this->response([], "Invalid Amount!", false, 404);    
                        }
                        $discount_amount = $request->discount_amount;
                    }


                    //  $discount_amount = $fees->total_fees - $discountAmount;
                    // exit; 
                } else {
                    return $this->response([], "Enter Discount Amount less than 100.", false, 400);
                }
            }
            if($request->discount_by == 'Rupee') {

                $paid_amount = Fees_colletion_model::where('institute_id', $request->institute_id)
                    ->where('student_id', $request->student_id)
                    ->sum('payment_amount');
                        if (!empty($paid_amount)) {
                            $remaing_fees = $fees->total_fees - $paid_amount;
                            if($fees->total_fees == $paid_amount){
                                return $this->response([], "Student Fees Already paid!", false, 404);    
                            }
                            else if($remaing_fees<=$request->discount_amount){
                                return $this->response([], "Discount amount is too large!", false, 404);
                            }else if($request->discount_amount < 0){
                                return $this->response([], "Invalid Amount!", false, 404);    
                            }if($fees->total_fees == $paid_amount){
                                return $this->response([], "Student Fees Already paid!", false, 404);    

                            }else
                            {
                               
                                $discount_amount = $request->discount_amount;
                            } 
                        } else{
                            if($request->discount_amount < 0){
                                return $this->response([], "Invalid Amount!", false, 404);    
                            }
                            $discount_amount = $request->discount_amount;
                        }

            }

            $discount = Discount_model::where('institute_id', $request->institute_id)->where('student_id', $request->student_id)->count();

            if ($discount == 1) {
                // echo $fees;exit;

                Discount_model::where('institute_id', $request->institute_id)->where('student_id', $request->student_id)
                    ->update([
                        'financial_year' => date('Y'),
                        'discount_amount' => $discount_amount,
                        'discount_by' => $request->discount_by
                    ]);
                return $this->response([], "Discount updated successfully");
            } else {
                Discount_model::Create(
                    [
                        'institute_id' => $request->institute_id,
                        'student_id' => $request->student_id,
                        'financial_year' => date('Y'),
                        'discount_amount' => $discount_amount,
                        'discount_by' => $request->discount_by,
                    ]
                );
                return $this->response([], "Discount added  successfully");
            }
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
    public function payment_type_new(Request $request)
    {
        try {
            // Fetch payment modes
            $payment_modes = Payment_type_model::whereNull('deleted_at')->get();
            $data = [];
            foreach ($payment_modes as $value) {
                $data[] = ['id' => $value->id, 'name' => $value->name];
            }

            // Fetch the latest fee collection record for the student
            $fees_collection = Fees_colletion_model::where('student_id', $request->student_id)->latest()->first();

            // Initialize invoice number
            $invoice = 1;
            if (!empty($fees_collection)) {
                $parts = explode('-', $fees_collection->invoice_no);
                if (count($parts) === 2) {
                    $number = (int) $parts[1];
                    $invoice = $number + 1;
                }
            }
            $invoiceNumber = 'INV' . $request->student_id . '-' . str_pad($invoice, 6, '0', STR_PAD_LEFT);

            // Fetch student information
            $student = User::where('id', $request->student_id)->first();
            $student_name = $student->firstname . ' ' . $student->lastname;

            // Fetch student fee and discount information
            $student_fees = Student_fees_model::where('student_id', $request->student_id)
                ->where('institute_id', $request->institute_id)
                ->first();
            //   echo "<pre>";print_r($student_fees);exit;

            $discount = Discount_model::where('student_id', $request->student_id)
                ->where('institute_id', $request->institute_id)
                ->first();

            // Calculate revised fee and discount data
            $revise_fee = 0;
            $discount_data = '00.00';
            if ($discount) {
                if ($discount->discount_by == 'Rupee') {
                    $revise_fee = $discount->discount_amount;
                    $discount_data = !empty($discount->discount_amount) ? $discount->discount_amount . '.00' : '00.00';
                } elseif ($discount->discount_by == 'Percentage') {
                    $revise_fee = $student_fees->total_fees * ($discount->discount_amount / 100);
                    $discount_data = !empty($discount->discount_amount) ? $discount->discount_amount . '%' : '0%';
                }
            }
            // echo $revise_fee;exit;

            // Fetch student payment history
            $student_history = Fees_colletion_model::where('student_id', $request->student_id)
                ->where('institute_id', $request->institute_id)
                ->orderBy('id', 'desc') 
                ->get();

            // Prepare history and calculate paid amount
            $history = [];
            $paid_amount = 0;
            foreach ($student_history as $value) {
                $history[] = [
                    'paid_amount' => $value->payment_amount,
                    'date' => $value->created_at,
                    'payment_mode' => $value->payment_type,
                    'invoice_no' => $value->invoice_no,
                    'transaction_id' => $value->transaction_id,
                ];
                if (!empty($value->payment_amount)) {
                    $paid_amount += $value->payment_amount;
                }
            }
             $remaing_maount =$student_fees->total_fees - $paid_amount - $revise_fee ;
            // Prepare the final data structure
            $data_final = [
                'invoice_number' => $invoiceNumber,
                'date' => date('Y-m-d'),
                'student_id' => $request->student_id,
                'student_name' => $student_name,
                'payment_type' => $data,
                'student_fees' => !empty($student_fees->total_fees) ? $student_fees->total_fees . '.00' : '00.00',
                'discount' => $discount_data,
                'paid_amount' => !empty($paid_amount) ? $paid_amount . '.00' : '00.00',
                'remaing_amount'=>!empty($remaing_maount) ? $remaing_maount . '.00' : '00.00',
                'history' => $history,
            ];

            return $this->response($data_final, "Fetch data successfully");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }
}
