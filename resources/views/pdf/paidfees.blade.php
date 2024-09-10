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
<h2>Paid Fees List</h2>
@if(!empty($data['students']))

@if(!empty($data['request_data']['batch_id']))
    <p><b>Batch_name:</b> {{ $data['students'][0]['batch_name'] }}</p>
@endif

@if(!empty($data['request_data']['standard_id']))
    <p><b>Standard_name:</b> {{ $data['students'][0]['standard_name'] }}</p>
@endif
@if(!empty($data['request_data']['status']))
    <p><b>Fees_Status:</b> {{ $data['students'][0]['status'] }}</p>
@endif
@endif
  <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                @if(empty($data['request_data']['batch_id'])) <th>Batch</th>@endif
                @if(empty($data['request_data']['standard_id'])) <th>Standard</th>@endif
                <th>Discount</th>
                @if(isset($data['request_data']['status']) && $data['request_data']['status'] == 'paid')
                    <th>Paid Amount</th>
                @else
                    <th>Due Amount</th>
                @endif
            </tr>
        </thead>
        <tbody>@php $i = 1; @endphp
          @if(!empty($data['students']))
            @foreach ($data['students'] as $item)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $item['student_name'] }}</td>
                        @if(empty($data['request_data']['batch_id'])) <td> {{ $item['batch_name'] }}</td>@endif
                        @if(empty($data['request_data']['standard_id'])) <td> {{ $item['standard_name'] }}</td>@endif
                        <td> {{ (!empty($item['discount'])) ? $item['discount'] : '0' }}</td>
                        <td> {{ !empty($item['paid_amount']) ? $item['paid_amount'] : $item['due_amount'] }}</td>
            </tr>
            @php $i++; @endphp
                    @endforeach
                @else
                    <tr>
                        <td colspan="7">No data available</td>
                    </tr>
                @endif
        </tbody>
    </table>
    </div>

</body>
</html>
