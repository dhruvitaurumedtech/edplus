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

    <h2>Institute Owner Detail</h2>
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Mobile No.</th>
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data['institute_detail'] as $item)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $item['firstname'].' '.$item['lastname'] }}</td>
                <td>{{ $item['email'] }}</td>
                <td>{{ $item['address'] }}</td>
                <td>{{ $item['mobile'] }}</td>
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

    <h2>Institute for List</h2>
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data['institute_for'] as $institute_for)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $institute_for['name'] }}</td>
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

    <h2>Board List</h2>
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data['institute_board'] as $institute_board)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $institute_board['name'] }}</td>
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

    <h2>Medium List</h2>
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data['institute_medium'] as $institute_medium)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $institute_medium['name'] }}</td>
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

    <h2>Class List</h2>
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data['institute_class'] as $institute_class)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $institute_class['name'] }}</td>
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

    <h2>Standard List</h2>
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data['institute_standard'] as $institute_standard)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $institute_standard['name'] }}</td>
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

    <h2>Subject List</h2>
    <div class="content">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data['institute_subject'] as $institute_subject)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $institute_subject['name'] }}</td>
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

</body>
</html>
