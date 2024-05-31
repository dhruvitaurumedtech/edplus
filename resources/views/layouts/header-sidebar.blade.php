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

        <!-- <a href="index.php" class="dashboard-nav-item border-t"><i class="fas fa-tachometer-alt"></i> dashboard </a> -->
        @php
        $menu = getDynamicMenu();
        @endphp
        @php
        function isActiveMenu($menu) {
        return request()->routeIs($menu['url']) || collect($menu['submenus'])->pluck('url')->contains(request()->path());
        }

        function isActiveLink($url) {
        return request()->routeIs($url) ? 'active' : '';
        }
        @endphp

        @foreach ($menu as $value)
        <div class="dashboard-nav-dropdown {{ isActiveMenu($value) }}">
            <a href="{{ isset($value['url']) && Route::has($value['url']) ? url($value['url']) : '#' }}" class="dashboard-nav-item dashboard-nav-dropdown-toggle {{ isActiveLink($value['url']) }}">
                <i class="fas fa-graduation-cap"></i>&nbsp;{{ $value['menu_name'] }}
            </a>
            @if (!empty($value['submenus']))
            <div class="dashboard-nav-dropdown-menu " style="{{ isActiveMenu($value) ? 'display: block;' : '' }}">
                @foreach ($value['submenus'] as $submenu)
                <a href="{{ url($submenu['url']) }}" class="dashboard-nav-dropdown-item {{ ($submenu['url']==request()->segment(1)) ? 'active': '' }}">
                    <i class="fas fa-angle-right"></i>&nbsp; {{ $submenu['menu_name'] }}
                </a>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach

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