</head>

<body>
  <div class="dashboard">
    @include('layouts/header-sidebar')
    <div class="dashboard-app">
      @include('layouts/header-topbar')
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
        <div class="col-lg-12 institute-form">
          <div class="create-title-btn">
            <h4 class="card-title">Chapter</h4>
            <div class="inner-list-search">
              <input type="search" class="form-control myInput" name="search" placeholder="Search">
              @canButton('add', 'List institute')
              <a href="{{url('create/topic')}}" class="btn text-white btn-rmv2" style="float: right;">Create Topic</a>
              @endCanButton
            </div>
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