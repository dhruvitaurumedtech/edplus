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
            text-align: center;

        }
        th {
            background-color: #f2f2f2;
            white-space: nowrap;
            text-align: center;
        }
        .custom-btn {
           color: #28a745;
        }
        .custom-btn-danger {
            color: #FF0000;
        }

    </style>
</head>
<body>

    <h2>Fees Report</h2>
    <hr>

    <!-- Check if $data is not empty -->
    @if (!empty($data['board_result']) && is_array($data))
        @foreach ($data['board_result'] as $item)
            <p><b>Board Name: </b>{{$item['board_name']}}</p>

            @if (!empty($item['medium']) && is_array($item['medium']))
                @foreach ($item['medium'] as $mediumDT)
                    <p><b>Medium Name: </b>{{$mediumDT['medium_name']}}</p>

                    @if (!empty($mediumDT['class']) && is_array($mediumDT['class']))
                        @foreach ($mediumDT['class'] as $classDT)
                            <p><b>Class Name: </b>{{$classDT['class_name']}}</p>

                            @if (!empty($classDT['standard']) && is_array($classDT['standard']))
                                @foreach ($classDT['standard'] as $standardDT)
                                    <p><b>Standard Name: </b>{{$standardDT['standard_name']}}</p>

                                    @if (!empty($standardDT['batch']) && is_array($standardDT['batch']))
                                        @foreach ($standardDT['batch'] as $batchDT)
                                            <p><b>Batch Name: </b>{{$batchDT['batch_name']}}</p>

                                            
                                                <table>
                                                    <tr>
                                                        <th width="10%" >No</th>
                                                        <th>Student Name</th>
                                                        <th>Total Fees</th>
                                                        <th>Due Fees</th>
                                                        <th>Paid Fees</th>
                                                        <th>Status</th>
                                                    </tr>

                                                    @php $i = 1 @endphp
                                                    @foreach ($batchDT['student'] as $studentDT)
                                                        <tr>
                                                            <td width="10%">{{$i}}</td>
                                                            <td>{{$studentDT['student_name']}}</td>
                                                            <td>{{$studentDT['student_fees']}}</td>
                                                            <td>{{$studentDT['remaing_amount']}}</td>
                                                            <td>{{$studentDT['paid_amount']}}</td>
                                                            <td> @if($studentDT['status']=='paid')
                                                                    <span class="custom-btn">Paid</span>
                                                                @else
                                                                    <span class="custom-btn-danger">Pending</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                        <td colspan="6">
                                                            @if(!empty($studentDT['history']))
                                                           
                                                                
                                                            <table class="table" style="border: none;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>Paid Amount</th>
                                                                        <th>Date</th>
                                                                        <th>Mode</th>
                                                                        <th>Invoice No</th>
                                                                        <th>Transaction ID</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $j = 1 @endphp
                                                                    @foreach ($studentDT['history'] as $historyDT)
                                                                        <tr>
                                                                            <td>{{$j}}</td>
                                                                            <td>{{$historyDT['paid_amount']}}</td>
                                                                            <td>{{$historyDT['date']}}</td>
                                                                            <td>{{$historyDT['payment_mode']}}</td>
                                                                            <td>{{$historyDT['invoice_no']}}</td>
                                                                            <td>{{$historyDT['transaction_id']}}</td>
                                                                        </tr>
                                                                        @php $j++ @endphp
                                                                    @endforeach
                                                                </tbody>
                                                            </table>

                                                            @endif
                                                            </td>
                                                        </tr>
                                                        
                                                       @php $i++ @endphp
                                                    @endforeach
                                                </table>
                                           @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
    @else
        <p>No data available.</p>
    @endif

</body>
</html>
