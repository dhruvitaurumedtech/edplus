<body>
    <div class="dashboard">
        @include('layouts/header-sidebar')
        <div class="dashboard-app">
            @include('layouts/header-topbar')
            <div class="link-dir">
                <h1 class="display-4">Edit Chapter List</h1>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">Base Table</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="chapter-list.php">Chapter</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)" class="active-link-dir">Create Chapter List</a></li>
                </ul>
            </div>
            <div class="dashboard-content side-content">
                <a href="{{url('chapter-list')}}" class="btn text-white btn-rmv2"> Chapter List</a>
                @foreach($Standard as $Standard_value)
                <form class="s-chapter-form" method="post" action="{{ url('chapter/update') }}" enctype="multipart/form-data">
                    <input type="hidden" name="chapter_id" value="{{$Standard_value->chapter_id}}">
                    @csrf
                    <div class="institute-list">
                        <h3>Select Standard</h3>
                        <div class="form-group">
                            <select class="form-control" id="standard_id" name="standard_id">
                                <option value=" ">Select Option</option>
                                @foreach($Standard_list as $value)
                                <option value="{{$value->base_id}}" {{($value->base_id==$Standard_value->base_id)?'selected':''}}>{{$value->name .'('.$value->board.','.$value->medium.','.$value->stream.')'}}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('standard_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <h3>Subject</h3>
                        <div class="form-group">
                            <select class="form-control" id="subject" name="subject" placeholder="Subject Name">
                                <option value=" ">Select Option</option>
                                @foreach($subject as $subject_value)
                                <option value="{{$subject_value->id}}" {{($subject_value->id == $Standard_value->subject_id)?'selected':''}}>{{$subject_value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('subject')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <h3>Chapter Number</h3>
                        <div class="border-line-chapter">
                            <div class="search-box-2 form-group">
                                <input type="search" name="chapter_no[]" placeholder="Chapter Number" class="form-control" value="{{$Standard_value->chapter_no}}">
                            </div>
                            <h3>Chapter Name</h3>
                            <div class="search-box-2 form-group">
                                <input type="search" name="chapter_name[]" placeholder="Chapter Name" class="form-control" multiple value="{{$Standard_value->chapter_name}}">
                            </div>
                            <h3>Chapter Image</h3>
                            <div class="search-box-2 form-group">
                                <input class="py-2 pl-2" type="file" name="chapter_image[]" onchange='openFile(event,"output")'>
                                <input type="hidden" value="{{$Standard_value->chapter_image}}" name="old_chapter_image[]">
                            </div>
                            <img id="output" src="{{url($Standard_value->chapter_image)}}" class="img-resize" />
                        </div>
                        <div class="submit-btn">
                            <input type="submit" value="Submit" class="btn bg-primary-btn text-white mt-4">
                        </div>
                </form>
                @endforeach
            </div>
        </div>
        @include('layouts/footer_new')
    </div>
    <script>
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

            $(document).ready(function() {
                $('#standard_id').on('change', function() {
                    var baseUrl = $('meta[name="base-url"]').attr('content');
                    var standard_id = $(this).val();
                    axios.post(baseUrl + 'chapter/get-subject', {
                            standard_id: standard_id,
                        })
                        .then(function(response) {
                            var secondDropdown = document.getElementById('subject');
                            secondDropdown.innerHTML = ''; // Clear existing options
                            secondDropdown.appendChild(new Option('Select Subject', ''));
                            response.data.subject.forEach(function(subjects) {
                                var option = new Option(subjects.name, subjects.id);
                                secondDropdown.appendChild(option);
                            });
                        })
                        .catch(function(error) {
                            console.error(error);
                        });
                });
            });
            var container = document.querySelector('.add-chapter-btn');
            var chapterCount = 0;


        });
    </script>
</body>

</html>