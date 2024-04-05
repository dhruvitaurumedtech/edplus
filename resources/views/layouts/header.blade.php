<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <title>@yield('title')</title> -->


  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('admin_assets/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- custom css file -->
  <link rel="stylesheet" href="{{asset('assets/css/custom_style.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('admin_assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('admin_assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{asset('admin_assets/plugins/jqvmap/jqvmap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('admin_assets/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('admin_assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('admin_assets/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('admin_assets/plugins/summernote/summernote-bs4.min.css')}}">
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">


    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>


      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
          <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
          </a>
          <div class="navbar-search-block">
            <form class="form-inline">
              <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                  <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                  <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </li>

        <!-- Messages Dropdown Menu -->

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-user"></i>
          </a>
          <div class="dropdown-menu">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
        <li><a class="dropdown-item" href="{{route('logout')}}" onclick="event.preventDefault();
                                  this.closest('form').submit();">Log Out</a>
        </li>
        </form>
        <form method="GET" action="{{ url('profile-edit') }}">
          <li><a class="dropdown-item" href="{{url('profile-edit')}}" onclick="event.preventDefault();
                                  this.closest('form').submit();">Profile</a>
          </li>
        </form>
  </div>

  </li>


  </ul>
  </nav>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="{{asset('admin_assets/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3">
      <span class="brand-text font-weight-light">{{Auth::user()->firstname}}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          @php $menu = getDynamicMenu() @endphp

          @foreach($menu as $value)
          <li class="nav-item {{ isActiveMenu($value) }}">
            <a href="{{ url($value['url']) }}" class="nav-link {{ isActiveLink($value['url']) }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>{{ $value['menu_name'] }}
              @if (!empty($value['submenus']))
              <i class="fas fa-angle-left right"></i>
              @endif
            </a>
            @if (!empty($value['submenus']))
            <ul class="nav nav-treeview">
              @foreach($value['submenus'] as $submenu)
              <li class="nav-item">
                <a href="{{ url($submenu['url']) }}" class="nav-link {{ isActiveLink($submenu['url']) }}">
                  <i class="far fa-circle nav-icon"></i>
                  {{ $submenu['menu_name'] }}
                </a>
              </li>
              @endforeach
            </ul>
            @endif
          </li>
          @endforeach

          @php
          function isActiveMenu($menu) {
          return request()->is($menu['url']) || collect($menu['submenus'])->pluck('url')->contains(request()->path()) ? 'menu-open active' : '';
          }

          function isActiveLink($url) {
          return request()->is($url) ? 'active' : '';
          }
          @endphp




        </ul>
      </nav>
    </div>
  </aside>