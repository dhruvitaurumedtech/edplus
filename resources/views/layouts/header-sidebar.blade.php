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
                <i class="fas fa-graduation-cap"></i> &nbsp; {{ $value['menu_name'] }}
            </a>
            @if (!empty($value['submenus']))
            <div class="dashboard-nav-dropdown-menu " style="{{ isActiveMenu($value) ? 'display: block;' : '' }}">
                @foreach ($value['submenus'] as $submenu)
                <a href="{{ url($submenu['url']) }}" class="dashboard-nav-dropdown-item {{ ($submenu['url']==request()->segment(1)) ? 'active': '' }}">
                    <i class="fas fa-angle-right"></i> &nbsp;{{ $submenu['menu_name'] }}
                </a>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach




    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>