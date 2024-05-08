</head>

<body>
    <div class="dashboard">
        @include('layouts/header-sidebar')
        <style>
            .dropdown-list li {
                list-style-type: none;
            }
        </style>
        <div class="dashboard-app">
            @include('layouts/header-topbar')
            <div class="link-dir">
                <h1 class="display-4">Announcement List</h1>
                <ul>
                    <li><a href="{{url('dashboard')}}">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">Institute</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="{{url('class-list')}}" class="active-link-dir">class</a></li>
                </ul>
            </div>
            @include('layouts/alert')
            <div class="dashboard-content side-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="institute-form">
                            <h3 class="card-title">Create Announcement </h3>
                            <form method="post" action="{{ url('announcement/save') }}" enctype="multipart/form-data" id="usereditModal">
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
                                                            <input type="checkbox" class="instituteCheckbox" id="institute_id" name="institute_id[]" value="{{$value['id']}}" {{ in_array($value['id'], old('institute_id', [])) ? 'checked' : '' }} />
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
                                                <label for="userDropdown">User Select:</label>
                                                <div id="userDropdown" class="dropdown" data-control="checkbox-dropdown">
                                                    <label class="dropdown-label">Select</label>
                                                    <div class="dropdown-list">
                                                        <ul>
                                                        </ul>
                                                    </div>
                                                    @error('selected_users')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="exampleInputtitle">Title : </label>
                                                <input type="text" class="form-control" name="title" value="{{old('title')}}">
                                                @error('title')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12">
                                                <label for="exampleInputEmail1">Announcement : </label>
                                                <textarea class="form-control" name="announcement">{{old('announcement')}}</textarea>
                                                @error('announcement')
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
                            <h3 class="card-title">Announcement List</h3>
                            <form action="#">
                                <div class="search-box">
                                    <input type="search" class="form-control myInput" name="search" placeholder="Search">
                                    <i class="fas fa-search"></i>
                                </div>
                            </form>
                            <table class="table table-js table-bordered table-responsive mt-5">
                                <thead>
                                    <tr>
                                        <th style="width: 200px">Title </th>
                                        <th style="width: 200px">Announcement </th>
                                        <th style="width: 200px">Institute</th>
                                        <th style="width: 500px">Teacher</th>
                                        <th style="width: 500px">Parent</th>
                                        <th style="width: 500px">Student</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="myTable">

                                    @foreach($response as $values)
                                    <tr>
                                        <td>{{$values['title']}}</td>
                                        <td>{{$values['announcement']}}</td>
                                        <td style="height: 100px; overflow-x: auto;">
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
                                        @foreach($values['parent_show'] as $parent)
                                            {{$parent['firstname']}}
                                            @if (!$loop->last)
                                            <br>
                                            @endif
                                            @endforeach
                                        </td>
                                        <td>
                                        @foreach($values['student_show'] as $student)
                                            {{$student['firstname']}}
                                            @if (!$loop->last)
                                            <br>
                                            @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <!-- <input type="submit" class="btn text-white blue-button announcement_new_editButton" data-user-id="{{ $values['id'] }}" value="Edit">&nbsp;&nbsp;
                                                &nbsp;&nbsp; -->
                                                <input type="submit" class="btn btn-danger announcement_new_deletebutton" data-del-id="{{ $values['id'] }}" value="Delete">
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts/footer_new')
    </div>
  
    <script>
    document.querySelectorAll('.announcement_new_editButton').forEach(function (button) {
    button.addEventListener('click', function () {
        var anouncement_id = this.getAttribute('data-user-id');
        var baseUrl = $('meta[name="base-url"]').attr('content');


        axios.post(baseUrl + '/announcement/edit', {
            anouncement_id: anouncement_id
        })
            .then(response => {
                var response_data = response.data.announcement;
                for (let result of response_data) {

                    var institute_id = result.institute_id;
                    var teacher_id = result.teacher_id;
                    $('#anouncement_id').val(result.id);
                    $('#announcement').val(result.announcement);
                    $('#title').val(result.title);

                    const institute_id_result = institute_id.split(',');
                    for (let institute of institute_id_result) {
                        $(`#institute_id[value="${institute.trim()}"]`).prop('checked', true);
                    }
                    const teacher_id_result = teacher_id.split(',');
                    for (let teacher of teacher_id_result) {
                        $(`#teacher_id[value="${teacher.trim()}"]`).prop('checked', true);
                    }
                    // $('#institute_id').prop('checked', result.institute_id);
                    // $('#teacher_id').prop('checked', result.teacher_id);
                }
            })
            .catch(error => {
                console.error(error);
            });
    });
});
                document.addEventListener("DOMContentLoaded", function() {
                    function fetchUsers(instituteIds) {
                        $.ajax({
                            url: "{{ route('fetch.users') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                institute_ids: instituteIds
                            },
                            success: function(users) {
                                // Clear previous options
                                $("#userDropdown .dropdown-list ul").empty();

                                // Map role type IDs to labels
                                var roleLabels = {
                                    3: "Institute",
                                    4: "Teacher",
                                    5: "Parent",
                                    6: "Student"
                                };

                                // Group users by role type
                                var userGroups = {};
                                users.forEach(function(user) {
                                    if (!userGroups[user.role_type]) {
                                        userGroups[user.role_type] = [];
                                    }
                                    userGroups[user.role_type].push(user);
                                });

                                // Sort user list alphabetically by first name
                                for (var roleType in userGroups) {
                                    userGroups[roleType].sort(function(a, b) {
                                        return a.firstname.localeCompare(b.firstname);
                                    });
                                }

                                // Populate dropdown list with users grouped by role type
                                for (var roleType in userGroups) {
                                    var roleLabel = roleLabels[roleType];
                                    $("#userDropdown .dropdown-list ul").append('<li><input type="checkbox" class="role-checkbox" data-role="' + roleType + '"> <strong>' + roleLabel + '</strong></li>');
                                    userGroups[roleType].forEach(function(user) {
                                        $("#userDropdown .dropdown-list ul").append('<li class="user-checkbox" data-role="' + roleType + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="selected_users[]" value="' + user.id + '"> ' + user.firstname + '</li>');
                                    });
                                }

                                // Handle role checkbox click event
                                $(".role-checkbox").click(function() {
                                    var role = $(this).data('role');
                                    var isChecked = $(this).prop('checked');

                                    // Toggle selection of users within the role
                                    $(".user-checkbox[data-role='" + role + "'] input[type='checkbox']").prop('checked', isChecked);
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching user data:', error);
                            }
                        });
                    }



                    // Event listener for institute selection change
                    $('.instituteCheckbox').change(function() {
                        var selectedInstitutes = $('.instituteCheckbox:checked').map(function() {
                            return $(this).val();
                        }).get();
                        fetchUsers(selectedInstitutes);
                    });

                    // Event listener for Check All
                    $('[data-toggle="check-all"]').click(function(e) {
                        e.preventDefault();
                        $('.instituteCheckbox').prop('checked', true);
                        var allInstitutes = $('.instituteCheckbox:checked').map(function() {
                            return $(this).val();
                        }).get();
                        fetchUsers(allInstitutes);
                    });

                    //edit
                   

                    $('form').submit(function(event) {
                        event.preventDefault();
                        var formData = $(this).serialize();
                        $.ajax({
                            url: "{{ route('annoucement.publish') }}",
                            type: "POST",
                            data: formData,
                            success: function(response) {
                                console.log(response);
                                window.location.href = "{{ route('announcement-create-new') }}";
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                            }
                        });
                    });



                });
            </script>

            <script>
            document.querySelectorAll('.announcement_new_deletebutton').forEach(function (button) {
            button.addEventListener('click', function () {
            var anouncement_id = this.getAttribute('data-del-id');
            var baseUrl = $('meta[name="base-url"]').attr('content');


            axios.post(baseUrl + '/announcement/delete', {
                anouncement_id: anouncement_id
            })
            .then(response => {
                alert('hl');
            })
            .catch(error => {
                console.error(error);
            });
    });
});

            </script>

           