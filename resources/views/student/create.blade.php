@include('layouts/header')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Student</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Student</li>
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
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Create Student</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ url('student/save') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">First Name  : </label>
                                            <input type="text" name="firstname" class="form-control" placeholder="Enter First Name">
                                            @error('firstname')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Last Name  : </label>
                                            <input type="text" name="lastname" class="form-control" placeholder="Enter Last Name">
                                            @error('lastname')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Mobile  : </label>
                                            <input type="tel" name="mobile" class="form-control" placeholder="Enter Mobile No.">
                                            @error('mobile')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">EmailID  : </label>
                                            <input type="email" name="email" class="form-control" placeholder="Enter Email ID">
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInputpassword">Password  : </label>
                                            <input type="password" name="password" class="form-control" placeholder="Enter Password">
                                            @error('password')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Address  : </label>
                                            <textarea name="address" class="form-control" placeholder="Address"></textarea>
                                            @error('address')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">DOB  : </label>
                                            <input type="date" name="dob" class="form-control" placeholder="Date Of Birth">
                                            @error('dob')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Image  : </label>
                                            <input type="file" name="image" onchange="previewFile()" class="form-control" placeholder="Image">
                                            @error('image')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <img src="" id="image"  alt="image" class="mt-4" style="display: none; width:80px; height:80px">
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Institute For  : </label>
                                            <select name="institute_for_id" id="institute_for_id" class="form-control" onchange="boardlist()">
                                            <option value="">Select Institute For</option>
                                                @foreach($institute_for as $stage)
                                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('institute_for_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <input type="hidden" name="institute_id" id="institute_id" value="{{ $institute_id }}">
                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Board  : </label>
                                            <select name="board_id" id="board_id" class="form-control">
                                            <option value="">Select Board</option>
                                                @foreach($board as $instituteboard)
                                                    <option value="{{ $instituteboard->id }}">{{ $instituteboard->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('board_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Medium : </label>
                                            <select name="medium_id" class="form-control">
                                            <option value="">Select Medium</option>
                                                @foreach($medium as $institutemedium)
                                                    <option value="{{ $institutemedium->id }}">{{ $institutemedium->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('medium_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Student Class  : </label>
                                            <select name="class_id" class="form-control">
                                            <option value="">Select Class</option>
                                                @foreach($class as $instituteclass)
                                                    <option value="{{ $instituteclass->id }}">{{ $instituteclass->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('class_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Standard  : </label>
                                            <select name="standard_id" class="form-control">
                                            <option value="">Select Standard</option>
                                                @foreach($standard as $institutestandard)
                                                    <option value="{{ $institutestandard->id }}">{{ $institutestandard->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('standard_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Stream  : </label>
                                            <select name="stream_id" class="form-control">
                                                <option value="">Select Stream</option>
                                                @foreach($stream as $institutestream)
                                                    <option value="{{ $institutestream->id }}">{{ $institutestream->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('stream_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Subject  : </label>
                                            <select name="subject_id" class="form-control">
                                            <option value="">Select Subject</option>
                                                @foreach($subject as $institutesubject)
                                                    <option value="{{ $institutesubject->id }}">{{ $institutesubject->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('subject_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                       
                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">status : </label>
                                            <select class="form-control" name="status">
                                                 <option value="">Select option</option>

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
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success" style="float: right;">Submit</button>
                            </div>
                    </div>
                </div>


                </form>
            </div>

        </div>

</div>

</div>
</section>
</div>
<script>
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

        function boardlist(){
            var institutefor_id = $('#institute_for_id').val();
            var institute_id = document.getElementById('institute_id').value;
            
            axios.get('/student/create-form-data', {
                institute_id: institute_id,
                institutefor_id:institutefor_id
                })
            .then(response => {
                
                response.forEach(boards => {
                    // Your loop body
                    alert(response.boards.name);
                    $('#board_id').val(response.name); // This line will execute in each iteration of the loop
                });
                
            })
            .catch(error => {
            console.error(error);
            });  
        }
</script>
@include('layouts/footer')