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
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="dashboard-content side-content">

                <div class="row">
                    <div class="col-lg-5">
                        <div class="institute-form">
                            <h2>Create New Banner Size</h2>
                            <form action="{{ route('banner-sizes.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="size">Size</label>
                                    <input type="text" class="form-control" name="size" required>
                                </div>
                                <div class="form-group">
                                    <label for="width">Width</label>
                                    <input type="number" class="form-control" name="width" required>
                                </div>
                                <div class="form-group">
                                    <label for="height">Height</label>
                                    <input type="number" class="form-control" name="height" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </form>

                        </div>
                    </div>
                    <div class="col-lg-7">
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
                                                    <button class="btn btn-primary editButton" data-banner-id="{{ $bannerSize->id }}">Edit</button>
                                                    &nbsp;&nbsp;
                                                    <button class="btn btn-danger deleteButton" data-banner-id="{{ $bannerSize->id }}">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                {!! $bannerSizes->withQueryString()->links('pagination::bootstrap-5') !!}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts/footer_new')
        <script type="text/javascript">
            $(document).ready(function() {

                document.querySelectorAll('.editButton').forEach(function(button) {
                    button.addEventListener('click', function() {
                        var bannerId = this.getAttribute('data-banner-id');

                        axios.post('/banner-sizes/edit', {
                                banner_id: bannerId
                            })
                            .then(response => {
                                var response_data = response.data.bannerSize;
                                console.log(response_data.size);
                                $('#id').val(response_data.id);
                                $('#size').val(response_data.size);
                                $('#width').val(response_data.width);
                                $('#height').val(response_data.height);
                                $('#usereditModal').modal('show');
                            })
                            .catch(error => {
                                console.error(error);
                            });
                    });
                });
            });
            document.querySelectorAll('.deletebutton').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    var bannerId = this.getAttribute('data-banner-id');

                    Swal.fire({
                        title: 'Are you sure want to delete?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post("{{ url('banner-sizes/destroy') }}", {
                                    bannerId: bannerId
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
        <div class="modal fade" id="usereditModal" tabindex="-1" aria-labelledby="usereditModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usereditModalLabel">Edit banner_size </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('banner-sizes/update') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <input type="hidden" name="id" id="id">
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
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
                </form>
            </div>

        </div>
    </div>
    </div>