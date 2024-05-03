<body>
    <div class="dashboard">
        @include('layouts/header-sidebar')
        <div class="dashboard-app">
            @include('layouts/header-topbar')
            <div class="link-dir">
                <h1 class="display-4">Create Chapter List</h1>
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
            @include('layouts/alert')
            <div class="dashboard-content side-content">
                <a href="{{url('chapter-list')}}" class="btn text-white btn-rmv2"> Chapter List</a>
                <form class="s-chapter-form" method="post" action="{{ url('chapter-save') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="institute-list">
                        <h3>Select Standard</h3>
                        <div class="form-group">
                            <select class="form-control" id="standard_id" name="standard_id">
                                <option value=" ">Select Option</option>
                                @foreach($Standard as $stdval)
                                <option value="{{$stdval->base_id}}">{{$stdval->name .'('.$stdval->board.','.$stdval->medium.','.$stdval->stream.')'}}</option>
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
                            </select>
                        </div>
                        @error('subject')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <h3>Chapter Number</h3>
                        <div class="border-line-chapter">
                            <div class="search-box-2 form-group">
                                <input type="search" name="chapter_no[]" placeholder="Chapter Number" class="form-control">
                            </div>
                            <h3>Chapter Name</h3>
                            <div class="search-box-2 form-group">
                                <input type="search" name="chapter_name[]" placeholder="Chapter Name" class="form-control" multiple>
                            </div>
                            <h3>Chapter Image</h3>
                            <div class="search-box-2 form-group">
                                <input class="py-2 pl-2" type="file" name="chapter_image[]" onchange='openFile(event,"output")'>
                            </div>
                            <img id="output" src="" class="img-resize" />
                        </div>
                        <div class="add-chapter-btn">
                            <a class="btn" id="addmore">
                                <i class="fas fa-plus"></i>
                            </a>
                            <label for="exampleInputEmail1">Add More Chapter</label>
                        </div>
                    </div>

                    <div class="submit-btn">
                        <input type="submit" value="Submit" class="btn bg-primary-btn text-white mt-4">
                    </div>

                </form>
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
            $('#addmore').click(function() {
                addChapter();
            });
            $(document).ready(function() {

                $('#standard_id').on('change', function() {
                    var standard_id = $(this).val();
                    axios.post('chapter/get-subject', {
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

            function addChapter() {
                chapterCount++;
                var chapterHtml = `
                <br>
                <div class="border-line-chapter">
                        <div class="row mt-3 added-chapter">
                            <div class="col-lg-12">
                                <i class="fas fa-times btn-rmv2 ml-3 remove-chapter"></i>
                                <h3>Chapter Number</h3>
                                <div class="search-box-2 form-group">
                                    <input type="search" name="chapter_no[]" placeholder="Chapter Number" class="form-control">
                                </div>
                                <h3>Chapter Name</h3>
                                <div class="search-box-2 form-group">
                                    <input type="search" name="chapter_name[]" placeholder="Chapter Name" class="form-control">
                                </div>
                                <h3>Chapter Image</h3>
                                <div class="search-box-2 form-group">
                                    <input class="py-2 pl-2" type="file" name="chapter_image[]" onchange="openFile(event, 'output${chapterCount}')">
                                </div>
                                <img id="output${chapterCount}" src="" class="preview1 ImgPreview" />
                            </div>
                        </div>`;
                $('.add-chapter-btn').before(chapterHtml);
                $('.remove-chapter').click(function() {
                    $(this).closest('.added-chapter').remove();
                });
                $('#imageInput').change(function() {
                    readURL(this);
                });

            }
        });
    </script>

</body>

</html>