@include('layouts/header')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Banner List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Banner List</li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Create Banner</h3>
                        </div>
                      <form method="post" action="{{ url('banner/save') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="row">
                                        @if(auth::user()->role_type == '3')
                                        <div class="col-md-6">
                                            <label for="exampleInputEmail1">Select Institute  : </label>
                                            <select name="institute_id" class="form-control">
                                                <option value="">Select option</option>
                                                @foreach($institute_list as $value)
                                                <option value="{{$value->id}}">{{$value->institute_name}}</option>
                                                @endforeach
                                            </select>
                                            @error('banner_image')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="exampleInputEmail1">Url  : </label>
                                            <input type="text" name="url" class="form-control" placeholder="Enter url">
                                            @error('url')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>   
                                        @endif
                                       <div class="col-md-6">
                                            <label for="exampleInputEmail1">Banner_image  : </label>
                                            <input type="file" name="banner_image[]" class="form-control" multiple>
                                            @error('banner_image')
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
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
                            </div>
                    </div>
                </div>


                </form>
            </div>

        </div>

</div>

</div>
</section>
</div>

@include('layouts/footer')