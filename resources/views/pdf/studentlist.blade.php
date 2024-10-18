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
            table-layout: fixed;/ Ensure even distribution of content /
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
        }

        th {
            background-color: #f2f2f2;
            white-space: nowrap;
        }

        / Set specific width for each column / th:nth-child(1) {
            width: 40px;
        }

        / No column / th:nth-child(2) {
            width: 100px;
        }

        / Student_ID column / th:nth-child(3) {
            width: 150px;
        }

        / Full Name column / th:nth-child(4) {
            width: 200px;
        }

        / Email column / th:nth-child(5) {
            width: 100px;
        }

        / Board column / th:nth-child(6) {
            width: 80px;
        }

        / Class column / th:nth-child(7) {
            width: 80px;
        }

        / Medium column / th:nth-child(8) {
            width: 80px;
        }

        / Standard column / / Prevent rows from breaking / tr {
            page-break-inside: avoid;
        }

        / Scale down table if needed / table {
            transform: scale(0.95);
            transform-origin: top left;
        }

        / Specific print styles for better layout / @media print {
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
    @if(!empty($data['studentdata']))

    @if(!empty($data['requestdata']['board_id']))
    <p><b>Board_name:</b> {{ $data['studentdata'][0]['board_name'] }}</p>
    @endif

    @if(!empty($data['requestdata']['batch_id']))
    <p><b>Batch_name:</b> {{ $data['studentdata'][0]['batch_name'] }}</p>
    @endif

    @if(!empty($data['requestdata']['class_id']))
    <p><b>Class_name:</b> {{ $data['studentdata'][0]['class_name'] }}</p>
    @endif

    @if(!empty($data['requestdata']['medium_id']))
    <p><b>Medium_name:</b> {{ $data['studentdata'][0]['medium_name'] }}</p>
    @endif

    @if(!empty($data['requestdata']['standard_id']))
    <p><b>Standard_name:</b> {{ $data['studentdata'][0]['standard_name'] }}</p>
    @endif

    @endif




    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    @if($data['fields']['id'] == 1)
                    <th>Stud_ID</th>
                    @endif
                    @if($data['fields']['firstname'] == 1 || $data['fields']['lastname'] == 1)
                    <th>Full Name</th>
                    @endif
                    @if($data['fields']['email'] == 1)
                    <th>Email</th>
                    @endif
                    @if(empty($data['requestdata']['board_id']) || $data['fields']['board_name'] == 1)
                    <th>Board</th>
                    @endif
                    @if(empty($data['requestdata']['batch_id']) || $data['fields']['batch_name'] == 1)
                    <th>Batch</th>
                    @endif
                    @if(empty($data['requestdata']['class_id']) || $data['fields']['class_name'] == 1)
                    <th>Class</th>
                    @endif
                    @if(empty($data['requestdata']['medium_id']) || $data['fields']['medium_name'] == 1)
                    <th>Medium</th>
                    @endif
                    @if(empty($data['requestdata']['standard_id']) || $data['fields']['standard_name'] == 1)
                    <th>Standard</th>
                    @endif
                </tr>
            </thead>
            <tbody>@php $i = 1; @endphp
                @if(!empty($data['studentdata']))
                @foreach ($data['studentdata'] as $item)
                <tr>
                    <td>{{ $i }}</td>
                    @if($data['fields']['id'] == 1)
                    <td>{{ $item['id'] }}</td>
                    @endif
                    @if($data['fields']['firstname'] == 1 || $data['fields']['lastname'] == 1)
                    <td>@if($data['fields']['firstname'] == 1) {{$item['firstname']}} @endif
                         @if($data['fields']['lastname'] == 1) $item['lastname'] @endif</td>
                    @endif
                    @if($data['fields']['email'] == 1)
                    <td>{{ $item['email'] }}</td>
                    @endif
                    @if(empty($data['requestdata']['board_id']) || $data['fields']['board_name'] == 1)
                    <td> {{ $item['board_name'] }}</td>
                    @endif

                    @if(empty($data['requestdata']['batch_id']) || $data['fields']['batch_name'] == 1)
                    <td> {{ $item['batch_name'] }}</td>
                    @endif

                    @if(empty($data['requestdata']['class_id']) || $data['fields']['class_name'] == 1)
                    <td> {{ $item['class_name'] }}</td>
                    @endif

                    @if(empty($data['requestdata']['medium_id']) || $data['fields']['medium_name'] == 1)
                    <td> {{ $item['medium_name'] }}</td>
                    @endif

                    @if(empty($data['requestdata']['standard_id']) || $data['fields']['standard_name'] == 1)
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