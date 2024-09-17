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

    <h2>Roles Wise Permissions</h2><br>
    <h3>{{$data['institute_data']['institute_name']}}</h3>
    <hr>
    
    <div class="content">
    <table>
    <thead>
        <tr>
        @if($data['requestdata']['name'] == '')
           <th>Role</th>
        @endif
           <th>Feacher</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['roleandpermission'] as $roleandpermission)
        <tr>
            <td>{{$roleandpermission['role_name'] }}</td>
            @foreach($roleandpermission['permissions'] as $permissions)
            <td>{{$permissions['feature_name'] }}</td>
            <td>{{$permissions['action'] }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
<br>
</div>

</body>
</html>
