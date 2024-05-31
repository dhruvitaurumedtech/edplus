<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include necessary libraries and styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="dashboard">
        @include('layouts.header-sidebar')
        <div class="dashboard-app">
            @include('layouts.header-topbar')
            <div class="link-dir">
                <h1 class="display-4">Permissions for {{ $role->role_name }}</h1>
                <ul>
                    <li><a href="{{ url('dashboard') }}">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">ACL</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="{{ route('app_role.list') }}">Roles</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)" class="active-link-dir">Permissions</a></li>
                </ul>
            </div>
            @include('layouts.alert')
            <div class="dashboard-content side-content">
                <div class="row">
                    <div class="col-lg-12 mt-3">
                        <div class="institute-form">
                            <form id="updatePermissionsForm" method="post" action="{{ route('app_role.update_permissions', $role->id) }}">
                                @csrf
                                <h4 class="mb-3">Manage Permissions for {{ $role->role_name }}</h4>

                                <div class="create-admin mt-4">
                                    <div class="form-group">
                                        <label for="permissions">Permissions</label>
                                        <div class="checkbox-group">
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-btn">
                                    <button type="submit" class="btn bg-primary-btn text-white">Update Permissions</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer_new')
    </div>

    <script>
        $(document).ready(function() {});
    </script>
</body>

</html>