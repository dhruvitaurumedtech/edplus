<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include necessary libraries and styles -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .module {
            margin-bottom: 20px;
        }
        .module-label {
            font-weight: bold;
            display: block;
        }
        .feature {
            margin-left: 20px;
            margin-bottom: 10px;
        }
        .feature-label {
            font-weight: bold;
            display: block;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            margin-left: 40px;
        }
        .action {
            margin-right: 20px;
        }
        .submit-btn {
            display: flex;
            justify-content: flex-end;
        }
    </style>
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
                                    @foreach($modules as $module)
                                    <div class="module">
                                        <div class="form-check module-label">
                                            <input class="form-check-input module-checkbox" type="checkbox" id="module-{{ $module->id }}" data-module-id="{{ $module->id }}">
                                            <label class="form-check-label" for="module-{{ $module->id }}">
                                                {{ $module->module_name }}
                                            </label>
                                        </div>
                                        @foreach($module->features as $feature)
                                        <div class="feature">
                                            <div class="form-check feature-label">
                                                <input class="form-check-input feature-checkbox" type="checkbox" id="feature-{{ $feature->id }}" data-feature-id="{{ $feature->id }}" data-module-id="{{ $module->id }}">
                                                <label class="form-check-label" for="feature-{{ $feature->id }}">
                                                    {{ $feature->feature_name }}
                                                </label>
                                            </div>
                                            <div class="actions">
                                                @foreach($feature->actions as $action)
                                                <div class="form-check action">
                                                    <input class="form-check-input action-checkbox" type="checkbox" name="permissions[]" value="{{ $action['id'] }}" id="action-{{ $action['id'] }}" {{ $action['has_permission'] ? 'checked' : '' }} data-feature-id="{{ $feature->id }}" data-module-id="{{ $module->id }}">
                                                    <label class="form-check-label" for="action-{{ $action['id'] }}">
                                                        {{ $action['name'] }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <hr>
                                    @endforeach
                                </div>
                                <div class="submit-btn">
                                    <button type="button" id="submitPermissions" class="btn bg-primary-btn text-white">Update Permissions</button>
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
        $(document).ready(function() {
            console.log('Document ready');

            function updateFeatureCheckbox(featureId) {
                var allChecked = true;
                $('input.action-checkbox[data-feature-id="' + featureId + '"]').each(function() {
                    if (!$(this).prop('checked')) {
                        allChecked = false;
                    }
                });
                $('#feature-' + featureId).prop('checked', allChecked);
            }

            function updateModuleCheckbox(moduleId) {
                var allChecked = true;
                $('input.feature-checkbox[data-module-id="' + moduleId + '"]').each(function() {
                    if (!$(this).prop('checked')) {
                        allChecked = false;
                    }
                });
                $('#module-' + moduleId).prop('checked', allChecked);
            }

            $('input.action-checkbox').change(function() {
                var featureId = $(this).data('feature-id');
                var moduleId = $(this).data('module-id');

                updateFeatureCheckbox(featureId);
                updateModuleCheckbox(moduleId);
            });

            $('input.feature-checkbox').change(function() {
                var featureId = $(this).data('feature-id');
                var moduleId = $(this).data('module-id');
                var isChecked = $(this).prop('checked');

                $('input.action-checkbox[data-feature-id="' + featureId + '"]').prop('checked', isChecked);
                updateModuleCheckbox(moduleId);
            });

            $('input.module-checkbox').change(function() {
                var moduleId = $(this).data('module-id');
                var isChecked = $(this).prop('checked');

                $('input.feature-checkbox[data-module-id="' + moduleId + '"]').prop('checked', isChecked).trigger('change');
                $('input.action-checkbox[data-module-id="' + moduleId + '"]').prop('checked', isChecked);
            });

            // Initialize checkboxes on page load
            $('input.feature-checkbox').each(function() {
                var featureId = $(this).attr('id').split('-')[1];
                updateFeatureCheckbox(featureId);
            });

            $('input.module-checkbox').each(function() {
                var moduleId = $(this).attr('id').split('-')[1];
                updateModuleCheckbox(moduleId);
            });

            // Handle form submission
            $('#submitPermissions').click(function() {
                var permissions = [];

                $('.feature').each(function() {
                    var featureId = $(this).find('.feature-checkbox').data('feature-id');
                    var actions = [];

                    $(this).find('.action-checkbox:checked').each(function() {
                        actions.push(parseInt($(this).val()));
                    });

                    if (actions.length > 0) {
                        permissions.push({
                            feature_id: featureId,
                            actions: actions
                        });
                    }
                });

                var data = {
                    role_id: {{ $role->id }},
                    permissions: permissions
                };
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route("app_role.update_permissions", $role->id) }}',
                    method: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });
        });
    </script>
</body>

</html>
