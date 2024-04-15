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
                                                <a href="{{ route('banner-sizes.edit', $bannerSize->id) }}" class="btn btn-sm btn-primary editButton">Edit</a>
                                                <form action="{{ route('banner-sizes.destroy', $bannerSize->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this banner size?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts/footer_new')