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
        

    </style>
</head>
<body>

    <h2>Timetable</h2>
    <hr>
    @if($data['requestdata']['standard_id'])
        <p><b>Standard Name: </b>{{$data['timetable'][0]['standardname'] ?? 'N/A' }}</p>
    @endif
    @if($data['requestdata']['batch_id'])
        <p><b>Batch Name: </b>{{$data['timetable'][0]['batch_name'] ?? 'N/A' }}</p>
    @endif
    @if($data['requestdata']['teacher_id'])
        <p><b>Teacher Name: </b>{{$data['timetable'][0]['firstname'] ?? '' }} {{$data['timetable'][0]['lastname'] ?? 'N/A'}}</p>
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
                            {{ $entry['start_time'] }} - {{ $entry['end_time'] }}
                            <br>{{ $entry['subjectname'] }} 
                            <br> {{ $entry['lecturtype'] }}
                            <br> {{ $entry['classroom'] }}
                            <br>
                            @if($data['requestdata']['teacher_id'] == '')
                                {{ $entry['firstname'] }} {{ $entry['lastname'] }} 
                            @endif
                        @else
                            N/A
                        @endif
                        
                        @if($data['requestdata']['batch_id'] == '')
                        <p><b>Batch Name: </b>{{$data['timetable'][0]['batch_name'] ?? 'N/A' }}</p>
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
