<body>

  <div class="dashboard">

    @include('layouts/header-sidebar')

    <!-- MAIN -->
    <div class="dashboard-app">

      @include('layouts/header-topbar')

      <!-- Sub MAIN -->
      <div class="link-dir">
        <h1 class="display-4">Institute admin</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('institute-admin')}}">Institute</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)" class="active-link-dir">Institute admin</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content">
        <div class="row">
          <div class="col-lg-12 mt-3 institute-form">
            <div class="create-title-btn">
              <h4 class="mb-0">Institute admin
              </h4>
              <div class="inner-list-search">
                <input type="search" class="form-control myInput" name="search" placeholder="Search">
                @canButton('add', 'Role')
                <a href="{{url('create/admin')}}" class="btn text-white btn-rmv2" style="float: right;">Create Institute Admin</a>
                @endCanButton

              </div>
              <table class="table table-js table-bordered institute-table mt-4">
                <thead>
                  <tr>
                    <th style="width: 10px">
                      <Sr class="No">No</Sr>
                    </th>
                    <th style="width: 400px">firstname</th>
                    <th style="width: 400px">lastname</th>
                    <th style="width: 400px">Email</th>
                    <th style="width: 400px">Mobile</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody class="myTable">
                  @php $i=1 @endphp
                  @foreach($institute as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>{{$value->firstname}}</td>
                    <td>{{$value->lastname}}</td>
                    <td>{{$value->email}}</td>
                    <td>{{$value->mobile}}</td>
                    <td>
                      <div class="d-flex">
                        <input type="submit" class="btn text-white btn-rmv2 institute_admin_editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        &nbsp;&nbsp;
                        <input type="submit" class="btn btn-danger institute_admin_deletebutton" data-user-id="{{ $value->id }}" value="Delete">
                      </div>
                  </tr>
                  @php $i++ @endphp
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="d-flex justify-content-end">
              {!! $institute->withQueryString()->links('pagination::bootstrap-5') !!}

            </div>

          </div>

        </div>
      </div>
    </div>
    @include('layouts/footer_new')
  </div>

  <div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="usereditModalLabel">Institute Admin </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="post" action="{{ url('admin/update') }}">
            @csrf
            <div class="card-body">
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-2 col-form-label">Select Role</label>
                <div class="col-sm-10">
                  <select class="form-control" name="role_type" id="role_type">
                    <option value="">Select Role</option>
                    <option value="2">Admin</option>
                    <option value="3">Institute</option>
                  </select>
                  @error('role_type')
                  <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">firstname</label>
                <div class="col-sm-10">
                  <input type="hidden" name="user_id" id="user_id">
                  <input type="text" id="firstname" name="firstname" class="form-control" placeholder="firstname">
                  @error('name')
                  <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">lastname</label>
                <div class="col-sm-10">
                  <input type="text" id="lastname" name="lastname" class="form-control" placeholder="lastname">
                  @error('name')
                  <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                  <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                  @error('email')
                  <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                  <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Mobile Number">
                </div>
              </div>

              <hr>
              <div class="">
                <button type="submit" class="btn text-white btn-rmv2" style="float:right">Update</button>
              </div>
          </form>
        </div>

      </div>
    </div>
  </div>