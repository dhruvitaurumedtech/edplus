<!-- resources/views/banner-sizes/create.blade.php -->

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
                <h1 class="display-4">Banner Size</h1>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">Banner</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="{{url('class-list')}}" class="active-link-dir">Banner Size</a></li>
                </ul>
            </div>

            <script>
                window.setTimeout(function() {
                    $(".alert-success").slideUp(500, function() {
                        $(this).remove();
                    });
                }, 3000);
            </script>
            <div class="dashboard-content side-content">

                <div class="row">
                    <div class="col-lg-5">
                        <div class="institute-form">
                            <h2>Create New Banner Size</h2>
                            <form action="{{ route('banner-sizes.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="size">Size</label>
                                    <input type="text" class="form-control" id="size" name="size" required>
                                </div>
                                <div class="form-group">
                                    <label for="width">Width</label>
                                    <input type="number" class="form-control" id="width" name="width" required>
                                </div>
                                <div class="form-group">
                                    <label for="height">Height</label>
                                    <input type="number" class="form-control" id="height" name="height" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </form>

                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="institute-form">
                            <h2>BannerSize List</h2>
                            <div class="card-body">
                                <table class="table table-responsive-sm table-bordered institute-table mt-4">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Size</th>
                                            <th>Width</th>
                                            <th>Height</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bannerSizes as $bannerSize)
                                        <tr>
                                            <td>{{ $bannerSize->id }}</td>
                                            <td>{{ $bannerSize->size }}</td>
                                            <td>{{ $bannerSize->width }}</td>
                                            <td>{{ $bannerSize->height }}</td>
                                            <td>

                                                <div class="d-flex">
                                                    <!-- @canButton('edit', 'Institute_for') -->
                                                    <input type="submit" class="btn btn-primary editButton" data-user-id="{{ $bannerSize->id }}" value="Edit">&nbsp;&nbsp;
                                                    <!-- @endCanButton -->
                                                    &nbsp;&nbsp;
                                                    <!-- @canButton('delete', 'Institute_for') -->
                                                    <input type="submit" class="btn btn-danger deletebutton" data-user-id="{{ $bannerSize->id }}" value="Delete">
                                                    <!-- @endCanButton -->
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.querySelectorAll('.editButton').forEach(function(button) {
                            button.addEventListener('click', function() {
                                var institute_id = this.getAttribute('data-user-id');

                                axios.post('banner-sizes/edit', {
                                        institute_id: institute_id
                                    })
                                    .then(response => {
                                        var reponse_data = response.data.Institute_for_model;
                                        var iconSrc = '{{ asset('
                                        ') }}' + reponse_data.icon;
                                        $('#institute_id').val(reponse_data.id);
                                        $('#name').val(reponse_data.name);
                                        $('#icon_update').attr('src', iconSrc);
                                        $('#old_icon').val(reponse_data.icon);
                                        $('#status').val(reponse_data.status);
                                        $('#usereditModal').modal('show');
                                    })
                                    .catch(error => {
                                        console.error(error);
                                    });
                            });
                        });
                        document.querySelectorAll('.deletebutton').forEach(function(button) {
                            button.addEventListener('click', function(event) {
                                event.preventDefault();
                                var institute_id = this.getAttribute('data-user-id');

                                Swal.fire({
                                    title: 'Are you sure want to delete?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Yes, delete it!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        axios.post('banner-sizes.destroy', {
                                                institute_id: institute_id
                                            })
                                            .then(response => {
                                                location.reload(true);

                                            })
                                            .catch(error => {
                                                console.error(error);
                                            });
                                    }
                                });
                            });
                        });
                    </script>
                </div>
            </div>
            @include('layouts/footer_new')