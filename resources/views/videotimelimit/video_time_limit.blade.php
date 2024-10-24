<body>
    <div class="dashboard">
        @include('layouts/header-sidebar')
        <div class="dashboard-app">
            @include('layouts/header-topbar')
            <div class="link-dir">
                <h1 class="display-4">Video Time Limit</h1>
                <ul>
                    <li><a href="{{url('dashboard')}}">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)" class="active-link-dir">Video time limit</a></li>
                </ul>
            </div>
            @include('layouts/alert')
            <div class="dashboard-content side-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="institute-form">
                            <h3 class="card-title">Video Time Limit List</h3>
                            <form method="post" action="{{ url('video-timelimit-save') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12 checbox-dropdown">
                                                <label for="exampleInputEmail1">Institute Name : </label>
                                                <div class="dropdown" data-control="checkbox-dropdown">
                                                    <label class="dropdown-label">Select</label>
                                                    <div class="dropdown-list">
                                                        <a href="#" data-toggle="check-all" class="dropdown-option">
                                                            Check All
                                                        </a>
                                                        @foreach($institute_list as $value)
                                                        <label class="dropdown-option">
                                                            <input type="checkbox" name="institute_id[]" value="{{$value['id']}}" {{ in_array($value['id'], old('institute_id', [])) ? 'checked' : '' }} />
                                                            {{$value['institute_name']}}
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                    @error('institute_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12 checbox-dropdown">
                                                <label for="exampleInputEmail1">Teacher Name : </label>
                                                <div class="dropdown" data-control="checkbox-dropdown">
                                                    <label class="dropdown-label">Select</label>
                                                    <div class="dropdown-list">
                                                        <a href="#" data-toggle="check-all" class="dropdown-option">
                                                            Check All
                                                        </a>
                                                        @foreach($teachers as $value)
                                                        <label class="dropdown-option">
                                                            <input type="checkbox" name="teacher_id[]" value="{{$value['id']}}" {{ in_array($value['id'], old('teacher_id', [])) ? 'checked' : '' }} />
                                                            {{$value['firstname']}}
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                    @error('teacher_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="exampleInputEmail1">Time Limit (Hour) : </label>
                                                <input type="text" name="time" class="form-control" value="{{old('time')}}">
                                                @error('time')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 submit-btn">
                                    <button type="submit" class="btn text-white blue-button">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="institute-form">
                            <h3 class="card-title">Video Time Limit List</h3>
                            <form action="#">
                                <div class="search-box">
                                    <input type="search" class="form-control myInput" name="search" placeholder="Search">
                                    <i class="fas fa-search"></i>
                                </div>
                            </form>
                            <table class="table table-js table-bordered table-responsive mt-4">
                                <thead>
                                    <tr>
                                        <th style="width: 200px">Time</th>
                                        <th style="width: 200px">Institute</th>
                                        <th style="width: 500px">Teacher</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($response as $values)
                                    <tr>
                                        <td>{{$values['time']}}</td>
                                        <td>
                                            @foreach($values['institute_show'] as $institute)
                                            {{$institute['institute_name']}}
                                            @if (!$loop->last)
                                            <br>
                                            @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($values['teacher_show'] as $teacher)
                                            {{$teacher['firstname']}}
                                            @if (!$loop->last)
                                            <br>
                                            @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <input type="submit" class="btn text-white blue-button video_limit_editButton" data-user-id="{{ $values['id'] }}" value="Edit">&nbsp;&nbsp;
                                                &nbsp;&nbsp;
                                                <input type="submit" class="btn btn-danger video_limit_deletebutton" data-user-id="{{ $values['id'] }}" value="Delete">
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="usereditModalLabel">Edit Time </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ url('video-timelimit-update') }}" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="id">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="row">

                                            <div class="col-md-12 checbox-dropdown">
                                                <label for="exampleInputEmail1">Institute Name : </label>

                                                <div class="dropdown" data-control="checkbox-dropdown">
                                                    <label class="dropdown-label">Select</label>

                                                    <div class="dropdown-list">
                                                        <a href="#" data-toggle="check-all" class="dropdown-option">
                                                            Check All
                                                        </a>
                                                        @foreach($institute_list as $value)
                                                        <label class="dropdown-option">
                                                            <input type="checkbox" name="institute_id[]" id="institute_id" value="{{$value['id']}}" />
                                                            {{$value['institute_name']}}
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                    @error('institute_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12 checbox-dropdown">
                                                <label for="exampleInputEmail1">Teacher Name : </label>
                                                <div class="dropdown" data-control="checkbox-dropdown">
                                                    <label class="dropdown-label">Select</label>
                                                    <div class="dropdown-list">
                                                        <a href="#" data-toggle="check-all" class="dropdown-option">
                                                            Check All
                                                        </a>
                                                        @foreach($teachers as $value)
                                                        <label class="dropdown-option">
                                                            <input type="checkbox" name="teacher_id[]" id="teacher_id" value="{{$value['id']}}" />
                                                            {{$value['firstname']}}
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                    @error('teacher_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="exampleInputEmail1">Time Limit (Hour) : </label>
                                                <input type="text" name="time" id="time" class="form-control">
                                                @error('time')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 submit-btn">
                                    <button type="submit" class="btn text-white blue-button">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts/footer_new')