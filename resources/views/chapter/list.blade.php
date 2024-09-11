<link rel="stylesheet" href="{{asset('mayal_assets/css/bootstrap.min.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/style.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/responsive.css')}}" />
</head>

<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
      <div class="link-dir">
        <h1 class="display-4">Chapter List</h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
         <li><a href="{{url('chapter-list')}}" class="active-link-dir">Chapter List</a></li>
        </ul>
      </div>
      <script>
        window.setTimeout(function() {
          $(".alert-success").slideUp(500, function() {
            $(this).remove();
          });
        }, 3000);
      </script>
      <div class="row">
        <div class="col-md-10 offset-md-1">
          @if (session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
          @endif
        </div>
      </div>
      <div class="dashboard-content side-content">

        <div class="row">
          <!-- table -->
          <div class="col-lg-12 mt-5">
            <div class="institute-form">
              <div class="create-title-btn">
                <h4 class="mb-0">Chapter List</h4>
                <a href="{{url('add-lists')}}" class="btn text-white btn-rmv2">Create Chapter</a>
              </div>
              <table class="table table-responsive-sm table-bordered institute-table mt-4">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Standard</th>
                    <th scope="col">Subjects</th>
                    <th scope="col">chapter_no</th>
                    <th scope="col">chapter_name</th>
                    <th scope="col">chapter_image</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>

                  @php
                  $i = 1;
                  @endphp
                  
                  @foreach($Standards as $value)
                  <tr>
                    <td>{{$i}}</td>
                    <td>
                      {{$value->standard_name .'('.$value->board.','.$value->medium.','.$value->stream.')'}}
                    </td>
                    @foreach($subjects as $subject_value)
                    @if($subject_value->id == $value->subject_id)
                    <td>
                      {{$subject_value->name}}
                    </td>
                    @endif
                    @endforeach
                    <td>{{$value->chapter_no}}</td>
                    <td>{{$value->chapter_name}}</td>
                    <td><img src="{{url($value->chapter_image)}}" class="img-resize"></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="d-flex">
                          <a href="{{url('chapter/edit/'.$value->id)}}" class="btn text-white btn-rmv2" value="Edit">Edit</a>&nbsp;&nbsp;
                         
                          &nbsp;&nbsp;
                          <input type="submit" class="btn btn-danger chapter_delete" data-user-id="{{$value->id}}" value="Delete">
                        </div>
                      </div>
                    </td>
                  </tr>
                  @php $i++ @endphp
                  @endforeach
                </tbody>
              </table>
              <div class="d-flex justify-content-end">
                {!! $Standards->withQueryString()->links('pagination::bootstrap-5') !!}
              </div>
            </div>
          </div>

        </div>
      </div>


    </div>

    @include('layouts/footer_new')
  </div>
  <!-- view chapters -->
  <div class="modal fade" id="chaptersModal" tabindex="-1" aria-labelledby="chaptersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="chaptersModalLabel">Chapters</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table class="table table-js table-bordered table-responsive">
            <thead>
              <tr>
                <th>Chapter No.</th>
                <th>Chapter Name</th>
                <th>Chapter Image</th>
              </tr>
            </thead>
            <tbody id="chapterdata">

            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <!-- end chapter view -->
  <script>
    //view chapter
    document.querySelectorAll('.viewButton').forEach(function(button) {
      button.addEventListener('click', function() {
        var subject_id = this.getAttribute('data-subject-id');
        var base_id = this.getAttribute('data-base-id');

        axios.post('chapter-list', {
            subject_id: subject_id,
            base_id: base_id
          })
          .then(response => {
            var chapterdata = document.getElementById('chapterdata');
            chapterdata.innerHTML = ''; // Clear existing 

            response.data.chapters.forEach(function(chapter) {
              var tr = document.createElement('tr');

              // Create a new table data (<td>) for chapter number
              var tdChapterNo = document.createElement('td');
              tdChapterNo.textContent = chapter.chapter_no;
              tr.appendChild(tdChapterNo);
              var tdChapterName = document.createElement('td');
              tdChapterName.textContent = chapter.chapter_name;
              tr.appendChild(tdChapterName);
              var tdChapterImage = document.createElement('td');
              var img = document.createElement('img');
              img.src = '{{ asset('
              ') }}' + chapter.chapter_image;
              img.alt = chapter.chapter_name;
              img.style.width = '70px'; // Example width
              img.style.height = '70px'; // Example height
              tdChapterImage.appendChild(img);
              tr.appendChild(tdChapterImage);
              chapterdata.appendChild(tr);
            });
            $('#chaptersModal').modal('show');

          })
          .catch(error => {
            console.error(error);
          });
      });
    });
  </script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <script>
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

    //add more

    $(document).ready(function() {
      var maxFields = 10; // Maximum number of input fields
      var addButton = $('.addmore'); // Add button selector
      var container = $('#container'); // Container selector

      var x = 1; // Initial input field counter
      $(container).on('click', '#delete', function() {
        $(this).parent().remove(); // Remove the parent element containing the input fields
        x--; // Decrement the field counter
      });
      // Triggered on click of add button
      $(addButton).click(function() {
        // Check maximum number of input fields
        if (x < maxFields) {
          x++; // Increment field counter
          // Add input field
          $(container).append(
            '<div class="col-md-12">' +
            '<label for="chapter_no">Chapter Number : </label>' +
            '<input type="text" name="chapter_no[]" id="chapter_no" class="form-control" placeholder="Enter Chapter Number">' +
            '@error('
            chapter_no + x ')' +
            '<div class="text-danger">{{ $message }}</div>' +
            '@enderror' +
            '</div>' +
            '<div class="col-md-12">' +
            '<label for="chapter_name">Chapter Name : </label>' +
            '<input type="text" name="chapter_name[]" id="chapter_name" class="form-control" placeholder="Enter Chapter Name">' +
            '@error('
            chapter_name + x ')' +
            '<div class="text-danger">{{ $message }}</div>' +
            '@enderror' +
            '</div>' +

            '<div class="col-md-12">' +
            '<label for="chapter_image">Chapter Image : </label>' +
            '<input type="file" name="chapter_image[]" id="chapter_image" class="form-control" placeholder="Select Chapter Image">' +
            '@error('
            chapter_image + x ')' +
            '<div class="text-danger">{{ $message }}</div>' +
            '@enderror' +
            '</div><br><a class="btn btn-success" id="delete"><i class="fas fa-trash"></i></a>'
          );
        } else {
          alert('Maximum ' + maxFields + ' input fields allowed.'); // Alert when maximum is reached
        }
      });
    });
  </script>
</body>