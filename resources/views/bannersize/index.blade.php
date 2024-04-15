<!-- index.blade.php -->

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
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Banner Sizes</div>

                            <div class="card-body">
                                <a href="{{ route('banner-sizes.create') }}" class="btn btn-primary mb-3">Create Banner Size</a>
                                <table class="table">
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
                                                <a href="{{ route('banner-sizes.edit', $bannerSize->id) }}" class="btn btn-sm btn-primary">Edit</a>
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