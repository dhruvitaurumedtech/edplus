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
      <!-- Main content -->
      <div class="link-dir">
        <h1 class="display-4">Create Role</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Settings</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('role_list')}}">Role</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)" class="active-link-dir">Create Role</a></li>
        </ul>
      </div>


      <!-- /.card-header -->
      <!-- form start -->

      <div class="dashboard-content side-content p-0">
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
          <form class="s-chapter-form institute-form" action="{{ url('roles/save') }}" method="post">
            @csrf
            <h4 class="mb-3">Create Admin</h4> 
            
            <div class="create-admin mt-4">
              <div class="form-group row">
                <div class="col-md-3">
                  <label>Enter Role</label>
                </div>
                <div class="col-md-9">
                  <input type="text" name="role_name" class="form-control" placeholder="Enter Role">
                </div>
              </div>
            </div>

            <div class="submit-btn">
              <input type="submit" value="Submit" class="btn bg-primary-btn text-white">
            </div>
          </form>
        </div>


      </div>


    </div>
  </div>
  @include('layouts/footer_new')