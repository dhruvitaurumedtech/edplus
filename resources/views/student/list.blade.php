</head>
<meta name="base-url" content="{{ url('/') }}">

<body>

  <div class="dashboard">

    @include('layouts/header-sidebar')

    <!-- MAIN -->
    <div class="dashboard-app">

      @include('layouts/header-topbar')

      <!-- Sub MAIN -->
      <div class="link-dir">
        <h1 class="display-4">Student list</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Institute</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">List Institute</a></li>
          <li><a href="javascript:void(0)">/</a></li>

          <li><a href="javascript:void(0)" class="active-link-dir">Student list</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content">
        <div class="institute-form">
          <div class="create-title-btn">
            <h4 class="mb-0">List of Student</h4>
            <div class="inner-list-search">
              <input type="search" class="form-control myInput" name="search" placeholder="Search">
              <a href="{{url('student/create/'.$institute_id)}}" class="btn btn-success btn-rmv2" style="float: right;">Create Student</a>


            </div>

            <table class="table table-js table-bordered institute-table mt-4">
              <thead>
                <tr>
                  <th style="width: 10px">
                    <Sr class="No">No</Sr>
                  </th>
                  <th style="width: 400px">Name</th>
                  <th style="width: 400px">Email</th>
                  <th style="width: 400px">Mobile</th>
                  <th style="width: 400px">Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody class="myTable">
                @php $i=1 @endphp
                @foreach($student as $value)
                <tr>
                  <td>{{$i}}</td>
                  <td>{{$value->firstname .' '.$value->lastname}}</td>
                  <td>{{$value->email}}</td>
                  <td>{{$value->mobile}}</td>
                  <td>{{$value->status}}</td>
                  <td>
                    <div class="d-flex">
                      <input type="submit" class="btn text-white btn-rmv2 student_editButton" data-student-id="{{ $value->id }}" data-institute-id="{{ $institute_id }}" value="Edit">&nbsp;&nbsp;
                      &nbsp;&nbsp;
                      <form method="post" action="{{url('/student/view')}}">
                        @csrf
                        <input type="submit" class="btn btn-warning student_viewButton" value="View">
                        <input type="hidden" id="institute_id" name="institute_id" value="{{ $institute_id }}">
                        <input type="hidden" name="student_id" value="{{ $value->id }}">
                      </form>&nbsp;&nbsp;
                      &nbsp;&nbsp;
                      <input type="submit" class="btn btn-danger student_deletebutton" data-student-id="{{ $value->id }}" value="Delete">

                    </div>
                </tr>
                @php $i++ @endphp
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-end">
            {!! $student->withQueryString()->links('pagination::bootstrap-5') !!}

          </div>
        </div>

      </div>
      </section>

    </div>
    <div class="modal fade bd-example-modal-lg" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="usereditModalLabel">Student </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post" action="{{ url('student/update') }}">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <div class="row">

                    <input type="hidden" name="institute_id" id="inst_id">
                    <input type="hidden" name="Student_detail_id" id="Student_detail_id">
                    <input type="hidden" name="student_id" id="student_id">
                    <div class="col-md-4">
                      <label for="exampleInputEmail1">First Name : </label>
                      <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Enter First Name">
                      @error('firstname')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Last Name : </label>
                      <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Enter Last Name">
                      @error('lastname')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Mobile : </label>
                      <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile No.">
                      @error('mobile')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">EmailID : </label>
                      <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email ID">
                      @error('email')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Address : </label>
                      <textarea name="address" id="address" class="form-control" placeholder="Address"></textarea>
                      @error('address')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">DOB : </label>
                      <input type="date" name="dob" id="dob" class="form-control" placeholder="Date Of Birth">
                      @error('dob')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Image : </label>
                      <input type="file" name="image" onchange="previewFile()" class="form-control" placeholder="Image">
                      <input type="hidden" name="uploded_image" id="uploded_image">

                      @error('image')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                      <img src="" id="image" alt="image" class="mt-4" style="width:150px; height:150px;display:none">
                    </div>


                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Institute For : </label>
                      <select name="institute_for_id" id="institute_for_id" class="form-control">
                        @foreach($institute_for as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                        @endforeach
                      </select>
                      @error('institute_for_id')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Board : </label>
                      <select name="board_id" id="board_id" class="form-control">
                        @foreach($board as $instituteboard)
                        <option value="{{ $instituteboard->id }}">{{ $instituteboard->name }}</option>
                        @endforeach
                      </select>
                      @error('board_id')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Medium : </label>
                      <select name="medium_id" id="medium_id" class="form-control">
                        @foreach($medium as $institutemedium)
                        <option value="{{ $institutemedium->id }}">{{ $institutemedium->name }}</option>
                        @endforeach
                      </select>
                      @error('medium_id')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Student Class : </label>
                      <select name="class_id" id="class_id" class="form-control">
                        @foreach($class as $instituteclass)
                        <option value="{{ $instituteclass->id }}">{{ $instituteclass->name }}</option>
                        @endforeach
                      </select>
                      @error('class_id')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Standard : </label>
                      <select name="standard_id" id="standard_id" class="form-control">
                        {{ print_r($standard);}}
                        @foreach($standard as $standardval)
                        <option value="{{ $standardval->id }}">{{ $standardval->name }}</option>
                        @endforeach
                      </select>
                      @error('standard_id')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Stream : </label>
                      <select name="stream_id" id="stream_id" class="form-control">
                        @foreach($stream as $institutestream)
                        <option value="{{ $institutestream->id }}">{{ $institutestream->name }}</option>
                        @endforeach
                      </select>
                      @error('stream_id')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Subject : </label>
                      <select name="subject_id[]" id="subject_id" class="form-control" multiple>
                        <option value="">Select Subject</option>
                        @foreach($subject as $institutesubject)
                        <option value="{{ $institutesubject->id }}">{{ $institutesubject->name }}</option>
                        @endforeach
                      </select>
                      @error('subject_id')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-4">
                      <label for="exampleInputEmail1">status : </label>
                      <select class="form-control" name="status" id="status">
                        <option value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">Denied</option>

                      </select>
                      @error('status')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-4">
                      <label for="exampleInputEmail1">Note : </label>
                      <input type="text" name="note" id="note" class="form-control" placeholder="Please enter note">
                    </div>
                  </div>
                </div>
              </div>
              <hr>
              <div class="">
                <button type="submit" class="btn btn-info" style="float:right">Update</button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
    <script>
      //image preview
      function previewFile() {
        $("#image").show();

        const preview = document.getElementById("image");
        const fileInput = document.querySelector("input[type=file]");
        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.addEventListener("load", () => {
          preview.src = reader.result;
        }, false);

        if (file) {
          reader.readAsDataURL(file);
        }
      }
    </script>
    <script src="{{asset('mayal_assets/js/file.js')}}"></script>

    @include('layouts/footer_new')