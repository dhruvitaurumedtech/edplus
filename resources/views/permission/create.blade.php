@include('layouts/header')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Permission</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Permission</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="row">
        <div class="col-md-8 offset-md-2">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        </div>
    </div>
    
    <script>
        window.setTimeout(function() {
            $(".alert-success").slideUp(500, function() {
                $(this).remove();
            });
        }, 3000);
    </script>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Create Permission</h3>
              </div>
              <form method="post" action="{{url('permission/insert')}}">   
                   @csrf
                   <input type="hidden" name="role_id" value="{{ $id }}">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 100px">Menu Name</th>
                    <th style="width: 100px">Sub Menu Name</th>
                    <th>Add</th>
                    <th>edit</th>
                    <th>view</th>
                    <th>delete</th>
                  </tr>
                </thead>
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
            </td><td>    <input type="hidden" name="permissions[{{ $value['id'] }}][add]" value="0">
                        <input type="checkbox" name="permissions[{{ $value['id'] }}][add]" value="1" @if(permissionExists($value['id'], 'add',$id)) checked @endif></td>
                <td>    <input type="hidden" name="permissions[{{ $value['id'] }}][edit]" value="0">
                        <input type="checkbox" name="permissions[{{ $value['id'] }}][edit]" value="1" @if(permissionExists($value['id'], 'edit',$id)) checked @endif></td>
                <td>    <input type="hidden" name="permissions[{{ $value['id'] }}][view]" value="0">
                        <input type="checkbox" name="permissions[{{ $value['id'] }}][view]" value="1" @if(permissionExists($value['id'], 'view',$id)) checked @endif></td>
                <td>    <input type="hidden" name="permissions[{{ $value['id'] }}][delete]" value="0">
        <input type="checkbox" name="permissions[{{ $value['id'] }}][delete]" value="1" @if(permissionExists($value['id'], 'delete',$id)) checked @endif></td>
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
              <input type="submit" value="submit" class="btn btn-success mb-2 mr-2" style="float: right;">
                  </form>
            </div>
        
          </div>
      
        </div>
      
      </div>
    </section>
</div>
@include('layouts/footer')
