<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/bootstrap.min.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/style.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/responsive.css')}}" />
<link rel="stylesheet" href="{{asset('javascript/common.js')}}" />

</head>

<body>

  <div class="dashboard">

    @include('layouts/header-sidebar')

    <!-- MAIN -->
    <div class="dashboard-app">

      @include('layouts/header-topbar')

      <!-- Sub MAIN -->
      <div class="link-dir">

        <h1 class="display-4">Institute For List</h1>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Institute</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('institute-admin')}}" class="active-link-dir">Institute For</a></li>
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

        <div class="row">
          <div class="col-lg-6">
            <div class="institute-form">
              <form method="post" action="{{ url('institute-for/save') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <div class="col-md-12">
                    <label for="exampleInputEmail1">Name : </label>
                    <input type="text" name="name" class="form-control search-box" placeholder="Enter Name">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-12">
                    <label>Icon : </label>
                    <div class="file">
                      <div class="input-group search-box-2">
                        <input type="text" class="form-control" placeholder="Chapter Image">
                        <div class="input-group-append">
                          <span class="btn_upload">
                            <input type="file" id="imag" title="" class="input-img  file__input--label search-box" name="icon" for="customFile" data-text-btn="Upload" />
                            Choose Image
                          </span>
                        </div>
                      </div>
                      <img id="ImgPreview" src="" class="preview1" />
                      <i class="fas fa-times ml-3 btn-rmv1" id="removeImage1"></i>
                    </div>
                    <!-- <label for="exampleInputEmail1">Icon : </label>
                    <input type="file" onchange="previewFile_create()" name="icon" class="form-control search-box"> -->
                    @error('icon')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-12">
                    <img src="" id="icon_create" alt="Icon" class="mt-2  mb-4 img-resize search-box" style="display: none;">
                  </div>
                  <div class="col-md-12">
                    <label for="exampleInputEmail1">status : </label>
                    <select class="form-control search-box" name="status">
                      <option value=" ">Select Option</option>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                    @error('status')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-12 submit-btn">
                    <button type="submit" class="btn btn-primary mt-3 ">Submit</button>
                  </div>
                </div>

              </form>
            </div>

          </div>
          <div class="col-lg-6">
            <div class="institute-form">
              <h3>List of Institute</h3>

              <form action="#">
                <div class="search-box">
                  <input type="search" name="search" placeholder="Search">
                  <i class="fas fa-search"></i>
                </div>
              </form>

              <table class="table table-responsive-sm table-bordered institute-table mt-4">
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
                    <td><img src="{{asset($value->icon) }}" style="height: 80px;width:80px" alt="Icon" class="img-resize"></td>
                    <td>@if($value->status == 'active')
                      <input type="button" value="Active" class="btn btn-success">
                      @else
                      <input type="button" value="Inactive" class="btn btn-danger">
                      @endif
                    </td>

                    <td>
                      <div class="d-flex">

                        @canButton('edit', 'Institute_for')
                        <input type="submit" class="btn btn-primary institute_for_editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        @endCanButton
                        &nbsp;&nbsp;
                        @canButton('delete', 'Institute_for')
                        <input type="submit" class="btn btn-danger institute_for_deletebutton" data-user-id="{{ $value->id }}" value="Delete">
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
                        <input type="file" onchange="previewFile_update(this)" name="icon" class="form-control">
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
            </div>
          </div>
          </form>
        </div>

      </div>
    </div>
  </div>
  @include('layouts/footer_new')

  <script>
    $(document).ready(function() {
      $('.institute_for_editButton').click(function() {
        var institute_id = $(this).data('user-id');

        $.ajax({
          url: '/institute-for/edit',
          method: 'POST',
          data: {
            institute_id: institute_id
          },
          success: function(response) {
            var reponse_data = response.Institute_for_model;
            var iconSrc = '{{ asset('
            ') }}' + reponse_data.icon;
            $('#institute_id').val(reponse_data.id);
            $('#name').val(reponse_data.name);
            $('#icon_update').attr('src', iconSrc);
            $('#old_icon').val(reponse_data.icon);
            $('#status').val(reponse_data.status);
            $('#usereditModal').modal('show');
          },
          error: function(error) {
            console.error(error);
          }
        });
      });
    });

  
  

    document.querySelectorAll('.institute_for_deletebutton').forEach(function(button) {
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

    function previewFile_update(inputElement) {
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