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

@if(!empty($data))
    @foreach ($data['base_table_response'] as $item)
        <p><b>Board Name:</b> {{ $item['board_name'] }}</p>
        <p><b>Medium Name:</b> {{ $item['medium_name'] }}</p>
        <p><b>Standard Name:</b> {{ $item['standard_name'] }}</p>
        <p><b>Subject name:</b> {{ $item['subject_name'] }}</p>
        
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Topic Name</th>
                        <th>Description</th>
                        <th>chapter No</th>
                        <th>chapter Name</th>
                  
                    </tr>
                </thead>
                <tbody>
                @php $i = 1; @endphp
                @foreach ($data['topic_response'] as $item2)
                   @if($item2['subject_id'] == $item['id'])
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ !empty($item2['topic_name']) ? $item2['topic_name'] : '' }}</td>
                        <td>{{ !empty($item2['topic_description']) ? $item2['topic_description'] : '' }}</td>
                        <td>{{ !empty($item2['chapter_no']) ? $item2['chapter_no'] : '' }}</td>
                        <td>{{ !empty($item2['chapter_name']) ? $item2['chapter_name'] : '' }}</td>
                  
                    </tr>
                    @endif
                    @php $i++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
       
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
