<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        h1 {
            color: #4CAF50;
        }
        p {
            margin: 0.5em 0;
        }
        .code {
            font-size: 1.2em;
            font-weight: bold;
            color: #FF5733;
        }
    </style>
</head>
<body>
Hey {{$name}},

<p>Welcome to EdWide! We’re excited to have you on board. To complete your account setup, please use the verification code below:</p>

<p>Verification Code: <b>{{$token}}</b></p>

<p>Please enter this code in the verification section of your account settings to activate your account. If you didn’t sign up for Edwide, you can ignore this email.</p>

<p>Thank you for choosing EdWide!</p>

<p>Best Regards,</p>
<p>EdWide</p>    
</body>
</html>

