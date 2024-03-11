@include('layouts/header')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Institute</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Institute</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    @include('alert')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Create Institute</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ url('institute/register') }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Name of Institute : </label>
                                            <input type="text" name="institute_name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name of Institute">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Email address : </label>
                                            <input type="email" name="email" class="form-control" placeholder="Email address">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Contact No : </label>
                                            <input type="text" name="contact_no" class="form-control" placeholder="Contact_no">
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Address : </label>
                                            <textarea name="address" class="form-control" placeholder="Address"></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Institute : </label>
                                            @foreach($institute_for_array as $index => $value)
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="school_{{ $index }}" name="institute_for_id[]" value="{{ $value->institute_for_id }}">
                                                <label for="school_{{ $index }}" class="custom-control-label">{{ $value->institute_for_name }}</label>
                                            </div>
                                            @endforeach


                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Board : </label>
                                            @foreach($board_array as $index => $value)
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="board_{{ $index }}" name="institute_board_id[]" value="{{ $value->id }}">
                                                <label for="board_{{ $index }}" class="custom-control-label">{{ $value->board_name }}</label>
                                            </div>
                                            @endforeach

                                            <!-- <div id="otherTextboxboard" style="display: none;">
                                                <label for="otherText">Board Name:</label>
                                                <input type="text" id="otherText" placeholder="Board name" name="boardother" class="form-control">
                                            </div> -->
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Medium: </label>
                                            @foreach($medium_array as $index => $value)

                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="medium_{{ $index }}" name="institute_medium_id[]" value="{{$value['medium_id']}}">
                                                <label for="medium_{{ $index }}" class="custom-control-label">{{ $value['medium_name'] }}</label>
                                            </div>
                                            @endforeach

                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Class: </label>
                                            @foreach($class_array as $index => $value)

                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="class_{{ $index }}" name="institute_for_class_id[]" value="{{$value['class_id']}}">
                                                <label for="class_{{ $index }}" class="custom-control-label">{{ $value['class_name'] }}</label>
                                            </div>
                                            @endforeach

                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Standard: </label>
                                            @foreach($standard_array as $index => $value)

                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="standard{{ $index }}" name="standard_id[]" value="{{$value['standard_id']}}">
                                                <label for="standard{{ $index }}" class="custom-control-label">{{ $value['standard_name'] }}</label>
                                            </div>
                                            @endforeach

                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Subject: </label>
                                            @php
                                            $uniqueSubjects = [];
                                            @endphp

                                            @foreach($subject_array as $subject)
                                            
                                            @php
                                            $subjectNames = explode(',', $subject['subject_name']);
                                            @endphp

                                            @foreach($subjectNames as $subjectName)
                                            @php
                                            $trimmedSubjectName = trim($subjectName);
                                            @endphp

                                            @if (!in_array($trimmedSubjectName, $uniqueSubjects))
                                            @php
                                            $uniqueSubjects[] = $trimmedSubjectName;
                                            @endphp
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="subject_{{ $loop->parent->index }}_{{ $loop->index }}" name="subject_id[]" value="{{ $subject['base_table_id'] }}">
                                                <label for="subject_{{ $loop->parent->index }}_{{ $loop->index }}" class="custom-control-label">{{ $trimmedSubjectName }}</label>
                                            </div>
                                            @endif
                                            @endforeach
                                            @endforeach



                                        </div>
                                        <div class="col-md-4">
                                            <?php if (!empty($stream_array)) { ?>
                                                <label for="exampleInputEmail1">Stream: </label>
                                            <?php } ?>
                                            @foreach($stream_array as $index => $value)
                                            <!-- $subject = explode(',',$value['subject_name']); -->

                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="stream_{{ $index }}" name="stream[]" value="{{$value['stream_id']}}">
                                                <label for="stream_{{ $index }}" class="custom-control-label">{{ $value['stream_name'] }}</label>
                                            </div>
                                            @endforeach

                                        </div>
                                        <div class="col-md-4">
                                            <?php if (!empty($do_business_with)) { ?>
                                                <label for="exampleInputEmail1">Do Business Work: </label>
                                            <?php } ?>
                                            @foreach($do_business_with as $index => $value)
                                            <!-- $subject = explode(',',$value['subject_name']); -->

                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="stream_{{ $index }}" name="institute_work_id[]" value="{{$value['id']}}">
                                                <label for="stream_{{ $index }}" class="custom-control-label">{{ $value['name'] }}</label>
                                            </div>
                                            @endforeach

                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </section>
</div>

@include('layouts/footer')