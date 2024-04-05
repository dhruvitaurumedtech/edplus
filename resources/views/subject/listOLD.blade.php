@include('layouts/header')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Subject List</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Subject List</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->
  @include('alert')
  <!-- Main content -->

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Subject List</h3>
              @canButton('add', 'Subject')
              <a href="{{url('create/subject-list')}}" class="btn btn-success" style="float: right;">Create subject </a>
              @endCanButton
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table class="table table-bordered table-responsive">
                <thead>
                  <tr>
                    <th style="width: 10px">
                      <Sr class="No">No</Sr>
                    </th>
                    <th style="width: 200px">Name</th>
                    <th style="width: 200px">Standard</th>
                    <th style="width: 200px">Stream</th>
                    <th style="width: 500px">Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @php $i=1 @endphp
                  @foreach($subjectlist as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->standard_name}}</td>
                    <td>{{$value->stream_name}}</td>
                    <td>@if($value->status == 'active')
                      <input type="button" value="Active" class="btn btn-success">
                      @else
                      <input type="button" value="Inactive" class="btn btn-danger">

                      @endif
                    </td>

                    <td>
                      <div class="d-flex">
                        @canButton('edit', 'Subject')
                        <input type="submit" class="btn btn-primary editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                        @endCanButton &nbsp;&nbsp;
                        @canButton('delete', 'Subject')
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
              {!! $subjectlist->withQueryString()->links('pagination::bootstrap-5') !!}

            </div>
          </div>

        </div>
  </section>

</div>
<div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usereditModalLabel">Edit Stream </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{ url('subject/update') }}">
          @csrf
          <div class="card-body">
            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <label for="exampleInputEmail1">Select standard : </label>
                  <select class="form-control" name="standard_id" id="standard_id">
                    <option value=" ">Select standard</option>
                    @foreach($standardlist as $value)
                    <option value="{{$value['id']}}">{{$value['name']}}</option>
                    @endforeach
                  </select>
                  @error('board_id')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-12">
                  <label for="exampleInputEmail1" id="stream_label"> Select Stream : </label>
                  <select class="form-control" name="stream_id" id="secondDropdown2" style="display: none;">
                    <option value=" ">Select stream</option>

                  </select>
                  @error('institute_id')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-12">
                  <input type="hidden" id="subject_id" name="subject_id">
                  <label for="exampleInputEmail1">Name : </label>
                  <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                  @error('name')
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
  $(document).ready(function() {

    $('#standard_id').on('change', function() {
      var standard_id = $(this).val();
      axios.post('/get/standard_wise_stream', {
          standard_id: standard_id,
        })
        .then(function(response) {
          console.log(response.data.streamlist);
          if (response.data.streamlist && Object.keys(response.data.streamlist).length > 0) {
            $('#secondDropdown2').show();
            $('#streamlabel').show();

            var secondDropdown = document.getElementById('secondDropdown2');
            secondDropdown.innerHTML = ''; // Clear existing options

            secondDropdown.appendChild(new Option('Select stream', ''));

            response.data.streamlist.forEach(function(stream) {
              var option = new Option(stream.name, stream.id);
              secondDropdown.appendChild(option);
            });
          } else {
            $('#secondDropdown2').hide();
            $('#streamlabel').hide();
          }

        })
        .catch(function(error) {
          console.error(error);
        });


    });
  });
</script>
@include('layouts/footer ')