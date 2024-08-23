<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/bootstrap.min.css')}}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{asset('mayal_assets/css/style.css')}}" />
<link rel="stylesheet" href="{{asset('mayal_assets/css/responsive.css')}}" />
<style>
    .dashboard-nav-dropdown-menu {
        display: none;
    }

    .dashboard-nav-dropdown-menu.open {
        display: block;
    }
</style>
<div class="dashboard-nav">
    <nav class="dashboard-nav-list">

    <div class="dashboard-nav-dropdown {{ Request::is('dashboard') ? 'show' : '' }}" id="admin-dropdown">
        <a href="dashboard" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="admin-toggle">
        <i class="fas fa-tachometer-alt"></i>&nbsp; dashboard
        </a>
   </div> 
    <div class="dashboard-nav-dropdown {{ Request::is('admin') ? 'show' : '' }}" id="admin-dropdown">
    <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="admin-toggle">
    <i class="fas fa-user"></i>&nbsp; Admin
    </a>
    <div class="dashboard-nav-dropdown-menu {{ Request::is('admin') ? 'show' : '' }}" id="admin-menu">
        <a href="{{ url('admin') }}" class="dashboard-nav-dropdown-item {{ Request::is('admin') ? 'active' : '' }}" id="submenu1-toggle">
            <i class="fas fa-angle-right"></i>&nbsp; Admin
        </a>
    </div>
</div>

        <div class="dashboard-nav-dropdown {{ Request::is('institute-admin') || Request::is('institute-list') || Request::is('institute-for-list') ||
           Request::is('board-list') || Request::is('class-list') || Request::is('medium-list') || Request::is('standard-list') || 
           Request::is('stream-list') || Request::is('subject-list') || Request::is('add-lists') || Request::is('do-business-with-list')? 'show' : ''  }}" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle {{ Request::is('institute-admin') ? 'active' : '' }}" id="access-control-toggle">
            <i class="fas fa-university"></i>&nbsp; Institute
            </a>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('institute-admin') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('institute-admin') }}" class="dashboard-nav-dropdown-item {{ Request::is('institute-admin') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Institutes Admins
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('institute-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('institute-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('institute-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; List institute
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('institute-for-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('institute-for-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('institute-for-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Institute_for
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('board-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('board-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('board-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Board
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('class-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('class-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('class-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Class
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('medium-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('medium-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('medium-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Medium
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('standard-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('standard-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('standard-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Standard
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('stream-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('stream-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('stream-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Stream
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('subject-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('subject-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('subject-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Subject
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('add-lists') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('add-lists') }}" class="dashboard-nav-dropdown-item {{ Request::is('add-lists') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Chapter
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('do-business-with-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('do-business-with-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('do-business-with-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Do_business_with
                </a>
            </div>
        </div>
        <div class="dashboard-nav-dropdown {{ Request::is('banner-sizes') || Request::is('banner-list') ? 'show' : '' }}" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle " id="access-control-toggle">
            <i class="fas fa-images"></i>&nbsp; Banner
            </a>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('banner-sizes') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('banner-sizes') }}" class="dashboard-nav-dropdown-item {{ Request::is('banner-sizes') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Banner-size
                </a>
                <a href="{{ url('banner-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('banner-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Banner
                </a>
                
            </div>
        </div>
        <div class="dashboard-nav-dropdown {{ Request::is('video-category-list')  ? 'show' : '' }}" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
            <i class="fas fa-certificate"></i>&nbsp; Category
            </a>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('video-category-list') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('video-category-list') }}" class="dashboard-nav-dropdown-item {{ Request::is('video-category-list') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Video Category
                </a>
            </div>
        </div>
        <div class="dashboard-nav-dropdown {{ Request::is('announcement-create-new')  ? 'show' : '' }}" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
            <i class="fas fa-bullhorn"></i>&nbsp; Announcement
            </a>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('announcement-create-new') ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('announcement-create-new') }}" class="dashboard-nav-dropdown-item {{ Request::is('announcement-create-new') ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Announcement
                </a>
            </div>
        </div>
        <div class="dashboard-nav-dropdown {{ Request::is('video-time-limit')  ? 'show' : '' }}" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
            <i class="fas fa-video"></i>&nbsp; Video Time Limit
            </a>
            <div class="dashboard-nav-dropdown-menu {{ Request::is('video-time-limit')  ? 'show' : '' }}" id="access-control-menu">
                <a href="{{ url('video-time-limit') }}" class="dashboard-nav-dropdown-item {{ Request::is('video-time-limit')  ? 'active' : '' }}" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Video Time Limit
                </a>
            </div>
        </div>
        <div class="dashboard-nav-dropdown {{ in_array(Route::currentRouteName(), ['module.list', 'feature.list', 'app_role.list']) ? 'show' : '' }}" id="access-control-dropdown">
    <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
    <i class="fas fa-gamepad"></i>&nbsp; Access Control
    </a>
    <div class="dashboard-nav-dropdown-menu">
        <a href="{{ route('module.list') }}" class="dashboard-nav-dropdown-item {{ Route::currentRouteName() == 'module.list' ? 'active' : '' }}" id="submenu1-toggle">
            <i class="fas fa-angle-right"></i>&nbsp; Module
        </a>
        <a href="{{ route('feature.list') }}" class="dashboard-nav-dropdown-item {{ Route::currentRouteName() == 'feature.list' ? 'active' : '' }}" id="submenu2-toggle">
            <i class="fas fa-angle-right"></i>&nbsp; Feature
        </a>
        <a href="{{ route('app_role.list') }}" class="dashboard-nav-dropdown-item {{ Route::currentRouteName() == 'app_role.list' ? 'active' : '' }}" id="submenu3-toggle">
            <i class="fas fa-angle-right"></i>&nbsp; App Role
        </a>
    </div>
</div>



    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const accessControlToggle = document.getElementById("access-control-toggle");
        const accessControlMenu = document.getElementById("access-control-menu");
        const submenu1Toggle = document.getElementById("submenu1-toggle");

        // Function to toggle the Access Control menu
        function toggleAccessControlMenu() {
            accessControlMenu.classList.toggle("open");
        }

        // Function to close the Access Control menu
        function closeAccessControlMenu() {
            if (accessControlMenu.classList.contains("open")) {
                accessControlMenu.classList.remove("open");
            }
        }

        // Event listener for the Access Control menu toggle
        accessControlToggle.addEventListener("click", function(e) {
            e.preventDefault();
            toggleAccessControlMenu();
        });

        // Event listener for the Submenu 1 toggle
        submenu1Toggle.addEventListener("click", function() {
            closeAccessControlMenu();
            // No need to call e.preventDefault() here to allow the default link behavior
        });
    });
</script>