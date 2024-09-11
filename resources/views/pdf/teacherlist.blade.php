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

    <h2>Teacher List</h2>
    <hr>
    @if($data['requestdata']['board_id'])
        <p><b>Board Name: </b>{{$data['teacherdata'][0]['board_name']}}</p>
    @endif
    @if($data['requestdata']['medium_id'])
        <p><b>Medium Name: </b>{{$data['teacherdata'][0]['medium_name']}}</p>
    @endif
    @if($data['requestdata']['class_id'])
        <p><b>Class Name: </b>{{$data['teacherdata'][0]['class_name']}}</p>
    @endif
    @if($data['requestdata']['standard_id'])
        <p><b>Standard: </b>{{$data['teacherdata'][0]['standard_id']}}</p>
    @endif
    @if($data['requestdata']['subject_id'])
        <p><b>Subject Name: </b>{{$data['teacherdata'][0]['subjectname']}}</p>
    @endif
    @if($data['requestdata']['creatdate'])
        <p><b>Date: </b>{{$data['teacherdata'][0]['created_at']}}</p>
    @endif
    
    
    <div class="content">
    <table>
          
            
        <thead>
            <tr>
                <th>No</th>
                <th>Full Name</th>
                <!-- <th>Email</th> -->
                @if($data['requestdata']['board_id'] == '')
                <th>Board</th>
                @endif
                @if($data['requestdata']['medium_id'] == '')
                <th>Medium</th>
                @endif
                @if($data['requestdata']['standard_id'] == '')
                <th>Standard</th>
                @endif
            </tr>
        </thead>
        <tbody><?php $i=1;?>
            @foreach ($data['teacherdata'] as $item)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $item['firstname'].' '.$item['lastname'] }}</td>
                <!-- <td>{{ $item['email'] }}</td> -->
                @if($data['requestdata']['board_id'] == '')
                <td>{{ $item['board_name'] }}</td>
                @endif
                @if($data['requestdata']['medium_id'] == '')
                <td>{{ $item['medium_name'] }}</td>
                @endif
                @if($data['requestdata']['standard_id'] == '')
                <td>{{ $item['standard_name'] }}</td>
                @endif
            </tr>
            <?php $i++ ?>
            @endforeach
        </tbody>
    </table>
    </div>

</body>
</html>
