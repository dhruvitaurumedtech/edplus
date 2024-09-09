<!DOCTYPE html>
<html>
<head>
    <title>Student List PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
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
        }
    </style>
</head>
<body>

    <h2>Student List</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Student_ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Board</th>
                <th>Medium</th>
                <th>Class</th>
                <th>Standard</th>
                <th>Batch</th>
                <th>Attendance</th>
                <th>Date</th>
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
                <td>{{ $item['class_name']}}</td>
                <td>{{ $item['standard_name'] }}</td>
                <td>{{ $item['batch_name'] }}</td>
                <td>{{ $item['date'] }}</td>
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>

</body>
</html>
