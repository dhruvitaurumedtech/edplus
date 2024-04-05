@include('layouts/header')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Video Category</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Video Category</li>
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

        <!-- create -->
        <div class="col-md-5">
          <!-- general form elements -->
          <div class="card card-success">
            <div class="card-header">
              <h3 class="card-title">Create Video Category</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form method="post" action="{{ url('video-category-save') }}" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-12">
                      <label for="exampleInputEmail1">Video Name : </label>
                      <input type="text" name="name" class="form-control" placeholder="Enter Board Name">
                      @error('name')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
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
                <button type="submit" class="btn btn-success" style="float: right;">Submit</button>
              </div>
            </form>
          </div>
        </div>

        <!-- list -->
        <div class="col-md-7">
          <div class="card card-success">
            <div class="card-header">
              <h3 class="card-title">Video Category List</h3>
              <!-- @canButton('add', 'Board')
              <a href="{{url('board-create')}}" class="btn btn-success" style="float: right;">Create Board </a>
              @endCanButton -->
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
                    <th style="width: 500px">Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @php $i=1 @endphp
                  @foreach($board_list as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>{{$value->name}}</td>
                    <td>@if($value->status == 'active')
                      <input type="button" value="Active" class="btn btn-success">
                      @else
                      <input type="button" value="Inactive" class="btn btn-danger">

                      @endif
                    </td>

                    <td>
                      <div class="d-flex">
                        @canButton('edit', 'Board')
                        <input type="submit" class="btn btn-primary editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        @endCanButton
                        &nbsp;&nbsp;
                        @canButton('delete', 'Board')
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
              {!! $board_list->withQueryString()->links('pagination::bootstrap-5') !!}

            </div>
          </div>

        </div>


  </section>

</div>
<div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usereditModalLabel">Edit Board </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{ url('video-category-update') }}" enctype="multipart/form-data">
          @csrf
          <div class="card-body">
            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <input type="hidden" id="video_category_id" name="video_category_id">
                  <label for="exampleInputEmail1">Name : </label>
                  <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                  @error('name')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
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
      var video_category_id = this.getAttribute('data-user-id');

      axios.post('video-category-edit', {
          video_category_id: video_category_id
        })
        .then(response => {

          var reponse_data = response.data.video_category_list;
          $('#video_category_id').val(reponse_data.id);
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

      var video_category_id = this.getAttribute('data-user-id');

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
          axios.post('video-category-delete', {
              video_category_id: video_category_id
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
@include('layouts/footer')