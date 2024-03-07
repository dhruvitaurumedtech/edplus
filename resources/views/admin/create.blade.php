@include('layouts/header')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Admin</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Admin</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="row">
        <div class="col-md-8 offset-md-2">
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
    <script>

function clearFormData() {
        document.getElementById("myForm").reset();
    }
    </script>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-info" >
              <div class="card-header">
                <h3 class="card-title">Create Admin</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form class="form-horizontal" method="post" id="myForm" action="{{ url('store/admin') }}">
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
                      <input type="text" id="firstname" name="firstname" class="form-control"   placeholder="Name">
                    @error('firstname')
                     <div class="text-danger">{{ $message }}</div>
                    @enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">lastname</label>
                    <div class="col-sm-10">
                      <input type="text" id="lastname" name="lastname" class="form-control"   placeholder="Name">
                    @error('lastname')
                     <div class="text-danger">{{ $message }}</div>
                    @enderror
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control"  id="email" name="email" placeholder="Email">
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
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                      <input type="text" id="mobile" name="mobile" class="form-control"   placeholder="Mobile Number">
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-info">Add</button>
                  <button type="submit" class="btn btn-default float-right" onclick="clearFormData()">Cancel</button>
                </div>
                <!-- /.card-footer -->
              </form>
              <!-- ........ -->
            </div>
            <!-- /.card -->

            <!-- general form elements -->
          
            <!-- /.card -->

            <!-- Input addon -->
            <!-- /.card -->
            <!-- Horizontal Form -->
          
            <!-- /.card -->

          </div>
          <!--/.col (left) -->
          <!-- right column -->
         
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
</div>
@include('layouts/footer')
