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
        <h1 class="display-4">Module</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">ACL</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)" class="active-link-dir">Module</a></li>
        </ul>
      </div>
      @include('layouts.alert')
      <div class="dashboard-content side-content">

        <div class="row">

          <div class="col-lg-6 mt-3">
            <div class="institute-form">
              <form id="createModuleForm" method="post">
                @csrf
                <h4 class="mb-3">Create Module</h4>

                <div class="create-admin mt-4">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label>Module Name</label>
                    </div>
                    <div class="col-md-9">
                      <input type="text" name="module_name" class="form-control" placeholder="Enter Module Name" value="{{old('module_name')}}">
                      <div class="invalid-feedback" id="module_name_error"></div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label>Type</label>
                    </div>
                    <div class="col-md-9">
                      <select name="type" class="form-control">
                        <option value="1">Admin</option>
                        <option value="2">Application</option>
                      </select>
                      <div class="invalid-feedback" id="type_error"></div>
                    </div>
                  </div>
                </div>
                <div class="submit-btn">
                  <button type="button" id="createModuleButton" class="btn bg-primary-btn text-white">Submit</button>
                </div>
              </form>
            </div>
          </div>
          <div class="col-lg-6 mt-3">
            <div class="institute-form">
              <div class="create-title-btn ">
                <h4 class="mb-0">List of Modules</h4>
                <div class="inner-list-search">
                  <input type="search" class="form-control myInput" name="search" placeholder="Search">
                </div>
              </div>
              <table class="table table-bordered institute-table mt-4 table-responsive-sm">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Module Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody class="myTable">
                  @php $i=1 @endphp
                  @foreach($modules as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>{{$value->module_name}}</td>
                    <td>{{$value->type == 1 ? 'Admin' : 'Application'}}</td>
                    <td>
                      <div class="d-flex">
                        <button class="btn text-white btn-rmv2 module_editButton" data-module-id="{{ $value->id }}">Edit</button>&nbsp;&nbsp;
                        <button class="btn btn-danger module_deleteButton" data-module-id="{{ $value->id }}">Delete</button>
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
            {!! $modules->withQueryString()->links('pagination::bootstrap-5') !!}
          </div>
        </div>

      </div>
    </div>
    @include('layouts.footer_new')
    <!-- Edit Module Modal -->
    <div class="modal fade" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModuleModalLabel">Edit Module</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="editModuleForm" method="post">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <label for="editModuleName">Module Name:</label>
                  <input type="hidden" id="editModuleId" name="id">
                  <input type="text" name="module_name" class="form-control" id="editModuleName" placeholder="Edit Module Name">
                  <div class="invalid-feedback" id="edit_module_name_error"></div>
                </div>
                <div class="form-group">
                  <label for="editType">Type:</label>
                  <select name="type" class="form-control" id="editType">
                    <option value="1">Admin</option>
                    <option value="2">Application</option>
                  </select>
                  <div class="invalid-feedback" id="edit_type_error"></div>
                </div>
              </div>
              <hr>
              <div class="">
                <button type="button" id="updateModuleButton" class="btn bg-primary-btn text-white" style="float:right">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      // Create Module
      $('#createModuleButton').on('click', function() {
        var formData = $('#createModuleForm').serialize();
        $.ajax({
          url: '{{ route("module.create") }}',
          method: 'POST',
          data: formData,
          success: function(response) {
            console.log(response);
            location.reload();
          },
          error: function(response) {
            if (response.status === 422) {
              var errors = response.responseJSON.errors;
              if (errors.module_name) {
                $('#module_name_error').text(errors.module_name[0]);
                $('input[name="module_name"]').addClass('is-invalid');
              } else {
                $('#module_name_error').text('');
                $('input[name="module_name"]').removeClass('is-invalid');
              }
              if (errors.type) {
                $('#type_error').text(errors.type[0]);
                $('select[name="type"]').addClass('is-invalid');
              } else {
                $('#type_error').text('');
                $('select[name="type"]').removeClass('is-invalid');
              }
            }
          }
        });
      });

      // Edit Module
      $(document).on('click', '.module_editButton', function() {
        var moduleId = $(this).data('module-id');
        $.ajax({
          url: '{{ route("module.edit") }}',
          method: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            module_id: moduleId
          },
          success: function(response) {
            console.log(response);
            $('#editModuleId').val(response.module.id);
            $('#editModuleName').val(response.module.module_name);
            $('#editType').val(response.module.type);
            $('#editModuleModal').modal('show');
          },
          error: function(response) {
            console.log(response);
          }
        });
      });

      // Update Module
      $('#updateModuleButton').on('click', function() {
        var formData = $('#editModuleForm').serialize();
        $.ajax({
          url: '{{ route("module.update") }}',
          method: 'POST',
          data: formData,
          success: function(response) {
            console.log(response)
            location.reload();
          },
          error: function(response) {
            console.log(response);
            if (response.status === 422) {
              var errors = response.responseJSON.errors;
              if (errors.module_name) {
                $('#edit_module_name_error').text(errors.module_name[0]);
                $('input[name="module_name"]').addClass('is-invalid');
              } else {
                $('#edit_module_name_error').text('');
                $('input[name="module_name"]').removeClass('is-invalid');
              }
              if (errors.type) {
                $('#edit_type_error').text(errors.type[0]);
                $('select[name="type"]').addClass('is-invalid');
              } else {
                $('#edit_type_error').text('');
                $('select[name="type"]').removeClass('is-invalid');
              }
            }
          }
        });
      });

      // Delete Module
      $(document).on('click', '.module_deleteButton', function() {
        console.log('Delete button clicked');
        var moduleId = $(this).data('module-id');
        if (confirm('Are you sure you want to delete this module?')) {
          $.ajax({
            url: '{{ route("module.delete") }}',
            method: 'POST',
            data: {
              _token: '{{ csrf_token() }}',
              module_id: moduleId
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