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
                    <!-- general form elements -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Create Subject</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ url('subject-save') }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="row">
                                    <div class="col-md-12">
                                    <label for="exampleInputEmail1">Institute For : </label>
                                        <br>
                                        @foreach($institute_for as $insval)
                                        <input type="radio" name="institute_for" value="{{$insval->id}}">{{$insval->name}} &nbsp;
                                       @endforeach
                                        @error('institute_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                    <label for="exampleInputEmail1">Board : </label>
                                        <br>
                                        @foreach($board as $insval)
                                        <input type="radio" name="board" value="{{$insval->id}}">{{$insval->name}} &nbsp;
                                       @endforeach
                                        @error('board')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                    <label for="exampleInputEmail1">Medium : </label>
                                        <br>
                                        @foreach($medium as $insval)
                                        <input type="radio" name="medium" value="{{$insval->id}}">{{$insval->name}} &nbsp;
                                       @endforeach
                                        @error('medium')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                    <label for="exampleInputEmail1">Institute For Class : </label>
                                        <br>
                                        @foreach($class as $insval)
                                        <input type="radio" name="institute_for_class" value="{{$insval->id}}">{{$insval->name}} &nbsp;
                                       @endforeach
                                        @error('institute_for_class')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                        
                                    <div class="col-md-12">
                                        <label for="exampleInputEmail1">Select Standard : </label>
                                        <select class="form-control" name="standard">
                                            <option value=" ">Select Option</option>
                                            @foreach($standard as $insval)
                                                <option value="{{$insval->id}}">{{$insval->name}}</option>
                                            @endforeach        
                                        </select>
                                        @error('standard')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="exampleInputEmail1">Stream (optional) : </label>
                                        <select class="form-control" name="stream">
                                            <option value=" ">Select Option</option>
                                            @foreach($stream as $insval)
                                                <option value="{{$insval->id}}">{{$insval->name}}</option>
                                            @endforeach        
                                        </select>
                                        @error('stream')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                        <br>
                                        <div class="col-md-12">
                                        <div class="col-md-3" id="container">
                                                <label for="exampleInputEmail1">Subject Name  : 
                                                    <a class="btn btn-success" id="addmore">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                </label>
                                                    
                                                <input type="text" name="subject[]" id="subject" class="form-control" placeholder="Enter Subject Name">
                                                <a class="btn btn-success" id="delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>   
                                                @error('subject')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>    
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
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success" style="float: right;">Submit</button>
                            </div>
                    </div>
                </div>

        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Subject List</h3>
            </div>
            <div class="card-body">
            <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 10px"><Sr class="No">No</Sr></th>
                    <th style="width: 200px">Standard</th>
                    <th style="width: 200px">Board/Medium</th>
                    <th style="width: 200px">Stream</th>
                    <th style="width: 200px">Subjects</th>
                    <th style="width: 500px">Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                @php $i=1 @endphp
                @foreach($addsubstandard as $value)
                
                
                <tr>
                <td>{{$i}}</td>
                <td>{{$value->name}}</td>
                <td>{{ $value->board.'-'.$value->medium}}</td>
                <td>{{$value->sname}}</td>
                <td>
                  @foreach($subject_list as $subvalue)
                  @if($value->base_id == $subvalue->baset_id)
                  {{$subvalue->name}}<br>
                  @endif
                  
                  @endforeach
                </td>
                
                <td>{{$value->status}}</td>
                </tr>
                
                @php $i++ @endphp
               
                @endforeach
                </tbody>
              </table>
            </div>  
            <div class="d-flex justify-content-end">
            {!! $addsubstandard->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
            </div> 
            </div>
            </section>
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
    
    $(document).ready(function () {
        
        $('#standard_id').on('change', function () {
            var standard_id = $(this).val();
            axios.post('/get/standard_wise_stream', {
                    standard_id: standard_id,
                })
                .then(function (response) {
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
                    }else{
                        $('#secondDropdown2').hide();
                        $('#streamlabel').hide();
                    }

                })
                .catch(function (error) {
                    console.error(error);
                });

            
        });
    });
    
    //add more

$(document).ready(function(){
  var maxFields = 10; // Maximum number of input fields
  var addButton = $('#addmore'); // Add button selector
  var container = $('#container'); // Container selector

  var x = 1; // Initial input field counter

  // Triggered on click of add button
  $(addButton).click(function(){
    // Check maximum number of input fields
    if(x < maxFields){ 
      x++; // Increment field counter
      // Add input field
      $(container).append('<input type="text" name="subject[]" class="form-control" placeholder="Enter Subject Name"/><a class="btn btn-success" id="delete"><i class="fas fa-trash"></i></a>');
    } else{
      alert('Maximum '+maxFields+' input fields allowed.'); // Alert when maximum is reached
    }
  });
});
</script>
@include('layouts/footer ')