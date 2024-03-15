@include('layouts/header')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Institute</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Institute</li>
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
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Create Institute</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ url('institute/register') }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Name of Institute : </label>
                                            <input type="text" name="institute_name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name of Institute">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Email address : </label>
                                            <input type="email" name="email" class="form-control" placeholder="Email address">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Contact No : </label>
                                            <input type="text" name="contact_no" class="form-control" placeholder="Contact_no">
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Address : </label>
                                            <textarea name="address" class="form-control" placeholder="Address"></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1">Institute : </label>
                                            @foreach($institute_for_array as $index => $value)
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="school_{{$value->institute_for_id}}"  name="institute_for_id[]" value="{{ $value->institute_for_id }}" onchange="handleCheckboxChange(this.id)">
                                                <label for="school_{{$value->institute_for_id}}" class="custom-control-label">{{ $value->institute_for_name }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="col-md-4">
                                        <div id="checkboxContainer"></div>

                                        </div>
                                      
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </section>
</div>
<script>
    function handleCheckboxChange(checkboxId) {
        var checkbox = document.getElementById(checkboxId);
       
        if(checkbox.checked)
        {
                var checkboxId = checkbox.id;
                let institute_for_id = checkboxId.replace('school_', '');
                // alert(institute_for_id);

                axios.post('{{ url('institute/get-board') }}', {
                        institute_for_id: institute_for_id
                    })
                    .then(response => {
                        var container = document.getElementById('checkboxContainer');

                        var response_data = response.data.board_list;
                        response_data.forEach(function(value) {
                        
                        var checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.id = value.id;
                        checkbox.value = value.name;
                        checkbox.name = 'board[]';
                        checkbox.className = 'custom-control-input';
                        checkbox.addEventListener('change', handleCheckboxChange);

                        var label = document.createElement('label');
                        label.setAttribute('for', 'Board');
                        label.className = 'custom-control-label';
                        label.textContent = 'Board';

                        var div = document.createElement('div');
                        div.className = 'custom-control custom-checkbox';

                        div.appendChild(checkbox);
                        div.appendChild(label);

                        container.appendChild(div);
                        });
                            
                    })
                    .catch(error => {
                        console.error(error);
                    });
                }else{
                    checkbox.style.display = "none";

                }
       
     }
</script>
@include('layouts/footer')