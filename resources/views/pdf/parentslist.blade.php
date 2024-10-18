<!DOCTYPE html>
<html>

<head>
    <title>Parents Report PDF</title>
    <style>
        body {
            margin: 0mm;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            word-wrap: break-word;
            table-layout: fixed;
            /* Ensure even distribution of content */
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

        /* Set specific width for each column */
        th:nth-child(1) {
            width: 40px;
        }

        /* No column */
        th:nth-child(2) {
            width: 100px;
        }

        /* Student_ID column */
        th:nth-child(3) {
            width: 150px;
        }

        /* Full Name column */
        th:nth-child(4) {
            width: 200px;
        }

        /* Email column */
        th:nth-child(5) {
            width: 100px;
        }

        /* Board column */
        th:nth-child(6) {
            width: 80px;
        }

        /* Class column */
        th:nth-child(7) {
            width: 80px;
        }

        /* Medium column */
        th:nth-child(8) {
            width: 80px;
        }

        /* Standard column */

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

    <h2>Parents List</h2>
    <hr>
    

    @if(!empty($data['parents']) && !empty($data['parents'][0]['mobile']) && !empty($data['requestdata']['mobile']))
    <p><b>Mobile: </b>{{ $data['parents'][0]['mobile'] }}</p>
    @endif

    @if(!empty($data['parents']) && !empty($data['parents'][0]['address']) && !empty($data['requestdata']['address']))
        <p><b>Address: </b>{{ $data['parents'][0]['address'] }}</p>
        @endif

    <div class="content">
        <table>
            <thead>
                <tr>
                    @if($data['fields']['id'] == 1)
                    <th>No</th>
                    @endif
                    @if($data['fields']['firstname'] == 1 || $data['fields']['lastname'] == 1)
                    <th>Parent Name</th>
                    @endif
                    @if($data['fields']['student_name'] == 1)
                    <th>Student Name</th>
                    @endif
                    <!-- <th>Email</th> -->
                    @if($data['requestdata']['address'] == '' || $data['fields']['address'] == 1)
                    <th>Address</th>
                    @endif
                    @if($data['requestdata']['mobile'] == '' || $data['fields']['mobile'] == 1)
                    <th>Mobile</th>
                    @endif
                </tr>
            </thead>
            <tbody><?php $i = 1; ?>
                @foreach ($data['parents'] as $item)
                <tr>
                    @if($data['fields']['id'] == 1)
                    <td>{{ $i }}</td>
                    @endif

                    @if($data['fields']['firstname'] == 1 || $data['fields']['lastname'] == 1)
                    <td>@if($data['fields']['firstname'] == 1) {{$item['firstname']}} @endif
                        @if($data['fields']['lastname'] == 1) $item['lastname'] @endif</td>
                    @endif

                    @if($data['fields']['student_name'] == 1)
                    <td>{{ $item['student_name'] }}</td>
                    @endif
                    <!-- <td>{{ $item['email'] }}</td> -->
                    @if($data['requestdata']['address'] == '' || $data['fields']['address'] == 1)
                    <td>{{ $item['address'] }}</td>
                    @endif
                    @if($data['requestdata']['mobile'] == '' || $data['fields']['mobile'] == 1)
                    <td>{{ $item['mobile'] }}</td>
                    @endif
                </tr>
                <?php $i++ ?>
                @endforeach
            </tbody>
        </table>
    </div>

</body>

</html>