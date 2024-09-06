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

    <h2>Student List</h2>
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Stud_ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Board</th>
                <th>Medium</th>
                <th>Standard</th>
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data as $item)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $item['id'] }}</td>
                <td>{{ $item['firstname'].' '.$item['lastname'] }}</td>
                <td>{{ $item['email'] }}</td>
                <td>{{ $item['board_name'] }}</td>
                <td>{{ $item['medium_name'] }}</td>
                <td>{{ $item['standard_name'] }}</td>
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

</body>
</html>
