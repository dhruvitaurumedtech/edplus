<header class='dashboard-toolbar'>
    <a href="index.php"><img src="{{asset('mayal_assets/images/logo.svg')}}" alt="e School logo" class="img-fluid eschool-logo-img"></a>

    <div class="nav-bar-icon">

        <nav class="pro-icon-navbar">
            <div class="pro-icon-navbar-container">
                <ul class="pro-icon-navbar-nav m-0">
                    <li class="pro-icon-nav-item pro-icon-nav-profile pro-icon-dropdown">
                        <a class="pro-icon-nav-link pro-icon-dropdown-toggle" id="pro-icon-profileDropdown" href="#" onclick="toggleDropdown()" aria-expanded="false">
                            <div class="pro-icon-nav-profile-text p-0">
                                <p class="mb-0 pro-icon-text-black"><i class="fas fa-user pro-icon"></i>&nbsp; Profile</p>
                            </div>
                        </a>
                        <div class="pro-icon-dropdown-menu pro-icon-navbar-dropdown" id="pro-icon-dropdownMenu" aria-labelledby="pro-icon-profileDropdown">
                            <a class="pro-icon-dropdown-item" href="profile.php"><i class="fa fa-user pro-icon text-info"></i>&nbsp; Profile</a>
                            <div class="pro-icon-dropdown-divider m-0"></div>
                            <a class="pro-icon-dropdown-item" href="change-password.php">
                                <i class="fas fa-sync-alt pro-icon text-success"></i>&nbsp; Change Password</a>
                            <div class="pro-icon-dropdown-divider m-0"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                    <li><a class="dropdown-item" href="{{route('logout')}}" onclick="event.preventDefault();
                                  this.closest('form').submit();">Log Out</a>
                    </li>
                    </form>
            </div>
            </li>
            </ul>
    </div>
    </nav>
    <a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a>
    </div>
</header>