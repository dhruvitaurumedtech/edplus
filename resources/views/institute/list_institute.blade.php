<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">Institute List</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('institute-list')}}" class="active-link-dir">List Institute</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content">


        <div class="col-lg-12 institute-form">
          <div class="create-title-btn">
            <h4 class="mb-0">Institute List</h4>
            <div class="inner-list-search">
              <input type="search" class="form-control myInput" name="search" placeholder="Search">
              <!-- <a href="{{url('/create/institute')}}" class="btn text-white btn-rmv2" style="float: right;">Create Institute</a> -->
            </div>
            <table class="table table-js table-bordered institute-table mt-4">
              <thead>
                <tr>
                  <th style="width: 10px">
                    <Sr class="No">No</Sr>
                  </th>
                  <th style="width: 400px">Name</th>
                  <th style="width: 400px">Email</th>
                  <th style="width: 400px">Mobile</th>
                  <th style="width: 400px">Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody class="myTable">
                @php $i=1 @endphp
                @foreach($institute_list as $value)
                <tr>
                  <td>{{$i}}</td>
                  <td>{{$value['institute_name']}}</td>
                  <td>{{$value['email']}}</td>
                  <td>{{$value['contact_no']}}</td>
                  <td>{{$value['status']}}</td>
                  <td>
                    <div class="d-flex">
                      <input type="submit" class="btn text-white btn-rmv2 institute_list_editButton" data-user-id="{{ $value['id'] }}"  value="Edit">&nbsp;&nbsp;
                      &nbsp;&nbsp;
                      <input type="submit" class="btn btn-danger institute_list_deletebutton" data-user-id="{{ $value['id'] }}" value="Delete">
                      &nbsp;&nbsp;
                      <a href="{{url('/student/list/'.$value['id'])}}" class="btn btn-warning" style="text-wrap: nowrap;">Student List</a>
                      <!-- &nbsp;&nbsp;
                      <a href="{{url('/teacher/list/'.$value['id'])}}" class="btn btn-info" style="text-wrap: nowrap;">Teacher List</a> -->
                    </div>
                </tr>
                @php $i++ @endphp
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-end mt-3">
            {!! $institute_list->withQueryString()->links('pagination::bootstrap-5') !!}

          </div>

        </div>

        @include('layouts/footer_new')
      </div>

    </div>
</body>

<div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usereditModalLabel">Institute List
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{ url('institute_admin/update') }}">
          @csrf
          <div class="card-body">
           
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
              <div class="col-sm-10">
                <input type="hidden" name="user_id" id="user_id">
                <input type="text" id="name" name="name" class="form-control" placeholder="Name">
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