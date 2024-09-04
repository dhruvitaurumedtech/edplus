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
            .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: 1px solid transparent;
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: .25rem;
        transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
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

            <b><p>Please Click Here to <a href="{{ url('/update-value/' . $id) }}"  style="  color: #fff;  background-color: #007bff;  border-color: #007bff;  display: inline-block; font-weight: 400; text-align: center; white-space: nowrap; vertical-align: middle;   -webkit-user-select: none;      -moz-user-select: none;  -ms-user-select: none;    user-select: none;     border: 1px solid transparent;   padding: .375rem .75rem;      font-size: 1rem;         line-height: 1.5;    border-radius: .25rem;  transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;" class="btn btn-primary">Verify</a></p></b>
            
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
            <p>Email : <b>{{$email}}</b></p>
            <p>Password : <b>{{$password}}</b></p>
        @endif
        <br>
        <a href="https://play.google.com/store/apps/details?id=com.aurum.edwide">https://play.google.com/store/apps/details?id=com.aurum.edwide</a>
    </form>

</body>

</html>