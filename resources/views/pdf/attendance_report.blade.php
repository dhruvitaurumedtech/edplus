<!DOCTYPE html>
<html>
<head>
    <title>Student List PDF</title>
    <style>
        body {
            margin: 0mm;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            word-wrap: break-word;
            table-layout: fixed; / Ensure even distribution of content /
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

    <h2>Attendance List</h2>
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
                    @foreach ($batchDT['subject'] as $key => $subjectDT)
                     <p><b>Subject Name: </b>{{$subjectDT['subject_name']}}</p>
                       
                            <div class="content">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>iD</th>
                                            <th>Student Name</th>
                                            <th>Attendance</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $i=1; @endphp  
                                    @foreach ($subjectDT['student'] as $studentDT)
                                        <tr>
                                           
                                                <td>{{ $i }}</td>
                                                <td>{{ $studentDT['student_id'] }}</td>
                                                <td>{{ $studentDT['student_name'] }}</td>
                                               
                                                <td>
                                                <table>
                                                    <tr><th>Total</th><th>Present</th><th>Absent</th></tr>
                                                    @if(!empty($studentDT['attendance']))
                                                <tr> @foreach ($studentDT['attendance'] as $attendanceDT)
                                                         <td>{{ $attendanceDT['total_attendance'] }}</td>
                                                         <td>{{ $attendanceDT['present_count'] }}</td>
                                                         <td>{{ $attendanceDT['absent_count'] }}</td>
                                                     @endforeach 
                                                     @else
                                                        <td colspan="3">No Data Available</td>
                                                     @endif
                                                 </tr>
                                                 </table>
                                                 </td>
                                               
                                        </tr>
                                        @php $i++; @endphp
                                    @endforeach
                                        
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endforeach 
                @endforeach 
            @endforeach
        @endforeach
    @endforeach

    
    
</body>
</html>