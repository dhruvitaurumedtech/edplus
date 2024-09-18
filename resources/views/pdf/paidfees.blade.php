<!DOCTYPE html>
<html>
<head>
    <title>Fees Report</title>
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

    <h2>Fees Report</h2>
    <hr>

    @foreach ($data as $item)
    <p><b>Board Name: </b>{{$item['board_name']}}</p>
        @foreach ($item['medium'] as $mediumDT)
        <p><b>Medium Name: </b>{{$mediumDT['medium_name']}}</p>
            @foreach ($mediumDT['class'] as $classDT)
            <p><b>Class Name: </b>{{$classDT['class_name']}}</p>
                @foreach ($classDT['standard'] as $standardDT)
                <p><b>Standard Name: </b>{{$standardDT['standard_name']}}</p>
                    @foreach ($standardDT['batch'] as $batchDT)
                    <p><b>Batch Name: </b>{{$batchDT['batch_name']}}</p>
                       <table>
                          <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Total Fees</th>
                            <th>Due Fees</th>
                            <th>paid Fees</th>
                            <th>Mobile</th>
                          </tr>
                          @php $i=1 @endphp
                          @foreach ($batchDT['students'] as $studentDT)
                              <tr>
                                <td>{{$i}}</td>
                                <td>{{$studentDT['student_name']}}</td>
                                <td>@if($studentDT['status'] === 'paid')
                                            <button class="btn btn-success">Paid</button>
                                        @elseif($studentDT['status'] === 'pending')
                                            <button class="btn btn-warning">Pending</button>
                                        @endif</td>
                                <td>{{$studentDT['total_fees']}}</td>
                                <td>{{$studentDT['due_amount']}}</td>
                                <td>{{$studentDT['paid_amount']}}</td>
                                <td>{{$studentDT['mobile']}}</td>

                              </tr>
                              @php $i++ @endphp
                          @endforeach 

                       </table>
                           
                       
                    @endforeach 
                @endforeach 
            @endforeach
        @endforeach
    @endforeach

    
    
</body>
</html>