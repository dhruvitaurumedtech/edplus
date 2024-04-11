<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/bootstrap.min.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/style.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/responsive.css')}}" />

</head>

<body>

    <div class="dashboard">

        @include('layouts/header-sidebar')

        <!-- MAIN -->
        <div class="dashboard-app">

            @include('layouts/header-topbar')

            <!-- Sub MAIN -->
            <div class="link-dir">
                <h1 class="display-4">Student </h1>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">Institute</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">List Institute</a></li>
                    <li><a href="javascript:void(0)">/</a></li>

                    <li><a href="javascript:void(0)" class="active-link-dir">Student </a></li>
                </ul>
            </div>

            <script>
                window.setTimeout(function() {
                    $(".alert-success").slideUp(500, function() {
                        $(this).remove();
                    });
                }, 3000);
            </script>
            <div class="dashboard-content side-content">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif
                    </div>
                </div>
                <!-- Main content -->
                <div class="dashboard-content side-content">
                    <form method="post" class="s-chapter-form" action="{{ url('student/save') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">First Name : </label>
                                        <input type="text" name="firstname" class="form-control" placeholder="Enter First Name">
                                        @error('firstname')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">Last Name : </label>
                                        <input type="text" name="lastname" class="form-control" placeholder="Enter Last Name">
                                        @error('lastname')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">Mobile : </label>
                                        <input type="tel" name="mobile" class="form-control" placeholder="Enter Mobile No.">
                                        @error('mobile')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">EmailID : </label>
                                        <input type="email" name="email" class="form-control" placeholder="Enter Email ID">
                                        @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="exampleInputpassword">Password : </label>
                                        <input type="password" name="password" class="form-control" placeholder="Enter Password">
                                        @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">Address : </label>
                                        <textarea name="address" class="form-control" placeholder="Address"></textarea>
                                        @error('address')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">DOB : </label>
                                        <input type="date" name="dob" class="form-control" placeholder="Date Of Birth">
                                        @error('dob')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">Image : </label>
                                        <input type="file" name="image" onchange="previewFile()" class="form-control" placeholder="Image">
                                        @error('image')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                        <img src="" id="image" alt="image" class="mt-4" style="display: none; width:80px; height:80px">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">Institute For : </label>
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
                                        <label for="exampleInputEmail1">Board : </label>
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
                                        <label for="exampleInputEmail1">Student Class : </label>
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
                                        <label for="exampleInputEmail1">Standard : </label>
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
                                        <label for="exampleInputEmail1">Stream : </label>
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
                                        <label for="exampleInputEmail1">Subject : </label>
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
                                            <option value="0">Pending</option>
                                            <option value="1">Approved</option>
                                            <option value="2">Denied</option>
                                        </select>
                                        @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="submit-btn">
                            <input type="submit" value="Submit" class="btn bg-primary-btn text-white mt-4">
                        </div>
                </div>
            </div>


            </form>

        </div>

        @include('layouts/footer_new')
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

            function boardlist() {
                var institutefor_id = $('#institute_for_id').val();
                var institute_id = document.getElementById('institute_id').value;

                axios.get('/student/create-form-data', {
                        institute_id: institute_id,
                        institutefor_id: institutefor_id
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