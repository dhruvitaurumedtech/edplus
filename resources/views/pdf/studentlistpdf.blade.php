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

    <h2>Student List</h2>
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
                       
                            <div class="content">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Student Name</th>
                                            <th>Subject Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $i=1; @endphp  
                                    @foreach ($batchDT['student'] as $studentDT)
                                       
                                       
                                        <tr>
                                           
                                                <td>{{ $i }}</td>
                                                <td>{{ $studentDT['student_name'] }}</td>
                                                <td> @foreach ($studentDT['subject'] as $key => $subjectDT) {{ $subjectDT['subject_name'] }} @endforeach    </td>
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

    
    
</body>
</html>