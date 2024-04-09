<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>e School</title>

  <!-- css  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
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
                <div class="card-icon card-icon-large"><i class="fas fa-shopping-bag"></i></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">New Orders</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      150
                    </h3>
                  </div>
                </div>
                <a href="#" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-sky-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"><i class="fas fa-chart-bar"></i></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Bounce Rate</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      53%
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
                <div class="card-icon card-icon-large"><i class="fas fa-user-plus"></i></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">User Registrations</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      44
                    </h3>
                  </div>
                </div>
                <a href="#" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 mt-md-0 mt-0">
            <div class="card l-bg-orange-dark">
              <div class="card-statistic-3 p-4">
                <div class="card-icon card-icon-large"><i class="fas fa-chart-pie"></i></div>
                <div class="mb-4">
                  <h5 class="card-title mb-0">Unique Visitors</h5>
                </div>
                <div class="row align-items-center mb-3 d-flex">
                  <div class="col-8">
                    <h3 class="d-flex align-items-center mb-0">
                      65
                    </h3>
                  </div>
                </div>
                <a href="#" class="card-link">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>

        </div>
        <!-- dashboard card end  -->

        <!-- Holiday & Leaves cards start -->
        <div class="h-l-card">
          <div class="row">
            <div class="col-lg-6">
              <div class="holiday-card bg-w-background">
                <h3>Holiday</h3>
                <table>
                  <tr>
                    <th>Dhuleti</th>
                    <td>25 - Mar</td>
                  </tr>
                  <tr>
                    <th>Good Friday</th>
                    <td>29 - Mar</td>
                  </tr>
                  <tr>
                    <th>Ram Navami</th>
                    <td>17 - Apr</td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="leaves-card bg-w-background">
                <h3>Leaves</h3>
                <div class="form-group">
                  <select class="form-control" id="exampleFormControlSelect1">
                    <option>Today</option>
                    <option>Tomorrow</option>
                    <option>Upcoming</option>
                  </select>
                </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Holiday & Leaves cards end -->

        <!-- Announcement list start -->
        <div class="announcement bg-w-background pb-5">
          <div class="row">
            <div class="col-lg-12">
              <h3>Announcement List</h3>

              <table class="table table-responsive-lg">
                <thead class="thead-primary-color">
                  <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Description</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td>1</td>
                    <td class="date">15 <span>feb</span></td>
                    <td class="standard">10 <span>Standard</span></td>
                    <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </td>
                  </tr>

                  <tr>
                    <td>2</td>
                    <td class="date">12 <span>feb</span></td>
                    <td class="standard">3 <span>Standard</span> </td>
                    <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </td>
                  </tr>

                  <tr>
                    <td>3</td>
                    <td class="date">8 <span>feb</span></td>
                    <td class="standard">All <span>Standard</span></td>
                    <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </td>
                  </tr>

                  <tr>
                    <td>4</td>
                    <td class="date">5 <span>feb</span></td>
                    <td class="standard">12 <span>Standard</span>(Science)</td>
                    <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </td>
                  </tr>

                  <tr>
                    <td>5</td>
                    <td class="date">4 <span>feb</span></td>
                    <td class="standard">11 <span>Standard</span> (Comm)</td>
                    <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </td>
                  </tr>

                  <tr>
                    <td>6</td>
                    <td class="date">1 <span>feb</span></td>
                    <td class="standard">5 <span>Standard</span></td>
                    <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div><!-- Sub Main Col END -->
    </div><!-- MAIN row END -->
    @include('layouts/footer_new')

  </div>

  <!-- js -->
  <script src="{{asset('mayal_assets/js/jquery-3.7.1.min.js')}}"></script>
  <script src="{{asset('mayal_assets/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('mayal_assets/js/main.js')}}"></script>
</body>

</html>