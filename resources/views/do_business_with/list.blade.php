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
        <h1 class="display-4">Do Business With</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Institute</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('class-list')}}" class="active-link-dir">Do Business With</a></li>
        </ul>
      </div>

      <script>
        window.setTimeout(function() {
          $(".alert-success").slideUp(500, function() {
            $(this).remove();
          });
        }, 3000);
      </script>
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
        <div class="row">

          <div class="col-md-5">
            <div class="institute-form">
              <form method="post" action="{{ url('do-business-with/save') }}">
                @csrf
                <div class="card-body">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-12">
                        <label for="exampleInputEmail1">Name : </label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Name">
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="col-md-6">
                        <label for="exampleInputEmail1">Category : </label>
                        <select class="form-control" name="category" id="category">
                          <option value=" ">Select Option</option>
                          @foreach($categories as $catval)
                          <option value="{{$catval->id}}">{{$catval->name}}</option>
                          @endforeach

                        </select>
                        @error('category')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-6">
                        <label for="exampleInputEmail1">status : </label>
                        <select class="form-control" name="status">
                          <option value=" ">Select Option</option>
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
                        </select>
                        @error('status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>

                    </div>

                  </div>
                </div>
                <div class="d-flex">
                  <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
                </div>



              </form>
            </div>
          </div>
          <div class="col-md-7">
            <div class="institute-form">
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-responsive">
                  <thead>
                    <tr>
                      <th style="width: 10px">
                        <Sr class="No">No</Sr>
                      </th>
                      <th style="width: 200px">Name</th>
                      <th style="width: 200px">Category</th>
                      <th style="width: 500px">Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $i=1 @endphp
                    @foreach($do_business_with as $value)
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{$value->name}}</td>
                      <td>{{$value->category}}</td>
                      <td>@if($value->status == 'active')
                        <input type="button" value="Active" class="btn btn-success">
                        @else
                        <input type="button" value="Inactive" class="btn btn-danger">

                        @endif
                      </td>

                      <td>
                        <div class="d-flex">
                          @canButton('edit', 'Do_business_with')
                          <input type="submit" class="btn btn-primary editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                          @endCanButton
                          &nbsp;&nbsp;
                          @canButton('delete', 'Do_business_with')
                          <input type="submit" class="btn btn-danger deletebutton" data-user-id="{{ $value->id }}" value="Delete">
                          @endCanButton
                        </div>
                    </tr>
                    @php $i++ @endphp
                    @endforeach
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-end">
                {!! $do_business_with->withQueryString()->links('pagination::bootstrap-5') !!}

              </div>
            </div>

          </div>

        </div>
      </div>
      @include('layouts/footer_new')
      <div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="usereditModalLabel">Edit Do Business With </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="post" action="{{ url('do-business-with/update') }}">
                @csrf
                <div class="card-body">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-12">
                        <input type="hidden" id="id" name="id">
                        <label for="exampleInputEmail1">Name : </label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-6">
                        <label for="exampleInputEmail1">Category : </label>
                        <select class="form-control" name="category" id="category">
                          <option value=" ">Select Option</option>
                          @foreach($category as $catval)
                          <option value="{{$catval->id}}">{{$catval->name}}</option>
                          @endforeach

                        </select>
                        @error('category')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-12">
                        <label for="exampleInputEmail1">status : </label>
                        <select class="form-control" name="status" id="status">
                          <option value=" ">Select Option</option>
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
                        </select>
                        @error('status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>

                    </div>

                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" style="float: right;">Update</button>
                </div>
            </div>
          </div>
          </form>
        </div>

      </div>
    </div>
  </div>
  <script>
    document.querySelectorAll('.editButton').forEach(function(button) {
      button.addEventListener('click', function() {
        var id = this.getAttribute('data-user-id');

        axios.post('/do-business-with/edit', {
            id: id
          })
          .then(response => {
            var reponse_data = response.data.Dobusinesswith_Model;

            $('#id').val(reponse_data.id);
            $('#name').val(reponse_data.name);
            $('#category').val(reponse_data.category_id);
            $('#status').val(reponse_data.status);

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

        var id = this.getAttribute('data-user-id');

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
            axios.post('/do-business-with/delete', {
                id: id
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