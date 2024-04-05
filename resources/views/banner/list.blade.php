@include('layouts/header')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Banner List</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Banner List</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->
  @include('alert')
  <!-- Main content -->

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Banner List</h3>
              @canButton('add', 'Banner')
              <a href="{{url('create/banner-list')}}" class="btn btn-success" style="float: right;">Create Banner </a>
              @endCanButton
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table class="table table-bordered table-responsive">
                <thead>
                  <tr>
                    <th style="width: 10px">
                      <Sr class="No">No</Sr>
                    </th>
                    @if(auth::user()->role_type == '3')
                    <th style="width: 10px">
                      <Sr class="No">Institute Name</Sr>
                    </th>
                    <th style="width: 10px">
                      <Sr class="No">Url</Sr>
                    </th>
                    @endif
                    <th style="width: 200px">Banner_image</th>
                    <th style="width: 500px">Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                
                  @php $i=1 @endphp
                  @foreach($banner_list as $value)
                  {{ print_r($value->id); }}
                  <tr>
                    <td>{{$i}}</td>
                    @if(auth::user()->role_type == '3')
                    <td>{{$value->institute_name}}</td>
                    <td>{{$value->url}}</td>
                    @endif
                    <td><img src="{{asset($value->banner_image) }}" alt="banner" style="width:100px;height:100px;"></td>
                    <td>@if($value->status == 'active')
                      <input type="button" value="Active" class="btn btn-success">
                      @else
                      <input type="button" value="Inactive" class="btn btn-danger">

                      @endif
                    </td>

                    <td>
                      <div class="d-flex">
                        @canButton('edit', 'Banner')
                        <input type="submit" class="btn btn-primary editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        @endCanButton
                        &nbsp;&nbsp;
                        @canButton('delete', 'Banner')
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
              {!! $banner_list->withQueryString()->links('pagination::bootstrap-5') !!}

            </div>
          </div>

        </div>
  </section>

</div>
<div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usereditModalLabel">Edit Banner </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{ url('banner/update') }}" enctype="multipart/form-data">
          @csrf
          <div class="card-body">
            <div class="form-group">
              <div class="row">

                <div class="col-md-9">
                  <input type="hidden" id="banner_id" name="banner_id">
                  <label for="exampleInputEmail1">Banner Image : </label>
                  <input type="hidden" name="old_banner_image" id="old_banner_image">
                  <input type="file" onchange="previewFile()" name="banner_image" class="form-control">
                  @error('icon')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-3">
                  <img src="" id="banner_image" alt="banner" class="mt-4" style="width:100px;height:100px;">
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
      var banner_id = this.getAttribute('data-user-id');

      axios.post('/banner/edit', {
          banner_id: banner_id
        })
        .then(response => {
          var reponse_data = response.data.banner_list;
          var iconSrc = '{{ asset('
          ') }}' + reponse_data.banner_image;
          $('#banner_id').val(reponse_data.id);
          $('#banner_image').attr('src', iconSrc);
          $('#old_banner_image').val(reponse_data.banner_image);
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

      var banner_id = this.getAttribute('data-user-id');

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
          axios.post('/banner/delete', {
              banner_id: banner_id
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

  function previewFile() {
    const preview = document.getElementById("banner_image");
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