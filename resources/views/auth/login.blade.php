<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Form - e School</title>

  <!-- css  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
  <link rel="stylesheet" href="{{asset('front_asset/css/bootstrap.min.css')}}" />
  <link rel="stylesheet" href="{{asset('front_asset/css/style.css')}}" />
  <link rel="stylesheet" href="{{asset('front_asset/css/responsive.css')}}" />
</head>

<body class="bg-gray">
  <div class="page-wrapper ">
    <div class="login-form">
      <form class="auth-form" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="logo1">
          <img src="{{asset('front_asset/images/form-logo.svg')}}" alt="e school logo" class="img-fluid">
        </div>

        <div class="form-group">
          <label for="exampleInputEmail1">Email</label>
          <input id="email" name="email" :value="old('email')" type="email" class="form-control" placeholder="Email address" required="required">

        </div>
        <div class="form-group">
          <label for="exampleInputPassword1">Password</label>
          <input id="password" name="password" type="password" class="form-control " placeholder="Password" required autocomplete="current-password">
        </div>
        <div class="form-group ml-4">
          <input class="form-check-input" type="checkbox" value="" id="remember_me" name="remember">
          <label class="form-check-label" for="remember_me">
            Remember me
          </label>
        </div>
        <button type="submit" class="btn w-40 bg-primary-btn mt-4">Submit</button>

      </form>
    </div>
  </div>

  <!-- js -->
  <script src="{{asset('front_asset/js/jquery-3.7.1.min.js')}}"></script>
  <script src="{{asset('front_asset/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('front_asset/js/main.js')}}"></script>
</body>

</html>