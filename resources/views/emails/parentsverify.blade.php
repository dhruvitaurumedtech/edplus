<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        p {
            color: black;
        }
    </style>
</head>
<body>
    <form >
        @csrf
        @if($institute !='')
            <p>Dear {{$name}},</p>
            <p>We are writing to inform you that {{$sname}} has expressed interest in enrolling at {{$institute}} for the upcoming academic term. </p>
            <p>As part of our enrollment process, we require confirmation from a parent or guardian to finalize the admission.</p>
            <br>
            <p>Please review the following details of the application: </p>
            <p>Student Name: {{$sname}}</p>
            <p>Standard: {{$standard}}</p>
            <p>Subject Applied For: </p>
            @foreach($subjects as $sub)
                <p>{{$sub['subname']}}</p> 
            @endforeach
            
            <br>
            <p>"I,confirm that my child, {{$sname}}, has my permission to enroll at {{$institute}} for the {{$year}}. </p>
            <p> I acknowledge that I have reviewed the enrollment details and agree to the terms and conditions outlined by the institute."
            </p>

            <p>Please Click Here to <a href="{{ url('/update-value/' . $id) }}" class="btn btn-success">"Verify"</a></p>
            
            <br>
            <p>Thank you for your prompt attention to this matter. We look forward to welcoming {{$sname}} to our institute.</p>
            <br>
            <p>Warm regards,</p>
            <br>
            <p>{{$institute}}</p>
            <p>{{$address}}</p>
            <p>{{$Iemail}}</p>
            <p>{{$contact_no}}</p>
            <p>{{$website_link}}</p>
            @else
            <p>Email : {{$email}}</p>
            <p>Password : {{$password}}</p>
        @endif
        <br>
        <a href="https://play.google.com/store/apps/details?id=com.aurum.edwide">https://play.google.com/store/apps/details?id=com.aurum.edwide</a>
    </form>

</body>

</html>