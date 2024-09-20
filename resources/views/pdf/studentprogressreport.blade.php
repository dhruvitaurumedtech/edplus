<!DOCTYPE html>
<html>
<head>
    <title>Fees Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                                                
                                                <!-- Generate a unique canvas ID using the $studentIndex -->
                                                <div style="width: 80%; margin: auto;">
        <canvas id="barChart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('barChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($studentDT['exam']),
                datasets: [{
                    label: 'Data',
                    data: @json($studentDT['exam']),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
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
