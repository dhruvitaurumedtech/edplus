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
    <h1>Please use the verification code below to sign in.</h1>
    <p>Dear {{$token}},</p>

    <p>If you didn’t request this, you can ignore this email.</p>

    <p>Thank you,</p>
    <p>The Support Team</p>
    
</body>
</html>

