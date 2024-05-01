</head>

<body>

    <div class="dashboard">

        @include('layouts/header-sidebar')

        <!-- MAIN -->
        <div class="dashboard-app">

            @include('layouts/header-topbar')

            <!-- Sub MAIN -->
            <div class="link-dir">
                <h1 class="display-4">subject List</h1>
                <ul>
                    <li><a href="{{url('dashboard')}}">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">Institute</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="{{url('class-list')}}" class="active-link-dir">subject</a></li>
                </ul>
            </div>

            @include('layouts/alert')

            <div class="dashboard-content side-content">
                <form method="post" action="{{ url('subject-save') }}" enctype="multipart/form-data" class="s-chapter-form">
                    @csrf
                    <div class="institute-list">
                        <h3>Institute For</h3>
                        @foreach($institute_for as $insval)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="institute_for" id="InstituteFor" value="{{ $insval->id }}" {{ $insval->id == old('institute_for') ? 'checked' : '' }}>
                            <label class="form-check-label" for="InstituteFor">{{ $insval->name }}</label>
                            &nbsp;
                        </div>

                        @endforeach
                        @error('institute_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <br>
                        <h3>Board</h3>
                        @foreach($board as $insval)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="board" id="board" value="{{ $insval->id }}" {{ $insval->id == old('board') ? 'checked' : '' }}>
                            <label class="form-check-label" for="board">{{ $insval->name }}</label>
                            &nbsp;
                        </div>
                        <!-- <input type="radio" name="board" value="{{$insval->id}}" {{ $insval->id == old('board') ? 'checked' : '' }}>{{$insval->name}} &nbsp; -->
                        @endforeach
                        @error('board')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror


                        <br>
                        <h3>Medium : </h3>
                        @foreach($medium as $insval)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="medium" id="medium" value="{{ $insval->id }}" {{ $insval->id == old('medium') ? 'checked' : '' }}>
                            <label class="form-check-label" for="medium">{{ $insval->name }}</label>

                        </div>
                        <!-- <input type="radio" name="medium" value="{{$insval->id}}" {{ $insval->id == old('medium') ? 'checked' : '' }}>{{$insval->name}} &nbsp; -->
                        @endforeach
                        @error('medium')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror


                        <br>
                        <h3>Institute For Class : </h3>
                        @foreach($class as $insval)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="institute_for_class" id="institute_for_class" value="{{ $insval->id }}" {{ $insval->id == old('institute_for_class') ? 'checked' : '' }}>
                            <label class="form-check-label" for="institute_for_class">{{ $insval->name }}</label>

                        </div>
                        <!-- <input type="radio" name="institute_for_class" value="{{$insval->id}}" {{ $insval->id == old('institute_for_class') ? 'checked' : '' }}>{{$insval->name}} &nbsp; -->
                        @endforeach
                        @error('institute_for_class')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <h3>Select Standard</span></h3>
                        <div class="form-group">
                            <select class="form-control" name="standard">
                                <option value=" ">Select Option</option>
                                @foreach($standard as $insval)
                                <option value="{{$insval->id}}" {{ $insval->id == old('standard') ? 'selected' : '' }}>{{$insval->name}}</option>
                                @endforeach
                            </select>
                            @error('standard')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <h3>Stream <span>(Optional)</span></h3>
                        <div class="form-group">
                            <select class="form-control" name="stream">
                                <option value=" ">Select Option</option>
                                @foreach($stream as $insval)
                                <option value="{{$insval->id}}" {{ $insval->id == old('stream') ? 'selected' : '' }}>{{$insval->name}}</option>
                                @endforeach
                            </select>
                            @error('stream')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <br>

                        <div class="border-line-subject">
                            <div class="row">
                                <div class="col-md-1 offset-md-11">
                                    <div class="f-icons">
                                        <a class="btn text-white btn-rmv2 addmore">
                                            <i class="fas fa-plus py-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <h3>Subject Name</h3>
                                    <input type="text" name="subject[]" class="form-control" placeholder="Add Subject Name">
                                </div>
                                <div class="col-md-4">
                                    <h3>Image:</h3>
                                    <input type="file" name="subject_image[]" class="form-control" onchange='openFile(event,"output")' placeholder="Select Subject Image">
                                </div>
                                <div class="col-md-2"><img src="" id='output' class="subject-img-resize mt-4" style="display: none;"></div>

                                <div class="col-md-2">
                                    <div class="f-icons"><a class="btn text-white btn-rmv2 delete"><i class="fas fa-trash py-1"></i></a></div>
                                </div>
                            </div>
                            <div id="container"></div>
                        </div>
                        @error('subject')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror

                    </div>

                    <h3>Status : </h3>
                    <select class="form-control" name="status">
                        <option value=" ">Select Option</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="col-md-12 submit-btn mt-3">
                        <button type="submit" class="btn text-white btn-rmv2" style="float: right;">Submit</button>
                    </div>
                </form>

            </div>



        </div>
        </section>
    </div>

    @include('layouts/footer_new')


    <script>
        //image preview
        var openFile = function(file, id) {
             var input = file.target;
            var reader = new FileReader();
            reader.onload = function() {
                var dataURL = reader.result;
                var output = document.getElementById(id);
                output.style.display = 'block';

                output.src = dataURL;

            };
            reader.readAsDataURL(input.files[0]);
        };

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


        // Add more 
        $(document).ready(function() {
            var maxFields = 10; // Maximum number of input fields
            var container = $('#container'); // Container selector

            $('.addmore').click(function() {
                if (container.children().length / 4 < maxFields) {
                    var id = container.children().length;
                    container.append(`
                    <div class="row">
                        <div class="col-md-4">
                            <h3>Subject Name</h3>
                            <input type="text" name="subject[]" class="form-control" placeholder="Add Subject Name">
                        </div>
                        <div class="col-md-4">
                            <h3>Image:</h3>
                            <input type="file" name="subject_image[]" class="form-control" onchange="openFile(event, 'output${id}')" placeholder="Select Subject Image">
                        </div>
                        <div class="col-md-2">
                            <img src="" id="output${id}" class="subject-img-resize mt-4" style="display: none;">
                        </div>
                        <div class="col-md-2">
                            <div class="f-icons">
                                <a class="btn text-white btn-rmv2 delete"><i class="fas fa-trash py-1"></i></a>
                            </div>
                        </div>
                    </div>
                `);
                } else {
                }
            });

            container.on('click', '.delete', function() {
                $(this).closest('.row').remove(); // Remove the parent row element
            });
        });
    </script>

</body>