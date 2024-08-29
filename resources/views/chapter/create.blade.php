<body>
    <div class="dashboard">
        @include('layouts/header-sidebar')
        <div class="dashboard-app">
            @include('layouts/header-topbar')
            <div class="link-dir">
                <h1 class="display-4">Create Chapter List</h1>
                <ul>
                    <li><a href="{{url('dashboard')}}">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)" class="active-link-dir">Chapter</a></li>
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
                        <div class="row">
                        <div class="col-lg-6" id="table_chapter"  style="display: none;">
                            <table border="2" style="padding: 10px;" class="table table-responsive-sm table-bordered institute-table mt-4" >
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 50%">Chapter No</th>
                                        <th>Chapter Name</th>
                                    </tr>
                                </thead>
                                <tbody id="chapter_table_body" class="text-center">
                                    <!-- Chapters will be dynamically inserted here -->
                                </tbody>
                            </table>
                        </div>
                        </div>
                        <h3>Chapter Number</h3>
                        <div class="border-line-chapter">
                            <div class="search-box-2 form-group">
                                <input type="search" name="chapter_no[]" placeholder="Chapter Number" class="form-control">
                            </div>
                            @error('chapter_no.*')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <h3>Chapter Name</h3>
                            <div class="search-box-2 form-group">
                                <input type="search" name="chapter_name[]" placeholder="Chapter Name" class="form-control" multiple>
                            </div>
                            @error('chapter_name.*')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <h3>Chapter Image</h3>
                            <div class="search-box-2 form-group">
                                <input class="py-2 pl-2" type="file" name="chapter_image[]" onchange='openFile(event,"output")'>
                            </div>
                            @error('chapter_image.*')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <img id="output" src="" class="img-resize" style="display: none;" />
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
            $('#subject').on('change', function() {
                var subject_id = $(this).val();
                var standard_id = $('#standard_id').val();

                if (subject_id && standard_id) {
                    axios.post('chapter/get-chapters', {
                            standard_id: standard_id,
                            subject_id: subject_id,
                        })
                        .then(function(response) {
                        var chapterTableBody = document.getElementById('chapter_table_body');
                        var table_chapter = document.getElementById('table_chapter');
                        table_chapter.style.display = 'block';
                        chapterTableBody.innerHTML = ''; // Clear existing rows

                        response.data.chapters.forEach(function(chapter) {
                            var row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${chapter.chapter_no}</td>
                                <td>${chapter.chapter_name}</td>
                            `;
                            chapterTableBody.appendChild(row);
                        });
                    })
                    .catch(function(error) {
                        console.error('Error fetching chapters:', error);
                    });
                } else {
                    // Clear the chapters dropdown if no subject is selected
                    document.getElementById('chapter').innerHTML = '<option value="">Select Chapter</option>';
                }
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
                                <img id="output${chapterCount}" src="" class="img-resize" style="display:none"/>
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