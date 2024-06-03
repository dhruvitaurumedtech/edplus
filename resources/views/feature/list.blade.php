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
                <h1 class="display-4">Feature</h1>
                <ul>
                    <li><a href="{{ url('dashboard') }}">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">ACL</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)" class="active-link-dir">Feature</a></li>
                </ul>
            </div>
            @include('layouts.alert')
            <div class="dashboard-content side-content">
                <div class="row">
                    <div class="col-lg-6 mt-3">
                        <div class="institute-form">
                            <form id="createFeatureForm" method="post">
                                @csrf
                                <h4 class="mb-3">Create Feature</h4>
                                <div class="create-admin mt-4">
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <label>Feature Name</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="feature_name" class="form-control" placeholder="Enter Feature Name">
                                            <div class="invalid-feedback" id="feature_name_error"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <label>Module</label>
                                        </div>
                                        <div class="col-md-9">
                                            <select name="module_id" class="form-control">
                                                @foreach($modules as $module)
                                                <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="module_id_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-btn">
                                    <button type="button" id="createFeatureButton" class="btn bg-primary-btn text-white">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-3">
                        <div class="institute-form">
                            <div class="create-title-btn">
                                <h4 class="mb-0">List of Features</h4>
                                <div class="inner-list-search">
                                    <input type="search" class="form-control myInput" name="search" placeholder="Search">
                                </div>
                            </div>
                            <table class="table table-bordered institute-table mt-4 table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Feature Name</th>
                                        <th scope="col">Module Name</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="featureTableBody">
                                    @foreach($features as $index => $feature)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $feature->feature_name }}</td>
                                        <td>{{ optional($feature->module)->module_name }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button class="btn text-white btn-rmv2 feature_editButton" data-feature-id="{{ $feature->id }}">Edit</button>&nbsp;&nbsp;
                                                <button class="btn btn-danger feature_deleteButton" data-feature-id="{{ $feature->id }}">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        {!! $features->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer_new')

        <!-- Edit Feature Modal -->
        <div class="modal fade" id="editFeatureModal" tabindex="-1" aria-labelledby="editFeatureModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFeatureModalLabel">Edit Feature</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editFeatureForm" method="post">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="editFeatureName">Feature Name:</label>
                                    <input type="hidden" id="editFeatureId" name="id">
                                    <input type="text" name="feature_name" class="form-control" id="editFeatureName" placeholder="Edit Feature Name">
                                    <div class="invalid-feedback" id="edit_feature_name_error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="editModuleId">Module:</label>
                                    <select name="module_id" class="form-control" id="editModuleId">
                                        @foreach($modules as $module)
                                        <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="edit_module_id_error"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="">
                                <button type="button" id="updateFeatureButton" class="btn bg-primary-btn text-white" style="float:right">Update</button>
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

            // Create Feature
            $('#createFeatureButton').on('click', function() {
                var formData = $('#createFeatureForm').serialize();
                $.ajax({
                    url: '{{ route("feature.create") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response);
                        location.reload();
                    },
                    error: function(response) {
                        console.log(response);
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            if (errors.feature_name) {
                                $('#feature_name_error').text(errors.feature_name[0]);
                                $('input[name="feature_name"]').addClass('is-invalid');
                            } else {
                                $('#feature_name_error').text('');
                                $('input[name="feature_name"]').removeClass('is-invalid');
                            }
                            if (errors.module_id) {
                                $('#module_id_error').text(errors.module_id[0]);
                                $('select[name="module_id"]').addClass('is-invalid');
                            } else {
                                $('#module_id_error').text('');
                                $('select[name="module_id"]').removeClass('is-invalid');
                            }
                        }
                    }
                });
            });

            // Edit Feature
            $(document).on('click', '.feature_editButton', function() {
                var featureId = $(this).data('feature-id');
                $.ajax({
                    url: '{{ route("feature.edit") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        feature_id: featureId
                    },
                    success: function(response) {
                        console.log(response);
                        $('#editFeatureId').val(response.feature.id);
                        $('#editFeatureName').val(response.feature.feature_name);
                        $('#editModuleId').val(response.feature.module_id);
                        $('#editFeatureModal').modal('show');
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });

            // Update Feature
            $('#updateFeatureButton').on('click', function() {
                var formData = $('#editFeatureForm').serialize();
                $.ajax({
                    url: '{{ route("feature.update") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response);
                        location.reload();
                    },
                    error: function(response) {
                        console.log(response);
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            if (errors.feature_name) {
                                $('#edit_feature_name_error').text(errors.feature_name[0]);
                                $('input[name="feature_name"]').addClass('is-invalid');
                            } else {
                                $('#edit_feature_name_error').text('');
                                $('input[name="feature_name"]').removeClass('is-invalid');
                            }
                            if (errors.module_id) {
                                $('#edit_module_id_error').text(errors.module_id[0]);
                                $('select[name="module_id"]').addClass('is-invalid');
                            } else {
                                $('#edit_module_id_error').text('');
                                $('select[name="module_id"]').removeClass('is-invalid');
                            }
                        }
                    }
                });
            });

            // Delete Feature
            $(document).on('click', '.feature_deleteButton', function() {
                var featureId = $(this).data('feature-id');
                if (confirm('Are you sure you want to delete this feature?')) {
                    $.ajax({
                        url: '{{ route("feature.delete") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            feature_id: featureId
                        },
                        success: function(response) {
                            console.log(response);
                            location.reload();
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>