<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Permission - e School</title>

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
                <h1 class="display-4">Permission</h1>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">Permission</a></li>
                </ul>
            </div>

            <div class="dashboard-content side-content">

                <div class="row">
                    <!-- table -->
                    <div class="col-lg-12 mt-5">
                        <form class="s-chapter-form" action="{{url('permission/insert')}}" method="post">
                            <input type="hidden" name="role_id" value="{{ $id }}">
                            @csrf
                            <table class="table table-bordered table-responsive-sm institute-table">
                                <thead>
                                    <tr>
                                        <th style="width: 100px">Menu Name</th>
                                        <th style="width: 100px">Sub Menu Name</th>
                                        <th><input type="checkbox" id="check-all-add"> Add</th>
                                        <th><input type="checkbox" id="check-all-edit"> edit</th>
                                        <th><input type="checkbox" id="check-all-view"> view</th>
                                        <th><input type="checkbox" id="check-all-delete"> delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tbody>

                                    @php $menu = get_all_menu_list() @endphp

                                    @foreach($menu as $value)
                                    <tr>
                                        <td>
                                            @if($value['sub_menu_id'] == 0)
                                            <input type="hidden" name="menu_id[]" value="{{ $value['id'] }}">
                                            {{$value['menu_name']}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($value['sub_menu_id'] != 0)
                                            <input type="hidden" name="menu_id[]" value="{{ $value['id'] }}">
                                            {{ $value['menu_name'] }}
                                            @endif
                                        </td>
                                        <td> <input type="hidden" name="permissions[{{ $value['id'] }}][add]" value="0">
                                            <input type="checkbox" class="permission-checkbox-add" name="permissions[{{ $value['id'] }}][add]" value="1" @if(permissionExists($value['id'], 'add' ,$id)) checked @endif>
                                        </td>
                                        <td> <input type="hidden" name="permissions[{{ $value['id'] }}][edit]" value="0">
                                            <input type="checkbox" class="permission-checkbox-edit" name="permissions[{{ $value['id'] }}][edit]" value="1" @if(permissionExists($value['id'], 'edit' ,$id)) checked @endif>
                                        </td>
                                        <td> <input type="hidden" name="permissions[{{ $value['id'] }}][view]" value="0">
                                            <input type="checkbox" class="permission-checkbox-view" name="permissions[{{ $value['id'] }}][view]" value="1" @if(permissionExists($value['id'], 'view' ,$id)) checked @endif>
                                        </td>
                                        <td> <input type="hidden" name="permissions[{{ $value['id'] }}][delete]" value="0">
                                            <input type="checkbox" class="permission-checkbox-delete" name="permissions[{{ $value['id'] }}][delete]" value="1" @if(permissionExists($value['id'], 'delete' ,$id)) checked @endif>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @php
                                    function permissionExists($menuId, $permissionType,$id) {
                                    $permissions = get_permission();
                                    foreach ($permissions as $permission) {
                                    if ($permission['menu_id'] == $menuId && $permission[$permissionType] == 1
                                    && $permission['role_id'] == $id) {
                                    return true;
                                    }
                                    }
                                    return false;
                                    }
                                    @endphp

                                </tbody>
                            </table>
                            <div class="submit-btn">
                                <input type="submit" value="Submit" class="btn bg-primary-btn text-white mt-4">
                            </div>

                        </form>
                    </div>

                </div>

            </div>

        </div>
        </section>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#check-all-add').click(function() {
                var isChecked = $(this).prop('checked');
                $('.permission-checkbox-add').prop('checked', isChecked);
            });
            $('#check-all-edit').click(function() {
                var isChecked = $(this).prop('checked');
                $('.permission-checkbox-edit').prop('checked', isChecked);
            });
            $('#check-all-view').click(function() {
                var isChecked = $(this).prop('checked');
                $('.permission-checkbox-view').prop('checked', isChecked);
            });
            $('#check-all-delete').click(function() {
                var isChecked = $(this).prop('checked');
                $('.permission-checkbox-delete').prop('checked', isChecked);
            });
        });
    </script>
    </script>
    @include('layouts/footer_new')