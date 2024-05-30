<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">Module</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">ACL</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)" class="active-link-dir">Module</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content">

        <div class="row">

          <div class="col-lg-6 mt-3">
            <div class="institute-form">
              <form class="s-chapter-form" action="{{ route('module.create') }}" method="post">
                @csrf
                <h4 class="mb-3">Create Module</h4>

                <div class="create-admin mt-4">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label>Module Name</label>
                    </div>
                    <div class="col-md-9">
                      <input type="text" name="module_name" class="form-control" placeholder="Enter Module Name" value="{{old('module_name')}}">
                    </div>
                  </div>
                </div>
                <div class="submit-btn">
                  <input type="submit" value="Submit" class="btn bg-primary-btn text-white">
                </div>
              </form>
            </div>
          </div>
          <div class="col-lg-6  mt-3">
            <div class="institute-form">
              <div class="create-title-btn ">
                <h4 class="mb-0">List of Module</h4>
                <div class="inner-list-search">
                  <input type="search" class="form-control myInput" name="search" placeholder="Search">

                </div>
              </div>
              <table class="table table-bordered institute-table mt-4 table-responsive-sm">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Module Name</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody class="myTable">
                  @php $i=1 @endphp
                  @foreach($modules as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>{{$value->module_name}}</td>
                    <td>
                      <div class="d-flex">
                        <input type="submit" class="btn text-white btn-rmv2 module_editButton" data-module-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        <input type="submit" class="btn btn-danger module_deletebutton" data-module-id="{{ $value->id }}" value="Delete">
                      </div>
                  </tr>
                  @php $i++ @endphp
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            {!! $modules->withQueryString()->links('pagination::bootstrap-5') !!}
          </div>
        </div>

      </div>
    </div>
    @include('layouts/footer_new')
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Module </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post" action="{{ route('module.update') }}">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <label for="exampleInputEmail1">Edit Module :</label>
                  <input type="hidden" id="module_id" name="id">
                  <input type="text" name="module_name" class="form-control" id="module_name" placeholder="Edit Module">
                </div>
              </div>
              <hr>
              <div class="">
                <button type="submit" class="btn bg-primary-btn text-white" style="float:right">Update</button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>

</body>

</html>