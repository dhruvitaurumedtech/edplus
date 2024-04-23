</head>

<body>

  <div class="dashboard">

    @include('layouts/header-sidebar')

    <!-- MAIN -->
    <div class="dashboard-app">

      @include('layouts/header-topbar')

      <!-- Sub MAIN -->
      <div class="link-dir">
        <h1 class="display-4">class List</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
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
              <form method="post" action="{{ url('class-list/save') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                  <div class="form-group">
                    <div class="row">

                      <div class="col-md-12">
                        <label for="exampleInputEmail1">Class Name : </label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Board Name">
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-12">
                        <label for="exampleInputEmail1">Icon : </label>
                        <input type="file" name="icon" onchange="previewFile_create()" class="form-control">
                        @error('icon')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-12">
                        <img src="" id="icon_create" alt="Icon" class="mt-2  mb-4 img-resize" style="display: none;">
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
                <div class="col-md-12 submit-btn">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
          </div>
          <div class="col-md-6">
            <div class="institute-form">
              <h3 class="card-title">Class List</h3>
              <form action="#">
                <div class="search-box">
                  <input type="search" class="form-control myInput" name="search" placeholder="Search">
                  <i class="fas fa-search"></i>
                </div>
              </form>
              <!-- /.card-header -->
              <table class="table table-bordered table-responsive mt-4">
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
                <tbody class="myTable">
                  @php $i=1 @endphp
                  @foreach($classlist as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>{{$value->name}}</td>
                    <td><img src="{{asset($value->icon) }}" alt="Icon" class="img-resize"></td>
                    <td>@if($value->status == 'active')
                      <input type="button" value="Active" class="btn btn-success">
                      @else
                      <input type="button" value="Inactive" class="btn btn-danger">

                      @endif
                    </td>

                    <td>
                      <div class="d-flex">
                        @canButton('edit', 'Class')
                        <input type="submit" class="btn btn-primary editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        @endCanButton
                        &nbsp;&nbsp;
                        @canButton('delete', 'Class')
                        <input type="submit" class="btn btn-danger deletebutton" data-user-id="{{ $value->id }}" value="Delete">
                        @endCanButton
                      </div>
                  </tr>
                  @php $i++ @endphp
                  @endforeach
                </tbody>
              </table>

              <div class="d-flex justify-content-end">
                {!! $classlist->withQueryString()->links('pagination::bootstrap-5') !!}

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
        function previewFile_create() {
          $("#icon_create").show();
          const preview = document.getElementById("icon_create");
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

        function previewFile_update() {
          $("#icon_update").show();
          const preview = document.getElementById("icon_update");
          const fileInput = document.getElementById("edit_icon");
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
      @include('layouts/footer_new')