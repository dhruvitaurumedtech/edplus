<div class="dashboard">

  @include('layouts/header-sidebar')

  <!-- MAIN -->
  <div class="dashboard-app">

    @include('layouts/header-topbar')

    <!-- Sub MAIN -->
    <div class="link-dir">
      <h1 class="display-4">Student list</h1>
      <ul>
        <li><a href="{{url('dashboard')}}">Home</a></li>
        <li><a href="javascript:void(0)">/</a></li>
        <li><a href="javascript:void(0)">Institute</a></li>
        <li><a href="javascript:void(0)">/</a></li>
        <li><a href="javascript:void(0)">List Institute</a></li>
        <li><a href="javascript:void(0)">/</a></li>

        <li><a href="javascript:void(0)" class="active-link-dir">Student list</a></li>
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
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-9 offset-md-1">

            <!-- Profile Image -->
            @foreach($student as $vlu)
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <!-- <img class="profile-user-img img-fluid img-circle" src="{{ url($vlu->image) }}" alt="User profile picture"> -->
                </div>

                <h3 class="profile-username text-center">{{ $vlu->firstname.' '.$vlu->lastname }}</h3>




                <div class="card-primary">
                  <div class="card-body">
                    <strong><i class="fas fa-book mr-1"></i> Mobile</strong>
                    <p class="text-muted">
                      {{ $vlu->mobile }}
                    </p>

                    <hr>

                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Email</strong>

                    <p class="text-muted">{{ $vlu->email }}</p>

                    <hr>
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>

                    <p class="text-muted">{{ $vlu->address }}</p>


                  </div>
                  <!-- /.card-body -->
                </div>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
      </section>
    </div>
    @endforeach
    @include('layouts/footer_new')