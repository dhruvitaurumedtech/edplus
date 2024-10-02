<!DOCTYPE html>
<html>
<head>
    <title>Role-wise Permissions Report PDF</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            word-wrap: break-word;
            table-layout: fixed;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
      
    </style>
</head>
<body>

    <h2>Roles Wise Permissions</h2>
    <h3>{{$data['institute_data']['institute_name']}}</h3>
    <hr>

    <div class="content">
                @foreach($data['roleandpermission'] as $roleandpermission)
                    <p><b><h1>{{$roleandpermission['role_name']}}</h1></b></p>
                            @foreach($roleandpermission['modules'] as $module)
                            <table class="nested-table">
                            <tr>
                                <td><strong>{{$module['module_name']}}</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    @foreach($module['permissions'] as $feature)
                                    <table class="nested-table">
                                    <tr>
                                        <td width="40%" >{{$feature['feature_name']}}</td>
                                        <td>
                                        @foreach($feature['actions'] as $action)
                                            <input type="checkbox" @if($action['has_permission']) checked @endif disabled>
                                            {{$action['name']}}
                                        @endforeach
                                        </td>
                                    </tr>
                                    </table>
                                    @endforeach
                                   
                                </td>
                            </tr>
                        </table>
                            @endforeach
                @endforeach
    </div>

</body>
</html>
