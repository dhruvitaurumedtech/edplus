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

    <h2>Deadstock</h2>
    <hr>
    @if($data['requestdata']['item_name'])
        <p><b>Item Name: </b>{{$data['deastock'][0]['item_name']}}</p>
    @endif
    <div class="content">
    <table>
    
    <thead>
        <tr>
        @if($data['requestdata']['item_name'] == '')
           <th>Item</th>
        @endif
           <th>No of Item</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['deastock'] as $deastock)
        <tr>
            @if($data['requestdata']['item_name'] == '')
            <td>{{$deastock['item_name']}}</td>
            @endif    
            <td>{{$deastock['no_of_item']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br>
</div>

</body>
</html>
