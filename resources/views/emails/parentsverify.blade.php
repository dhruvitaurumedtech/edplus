<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body>
    <form >
        @csrf
        @if($institute !='')
            <p>Please click the button below to verify your email address For {{$institute}}:</p>
            <a href="{{ url('/update-value/' . $id) }}" class="btn btn-success">Verify  Email </a>
        @else
            <p>Email : {{$email}}</p>
            <p>Password : {{$password}}</p>
        @endif
        
    </form>

</body>

</html>