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
        th:nth-child(1) { width: 40px; }   /* Sr. No column */
        th:nth-child(2) { width: 100px; }  /* Subject column */
        th:nth-child(3) { width: 150px; }  /* Name column */
        th:nth-child(4) { width: 200px; }  /* Description column */
        th:nth-child(5) { width: 100px; }  /* Video column */

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
<h2>Content List</h2>
<hr>
@php $i = 1; @endphp
@if(!empty($data))
    @foreach ($data as $item)
        <p><b>Board Name:</b> {{ $item['board_name'] }}</p>
        <p><b>Medium Name:</b> {{ $item['medium_name'] }}</p>
        <p><b>Standard Name:</b> {{ $item['standard_name'] }}</p>

        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Subject</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Video</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ !empty($item['subject_name']) ? $item['subject_name'] : '' }}</td>
                        <td>{{ !empty($item['topic_name']) ? $item['topic_name'] : '' }}</td>
                        <td>{{ !empty($item['topic_description']) ? $item['topic_description'] : '' }}</td>
                        <td>{{ !empty($item['topic_video']) ? $item['topic_video'] : '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @php $i++; @endphp
    @endforeach
@else
    <div class="content">
        <table>
            <tr>
                <td colspan="5">No data available</td>
            </tr>
        </table>
    </div>
@endif
</body>
</html>
