@include('layouts/header')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Student Admin</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Student Admin</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->
  <div class="row">
    <div class="col-md-8 offset-md-2">
      @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
      @endif
    </div>
  </div>

  <script>
    window.setTimeout(function() {
      $(".alert-success").slideUp(500, function() {
        $(this).remove();
      });
    }, 3000);
  </script>
  <!-- Main content -->

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Student List</h3>
              <form method="post" action="{{url('student/create')}}">
                @csrf
              <input type="submit" value="Create Student" class="btn btn-success" style="float: right;">
              <input type="hidden" name="institute_id" id="institute_id" value="{{ $institute_id }}">
              </form>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 10px"><Sr class="No"></Sr></th>
                    <th style="width: 400px">Name</th>
                    <th style="width: 400px">Email</th>
                    <th style="width: 400px">Mobile</th>
                    <th style="width: 400px">Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
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
                      <input type="submit" class="btn btn-info editButton" data-student-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                      &nbsp;&nbsp;
                    <form method="post" action="{{url('/student/view')}}">
                      @csrf
                      <input type="submit" class="btn btn-success viewButton" value="View">
                      <input type="hidden" id="institute_id" name="institute_id" value="{{ $institute_id }}">
                      <input type="hidden" name="student_id" value="{{ $value->id }}">
                    </form>&nbsp;&nbsp;
                      &nbsp;&nbsp;
                      <input type="submit" class="btn btn-danger deletebutton" data-student-id="{{ $value->id }}" value="Delete">
                      
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
<div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
  <div class="modal-dialog">
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
                                    <input type="hidden" name="Student_detail_id" id="Student_detail_id">
                                    <input type="hidden" name="student_id" id="student_id">
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">First Name  : </label>
                                            <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Enter First Name">
                                            @error('firstname')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Last Name  : </label>
                                            <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Enter Last Name">
                                            @error('lastname')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Mobile  : </label>
                                            <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile No.">
                                            @error('mobile')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">EmailID  : </label>
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email ID">
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Address  : </label>
                                            <textarea name="address" id="address" class="form-control" placeholder="Address"></textarea>
                                            @error('address')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">DOB  : </label>
                                            <input type="date" name="dob" id="dob" class="form-control" placeholder="Date Of Birth">
                                            @error('dob')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Image  : </label>
                                            <input type="file" name="image" onchange="previewFile()" class="form-control" placeholder="Image">
                                            <input type="hidden" name="uploded_image" id="uploded_image">
                                           
                                            @error('image')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <img src="" id="image"  alt="image" class="mt-4"  style="width:150px; height:150px">
                                        </div>
                                        

                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Institute For  : </label>
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
                                            <label for="exampleInputEmail1">Board  : </label>
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
                                            <label for="exampleInputEmail1">Student Class  : </label>
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
                                            <label for="exampleInputEmail1">Stream  : </label>
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
                                            <label for="exampleInputEmail1">Subject  : </label>
                                            <select name="subject_id" id="subject_id" class="form-control">
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
                                                  <option value="pending">Pending</option>
                                                  <option value="denied">Denied</option>
                                                  <option value="approved">Approved</option>
                                            </select>
                                            @error('status')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
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
  document.querySelectorAll('.editButton').forEach(function(button) {
    button.addEventListener('click', function() {
      var student_id = this.getAttribute('data-student-id');
      var institute_id = $('#institute_id').val();
      axios.post('/student/edit', {
        student_id: student_id,
        institute_id: institute_id
        })
        .then(response => {
         
          var reponse_student = response.data.studentDT;
          var reponse_studentdetail = response.data.studentsdetailsDT;

          if (reponse_student !== null) {
          var imgsrc =   'http://127.0.0.1:8000/'+reponse_student.image;
         
          $('#student_id').val(reponse_student.id);
          $('#firstname').val(reponse_student.firstname);
          $('#lastname').val(reponse_student.lastname);
          $('#email').val(reponse_student.email);
          $('#mobile').val(reponse_student.mobile);
          $('#address').val(reponse_student.address);
          $('#dob').val(reponse_student.dob);
          $('#image').attr('src',imgsrc);
          $('#uploded_image').val(reponse_student.image);
          }
          if (reponse_studentdetail !== null) {
          $('#status').val(reponse_studentdetail.status);
          $('#Student_detail_id').val(reponse_studentdetail.id);
          $('#institute_for_id').val(reponse_studentdetail.institute_for_id);
          $('#board_id').val(reponse_studentdetail.board_id);
          $('#medium_id').val(reponse_studentdetail.medium_id);
          $('#class_id').val(reponse_studentdetail.class_id);
          $('#stream_id').val(reponse_studentdetail.stream_id);
          $('#subject_id').val(reponse_studentdetail.subject_id);
          }
          $('#usereditModal').modal('show');
        })
        .catch(error => {
          console.error(error);
        });
    });
  });
  
  document.querySelectorAll('.deletebutton').forEach(function(button) {
    button.addEventListener('click', function(event) {
      event.preventDefault(); // Prevent the default form submission

      var student_id = this.getAttribute('data-student-id');

      // Show SweetAlert confirmation
      Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('/student/delete', {
            student_id: student_id
            })
            .then(response => {
              location.reload(true);

            })
            .catch(error => {
              console.error(error);
            });
        }
      });
    });
  });
  
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
@include('layouts/footer ')