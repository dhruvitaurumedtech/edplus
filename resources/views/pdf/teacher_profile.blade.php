<!DOCTYPE html>
<html>
<head>
    <title>Teacher Profile PDF</title>
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

    <h2>{{$data['firstname']}} Profile</h2>
    <hr>
    <div class="content">
    <table>
    
    <thead>
        <tr>
            <th>Image</th>
            <td><img src="{{$data['image']}}"></td>
        </tr>
        <tr>
            <th>Name</th>
            <td>{{$data['firstname'] .' '.$data['lastname']}}</td>
        </tr>            
        <tr>
            <th>email</th>
            <td>{{$data['email']}}</td>
        </tr>    
        <tr>
            <th>mobile</th>
            <td>{{$data['mobile']}}</td>
        </tr>    
        <tr>
            <th>DOB</th>
            <td>{{$data['dob']}}</td>
        </tr>    
        <tr>
            <th>Address</th>
            <td>{{$data['address']}}</td>
        </tr>
        <tr>
            <th>Country</th>
            <td>{{$data['country']}}</td>
        </tr> 
        <tr>
            <th>State</th>
            <td>{{$data['state']}}</td>
        </tr>    
        <tr>
            <th>City</th>
            <td>{{$data['city']}}</td>
        </tr>
        <tr>
            <th>Pincode</th>
            <td>{{$data['pincode']}}</td>
        </tr>
        <tr>
            <th>About as</th>
            <td>{{$data['about_us']}}</td>
        </tr>
    </thead>
    
</table>
<br>
<table>
    <thead>
    <tr>
    <th>Education</th>
    </tr>
    </thead>
    <tbody>
        @foreach($data['education'] as $education)
        <tr>
            <td>{{$education['qualification']}}</td>
        </tr>
        @endforeach
    </tbody>
    </table>

    <br>
    <h1>Experience</h1>
<table>
    <thead>
    <tr>
    <th>Experience</th>
    </tr>
    </thead>
    <tbody>
        @foreach($data['experience'] as $experience)
        <tr>
            <td>{{$experience['experience']}}</td>
        </tr>
        @endforeach
    </tbody>
    </table>

    <br>
    <h1>Emergency Contacts</h1>    
<table>
    <thead>
    <tr>
    <th>Name</th>
    <th>Relation with</th>
    <th>Mobile</th>
    </tr>
    </thead>
    <tbody>
        @foreach($data['emergency_contacts'] as $emergencycontacts)
        <tr>
            <td>{{$emergencycontacts['name']}}</td>
            <td>{{$emergencycontacts['relation_with']}}</td>
            <td>{{$emergencycontacts['country_code'].' '.$emergencycontacts['mobile_no']}}</td>
        </tr>
        @endforeach
    </tbody>
    </table>

</div>

</body>
</html>
