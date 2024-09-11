</head>

<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">Change Password</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Admin</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('change-password')}}" class="active-link-dir">Change Password</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content">

        <!-- /.card-header -->
        <!-- form start -->
        <div class="row">
          <div class="col-lg-8">
            <div class="institute-form">
            <form method="post" action="{{ url('change-password-save') }}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <label for="old_password">Old Password:</label>
            <input type="password" name="old_password" class="form-control" placeholder="Old password" value="{{ old('old_password') }}">
            @error('old_password')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-12">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" class="form-control" placeholder="New password" value="{{ old('new_password') }}">
            @error('new_password')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-12">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" value="{{ old('confirm_password') }}">
            @error('confirm_password')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-12 mt-3">
        <button type="submit" class="btn text-white btn-rmv2 mt-3 ">Submit</button>
        </div>
    </div>
</form>

                
            </div>
          </div>

          <!-- list -->
         
        </div>


    

      </div>
    </div>
    @include('layouts/footer_new')
  </div>
</body>
