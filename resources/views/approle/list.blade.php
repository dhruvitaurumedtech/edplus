<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include necessary libraries and styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="dashboard">
        @include('layouts.header-sidebar')
        <div class="dashboard-app">
            @include('layouts.header-topbar')
            <div class="link-dir">
                <h1 class="display-4">Role</h1>
                <ul>
                    <li><a href="{{ url('dashboard') }}">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">ACL</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)" class="active-link-dir">Role</a></li>
                </ul>
            </div>
            @include('layouts.alert')
            <div class="dashboard-content side-content">

                <div class="row">

                    <div class="col-lg-6 mt-3">
                        <div class="institute-form">
                            <form id="createRoleForm" method="post">
                                @csrf
                                <h4 class="mb-3">Create Role</h4>

                                <div class="create-admin mt-4">
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <label>Role Name</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="role_name" class="form-control" placeholder="Enter Role Name" value="{{old('role_name')}}">
                                            <div class="invalid-feedback" id="role_name_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-btn">
                                    <button type="button" id="createRoleButton" class="btn bg-primary-btn text-white">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6  mt-3">
                        <div class="institute-form">
                            <div class="create-title-btn ">
                                <h4 class="mb-0">List of Roles</h4>
                                <div class="inner-list-search">
                                    <input type="search" class="form-control myInput" name="search" placeholder="Search">
                                </div>
                            </div>
                            <table class="table table-bordered institute-table mt-4 table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Role Name</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="myTable">
                                    @php $i=1 @endphp
                                    @foreach($roles as $value)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$value->role_name}}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button class="btn text-white btn-rmv2 role_editButton" data-role-id="{{ $value->id }}">Edit</button>&nbsp;&nbsp;
                                                <button class="btn btn-danger role_deleteButton" data-role-id="{{ $value->id }}">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    @php $i++ @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        {!! $roles->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>

            </div>
        </div>
        @include('layouts.footer_new')
        <!-- Edit Role Modal -->
        <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editRoleForm" method="post">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="editRoleName">Role Name:</label>
                                    <input type="hidden" id="editRoleId" name="id">
                                    <input type="text" name="role_name" class="form-control" id="editRoleName" placeholder="Edit Role">
                                    <div class="invalid-feedback" id="edit_role_name_error"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="">
                                <button type="button" id="updateRoleButton" class="btn bg-primary-btn text-white" style="float:right">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            console.log('Document ready');

            // Create Role
            $('#createRoleButton').on('click', function() {
                console.log('Create button clicked');
                var formData = $('#createRoleForm').serialize();
                $.ajax({
                    url: '{{ route("app_role.create") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response);
                        alert(response.success);
                        location.reload();
                    },
                    error: function(response) {
                        console.log(response);
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            if (errors.role_name) {
                                $('#role_name_error').text(errors.role_name[0]);
                                $('input[name="role_name"]').addClass('is-invalid');
                            } else {
                                $('#role_name_error').text('');
                                $('input[name="role_name"]').removeClass('is-invalid');
                            }
                        }
                    }
                });
            });

            // Edit Role
            $(document).on('click', '.role_editButton', function() {
                console.log('Edit button clicked');
                var roleId = $(this).data('role-id');
                $.ajax({
                    url: '{{ route("app_role.edit") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        role_id: roleId
                    },
                    success: function(response) {
                        console.log(response);
                        $('#editRoleId').val(response.role.id);
                        $('#editRoleName').val(response.role.role_name);
                        $('#editRoleModal').modal('show');
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });

            // Update Role
            $('#updateRoleButton').on('click', function() {
                console.log('Update button clicked');
                var formData = $('#editRoleForm').serialize();
                $.ajax({
                    url: '{{ route("app_role.update") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response);
                        alert(response.success);
                        location.reload();
                    },
                    error: function(response) {
                        console.log(response);
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            if (errors.role_name) {
                                $('#edit_role_name_error').text(errors.role_name[0]);
                                $('input[name="role_name"]').addClass('is-invalid');
                            } else {
                                $('#edit_role_name_error').text('');
                                $('input[name="role_name"]').removeClass('is-invalid');
                            }
                        }
                    }
                });
            });

            // Delete Role
            $(document).on('click', '.role_deleteButton', function() {
                console.log('Delete button clicked');
                var roleId = $(this).data('role-id');
                if (confirm('Are you sure you want to delete this role?')) {
                    $.ajax({
                        url: '{{ route("app_role.delete") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            role_id: roleId
                        },
                        success: function(response) {
                            console.log(response);
                            alert(response.success);
                            location.reload();
                        },
                        error: function(response) {
                            console.log(response);
                            alert('Error: ' + response.responseJSON.message);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>