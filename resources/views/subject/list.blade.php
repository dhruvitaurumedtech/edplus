<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">subject List</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('subject-list')}}" class="active-link-dir">subject</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content ">
        <div class="col-lg-12 institute-form">
          <div class="create-title-btn">
            <h4 class="mb-0">Subject List</h4>
            <div class="inner-list-search">
              <input type="search" class="form-control myInput" name="search" placeholder="Search">
              <a href="{{url('create/subject')}}" class="btn text-white btn-rmv2">Create Subject</a>

            </div>
          </div>

          <table class="table table-js table-responsive-sm table-responsive table-bordered institute-table mt-4">
            <thead>
              <tr>
                <th style="width: 10px">
                  <Sr class="No">No</Sr>
                </th>
                <th style="width: 200px">Standard</th>
                <th style="width: 200px">Board/Medium</th>
                <th style="width: 200px">Stream</th>
                <th style="width: 200px">Subjects</th>
                <th style="width: 500px">Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody class="myTable">
              @php $i=1 @endphp
              @foreach($addsubstandard as $value)
              <tr>
                <td>{{$i}}</td>
                <td>{{$value->standard}}</td>
                <td>{{$value->board.'-'.$value->medium}}</td>
                <td>{{($value->sname)? $value->sname : 'empty'}}</td>
                <td>
                  @foreach($subject_list as $subvalue)
                  @if($value->base_id == $subvalue->baset_id)
                  {{$subvalue->name}}<br>
                  @endif
                  @endforeach
                </td>
                <td>
                  @if($value->status == 'active')
                  <button type="button" class="btn btn-success">Active</button>
                  @else
                  <button type="button" class="btn btn-secondary">Inactive</button>
                  @endif
                </td>
                <td>
                  <div class="d-flex">
                    <a href="{{url('subject/edit/'.$value->base_id)}}" class="btn text-white blue-button" data-base-id="{{ $value->base_id }}" value="">Edit</a>&nbsp;&nbsp;
                    &nbsp;&nbsp;
                   <a href="{{url('subject/delete/'.$value->base_id)}}" class="btn text-white btn-danger" data-base-id="{{ $value->base_id }}" value="">Delete</a>
                  </div>
                </td>
              </tr>
              @php $i++ @endphp
              @endforeach
            </tbody>
          </table>
          <div class="d-flex justify-content-end">
            {!! $addsubstandard->withQueryString()->links('pagination::bootstrap-5') !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('layouts/footer_new')
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script>
    document.querySelectorAll('.editButton').forEach(function(button) {
      button.addEventListener('click', function() {
        var subject_id = this.getAttribute('data-user-id');
        axios.post('/subject/edit', {
            subject_id: subject_id
          })
          .then(response => {
            var reponse_data = response.data.subjectlist;
            $('#subject_id').val(reponse_data.id);
            $('#standard_id').val(reponse_data.standard_id);
            $('#name').val(reponse_data.name);
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

        var subject_id = this.getAttribute('data-user-id');

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
            axios.post('/subject/delete', {
                subject_id: subject_id
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