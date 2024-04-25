</head>
<meta name="base-url" content="{{ url('/') }}">

<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <!-- MAIN -->
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <!-- /.content-header -->
      <script>
        window.setTimeout(function() {
          $(".alert-success").slideUp(500, function() {
            $(this).remove();
          });
        }, 3000);
      </script>
      <div class="link-dir">
        <h1 class="display-4">Banner List</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Banner</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('institute-list')}}">Create Banner</a></li>
        </ul>
      </div>

      <!-- /.card-header -->
      <!-- form start -->

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

        <div class="dashboard-content side-content">

          <div class="row">
            <div class="col-lg-6">
              <div class="institute-form">
                <form method="post" action="{{ url('banner/save') }}" enctype="multipart/form-data">
                  @csrf

                  @if(auth::user()->role_type == '3')
                  <label for="exampleInputEmail1">Select Institute : </label>
                  <select name="institute_id" class="form-control">
                    <option value="">Select option</option>
                    @foreach($institute_list as $value)
                    <option value="{{$value->id}}">{{$value->institute_name}}</option>
                    @endforeach
                  </select>
                  @error('banner_image')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                  <label for="exampleInputEmail1">Url : </label>
                  <input type="text" name="url" class="form-control" placeholder="Enter url">
                  @error('url')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                  @endif
                  <label for="exampleInputEmail1">Banner_image : </label>
                  <input type="file" name="banner_image[]" class="form-control" multiple>
                  @error('banner_image')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror


                  <label for="exampleInputEmail1">status : </label>
                  <select class="form-control" name="status">
                    <option value=" ">Select Option</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                  @error('status')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                  <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="">
                <div class="institute-form">

                  <!-- /.card-header -->
                  <table class="table table-js table-bordered">
                    <thead>
                      <tr>
                        <th>
                          <Sr class="No">No</Sr>
                        </th>
                        @if(auth::user()->role_type == '3')
                        <th>
                          <Sr class="No">Institute Name</Sr>
                        </th>
                        <th>
                          <Sr class="No">Url</Sr>
                        </th>
                        @endif
                        <th>Banner_image</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php $i=1 @endphp
                      @foreach($banner_list as $value)
                      <tr>
                        <td>{{$i}}</td>
                        @if(auth::user()->role_type == '3')
                        <td>{{$value->institute_name}}</td>
                        <td>{{$value->url}}</td>
                        @endif
                        <td><img src="{{asset($value->banner_image) }}" alt="banner" class="img-resize mt-3"></td>
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

                  <div class="d-flex justify-content-end">
                    {!! $banner_list->withQueryString()->links('pagination::bootstrap-5') !!}
                  </div>
                </div>

              </div>
            </div>

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
                            <img src="" id="banner_image" alt="banner" class="img-resize mt-3">
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
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary" style="float: right;">Update</button>
                    </div>
                </div>
              </div>
              </form>
            </div>

          </div>

        </div>

      </div>
      @include('layouts/footer_new')
      <script>
        document.querySelectorAll('.editButton').forEach(function(button) {
          button.addEventListener('click', function() {
            var banner_id = this.getAttribute('data-user-id');
            var baseUrl = $('meta[name="base-url"]').attr('content');
            axios.post('/banner/edit', {
                banner_id: banner_id
              })
              .then(response => {

                var reponse_data = response.data.banner_list;
                var iconSrc = baseUrl + '/' + reponse_data.banner_image;

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

    </div>
  </div>
</body>

</html>