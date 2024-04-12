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
                <h1 class="display-4">Announcement List</h1>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">Institute</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="{{url('class-list')}}" class="active-link-dir">class</a></li>
                </ul>
            </div>

            <script>
                window.setTimeout(function() {
                    $(".alert-success").slideUp(500, function() {
                        $(this).remove();
                    });
                }, 3000);
            </script>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="dashboard-content side-content">

                <!-- /.card-header -->
                <!-- form start -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="institute-form">
                            <form method="post" action="{{ url('announcement/save') }}" enctype="multipart/form-data">
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
                                                            <input type="checkbox" name="institute_id[]" value="{{$value['id']}}" />
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
                                                            <input type="checkbox" name="teacher_id[]" value="{{$value['id']}}" />
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
                                                <label for="exampleInputEmail1">Announcement : </label>
                                                <textarea class="form-control" name="announcement"></textarea>
                                                @error('announcement')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>


                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-12 submit-btn">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="institute-form">
                            <h3 class="card-title">Announcement List</h3>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table class="table table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <th style="width: 200px">Announcement </th>
                                            <th style="width: 200px">Institute</th>
                                            <th style="width: 500px">Teacher</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($response as $values)
                                        <tr>
                                            <td>{{$values['announcement']}}</td>
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
                                            <td><a href="" class="btn btn-success">Edit</a><a href="" class="btn btn-danger">Delete</a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end">

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="usereditModalLabel">Edit Class </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ url('class/update') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <input type="hidden" id="class_id" name="class_id">
                                                <label for="exampleInputEmail1">Name : </label>
                                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                                                @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-9">
                                                <label for="exampleInputEmail1">Icon : </label>
                                                <input type="hidden" name="old_icon" id="old_icon">
                                                <input type="file" onchange="previewFile_update()" name="icon" id="edit_icon" class="form-control">
                                                @error('icon')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <img src="" id="icon_update" alt="Icon" class="mt-4">
                                            </div>

                                            <div class="col-md-12">
                                                <label for="exampleInputEmail1">status : </label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value=" ">Select Option</option>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                                @error('status')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" style="float: right;">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

            <script>
                document.querySelectorAll('.editButton').forEach(function(button) {
                    button.addEventListener('click', function() {
                        var class_id = this.getAttribute('data-user-id');

                        axios.post('/class-list/edit', {
                                class_id: class_id
                            })
                            .then(response => {
                                var reponse_data = response.data.class_list;
                                var iconSrc = '{{ asset('
                                ') }}' + reponse_data.icon;

                                $('#class_id').val(reponse_data.id);
                                $('#old_icon').val(reponse_data.icon);
                                $('#icon_update').attr('src', iconSrc);
                                $('#name').val(reponse_data.name);
                                $('#status').val(reponse_data.status);
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

                        var class_id = this.getAttribute('data-user-id');

                        // Show SweetAlert confirmation
                        Swal.fire({
                            title: 'Are you sure want to delete?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                axios.post('/class/delete', {
                                        class_id: class_id
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
            </script>

            <script>

            </script>
            @include('layouts/footer_new')
            <style>

            </style>