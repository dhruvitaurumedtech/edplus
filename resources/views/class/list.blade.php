<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
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
      @include('layouts/alert')
      <div class="dashboard-content side-content">
        <div class="row">
          <div class="col-md-6">
            <div class="institute-form">
              <form method="post" action="{{ url('class-list/save') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">

                  <div class="col-md-12">
                    <label for="exampleInputEmail1">Class Name : </label>
                    <input type="text" name="name" class="form-control" placeholder="Enter Class Name" value="{{old('name')}}">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6">
                    <label for="exampleInputEmail1">Icon : </label>
                    <input type="file" name="icon" onchange="previewFile_create()" class="form-control">
                    @error('icon')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6">
                    <img src="" id="icon_create" alt="Icon" class="img-resize mt-3" style="display: none;">
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

                <div class="col-md-12 submit-btn mt-3">
                  <button type="submit" class="btn text-white btn-rmv2">Submit</button>
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
                  @foreach($classlist as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>{{$value->name}}</td>
                    <td>
                            <img src="{{ !empty($value->icon) ? asset($value->icon) : asset('no-image.png') }}" 
                                alt="{{ !empty($value->icon) ? 'Icon' : 'No image available' }}" 
                                class="img-resize" >
                        </td>
                    <td>@if($value->status == 'active')
                      <input type="button" value="Active" class="btn btn-success">
                      @else
                      <input type="button" value="Inactive" class="btn btn-danger">

                      @endif
                    </td>

                    <td>
                      <div class="d-flex">
                        <input type="submit" class="btn text-white btn-rmv2 class_editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        &nbsp;&nbsp;
                        <input type="submit" class="btn btn-danger class_deletebutton" data-user-id="{{ $value->id }}" value="Delete">
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
                  <button type="submit" class="btn btn-primary blue-button" style="float: right;">Update</button>
                </div>
              </form>
            </div>
          </div>

        </div>

      </div>
    </div>
    @include('layouts/footer_new')
  </div>
</body>
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