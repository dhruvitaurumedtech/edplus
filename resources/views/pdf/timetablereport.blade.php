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
            table-layout: fixed; /* Ensure even distribution of content */
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

        /* Set specific width for each column */
        th:nth-child(1) { width: 40px; }   /* No column */
        th:nth-child(2) { width: 100px; }  /* Student_ID column */
        th:nth-child(3) { width: 150px; }  /* Full Name column */
        th:nth-child(4) { width: 200px; }  /* Email column */
        th:nth-child(5) { width: 100px; }  /* Board column */
        th:nth-child(6) { width: 80px; }   /* Class column */
        th:nth-child(7) { width: 80px; }   /* Medium column */
        th:nth-child(8) { width: 80px; }   /* Standard column */

        /* Prevent rows from breaking */
        tr {
            page-break-inside: avoid;
        }

        /* Scale down table if needed */
        table {
            transform: scale(0.95);
            transform-origin: top left;
        }

        /* Specific print styles for better layout */
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
    @if($data['requestdata']['standard_id'])
        <p><b>Medium Name: </b>{{$data['timetable'][0]['standardname']}}</p>
    @endif
    @if($data['requestdata']['batch_id'])
        <p><b>Batch Name: </b>{{$data['timetable'][0]['batch_name']}}</p>
    @endif
    @if($data['requestdata']['teacher_id'])
        <p><b>Teacher Name: </b>{{$data['timetable'][0]['firstname']}} {{$data['timetable'][0]['lastname']}}</p>
    @endif

    <div class="content">
    <table>
        <thead>
            <tr>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Saturday</th>
                <th>Sunday</th>
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data['timetable'] as $item)
            <tr>
                <td>{{ $item['firstname'].' '.$item['lastname'] }}</td>
                @if($data['requestdata']['board_id'] == '')
                <td>{{ $item['board_name'] }}</td>
                @endif
                @if($data['requestdata']['medium_id'] == '')
                <td>{{ $item['medium_name'] }}</td>
                @endif
                @if($data['requestdata']['standard_id'] == '')
                <td>{{ $item['standard_name'] }}</td>
                @endif
                @if($data['requestdata']['standard_id'] == '')
                <td>{{ $item['firstname'].' '.$item['lastname'] }}</td>
                @endif
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

</body>
</html>
