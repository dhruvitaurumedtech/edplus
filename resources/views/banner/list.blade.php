<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">Banner List</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Banner</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('institute-list')}}" class="active-link-dir">Create Banner</a></li>
        </ul>
      </div>
      @include('layouts/alert')
      <div class="dashboard-content side-content">
        <div class="dashboard-content side-content">
          <div class="row">
            <div class="col-lg-6">
              <div class="institute-form">
                <h3>Banner create</h3>
                <form method="post" action="{{ url('banner/save') }}" enctype="multipart/form-data">
                  @csrf
                  @if(auth()->user()->role_type == '3')
                  <label for="exampleInputEmail1">Select Institute : </label>
                  <select name="institute_id" class="form-control">
                    <option value="">Select option</option>
                    @foreach($institute_list as $value)
                    <option value="{{ $value->id }}" {{ $value->id == old('institute_id') ? 'selected' : '' }}>
                      {{ $value->institute_name }}
                    </option>
                    @endforeach
                  </select>
                  @error('banner_image')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                  <label for="exampleInputEmail1">Url : </label>
                  <input type="text" name="url" class="form-control" placeholder="Enter url" value="{{url('url')}}">
                  @error('url')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                  @endif
                  <div class="row">
                    <div class="col-md-8">
                      <label for="banner_image">Banner Image:</label>
                      <input type="file" id="banner_image" name="banner_image[]" class="form-control" multiple>
                      @error('banner_image')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-4">
                      <div id="imagePreviewContainer"></div>
                    </div>
                  </div>
                  <label for="exampleInputEmail1">status : </label>
                  <select class="form-control" name="status">
                    <option value=" ">Select Option</option>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                  </select>
                  @error('status')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                  <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn text-white blue-button" style="float: right;">Submit</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="col-lg-6">

              <div class="">
                <div class="institute-form">
                  <h3>Banner List</h3>

                  <form action="#">
                    <div class="search-box">
                      <input type="search" class="form-control myInput" name="search" placeholder="Search">
                      <i class="fas fa-search"></i>
                    </div>
                  </form> <br>
                  <table class="table table-js table-bordered">
                    <thead>
                      <tr>
                        <th>
                          <Sr class="No">No</Sr>
                        </th>
                        @if(auth()->user()->role_type == '3')
                        <th>
                          <Sr class="No">Institute Name</Sr>
                        </th>
                        <th>
                          <Sr class="No">Url</Sr>
                        </th>
                        @endif
                        <th>Banner_image</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody class="myTable">
                      @php $i=1 @endphp
                      @foreach($banner_list as $value)
                      <tr>
                        <td>{{$i}}</td>
                        @if(auth()->user()->role_type == '3')
                        <td>{{$value->institute_name}}</td>
                        <td>{{$value->url}}</td>
                        @endif
                        <td>
                            <img src="{{ !empty($value->banner_image) ? asset($value->banner_image) : asset('no-image.png') }}" 
                                alt="{{ !empty($value->banner_image) ? 'Banner image' : 'No image available' }}" 
                                class="img-resize" >
                        </td>
                        <td>@if($value->status == 'active')
                          <input type="button" value="Active" class="btn btn-success">
                          @else
                          <input type="button" value="Inactive" class="btn btn-danger">
                          @endif
                        </td>
                        <td>
                          <div class="d-flex">
                            <input type="submit" class="btn text-white blue-button banner_editButton" data-user-id="{{ $value->id }}" value="Edit">&nbsp;&nbsp;
                            &nbsp;&nbsp;
                            <input type="submit" class="btn btn-danger banner_deletebutton" data-user-id="{{ $value->id }}" value="Delete">
                          </div>
                      </tr>
                      @php $i++ @endphp
                      @endforeach
                    </tbody>
                  </table>

                  <div class="d-flex justify-content-end">
                    {!! $banner_list->withQueryString()->links('pagination::bootstrap-5') !!}
                  </div>
                </div>

              </div>
            </div>

          </div>
          <div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="usereditModalLabel">Edit Banner </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form method="post" action="{{ url('banner/update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                      <div class="form-group">
                        <div class="row">

                          <div class="col-md-9">
                            <input type="hidden" id="banner_id" name="banner_id">
                            <label for="exampleInputEmail1">Banner Image : </label>
                            <input type="hidden" name="old_banner_image" id="old_banner_image">
                            <input type="file" onchange="previewFile()" name="banner_image" class="form-control">
                            @error('icon')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                          </div>
                          <div class="col-md-3">
                            <img src="" id="banner_image" alt="banner" class="img-resize mt-3">
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
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn text-white blue-button" style="float: right;">Update</button>
                    </div>
                </div>
              </div>
              </form>
            </div>

          </div>

        </div>

      </div>
      @include('layouts/footer_new')
      <script>
        function previewFile() {
          const preview = document.getElementById("banner_image");
          const fileInput = document.querySelector("input[type=file]");
          const file = fileInput.files[0];
          const reader = new FileReader();

          reader.addEventListener("load", () => {
            preview.src = reader.result;
          }, false);

          if (file) {
            reader.readAsDataURL(file);
          }
        }
      </script>

    </div>
  </div>
</body>

</html>
<script>
  $(document).ready(function() {
    // Function to handle image preview
    function readURL(input, container) {
      if (input.files && input.files.length > 0) {
        for (var i = 0; i < input.files.length; i++) {
          var reader = new FileReader();

          reader.onload = function(e) {
            var img = $('<img>').attr('src', e.target.result);
            $(container).append(img);
          }

          reader.readAsDataURL(input.files[i]); // Read the selected file as a URL
        }
      }
    }

    // Attach change event listener to the file input element
    $('#banner_image').change(function() {
      $('#imagePreviewContainer').empty(); // Clear previous previews
      readURL(this, '#imagePreviewContainer'); // Call readURL function when file input changes
    });
  });
</script>