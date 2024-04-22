</head>

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
        <h1 class="display-4">Institute List</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Admin</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('institute-list')}}">Admin List</a></li>
        </ul>
      </div>

      <!-- /.card-header -->
      <!-- form start -->

      <div class="row">
        <div class="col-md-10 offset-md-1">
          @if (session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
          @endif
        </div>
      </div>

      <script>
        window.setTimeout(function() {
          $(".alert-success").slideUp(500, function() {
            $(this).remove();
          });
        }, 3000);
      </script>
      <!-- Main content -->

      <div class="dashboard-content side-content">


        <div class="col-lg-12 institute-form">
          <div class="create-title-btn">
            <h4 class="mb-0">List of Institute</h4>
            <a href="{{url('create/admin')}}" class="btn btn-success" style="float: right;">Create Admin</a>
          </div>
          <!-- /.card-header -->
          <table class="table table-responsive table-bordered institute-table mt-4">
            <thead>
              <tr>
                <th style="width: 10px">
                  <Sr class="No"></Sr>
                </th>
                <th style="width: 400px">Name</th>
                <th style="width: 400px">Email</th>
                <th style="width: 400px">Mobile</th>
                <th style="width: 400px">Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php $i=1 @endphp
              @foreach($users as $value)
              <tr>
                <td>{{$i}}</td>
                <td>{{$value->firstname.' '.$value->lastname}}</td>
                <td>{{$value->email}}</td>
                <td>{{$value->mobile}}</td>
                <td>{{$value->status}}</td>
                <td>
                  <div class="d-flex">
                    <input type="submit" class="btn btn-primary editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                    &nbsp;&nbsp;
                    <input type="submit" class="btn btn-danger deletebutton" data-user-id="{{ $value->id }}" value="Delete">
                  </div>
              </tr>
              @php $i++ @endphp
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-end">
          {!! $users->withQueryString()->links('pagination::bootstrap-5') !!}

        </div>
      </div>

    </div>
  </div>
</body>

<div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usereditModalLabel">Role </h5>
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
              <button type="submit" class="btn btn-info" style="float:right">Update</button>
            </div>
        </form>
      </div>

    </div>
  </div>
</div>
<script>
  document.querySelectorAll('.editButton').forEach(function(button) {
    button.addEventListener('click', function() {
      var user_id = this.getAttribute('data-user-id');

      axios.post('/admin/edit', {
          user_id: user_id
        })
        .then(response => {
          var reponse_data = response.data.userDT;

          $('#user_id').val(reponse_data.id);
          $('#role_type').val(reponse_data.role_type);
          $('#name').val(reponse_data.name);
          $('#email').val(reponse_data.email);
          $('#mobile').val(reponse_data.mobile);
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

      var user_id = this.getAttribute('data-user-id');

      // Show SweetAlert confirmation
      Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('/admin/delete', {
              user_id: user_id
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
@include('layouts/footer_new')