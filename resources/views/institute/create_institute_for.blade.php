@include('layouts/header')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Institute For</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Institute For</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  @include('alert')
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-5">
          <div class="card card-suceess">
            <div class="card-header">
              <h3 class="card-title">Create Institute For</h3>

            </div>
            <form method="post" action="{{ url('institute-for/save') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-12">
                      <label for="exampleInputEmail1">Name : </label>
                      <input type="text" name="name" class="form-control" placeholder="Enter Name">
                      @error('name')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-9">
                      <label for="exampleInputEmail1">Icon : </label>
                      <input type="file" onchange="previewFile_create()" name="icon" id="icon" class="form-control">
                      @error('icon')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-3">
                      <img src="" id="icon_create" alt="Icon" class="mt-4" style="display: none;height:80px;width:80px;">
                    </div>
                    <div class="col-md-12">
                      <label for="exampleInputEmail1">status : </label>
                      <select class="form-control" name="status">
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
                <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-7">
          <div class="card">
            <div class="card card-suceess">
              <div class="card-header">
                <h3 class="card-title">Institute For List</h3>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table class="table table-bordered table-responsive">
                <thead>
                  <tr>
                    <th style="width: 10px">
                      <Sr class="No">No</Sr>
                    </th>
                    <th style="width: 200px">Name</th>
                    <th style="width: 200px">Icon</th>
                    <th style="width: 500px">Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @php $i=1 @endphp
                  @foreach($institute_for as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>{{$value->name}}</td>
                    <td><img src="{{asset($value->icon) }}" alt="Icon" style="height:50px;">></td>
                    <td>@if($value->status == 'active')
                      <input type="button" value="Active" class="btn btn-success">
                      @else
                      <input type="button" value="Inactive" class="btn btn-danger">
                      @endif
                    </td>

                    <td>
                      <div class="d-flex">
                        @canButton('edit', 'Institute_for')
                        <input type="submit" class="btn btn-primary editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        @endCanButton
                        &nbsp;&nbsp;
                        @canButton('delete', 'Institute_for')
                        <input type="submit" class="btn btn-danger deletebutton" data-user-id="{{ $value->id }}" value="Delete">
                        @endCanButton
                      </div>
                  </tr>
                  @php $i++ @endphp
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-end">
              {!! $institute_for->withQueryString()->links('pagination::bootstrap-5') !!}

            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usereditModalLabel">Edit Institute For </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{ url('institute-for/update') }}" enctype="multipart/form-data">
          @csrf
          <div class="card-body">
            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <input type="hidden" id="institute_id" name="institute_id">
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
                  <img src="" id="icon_update" alt="Icon" class="mt-4" style="height:50px;">
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
      </div>
    </div>
    </form>
  </div>

</div>
</div>
</div>

<script>
  document.querySelectorAll('.editButton').forEach(function(button) {
    button.addEventListener('click', function() {
      var institute_id = this.getAttribute('data-user-id');

      axios.post('/institute-for/edit', {
          institute_id: institute_id
        })
        .then(response => {
          var reponse_data = response.data.Institute_for_model;
          var iconSrc = '{{ asset('
          ') }}' + reponse_data.icon;
          $('#institute_id').val(reponse_data.id);
          $('#name').val(reponse_data.name);
          $('#icon_update').attr('src', iconSrc);
          $('#old_icon').val(reponse_data.icon);
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
      event.preventDefault();
      var institute_id = this.getAttribute('data-user-id');

      Swal.fire({
        title: 'Are you sure want to delete?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('/institute-for/delete', {
              institute_id: institute_id
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

  function previewFile_create() {
    $("#icon_create").show();
    const preview = document.getElementById("icon_create");
    const efileInput = document.getElementById("icon");
    const file = fileInput.files[0];
    const reader = new FileReader();

    reader.addEventListener("load", () => {
      preview.src = reader.result;
    }, false);

    if (file) {
      reader.readAsDataURL(file);
    }
  }

  function previewFile_update() {
    const preview = document.getElementById("icon_update");
    const file = inputElement.files[0];
    const reader = new FileReader();

    reader.addEventListener("load", () => {
      preview.src = reader.result;
    }, false);

    if (file) {
      reader.readAsDataURL(file);
    }
  }
</script>
@include('layouts/footer')