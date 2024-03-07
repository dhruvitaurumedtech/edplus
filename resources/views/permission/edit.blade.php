<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>{{ __('Profile') }}</title>
    
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="description" content="Portal - Bootstrap 5 Admin Dashboard Template For Developers">
    <meta name="author" content="Xiaoying Riley at 3rd Wave Media">    
    <link rel="shortcut icon" href="favicon.ico"> 
    
    <!-- FontAwesome JS-->
    <script defer src="{{asset('assets/plugins/fontawesome/js/all.min.js')}}"></script>
    
    <!-- App CSS -->  
    <link id="theme-style" rel="stylesheet" href="{{asset('assets/css/portal.css')}}">

</head> 

<body class="app">   	
@extends('layouts/navigation')
<div class="app-wrapper">
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
            <div class="row gy-4">
                <div class="col-12 col-lg-12">
                    <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
                        <div class="app-card-header p-3 border-bottom-0">
                            <div class="row align-items-center gx-3">
                                <div class="col-auto">
                                    <div class="app-icon-holder">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
</svg>
                                    </div><!--//icon-holder-->
                                    
                                </div><!--//col-->
                                <div class="col-auto">
                                    <h4 class="app-card-title">User Permission</h4>
                                </div><!--//col-->
                            </div><!--//row-->
                        </div><!--//app-card-header-->
                        <div class="app-card-body px-4 w-100">
                            <div class="item py-3">
                                <div class="row justify-content-between align-items-center">
                                <form method="post" action="{{ route('permissions.update',$user_id) }}" class="mt-6 space-y-6">
                                @csrf
                                @method('patch')
                                <table class="info-table">
                                <thead class="info-table__head">
                                    <tr>
                                        <th>List</th>
                                        <th>Add</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody class="info-table__body">
                                    <input type="hidden" name="user_id" value="{{$user_id}}">
                                    @foreach($module as $dt)
                                    
                                    <tr>
                                        <td>
                                    
                                    {{ $dt->module_name }}  </td>
                                        
									    <td> <input type="hidden" name="module_id[]" value="{{$dt->id}}"> 
                                        <input class="form-check-input" type="checkbox" name="add[]" value="1" id="settings-checkbox-3" @if($dt->add && $dt->user_id == $user_id) checked @endif >  </td>
                                        <td> <input class="form-check-input" type="checkbox" name="edit[]" value="1" id="settings-checkbox-3" @if($dt->edit && $dt->user_id == $user_id) checked @endif>  </td>
                                        <td> <input class="form-check-input" type="checkbox" name="delete[]" value="1" id="settings-checkbox-3" @if($dt->delete && $dt->user_id == $user_id) checked @endif>  </td>
                                        <td> <input class="form-check-input" type="checkbox" name="view[]" value="1" id="settings-checkbox-3" @if($dt->view && $dt->user_id == $user_id) checked @endif>  </td>
                                      
                                    
                                    </td>
                                        </tr>
                                    @endforeach
                                    </tbody>   
                                </table>   
                                <button type="submit" class="btn app-btn-primary" align="right">{{ __('Save') }}</button> 
                                </div><!--//row-->
                            </div><!--//item-->
                            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
                            </form>
                        </div><!--//app-card-body-->
                    </div><!--//app-card-->
                </div><!--//col-->

            </div>
        </div>
    </div>
</div>
@extends('layouts/footer')
	    
    </div><!--//app-wrapper-->    					

 
    <!-- Javascript -->          
    <script src="assets/plugins/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>  

    <!-- Charts JS -->
    <script src="assets/plugins/chart.js/chart.min.js"></script> 
    <script src="assets/js/index-charts.js"></script> 
    
    <!-- Page Specific JS -->
    <script src="assets/js/app.js"></script> 

</body>
</html> 
