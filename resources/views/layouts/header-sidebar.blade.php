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

       
        <div class="dashboard-nav-dropdown" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
                <i class="fas fa-graduation-cap"></i>&nbsp; Admin
            </a>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('admin') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Admin
                </a>
               </div>
        </div>
        <div class="dashboard-nav-dropdown" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
                <i class="fas fa-graduation-cap"></i>&nbsp; Institute
            </a>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('institute-admin') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Institutes Admins
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('institute-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; List institute
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('institute-for-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Institute_for
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('board-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Board
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('class-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Class
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('medium-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Medium
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('standard-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Standard
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('stream-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Stream
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('subject-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Subject
                </a>
            </div>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('do-business-with-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Do_business_with
                </a>
            </div>
        </div>
        <div class="dashboard-nav-dropdown" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
                <i class="fas fa-graduation-cap"></i>&nbsp; Banner
            </a>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('banner-sizes') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Banner-size
                </a>
                <a href="{{ url('banner-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; banner
                </a>
                
            </div>
        </div>
        <div class="dashboard-nav-dropdown" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
                <i class="fas fa-graduation-cap"></i>&nbsp; Category
            </a>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('video-category-list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Video Category
                </a>
            </div>
        </div>
        <div class="dashboard-nav-dropdown" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
                <i class="fas fa-graduation-cap"></i>&nbsp; Announcement
            </a>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('announcement-create-new') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; announcement
                </a>
            </div>
        </div>
        <div class="dashboard-nav-dropdown" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
                <i class="fas fa-graduation-cap"></i>&nbsp; Video Time Limit
            </a>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ url('video-time-limit') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Video Time Limit
                </a>
            </div>
        </div>
        <div class="dashboard-nav-dropdown" id="access-control-dropdown">
            <a href="#" class="dashboard-nav-item dashboard-nav-dropdown-toggle" id="access-control-toggle">
                <i class="fas fa-graduation-cap"></i>&nbsp; Access Control
            </a>
            <div class="dashboard-nav-dropdown-menu" id="access-control-menu">
                <a href="{{ route('module.list') }}" class="dashboard-nav-dropdown-item" id="submenu1-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Module
                </a>
                <a href="{{ route('feature.list') }}" class="dashboard-nav-dropdown-item" id="submenu2-toggle">
                    <i class="fas fa-angle-right"></i>&nbsp; Feature
                </a>
                <a href="{{ route('app_role.list') }}" class="dashboard-nav-dropdown-item" id="submenu2-toggle">
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