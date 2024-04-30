</head>

<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">Admin</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
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
      @include('layouts/alert')
      <!-- Main content -->
      <div class="dashboard-content side-content">
        <form class="s-chapter-form institute-form" method="post" id="myForm" action="{{ url('store/admin') }}">
          @csrf
          <h4 class="mb-3">Create Admin</h4>
          <div class="card-body">
            <div class="form-group row">
              <label for="inputPassword3" class="col-sm-2 col-form-label">Select Role</label>
              <div class="col-sm-10">
                <select class="form-control" name="role_type" id="role_type">
                  <option value="">Select Role</option>
                  <option value="2" {{ old('role_type') == '2' ? 'selected' : '' }}>Admin</option>
                  <option value="3" {{ old('role_type') == '3' ? 'selected' : '' }}>Institute</option>
                </select>
                @error('role_type')
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">firstname</label>
              <div class="col-sm-10">
                <input type="text" id="firstname" name="firstname" class="form-control" placeholder="first name" value="{{old('firstname')}}">
                @error('firstname')
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">lastname</label>
              <div class="col-sm-10">
                <input type="text" id="lastname" name="lastname" class="form-control" placeholder="last name" value="{{old('lastname')}}">
                @error('lastname')
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
              <div class="col-sm-10">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{old('email')}}">
                @error('email')
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
              <div class="col-sm-10">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" value="{{old('password')}}">
                @error('password')
                <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-2 col-form-label">Phone </label>
              <div class="col-sm-10">
                <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Mobile Number" value="{{old('mobile')}}">
              </div>
            </div>
          </div>
          <div class="d-flex">
            <button type="submit" class="btn text-white btn-rmv2" style="margin-left: auto;">Submit</button>
          </div>

        </form>
      </div>

    </div>

    @include('layouts/footer_new')

  </div>
</body>