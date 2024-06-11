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
    <h1>Password Reset Request</h1>
    <p>Dear {{$name}},</p>
    <p>We received a request to reset the password for your account associated with this email address.</p>
    <p>To reset your password, please use the following 6-digit verification code:</p>
    <p class="code">{{$token}}</p>
    <p>Please enter this code in the verification field on the password reset page.</p>
    <p>If you did not request a password reset, please ignore this email or contact our support team.</p>
    <p>Thank you,</p>
    <p>The Support Team</p>
</body>
</html>
