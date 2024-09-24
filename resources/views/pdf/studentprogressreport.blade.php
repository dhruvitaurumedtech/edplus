<!DOCTYPE html>
<html>

<head>
    <title>Fees Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .chart-container {
            display: block;
            margin: 20px 0;
            height: 300px;
            position: relative;
            margin: 20px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            background-color: #fff;
        }

        .bar-chart {
            display: inline-block;
            /* position: relative; */
            
        }

        .bar {
            width: 50px;
            background-color: #4CAF50;
            color: #fff;
            bottom: 0;
            margin-left:30px;
            display: flex;
            justify-content: end;
            align-items: end;
            
        }

        .y-axis-labels {
            position: absolute;
            left: -40px;
            top: 60;
            bottom: 0;
            height: 100%;
            display: flex;
            gap:10px;
            flex-direction: column;
            padding: 10px;
        }
        .y-label {
            text-align: right;
        }
        .x-axis-labels{
            display: inline-block;
            width: 100px;
            text-align: center;
        }

        .axis {
            
        }
    </style>
</head>

<body>

    <h2>Fees Report</h2>

    <!-- Check if $data is not empty -->
    @if (!empty($data['board_result']) && is_array($data))
        @foreach ($data['board_result'] as $item)
            <p><b>Board Name: </b>{{ $item['board_name'] }}</p>

            @if (!empty($item['medium']) && is_array($item['medium']))
                @foreach ($item['medium'] as $mediumDT)
                    <p><b>Medium Name: </b>{{ $mediumDT['medium_name'] }}</p>

                    @if (!empty($mediumDT['class']) && is_array($mediumDT['class']))
                        @foreach ($mediumDT['class'] as $classDT)
                            <p><b>Class Name: </b>{{ $classDT['class_name'] }}</p>

                            @if (!empty($classDT['standard']) && is_array($classDT['standard']))
                                @foreach ($classDT['standard'] as $standardDT)
                                    <p><b>Standard Name: </b>{{ $standardDT['standard_name'] }}</p>

                                    @if (!empty($standardDT['batch']) && is_array($standardDT['batch']))
                                        @foreach ($standardDT['batch'] as $batchDT)
                                            <p><b>Batch Name: </b>{{ $batchDT['batch_name'] }}</p>

                                            @foreach ($batchDT['student'] as $studentIndex => $studentDT)
                                                <p><b>Student Name: </b>{{ $studentDT['student_name'] }}</p>

                                                <!-- Student Exam Chart -->
                                                <h3>Student Exam Chart</h3>
                                                <div class="chart-container">
                                                    <div class="y-axis-labels">
                                                        @for ($i = 100; $i >= 0; $i -= 10)
                                                            <div class="axis">{{ $i }}</div>
                                                        @endfor
                                                    </div>
                                                    <div style="display:grid !important;gap:160px !important;margin-left: 100px;position: relative;bottom:0;height:100%;align-items: end;">
                                                    @foreach ($studentDT['exam'] as $examDT)
                                                        <div class="bar-chart">
                                                            <div class="bar" style="height: {{ $examDT['percentage'] }}%;">
                                                                {{ $examDT['percentage'] }}%
                                                            </div>
                                                           
                                                        </div>
                                                    @endforeach
                                                    </div>
                                                    </div>
                                                    <div style="display:flex;gap:65px;justify-content: left;margin-left: 100px;">
        
                                                            @foreach($studentDT['exam'] as $examDT)
                                                            <div class="x-axis-labels" >
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
