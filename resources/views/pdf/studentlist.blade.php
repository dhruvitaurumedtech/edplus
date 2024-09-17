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
            table-layout: fixed; / Ensure even distribution of content /
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

        / Set specific width for each column /
        th:nth-child(1) { width: 40px; }   / No column /
        th:nth-child(2) { width: 100px; }  / Student_ID column /
        th:nth-child(3) { width: 150px; }  / Full Name column /
        th:nth-child(4) { width: 200px; }  / Email column /
        th:nth-child(5) { width: 100px; }  / Board column /
        th:nth-child(6) { width: 80px; }   / Class column /
        th:nth-child(7) { width: 80px; }   / Medium column /
        th:nth-child(8) { width: 80px; }   / Standard column /

        / Prevent rows from breaking /
        tr {
            page-break-inside: avoid;
        }

        / Scale down table if needed /
        table {
            transform: scale(0.95);
            transform-origin: top left;
        }

        / Specific print styles for better layout /
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
<h2>Student List</h2>
<hr>
@if(!empty($data['student_list']))

@if(!empty($data['request_data']['board_id']))
    <p><b>Board_name:</b> {{ $data['student_list'][0]['board_name'] }}</p>
@endif

@if(!empty($data['request_data']['batch_id']))
    <p><b>Batch_name:</b> {{ $data['student_list'][0]['batch_name'] }}</p>
@endif

@if(!empty($data['request_data']['class_id']))
    <p><b>Class_name:</b> {{ $data['student_list'][0]['class_name'] }}</p>
@endif

@if(!empty($data['request_data']['medium_id']))
    <p><b>Medium_name:</b> {{ $data['student_list'][0]['medium_name'] }}</p>
@endif

@if(!empty($data['request_data']['standard_id']))
    <p><b>Standard_name:</b> {{ $data['student_list'][0]['standard_name'] }}</p>
@endif

@endif

     
    
     
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Stud_ID</th>
                <th>Full Name</th>
                <th>Email</th>
                @if(empty($data['request_data']['board_id']))
                <th>Board</th>
                @endif
                @if(empty($data['request_data']['batch_id']))
                <th>Batch</th>
                @endif
                @if(empty($data['request_data']['class_id']))
                <th>Class</th>
                @endif
                @if(empty($data['request_data']['medium_id']))
                <th>Medium</th>
                @endif
                @if(empty($data['request_data']['standard_id']))
                <th>Standard</th>
                @endif
            </tr>
        </thead>
        <tbody>@php $i = 1; @endphp
          @if(!empty($data['student_list']))
            @foreach ($data['student_list'] as $item)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $item['id'] }}</td>
                <td>{{ $item['firstname'].' '.$item['lastname'] }}</td>
                <td>{{ $item['email'] }}</td>
                @if(empty($data['request_data']['board_id']))
                        <td> {{ $item['board_name'] }}</td>
                @endif

                @if(empty($data['request_data']['batch_id']))
                    <td> {{ $item['batch_name'] }}</td>
                @endif

                @if(empty($data['request_data']['class_id']))
                    <td> {{ $item['class_name'] }}</td>
                @endif

                @if(empty($data['request_data']['medium_id']))
                    <td> {{ $item['medium_name'] }}</td>
                @endif

                @if(empty($data['request_data']['standard_id']))
                    <td> {{ $item['standard_name'] }}</td>
                @endif
            </tr>
            @php $i++; @endphp
                    @endforeach
                @else
                    <tr>
                        <td colspan="9">No data available</td>
                    </tr>
                @endif
        </tbody>
    </table>
    </div>

</body>
</html>