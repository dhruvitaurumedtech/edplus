<!DOCTYPE html>
<html>

<head>
    <title>Fees Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    <!-- Check if $data is not empty -->
    @if (!empty($data['board_result']) && is_array($data))
    @foreach ($data['board_result'] as $item)
    <p><b>Board Name: </b>{{$item['board_name']}}</p>

    @if (!empty($item['medium']) && is_array($item['medium']))
    @foreach ($item['medium'] as $mediumDT)
    <p><b>Medium Name: </b>{{$mediumDT['medium_name']}}</p>

    @if (!empty($mediumDT['class']) && is_array($mediumDT['class']))
    @foreach ($mediumDT['class'] as $classDT)
    <p><b>Class Name: </b>{{$classDT['class_name']}}</p>

    @if (!empty($classDT['standard']) && is_array($classDT['standard']))
    @foreach ($classDT['standard'] as $standardDT)
    <p><b>Standard Name: </b>{{$standardDT['standard_name']}}</p>

    @if (!empty($standardDT['batch']) && is_array($standardDT['batch']))
    @foreach ($standardDT['batch'] as $batchDT)
    <p><b>Batch Name: </b>{{$batchDT['batch_name']}}</p>
    @foreach ($batchDT['student'] as $studentIndex => $studentDT)
    <p><b>Student Name: </b>{{ $studentDT['student_name'] }}</p>


    <h1>Student Exam Chart </h1>
    <div class="chart-container">
        <div class="y-axis-labels">
            @for ($i = 100; $i >= 0; $i -= 10)
            <div class="y-label">{{ $i }}</div>
            @endfor
        </div>
        <div style="display:flex !important;gap:160px !important;margin-left: 100px;position: relative;height:100%;align-items: end;">
            @foreach($studentDT['exam'] as $index => $examDT)
            <div class="test-{{$index}}" style="height: {{ $examDT['percentage'] }}%; width: 70px; background-color: #4CAF50;text-align:center;color:#fff">{{$examDT['percentage'].'%'}}<br>{{$examDT['mark'].'/'.$examDT['total_mark']}}</div>
            @endforeach
        </div>

    </div>
    <div style="display:flex;gap:65px;justify-content: left;margin-left: 100px;">
        
        @foreach($studentDT['exam'] as $examDT)
        <div class="label" style="left: 10px;width:165px;display: flex; padding: 10px;">
        {{date('d-m-20y',strtotime($examDT['exam_date']))}}
        </div>
        @endforeach
    </div>
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