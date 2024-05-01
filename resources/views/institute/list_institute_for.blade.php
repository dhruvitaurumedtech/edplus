</head>

<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">Institute For List</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Institute</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('institute-admin')}}" class="active-link-dir">Institute For</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content">

        <div class="row">
          <div class="col-lg-6">
            <div class="institute-form">
              <form method="post" action="{{ url('institute-for/save') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <div class="col-md-12">
                    <label for="exampleInputEmail1">Name : </label>
                    <input type="text" name="name" class="form-control search-box" placeholder="Enter Name" value="{{old('name')}}">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-8 ">
                    <label>Image: </label>
                    <div class="input-group search-box-2">
                      <input type="file" name="icon" class="form-control" onchange="openFile(event)" />
                      </span>
                    </div>
                  </div>
                  <div class="col-md-4 mt-5">
                    <img id='output' src="" class="preview1" />
                    @error(' icon') <div class="text-danger">{{ $message }}
                    </div>
                    @enderror
                  </div>


                  <div class="col-md-12">
                    <img src="" id="icon_create" alt="Icon" class="mt-2  mb-4 img-resize search-box" style="display: none;">
                  </div>
                  <div class="col-md-12">
                    <label for="exampleInputEmail1">status : </label>
                    <select class="form-control search-box" name="status">
                      <option value=" ">Select Option</option>
                      <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                      <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    @error('status')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-12 submit-btn">
                    <button type="submit" class="btn text-white btn-rmv2 mt-3 ">Submit</button>
                  </div>
                </div>

              </form>
            </div>

          </div>
          <div class="col-lg-6">
            <div class="institute-form">
              <h3>Institute For</h3>

              <form action="#">
                <div class="search-box">
                  <input type="search" class="form-control myInput" name="search" placeholder="Search">
                  <i class="fas fa-search"></i>
                </div>
              </form>

              <table class="table table-js table-bordered institute-table mt-4">
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
                  @foreach($institute_for as $value)
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

                        @canButton('edit', 'Institute_for')
                        <input type="button" class="btn text-white btn-rmv2 institute_for_editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
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
                    <div class="row justify-content-center">
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
                        <img src="" id="icon_update" alt="Icon" class="img-resize mt-3">
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
                  <button type="submit" class="btn text-white btn-rmv2">Update</button>
                </div>
            </div>
          </div>
          </form>
        </div>

      </div>
    </div>
  </div>
  <script>
    var openFile = function(file) {
      var input = file.target;
      var reader = new FileReader();
      reader.onload = function() {
        var dataURL = reader.result;
        var output = document.getElementById('output');
        output.style.display = 'block';

        output.src = dataURL;

      };
      reader.readAsDataURL(input.files[0]);
    };


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
  @include('layouts/footer_new')