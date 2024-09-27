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

   
    @if (!empty($data['board_result']) && is_array($data))
    @foreach ($data['board_result'] as $item)
   
    @if (!empty($item['medium']) && is_array($item['medium']))
    @foreach ($item['medium'] as $mediumDT)
   
    @if (!empty($mediumDT['class']) && is_array($mediumDT['class']))
    @foreach ($mediumDT['class'] as $classDT)
   
    @if (!empty($classDT['standard']) && is_array($classDT['standard']))
    @foreach ($classDT['standard'] as $standardDT)
   
    @if (!empty($standardDT['batch']) && is_array($standardDT['batch']))
    @foreach ($standardDT['batch'] as $batchDT)
    @foreach ($batchDT['student'] as $studentIndex => $studentDT)
    


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
    <div style="display:flex;gap:45px;justify-content: left;margin-left: 100px;">
        
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
    @endif

</body>

</html>