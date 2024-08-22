<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>e School</title>

  <!-- css  -->
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
        <h1 class="display-4">Dashboard</h1>
        <ul>
          <li><a href="{{url('/dashboard')}}" class="active-link-dir">Home</a></li>
        </ul>
      </div>

      <div class="dashboard-content side-content">
        <!-- dashboard card start  -->
        <div class="row dashboard-inner">

          <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Total Institute</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      {{ $institute_count }}
                    </h3>
                  </div>
                </div>
                <a href="institute-list" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-sky-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"></i></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Total Student</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      {{ $student_count }}
                    </h3>
                  </div>
                </div>
                <a href="#" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-green-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Total Banner</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      {{$banner_count}}
                    </h3>
                  </div>
                </div>
                <a href="banner-list" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 mt-md-0 mt-0">
            <div class="card l-bg-orange-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"></i></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Total Institute_for</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      {{ $institute_for_count }}
                    </h3>
                  </div>
                </div>
                <a href="institute-for-list" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 mt-md-0 mt-0">
            <div class="card l-bg-orange-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"></i></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Total Board</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      {{ $board_for_count }}
                    </h3>
                  </div>
                </div>
                <a href="board-list" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-green-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Total Class</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      {{$class_for_count}}
                    </h3>
                  </div>
                </div>
                <a href="class-list" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-sky-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Total Standard</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      {{ $standard_for_count }}
                    </h3>
                  </div>
                </div>
                <a href="standard-list" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"></i></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Total Subject</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      {{ $subject_for_count }}
                    </h3>
                  </div>
                </div>
                <a href="subject-list" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>

        </div>
     
        <div class="announcement bg-w-background pb-5">
          <div class="row">
            <div class="col-lg-12">
              <h3>Announcement List</h3>

              <table class="table table-js table-responsive-sm table-responsive">
                <thead class="thead-primary-color">
                  <tr>
                    <th>No</th>
                    <th>Title</th>
                    <th>Annoucement</th>
                    <th>Institute</th>
                    <th>Teacher</th>
                    <th>Parent</th>
                    <th>student</th>
                  
                  </tr>
                </thead>

                <tbody>
                  @php $i = 1 @endphp
                @foreach($response as $values)
                                    <tr>
                                    <td>{{$i}}</td>
                                        <td>{{$values['title']}}</td>
                                        <td>{{$values['announcement']}}</td>
                                        <td>
                                            @foreach($values['institute_show'] as $institute)
                                            {{$institute['institute_name']}}
                                            @if (!$loop->last)
                                            @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($values['teacher_show'] as $teacher)
                                            {{$teacher['firstname']}}
                                            @if (!$loop->last)
                                            @endif
                                            @endforeach
                                        </td>
                                        <td>
                                        @foreach($values['parent_show'] as $parent)
                                            {{$parent['firstname']}}
                                            @if (!$loop->last)
                                            @endif
                                            @endforeach
                                        </td>
                                        <td>
                                        @foreach($values['student_show'] as $student)
                                            {{$student['firstname']}}
                                            @if (!$loop->last)
                                            @endif
                                            @endforeach
                                        </td>
                                        
                                    </tr>
                                    @php $i++ @endphp
                                    @endforeach
                  </tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    @include('layouts/footer_new')

  </div>

  <script src="{{asset('mayal_assets/js/jquery-3.7.1.min.js')}}"></script>
  <script src="{{asset('mayal_assets/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('mayal_assets/js/main.js')}}"></script>
</body>

</html>