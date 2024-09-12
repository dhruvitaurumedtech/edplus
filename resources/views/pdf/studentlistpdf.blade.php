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

        
        th:nth-child(1) { width: 40px; }   
        th:nth-child(2) { width: 100px; }  
        th:nth-child(3) { width: 150px; }   
        th:nth-child(4) { width: 200px; }  
        th:nth-child(5) { width: 100px; }  
        th:nth-child(6) { width: 80px; }   
        th:nth-child(7) { width: 80px; }   
        th:nth-child(8) { width: 80px; }   

        tr {
            page-break-inside: avoid;
        }

        table {
            transform: scale(0.95);
            transform-origin: top left;
        }

        @media print {
            body {
                margin: 0mm;
            }
            table {
                width: 100%;
                word-wrap: break-word;
            }
            tr {
                page-break-inside: avoid;
            }
        }

    </style>
</head>
<body>

    <h2>Teacher List</h2>
    <hr>

    @foreach ($data['teacherdata'] as $item)
    <p><b>Board Name: </b>{{$item['board_name']}}</p>
        @foreach ($item['medium'] as $mediumDT)
        <p><b>Medium Name: </b>{{$mediumDT['medium_name']}}</p>
            @foreach ($mediumDT['class'] as $classDT)
                @foreach ($classDT['standard'] as $standardDT)
                <p><b>Standard Name: </b>{{$standardDT['standard_name'] . ' (' . $classDT['class_name'] . ') '}}</p>
                    @foreach ($standardDT['subject'] as $subjectDT)
                    <p><b>Subject Name: </b>{{$subjectDT['subject_name'] }}</p>
                        
                        <div class="content">
                            <table>
                                    
                                <thead>
                                <tr><th>Teachers</th></tr>
                                </thead>
                                <tbody>
                                @foreach ($subjectDT['teachers'] as $teachersDT)
                                <tr>
                                    <td>{{$teachersDT['name'] }}</td>
                                </tr>
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