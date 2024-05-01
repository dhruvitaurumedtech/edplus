<link rel="stylesheet" href="{{asset('mayal_assets/css/bootstrap.min.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/style.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/responsive.css')}}" />

</head>

<body>

  <div class="dashboard">

    @include('layouts/header-sidebar')

    <!-- MAIN -->
    <div class="dashboard-app">

      @include('layouts/header-topbar')

      <!-- Sub MAIN -->
      <div class="link-dir">
        <h1 class="display-4">Topic List
        </h1>
        <ul>
          <li><a href="{{url('dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="javascript:void(0)">Institute</a></li>
          <li><a href="javascript:void(0)">/</a></li>
          <li><a href="{{url('class-list')}}" class="active-link-dir">Topic List
            </a></li>
        </ul>
      </div>

      @include('layouts/alert')


      <div class="dashboard-content side-content">

        <div class="row">
          <div class="col-lg-12">
            <div class="institute-form">
              <h3 class="card-title">Topic</h3>
              <form method="post" action="{{ url('topic-save') }}" enctype="multipart/form-data">
                @csrf
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="col-md-12">
                    <label for="standard_id">Standard : </label>
                    <select class="form-control" name="standard_id" id="standard_id">
                      <option value=" ">Select Option</option>
                      @foreach($Standard as $stdval)
                      <option value="{{$stdval->base_id}}">{{$stdval->name .'('.$stdval->board.','.$stdval->medium.','.$stdval->stream.')'}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-12">
                    <label for="subject">Subject : </label>
                    <select class="form-control" name="subject" id="subject" on>
                      <option value=" ">Select Option</option>
                    </select>
                  </div>
                  <div class="col-md-12">
                    <label for="subject">chapter : </label>
                    <select class="form-control" name="chapter_id" id="chapter_id" on>
                      <option value=" ">Select Option</option>
                    </select>
                  </div>
                  <div class="col-md-12">
                    <label for="subject">Institute : </label>
                    <select class="form-control" name="institute_id" id="institute_id" on>
                      <option value="">Select Option</option>
                      @foreach($institute_list as $value)
                      <option value="{{$value->id}}">{{$value->institute_name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <br>
                  <a class="btn text-white btn-rmv2" id="addmore">
                    <i class="fas fa-plus"></i>
                  </a>
                  <br>
                  <div id="container">
                    <div class="col-md-12">
                      <label for="chapter_no">Topic no : </label>
                      <input type="text" name="topic_no[]" id="topic_no" class="form-control" placeholder="Enter Topic no">
                      @error('topic_no')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-12">
                      <label for="chapter_name">Topic Name : </label>
                      <input type="text" name="topic_name[]" id="topic_name" class="form-control" placeholder="Enter Topic Name">
                      @error('topic_name')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-12">
                      <label for="chapter_name">Video Category: </label>
                      <select class="form-control" name="video_category[]">
                        <option>Select Option</option>
                        @foreach($videolist as $value)
                        <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                      </select>
                      @error('chapter_name')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-md-12">
                      <label for="chapter_image">Video Upload: </label>
                      <input type="file" name="topic_video[]" id="topic_video" class="form-control" placeholder="Select Chapter Image" multiple>
                      @error('chapter_image')
                      <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <button type="submit" class="btn text-white btn-rmv2 mt-3" style="float: right;">Submit</button>
              </form>
              <br>
              <br>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-lg-12">
          <div class="institute-form">
            <h3 class="card-title">Chapter</h3>
            <table class="table table-js table-bordered table-responsive">
              <thead>
                <tr>
                  <th style="width: 10px">No</th>
                  <th style="width: 250px">Standard</th>
                  <th>Subject</th>
                  <th>Chapter</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @php $i=1 @endphp
                @foreach($topics as $value)
                <tr>
                  <td>{{$i}}</td>
                  <td>{{$value->name .'('.$value->board.','.$value->medium.','.$value->stream.')'}}</td>
                  <td>
                    @foreach($subjects as $sbvalue)
                    @if($sbvalue->base_table_id == $value->base_id)
                    {{$sbvalue->name}}
                    @endif
                    @endforeach
                  </td>
                  <td>
                    {{$value->chapter_name}}
                  </td>
                  <td>
                    @canButton('edit', 'Topic')
                    <input type="submit" class="btn text-white blue-button editButton" data-user-id="{{ $sbvalue->id }}" value="Edit">&nbsp;&nbsp;
                    @endCanButton
                    @canButton('view', 'Topic')
                    <input type="submit" class="btn btn-primary viewButton" data-subject-id="{{ $value->subject_id }}" data-base-id="{{ $value->base_id }}" data-chapter-id="{{$value->chapter_id}}" value="View">&nbsp;&nbsp;
                    @endCanButton
                    @canButton('delete', 'Topic')
                    <input type="submit" class="btn btn-danger deletebutton" data-user-id="{{ $sbvalue->id }}" value="Delete">
                    @endCanButton
                  </td>
                </tr>
                @php $i++ @endphp
                @endforeach
              </tbody>
            </table>

            <div class="d-flex justify-content-end">
              {!! $topics->withQueryString()->links('pagination::bootstrap-5') !!}

            </div>
          </div>
        </div>
        <!--  -->
      </div>
    </div>

  </div>
  @include('layouts/footer_new')
  </div>
</body>
<!-- view chapters -->
<div class="modal fade" id="chaptersModal" tabindex="-1" aria-labelledby="chaptersModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chaptersModalLabel">Topic List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-js table-bordered table-responsive">
          <thead>
            <tr>
              <th>Topic No.</th>
              <th>Topic Name</th>
              <th>Category</th>
            </tr>
          </thead>
          <tbody id="chapterdata">

          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>
<!-- end chapter view -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
  //view chapter
  document.querySelectorAll('.viewButton').forEach(function(button) {
    button.addEventListener('click', function() {
      var subject_id = this.getAttribute('data-subject-id');
      var base_id = this.getAttribute('data-base-id');
      var chapter_id = this.getAttribute('data-chapter-id');

      axios.post('topic-list', {
          subject_id: subject_id,
          base_id: base_id,
          chapter_id: chapter_id,
        })
        .then(response => {
          var chapterdata = document.getElementById('chapterdata');
          chapterdata.innerHTML = ''; // Clear existing 

          response.data.topic_list.forEach(function(topic) {
            var tr = document.createElement('tr');

            // Create a new table data (<td>) for chapter number
            var tdtopicno = document.createElement('td');
            tdtopicno.textContent = topic.topic_no;
            tr.appendChild(tdtopicno);
            var tdtopicname = document.createElement('td');
            tdtopicname.textContent = topic.topic_name;
            tr.appendChild(tdtopicname);
            var tdtopicname = document.createElement('td');
            tdtopicname.textContent = topic.topic_name;
            tr.appendChild(tdtopicname);
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
    $('#subject').on('change', function() {
      var subject_id = $(this).val();
      axios.post('chapter/get-chapter', {
          subject_id: subject_id,
        })
        .then(function(response) {
          // alert(response.data.chapter);

          var secondDropdown = document.getElementById('chapter_id');
          secondDropdown.innerHTML = ''; // Clear existing options

          secondDropdown.appendChild(new Option('Select Subject', ''));

          response.data.chapter.forEach(function(chapters) {
            var option = new Option(chapters.chapter_name, chapters.id);
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
    var addButton = $('#addmore'); // Add button selector
    var container = $('#container'); // Container selector

    var x = 1; // Initial input field counter

    // Triggered on click of add button
    $(addButton).click(function() {
      // Check maximum number of input fields
      if (x < maxFields) {
        x++; // Increment field counter
        // Add input field
        $(container).append(
          '<div class="row">' +
          '<div class="col-md-12 mt-5">' +
          '<a class="btn text-white btn-rmv2 mb-2 delete"><i class="fas fa-trash"></i></a>' +
          '<label for="chapter_no">Topic No : </label>' +
          '<input type="text" name="topic_no[]" class="form-control" placeholder="Enter Topic No">' +
          '</div>' +
          '<div class="col-md-12">' +
          '<label for="chapter_name">Topic Name : </label>' +
          '<input type="text" name="topic_name[]" class="form-control" placeholder="Enter Topic Name">' +
          '</div>' +
          '<div class="col-md-12">' +
          '<label for="chapter_name">Video Category : </label>' +
          '<select class="form-control" name="video_category[]">' +
          '<option>Select Option</option>' +
          '<?php foreach ($videolist as $value) : ?>' +
          '<option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>' +
          '<?php endforeach; ?>' +
          '</select>' +
          '</div>' +
          '<div class="col-md-12">' +
          '<label for="chapter_image">Video Upload : </label>' +
          '<input type="file" name="topic_video[]" class="form-control" placeholder="Select Video Upload" multiple>' +
          '</div>' +
          '</div>'
        );
      } else {
        alert('Maximum ' + maxFields + ' input fields allowed.'); // Alert when maximum is reached
      }
    });

    // Triggered on click of delete button
    $(document).on('click', '.delete', function() {
      $(this).closest('.row').remove(); // Remove the closest row
      x--; // Decrement field counter
    });
  });
</script>