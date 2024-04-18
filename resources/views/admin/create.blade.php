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

      <!-- /.content-header -->

      <div class="col-md-10 offset-md-1">
        @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
        @endif
      </div>
      <script>
        window.setTimeout(function() {
          $(".alert-success").slideUp(500, function() {
            $(this).remove();
          });
        }, 3000);
      </script>
      <!-- Main content -->
      <div class="link-dir">
        <h1 class="display-4">Admin</h1>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Institute</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('institute-admin')}}">Admin</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)" class="active-link-dir">Create Admin</a></li>
        </ul>
      </div>

      <script>
        function clearFormData() {
          document.getElementById("myForm").reset();
        }
      </script>
      <!-- Main content -->
      <div class="col-md-12">
        <form class="s-chapter-form" method="post" id="myForm" action="{{ url('store/admin') }}">
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
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">firstname</label>
              <div class="col-sm-10">
                <input type="text" id="firstname" name="firstname" class="form-control" placeholder="first name">
                @error('firstname')
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">lastname</label>
              <div class="col-sm-10">
                <input type="text" id="lastname" name="lastname" class="form-control" placeholder="last name">
                @error('lastname')
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
              <div class="col-sm-10">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                @error('email')
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
              <div class="col-sm-10">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                @error('password')
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">Phone </label>
              <div class="col-sm-10">
                <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Mobile Number">
              </div>
            </div>
          </div>
          <div class="d-flex">
            <button type="submit" class="btn btn-info" style="margin-left: auto;">Submit</button>
          </div>

        </form>
      </div>

    </div>
    @include('layouts/footer_new')