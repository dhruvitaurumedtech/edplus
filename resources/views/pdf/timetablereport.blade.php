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

    <h2>Timetable</h2>
    <hr>
    @if($data['requestdata']['standard_id'])
        <p><b>Standard Name: </b>{{$data['timetable'][0]['standardname']}}</p>
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
            @foreach ($data['days'] as $days)
                <th>{{ $days['day'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <!-- Determine the unique time slots across all days -->
        @php
            $timeSlots = collect($data['timetable'])->unique('start_time')->pluck('start_time')->sort();
        @endphp

        <!-- Loop through each unique time slot -->
        @foreach ($timeSlots as $timeSlot)
            <tr>
                @foreach ($data['days'] as $day)
                    <!-- Check if there's a timetable entry for this day and time slot -->
                    @php
                        $entry = collect($data['timetable'])->firstWhere(function ($item) use ($day, $timeSlot) {
                            return $item['day'] == $day['id'] && $item['start_time'] == $timeSlot;
                        });
                    @endphp

                    <!-- If an entry exists, display it; otherwise, display an empty cell -->
                    <td>
                        @if($entry)
                            {{ $entry['subjectname'] }} <br> {{ $entry['start_time'] }} - {{ $entry['end_time'] }}
                        @else
                            N/A
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

    

    </div>

</body>
</html>
