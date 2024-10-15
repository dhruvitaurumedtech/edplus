<!DOCTYPE html>
<html>
<head>
    <title>Deadstock report PDF</title>
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
        

    </style>
</head>
<body>

    <h2>Staff List</h2><br>
    <h3>{{$data['institute_data']['institute_name']}}</h3>
    <hr>
    @if($data['requestdata']['name'])
        <p><b>Name: </b>{{$data['stafflist'][0]['firstname']}}</p>
    @endif
    <div class="content">
    <table>
    
    <thead>
        <tr>
        @if($data['requestdata']['name'] == '')
           <th>Name</th>
        @endif
        <th>Role Name</th>   
        <th>Mobile</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['stafflist'] as $stafflist)
        <tr>
            @if($data['requestdata']['name'] == '')
            <td>{{$stafflist['firstname'] .' '. $stafflist['lastname']}}</td>
            @endif    
            <td>{{$stafflist['role_name']}}</td>
            <td>{{$stafflist['mobile']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br>
</div>

</body>
</html>
