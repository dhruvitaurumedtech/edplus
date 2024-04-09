<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/bootstrap.min.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/style.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/responsive.css')}}" />

</head>

<body>

  <div class="dashboard">

    @include('layouts/header-sidebar')

    <!-- MAIN -->
    <div class="dashboard-app">

      @include('layouts/header-topbar')

      <!-- Sub MAIN -->
      <div class="link-dir">
        <h1 class="display-4">Role</h1>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Settings</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)" class="active-link-dir">Role</a></li>
        </ul>
      </div>

      <script>
        window.setTimeout(function() {
          $(".alert-success").slideUp(500, function() {
            $(this).remove();
          });
        }, 3000);
      </script>
      <div class="dashboard-content side-content">
        <div class="row">
          <div class="col-md-10 offset-md-1">
            @if (session('success'))
            <div class="alert alert-success">
              {{ session('success') }}
            </div>
            @endif
          </div>
        </div>

        <div class="row">
          <!-- table -->
          <div class="col-lg-12 mt-5">
            <div class="create-title-btn">
              <h4 class="mb-0">List of Role</h4>
              <!-- <a href="role.php" class="btn text-white btn-rmv2">Create Role</a> -->
              <div class="inner-list-search">
                <input type="search" class="form-control myInput" name="search" placeholder="Search">
                @canButton('add', 'Role')
                <a href="{{ url('create/role') }}" class="btn text-white btn-rmv2">Create role</a>
                @endCanButton

              </div>

            </div>
            <table class="table table-responsive-sm table-bordered institute-table mt-4">
              <thead>
                <tr>
                  <th scope="col">No</th>
                  <th scope="col">Role Name</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody class="myTable">
                @php $i=1 @endphp
                @foreach($roles as $value)
                <tr>
                  <td>{{$i}}</td>
                  <td>{{$value->role_name}}</td>
                  <td>
                    <div class="d-flex">
                      @canButton('edit', 'Role')
                      <input type="submit" class="btn btn-primary editButton" data-role-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                      @endCanButton
                      <form method="get" action="{{url('permission')}}">
                        @csrf
                        <input type="hidden" value="{{ $value->id }}" name="id">
                        <input type="submit" class="btn btn-success" value="Permission">

                      </form>
                      &nbsp;&nbsp;
                      @canButton('delete', 'Role')

                      @if($value->id !='1')
                      <input type="submit" class="btn btn-danger deletebutton" data-role-id="{{ $value->id }}" value="Delete">
                      @endif
                      @endCanButton
                    </div>
                </tr>
                @php $i++ @endphp
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-end">
            {!! $roles->withQueryString()->links('pagination::bootstrap-5') !!}

          </div>

        </div>
      </div><!-- Sub Main Col END -->
    </div><!-- MAIN row END -->
    @include('layouts/footer_new')
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Role </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post" action="{{ url('roles/update') }}">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <label for="exampleInputEmail1">Edit Role : </label>
                  <input type="hidden" id="role_id" name="id">
                  <input type="text" name="role_name" class="form-control" id="role_name" placeholder="Edit role">
                </div>
              </div>
              <hr>
              <div class="">
                <button type="submit" class="btn btn-primary" style="float:right">Update</button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>


    <script>
      document.querySelectorAll('.editButton').forEach(function(button) {
        button.addEventListener('click', function() {
          var roleId = this.getAttribute('data-role-id');

          axios.post('/roles/edit', {
              roleId: roleId
            })
            .then(response => {
              var reponse_data = response.data.roles;
              $('#role_id').val(reponse_data.id);
              $('#role_name').val(reponse_data.role_name);
              $('#exampleModal').modal('show');
            })
            .catch(error => {
              console.error(error);
            });
        });
      });
      document.querySelectorAll('.deletebutton').forEach(function(button) {
        button.addEventListener('click', function(event) {
          event.preventDefault(); // Prevent the default form submission

          var roleId = this.getAttribute('data-role-id');

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
              axios.post('/roles/delete', {
                  roleId: roleId
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
  </div>

  <!-- js -->
  <script src="{{asset('mayal_assets/js/jquery-3.7.1.min.js')}}"></script>
  <script src="{{asset('mayal_assets/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('mayal_assets/js/main.js')}}"></script>

</body>


</html>