<!DOCTYPE html>
<html>
<head>
    <title>Fees Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                              
    <style>
       
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
                                                <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Static Vertical Line Graph</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            margin: 0;
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
            width: 600px;
            position: relative;
            margin: 20px;
            border-left: 2px solid #333;
            border-bottom: 2px solid #333;
            background-color: #fff;
        }

       

        .line {
            position: absolute;
            bottom: 0;
            width: 50px; /* Adjusted line width */
            background-color: #4CAF50;
        }

        /* Heights for the points */
        .point-Italy { left: 50px; bottom: 55%; }
        .point-France { left: 150px; bottom: 49%; }
        .point-Spain { left: 250px; bottom: 44%; }
        .point-USA { left: 350px; bottom: 24%; }
        .point-Argentina { left: 450px; bottom: 15%; }

        /* Dynamic line heights based on points */
        .line-Italy { height: 55%; left: 50px; }
        .line-France { height: 49%; left: 150px; }
        .line-Spain { height: 44%; left: 250px; }
        .line-USA { height: 24%; left: 350px; }
        .line-Argentina { height: 15%; left: 450px; }

        .labels {
            display: flex;
            justify-content: space-between;
            width: 100%;
            position: relative;
            margin-top: 10px;
        }

        .label {
            text-align: center;
            position: relative;
            width: 50px; /* Set width for proper alignment */
            top: 5px; /* Adjust to position label closer to the chart */
        }

        /* Y-axis labels */
        .y-axis-labels {
            position: absolute;
            left: -40px; /* Positioning for Y-axis labels */
            top: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 5px;
        }

        .y-label {
            text-align: right;
        }
    </style>
</head>
<body>

    <h1>World Wine Production 2018</h1>
    <div class="chart-container">
        <div class="y-axis-labels">
            <div class="y-label">60</div>
            <div class="y-label">50</div>
            <div class="y-label">40</div>
            <div class="y-label">30</div>
            <div class="y-label">20</div>
            <div class="y-label">10</div>
        </div>

        <div class="line line-Italy"></div>
        <div class="point point-Italy"></div>

        <div class="line line-France"></div>
        <div class="point point-France"></div>

        <div class="line line-Spain"></div>
        <div class="point point-Spain"></div>

        <div class="line line-USA"></div>
        <div class="point point-USA"></div>

        <div class="line line-Argentina"></div>
        <div class="point point-Argentina"></div>
    </div>

    <div class="labels">
        <div class="label" style="left: 50px;">Italy</div>
        <div class="label" style="left: 150px;">France</div>
        <div class="label" style="left: 250px;">Spain</div>
        <div class="label" style="left: 350px;">USA</div>
        <div class="label" style="left: 450px;">Argentina</div>
    </div>

</body>
</html>




                    
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
