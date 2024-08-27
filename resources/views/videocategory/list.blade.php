</head>

<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">Content category</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
             <li><a href="javascript:void(0)" class="active-link-dir">Content category</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content">
        <div class="row">
          <div class="col-md-5">
            <div class="institute-form">
              <h3 class="card-title">Create Content Category</h3>
              <form method="post" action="{{ url('video-category-save') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-12">
                        <label for="exampleInputEmail1">Content Name : </label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Content Name" value="{{old('name')}}">
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
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
                  </div>
                </div>
                <div class="d-flex">
                  <button type="submit" class="btn text-white blue-button" style="float: right;">Submit</button>
                </div>
              </form>
            </div>
          </div>
          <!-- list -->
          <div class="col-md-7">
            <div class="institute-form">
              <h3 class="card-title">Content Category List</h3>
              <form action="#">
                <div class="search-box">
                  <input type="search" class="form-control myInput" name="search" placeholder="Search">
                  <i class="fas fa-search"></i>
                </div>
              </form>
              <table class="table table-js table-bordered table-responsive mt-4">
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
                <tbody class="myTable">
                  @php $i=1 @endphp
                  @foreach($board_list as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>{{$value->name}}</td>
                    <!-- <td>@if($value->status == 'active')
                      <input type="button" value="Active" class="btn btn-success">
                      @else
                      <input type="button" value="Inactive" class="btn btn-danger">

                      @endif
                    </td> -->
                    <td>
                          <button id="status-button-{{ $value->id }}" data-user-id="{{ $value->id }}" data-name-id="video-category-list" class="{{ $value->status === 'active' ? 'btn btn-active' : 'btn btn-inactive' }}">
                              {{ ucfirst($value->status) }}
                          </button>
                      </td>

                    <td>
                      <div class="d-flex">
                        <input type="submit" class="btn text-white blue-button videocategory_editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        &nbsp;&nbsp;
                        <input type="submit" class="btn btn-danger videocategory_deletebutton" data-user-id="{{ $value->id }}" value="Delete">
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
            <div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="usereditModalLabel">Edit Content Category </h5>
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
                              <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" value="{{old('name')}}">
                              @error('name')
                              <div class="text-danger">{{ $message }}</div>
                              @enderror
                            </div>

                            <div class="col-md-12">
                              <label for="exampleInputEmail1">status : </label>
                              <select class="form-control" name="status" id="status">
                                <option value=" ">Select Option</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>

                              </select>
                              @error('status')
                              <div class="text-danger">{{ $message }}</div>
                              @enderror
                            </div>

                          </div>

                        </div>
                      </div>
                      <div class="d-flex justify-content-end">
                        <button type="submit" class="btn text-white blue-button" style="float: right;">Update</button>
                      </div>
                  </div>
                </div>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
    @include('layouts/footer_new')
  </div>
  <style>
.btn-active {
    background-color: green;
    color: white;
}

.btn-inactive {
    background-color: red;
    color: white;
}
  </style>