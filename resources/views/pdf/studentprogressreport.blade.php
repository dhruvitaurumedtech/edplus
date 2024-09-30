<!DOCTYPE html>
<html>

<head>
    <title>Fees Report</title>
 
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            word-wrap: break-word;
            table-layout: fixed;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            text-align: center;

        }

        th {
            background-color: #f2f2f2;
            white-space: nowrap;
            text-align: center;
        }

        .custom-btn {
            color: #28a745;
        }

        .custom-btn-danger {
            color: #FF0000;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .chart-container {
            display: flex;
            align-items: flex-end;
            height: 300px;
            /* width: 600px; */
            position: relative;
            margin: 20px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            background-color: #fff;
        }



        .line {
            position: absolute;
            bottom: 0;
            width: 50px;
            background-color: #4CAF50;
        }






        .label {
            text-align: center;
            position: relative;
            width: 50px;
            top: 5px;
        }

        .y-axis-labels {
            position: absolute;
            left: -40px;
            top: 10;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 0px;
        }

        .y-label {
            text-align: right;
        }
    </style>
</head>

<body>

    <h2>Fees Report</h2>
    <hr>

    @if (!empty($data['board_result']) && is_array($data))
    @foreach ($data['board_result'] as $board_index=>$item)
    <p><b>Board Name: </b>{{$item['board_name']}}</p>

    @if (!empty($item['medium']) && is_array($item['medium']))
    @foreach ($item['medium'] as $medium_index=>$mediumDT)
    <p><b>Medium Name: </b>{{$mediumDT['medium_name']}}</p>

    @if (!empty($mediumDT['class']) && is_array($mediumDT['class']))
    @foreach ($mediumDT['class'] as $class_index=>$classDT)
    <p><b>Class Name: </b>{{$classDT['class_name']}}</p>

    @if (!empty($classDT['standard']) && is_array($classDT['standard']))
    @foreach ($classDT['standard'] as $standard_index=>$standardDT)
    <p><b>Standard Name: </b>{{$standardDT['standard_name']}}</p>

    @if (!empty($standardDT['batch']) && is_array($standardDT['batch']))
    @foreach ($standardDT['batch'] as $batch_index=>$batchDT)
    <p><b>Batch Name: </b>{{$batchDT['batch_name']}}</p>
    @foreach ($batchDT['student'] as $studentIndex => $studentDT)
    


    <center><h1><p><b>Student Name: </b>{{ $studentDT['student_name'] }}</p> </h1></center>
    @php
    $imagePath = public_path('student_report_graph/student_image_report_' . $board_index . $medium_index . $class_index . $standard_index . $batch_index . $studentIndex . '.png');
    $imageData = file_exists($imagePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath)) : '';
    @endphp

    @if ($imageData)
        <img src="{{ $imageData }}" alt="Student Image" style="width: 100%; height: auto;">
    @else
        <p>No image available for this student.</p>
    @endif
    @endforeach
    @endforeach
    @endif
    @endforeach
    @endif
    @endforeach
    @endif
    @endforeach
    @endif
    @endforeach
    @else
    <p>No data available.</p>
    @endif

</body>

</html>