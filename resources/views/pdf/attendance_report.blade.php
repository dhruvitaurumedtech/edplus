<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
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
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            white-space: nowrap;
            text-align: center;
        }

        
      
    </style>
</head>
<body>

    <h2>Attendance Report</h2>
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
                                            <th width="10%" >No</th>
                                            <th width="65%">Student Name</th>
                                            <th> Present</th>
                                            <th> Absent </th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $i=1; @endphp  
                                    @foreach ($subjectDT['student'] as $studentDT)
                                        <tr>
                                           
                                                <td>{{ $i }}</td>
                                                <td style="text-align: left;">{{ $studentDT['student_name'] }}</td>
                                               
                                                
                                                    @if(!empty($studentDT['attendance']))
                                                     @foreach ($studentDT['attendance'] as $attendanceDT)
                                                         <td>{{ $attendanceDT['present_count'] }}</td>
                                                         <td>{{ $attendanceDT['absent_count'] }}</td>
                                                     @endforeach 
                                                     @else
                                                        <td colspan="5">No Data Available</td>
                                                     @endif
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