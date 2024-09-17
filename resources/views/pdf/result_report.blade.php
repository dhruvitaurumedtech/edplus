<!DOCTYPE html>
<html>
<head>
    <title>Result Report</title>
    <style>
        body {
            margin: 0mm;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            word-wrap: break-word;
            table-layout: fixed; 
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            white-space: nowrap;
        }
      
    </style>
</head>
<body>

    <h2>Result Report</h2>
    <hr>

    @foreach ($data as $item)
    <p><b>Board Name: </b>{{$item['board_name']}}</p>
        @foreach ($item['medium'] as $mediumDT)
        <p><b>Medium Name: </b>{{$mediumDT['medium_name']}}</p>
            @foreach ($mediumDT['class'] as $classDT)
             <p><b>Class Name: </b>{{$classDT['class_name']}}</p>
                @foreach ($classDT['standard'] as $standardDT)
                <p><b>Standard Name: </b>{{$standardDT['standard_name']}}</p>
                    @foreach ($standardDT['batch'] as $batchDT)
                    <p><b>Batch Name: </b>{{$batchDT['batch_name']}}</p>
                        @foreach ($batchDT['exam'] as $examDT)
                        @php
                                $startTime = !empty($examDT['start_time']) ? new DateTime($examDT['start_time']) : null;
                                $endTime = !empty($examDT['end_time']) ? new DateTime($examDT['end_time']) : null;
                                $duration = '';

                                if ($startTime && $endTime) {
                                    $interval = $startTime->diff($endTime);
                                    $duration = $interval->format('%H:%I'); // Format as hours:minutes
                                }
                            @endphp

                            <p><b>Date:</b> {{ !empty($examDT['exam_date']) ? date('d-m-Y', strtotime($examDT['exam_date'])) : '' }} 
                            @if($duration)
                                (Duration: {{ $duration }})
                            @endif
                            </p>

                            @php $i = 1 @endphp

                       <table>
                            @if(!empty($batchDT['exam']))
                            <tr>
                                <th width="10%" style="text-align: center;">No</th>
                                <th style="text-align: center;">Exam</th>
                                <th style="text-align: center;">Subject</th>
                                <th style="text-align: center;">Total Mark</th>
                            </tr>

                                        
                                           
                                                <tr>
                                                    <td  style="text-align: center;">{{ $i }}</td>
                                                    <td  style="text-align: center;">{{ !empty($examDT['exam_name']) ? $examDT['exam_name'] : '' }}</td>
                                                    <td  style="text-align: center;">{{ !empty($examDT['subject_name']) ? $examDT['subject_name'] : '' }}</td>
                                                    <td  style="text-align: center;">{{ !empty($examDT['total_mark']) ? $examDT['total_mark'] : '' }}</td>
                                                </tr>
                                                @if(!empty($examDT['exam_wise_student']))
                                                    <tr>
                                                        <td colspan="4">
                                                            <table >
                                                                <tr>
                                                                    <th width="8.5%">No</th>
                                                                    <th width="62%">Student Name</th>
                                                                    <th >Mark</th>
                                                                </tr>
                                                                @php $j=1 @endphp 
                                                                @if(!empty($examDT['exam_wise_student']))
                                                                @foreach ($examDT['exam_wise_student'] as $exam_wise_studentDT)
                                                                    <tr>
                                                                        <td >{{ $j }}</td>
                                                                        <td>{{ $exam_wise_studentDT['student_name'] }}</td>
                                                                        <td>{{ $exam_wise_studentDT['marks'] }}</td>
                                                                        </tr>
                                                                    @php $j++ @endphp 
                                                                @endforeach
                                                                @else
                                                                <tr>
                                                                    <td colspan="3">No Data Found!</td>
                                                                </tr>
                                                                @endif
                                                            </table>
                                                        </td>
                                                    </tr>
                                                @endif
                                                
                                            
                                        @else
                                            <tr>
                                                <td colspan="4">No Data Found!</td>
                                            </tr>
                                        @endif
                                    </table>
                                    @php $i++ @endphp 
                        @endforeach
                        @endforeach
                    @endforeach 
                @endforeach 
            @endforeach
        @endforeach
</body>
</html>