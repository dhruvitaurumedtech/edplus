</head>

<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">Board List</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Institute</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('board-list')}}" class="active-link-dir">Board</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content">

        <!-- /.card-header -->
        <!-- form start -->
        <div class="row">
          <div class="col-lg-6">
            <div class="institute-form">
              <form method="post" action="{{ url('board-save') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <div class="col-md-12">
                    <label for="exampleInputEmail1">Board Name : </label>
                    <input type="text" name="name" class="form-control" placeholder="Enter Board Name" value="{{old('name')}}">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6">
                    <label for="exampleInputEmail1">Icon : </label>
                    <input type="file" onchange="previewFile()" name="icon" id="nicon" class="form-control">
                    @error('icon')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6">
                    <img src="" id="icon" alt="Icon" class="img-resize mt-4" style="display: none;">
                  </div>
                  <div class="col-md-12">
                    <label for="exampleInputEmail1">status : </label>
                    <select class="form-control" name="status">
                      <option value=" ">Select Option</option>
                      <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                      <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                </div>


                <div class="col-md-12 submit-btn">
                  <button type="submit" class="btn text-white btn-rmv2 mt-3 ">Submit</button>
                </div>
              </form>
            </div>
          </div>

          <!-- list -->
          <div class="col-lg-6">
            <div class="institute-form">
              <h3 class="card-title">Board List</h3>
              <form action="#">
                <div class="search-box">
                  <input type="search" class="form-control myInput" name="search" placeholder="Search">
                  <i class="fas fa-search"></i>
                </div>
              </form>
              <!-- @canButton('add', 'Board')
              <a href="{{url('board-create')}}" class="btn btn-success" style="float: right;">Create Board </a>
              @endCanButton -->
              <!-- /.card-header -->
              <table class="table table-js table-bordered table-responsive mt-4">
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
                  @foreach($board_list as $value)
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
                        @canButton('edit', 'Board')
                        <input type="submit" class="btn text-white btn-rmv2 board_editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        @endCanButton
                        &nbsp;&nbsp;
                        @canButton('delete', 'Board')
                        <input type="submit" class="btn btn-danger board_deletebutton" data-user-id="{{ $value->id }}" value="Delete">
                        @endCanButton
                      </div>
                  </tr>
                  @php $i++ @endphp
                  @endforeach
                </tbody>
              </table>

              <div class="d-flex justify-content-end">
                {!! $board_list->withQueryString()->links('pagination::bootstrap-5') !!}

              </div>
            </div>

          </div>
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
                <form method="post" action="{{ url('board-update') }}" enctype="multipart/form-data">
                  @csrf
                  <div class="card-body">
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-12">
                          <input type="hidden" id="board_id" name="board_id">
                          <label for="exampleInputEmail1">Name : </label>
                          <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                          @error('name')
                          <div class="text-danger">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="col-md-8">
                          <label for="exampleInputEmail1">Icon : </label>
                          <input type="hidden" name="old_icon" id="old_icon">
                          <input type="file" onchange="previewFile_update(this)" name="icon" class="form-control">
                          @error('icon')
                          <div class="text-danger">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="col-md-4">
                          <img src="" id="icon_update" alt="Icon" class="mt-4 img-resize">
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
                </form>
              </div>
            </div>
          </div>
          </form>
        </div>

      </div>
    </div>
    @include('layouts/footer_new')
  </div>
  <script>
    //create form function
    function previewFile() {
      $("#icon").show();
      const preview = document.getElementById("icon");
      const fileInput = document.getElementById("nicon");
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
      $("#icon_update").show();
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