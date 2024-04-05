@include('layouts/header')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Chapter List</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Chapter List</li>
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
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Chapter</h3>
            </div>
            <form method="post" action="{{ url('chapter-save') }}" enctype="multipart/form-data">
              @csrf
              <!-- /.card-header -->
              <div class="card-body">
                <div class="col-md-6">
                  <label for="standard_id">Standard : </label>
                  <select class="form-control" name="standard_id" id="standard_id" onchange="getsubject()">
                    <option value=" ">Select Option</option>
                    @foreach($Standard as $stdval)
                    <option value="{{$stdval->base_id}}">{{$stdval->name .'('.$stdval->board.','.$stdval->medium.','.$stdval->stream.')'}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="subject">Subject : </label>
                  <select class="form-control" name="subject" id="subject" on>
                    <option value=" ">Select Option</option>
                  </select>
                </div>
                <br>
                <a class="btn btn-success" id="addmore">
                  <i class="fas fa-plus"></i>
                </a>
                <br>
                <div id="container">
                  <div class="col-md-6">
                    <label for="chapter_no">Chapter Number : </label>
                    <input type="text" name="chapter_no[]" id="chapter_no" class="form-control" placeholder="Enter Chapter Number">
                    @error('chapter_no')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6">
                    <label for="chapter_name">Chapter Name : </label>
                    <input type="text" name="chapter_name[]" id="chapter_name" class="form-control" placeholder="Enter Chapter Name">
                    @error('chapter_name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6">
                    <label for="chapter_image">Chapter Image : </label>
                    <input type="file" name="chapter_image[]" id="chapter_image" class="form-control" placeholder="Select Chapter Image">
                    @error('chapter_image')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <button type="submit" class="btn btn-success" style="float: right;">Submit</button>
            </form>
            <br>
            <br>
          </div>
        </div>
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Chapter</h3>
            </div>
            <table class="table table-bordered table-responsive">
              <thead>
                <tr>
                  <th style="width: 10px">No</th>
                  <th style="width: 250px">Standard</th>
                  <th>Subject</th>
                </tr>
              </thead>
              <tbody>
                @php $i=1 @endphp
                @foreach($Standards as $value)
                <tr>
                  <td>{{$i}}</td>
                  <td>{{$value->name .'('.$value->board.','.$value->medium.','.$value->stream.')'}}</td>
                  <td>
                    @foreach($subjects as $sbvalue)
                    @if($sbvalue->base_table_id == $value->base_id)
                    <div class="d-flex align-items-center">
                      {{$sbvalue->name}}
                      @canButton('edit', 'Subject')
                      <input type="submit" class="btn btn-primary editButton" data-user-id="{{ $sbvalue->id }}" value="Edit">&nbsp;&nbsp;
                      @endCanButton
                      @canButton('view', 'Subject')
                      <input type="submit" class="btn btn-primary viewButton" data-subject-id="{{ $sbvalue->id }}" data-base-id="{{ $value->base_id }}" value="View">&nbsp;&nbsp;
                      @endCanButton
                      @canButton('delete', 'Subject')
                      <input type="submit" class="btn btn-danger deletebutton" data-user-id="{{ $sbvalue->id }}" value="Delete">
                      @endCanButton
                    </div>
                    <br>
                    @endif
                    @endforeach
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
        <!--  -->
      </div>
    </div>
</div>
</section>

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
        <table class="table table-bordered table-responsive">
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
          '<div class="col-md-6">' +
          '<label for="chapter_no">Chapter Number : </label>' +
          '<input type="text" name="chapter_no[]" id="chapter_no" class="form-control" placeholder="Enter Chapter Number">' +
          '@error('
          chapter_no + x ')' +
          '<div class="text-danger">{{ $message }}</div>' +
          '@enderror' +
          '</div>' +
          '<div class="col-md-6">' +
          '<label for="chapter_name">Chapter Name : </label>' +
          '<input type="text" name="chapter_name[]" id="chapter_name" class="form-control" placeholder="Enter Chapter Name">' +
          '@error('
          chapter_name + x ')' +
          '<div class="text-danger">{{ $message }}</div>' +
          '@enderror' +
          '</div>' +

          '<div class="col-md-6">' +
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
@include('layouts/footer ')