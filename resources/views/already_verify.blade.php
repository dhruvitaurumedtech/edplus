<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
    
    @if($data == 1)
        <h1>Email Verification Successful</h1>
        <p>Please check Login detail mail</p>
    @elseif($data == 2)
        <p>Your email has already been verified. You can now use all the features available to you.</p> 
    @elseif($data == 3)
    <p>Record not found.</p>
    @else
    <p>Something went wrong.</p>
    @endif
    </div>
</body>
</html>