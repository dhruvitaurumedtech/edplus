<style>
     .page-break {
            page-break-before: always;
        }
       
</style>


@if (!empty($data['board_result']) && is_array($data['board_result']))
    @foreach ($data['board_result'] as $board_index => $item)
        @if (!empty($item['medium']) && is_array($item['medium']))
            @foreach ($item['medium'] as $medium_index => $mediumDT)
                @if (!empty($mediumDT['class']) && is_array($mediumDT['class']))
                    @foreach ($mediumDT['class'] as $class_index => $classDT)
                        @if (!empty($classDT['standard']) && is_array($classDT['standard']))
                            @foreach ($classDT['standard'] as $standard_index => $standardDT)
                                @if (!empty($standardDT['batch']) && is_array($standardDT['batch']))
                                    @foreach ($standardDT['batch'] as $batch_index => $batchDT)
                                        @foreach ($batchDT['student'] as $studentIndex => $studentDT)
                                            
                                            <!-- Table for displaying data in two columns -->
                                            <h1>Student Progress Report</h1>
                                            <hr>

                                            @if (!empty($data['institute']) && is_array($data['institute']))
                                                <p><b>Institute Name: </b>{{ $data['institute']['institute_name'] }}</p>
                                                <p><b>Address: </b>{{ $data['institute']['address'] }}</p>
                                                <p><b>Student Name:</b>{{ $studentDT['student_name'] }}</p>
                                                <p>{{ $item['board_name'] }}-{{ $standardDT['standard_name'] }}-{{ $batchDT['batch_name'] }}</p> 
                                                @endif
                                          
                                                   
                                                        @php
                                                            $imagePath = public_path('student_report_graph/student_image_report_' . $board_index . $medium_index . $class_index . $standard_index . $batch_index . $studentIndex . '.png');
                                                            $imageData = file_exists($imagePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath)) : '';
                                                        @endphp

                                                        @if ($imageData)
                                                            <img src="{{ $imageData }}" alt="Student Image" style="width: 350px; height: 260px;border: none;">
                                                        @else
                                                            No image available
                                                        @endif
                                                        @php
                                                            $imagePath2 = public_path('student_report_graph/student_attendance_report_' . $board_index . $medium_index . $class_index . $standard_index . $batch_index . $studentIndex . '.png');
                                                            $imageData2 = file_exists($imagePath2) ? 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath2)) : '';
                                                        @endphp

                                                        @if ($imageData2)
                                                            <img src="{{ $imageData2 }}" alt="Student Image" style="width: 350px; height: 260px;border: none;">
                                                        @else
                                                            No image available
                                                        @endif
                                            <h2>Student Fees Detail</h2>
                                            <table border="1" style="border-collapse: collapse; width: 100%; font-family: 'Times New Roman', Times, serif;">

                                                    <tr>
                                                        <th width="10%" >No</th>
                                                        <th>Student Name</th>
                                                        <th>Total Fees</th>
                                                        <th>Due Fees</th>
                                                        <th>Paid Fees</th>
                                                        <th>Status</th>
                                                    </tr>

                                                    @php $i = 1 @endphp
                                                    @foreach ($studentDT['fees_response'] as $FeesDT)
                                                        <tr>
                                                            <td width="10%" style="text-align: center;">{{$i}}</td>
                                                            <td style="text-align: center;">{{$FeesDT['student_name']}}</td>
                                                            <td style="text-align: center;">{{$FeesDT['student_fees']}}</td>
                                                            <td style="text-align: center;">{{$FeesDT['remaing_amount']}}</td>
                                                            <td style="text-align: center;">{{$FeesDT['paid_amount']}}</td>
                                                            <td style="text-align: center;"> @if($FeesDT['status']=='paid')
                                                                    <span class="custom-btn">Paid</span>
                                                                @else
                                                                    <span class="custom-btn-danger">Pending</span>
                                                                @endif
                                                            </td>
                                                        </tr>

                                                        
                                                       @php $i++ @endphp
                                                    @endforeach
                                                </table>
                                                <div class="page-break"></div>

                                        @endforeach

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
