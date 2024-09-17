<!DOCTYPE html>
<html>
<head>
    <title>Fees Report</title>
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

    <h2>Fees Report</h2>
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
                       
                           
                       
                    @endforeach 
                @endforeach 
            @endforeach
        @endforeach
    @endforeach

    
    
</body>
</html>