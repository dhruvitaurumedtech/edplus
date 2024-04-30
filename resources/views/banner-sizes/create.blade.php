<body>
    <div class="dashboard">
        @include('layouts/header-sidebar')
        <div class="dashboard-app">
            @include('layouts/header-topbar')
            <div class="link-dir">
                <h1 class="display-4">Banner Size</h1>
                <ul>
                    <li><a href="{{url('dashboard')}}">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">Banner</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="{{url('class-list')}}" class="active-link-dir">Banner Size</a></li>
                </ul>
            </div>
            @include('layouts/alert')
            <div class="dashboard-content side-content">

                <div class="row">
                    <div class="col-lg-5">
                        <div class="institute-form">
                            <h3>Create New Banner Size</h3>
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
                                <div class="flex mb-5">
                                    <button type="submit" class="btn text-white blue-button" style="float: right;">Create</button>
                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="institute-form">
                            <h3>BannerSize List</h3>
                            <div class="card-body">
                                <table class="table table-js table-bordered table-responsive mt-4">
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
                                                    <button class="btn text-white blue-button banner_size_editButton" data-banner-id="{{ $bannerSize->id }}">Edit</button>
                                                    &nbsp;&nbsp;
                                                    <button class="btn btn-danger banner_size_deleteButton" data-banner-id="{{ $bannerSize->id }}">Delete</button>
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
                            <button type="submit" class="btn text-white blue-button" style="float: right;">Update</button>
                        </form>
                    </div>
                </div>
                </form>
            </div>

        </div>
    </div>
    </div>