<h2>Student Progress Report</h2>
<hr>

@if (!empty($data['institute']) && is_array($data['institute']))
    <p><b>Institute Name: </b>{{ $data['institute']['institute_name'] }}</p>
    <p><b>Address: </b>{{ $data['institute']['address'] }}</p>
@endif

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
                                            <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td><b>Board Name</b></td>
                                                    <td>{{ $item['board_name'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Medium Name</b></td>
                                                    <td>{{ $mediumDT['medium_name'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Class Name</b></td>
                                                    <td>{{ $classDT['class_name'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Standard Name</b></td>
                                                    <td>{{ $standardDT['standard_name'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Batch Name</b></td>
                                                    <td>{{ $batchDT['batch_name'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Student Name</b></td>
                                                    <td>{{ $studentDT['student_name'] }}</td>
                                                </tr>
                                                <tr style="border: none;">
                                                    <td>
                                                        @php
                                                            $imagePath = public_path('student_report_graph/student_image_report_' . $board_index . $medium_index . $class_index . $standard_index . $batch_index . $studentIndex . '.png');
                                                            $imageData = file_exists($imagePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath)) : '';
                                                        @endphp

                                                        @if ($imageData)
                                                            <img src="{{ $imageData }}" alt="Student Image" style="width: 350px; height: 260px;border: none;">
                                                        @else
                                                            No image available
                                                        @endif
                                                    </td>
                                                    <td colspan="2">
                                                        @php
                                                            $imagePath2 = public_path('student_report_graph/student_attendance_report_' . $board_index . $medium_index . $class_index . $standard_index . $batch_index . $studentIndex . '.png');
                                                            $imageData2 = file_exists($imagePath2) ? 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath2)) : '';
                                                        @endphp

                                                        @if ($imageData2)
                                                            <img src="{{ $imageData2 }}" alt="Student Image" style="width: 350px; height: 260px;border: none;">
                                                        @else
                                                            No image available
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                            <table>
                                                <tr><th>student_name</th><th>Total_Fees</th><th>Discount</th><th>paid_amount</th></tr>
                                            </table>
                                            

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
