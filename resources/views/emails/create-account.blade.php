<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
    <body style="padding: 0px; margin: 0px;">
        <div style="padding: 10px; border-radius: 10px; background-color: #FCE5F5; color: #9F5A8D">
            <center><h3>Create Account</h3></center>
            <br />
            <center><span>Your account successfully created by admin</span></center>
            <br />
            <hr style="border:1px solid #9F5A8D" />
            <br />
            <center><span>Email: <b>{{ $data['email'] }}</b></span></center>
            <center><span>Password: <b>{{ $data['password'] }}</b></span></center>
            <br />
            <hr style="border:1px solid #9F5A8D" />
            <br />
            <center><span>But your account is not active yet</span></center>
            <center><span>Click the button below to activate your account</span></center>
            <br />
            <br />
            <center><a href="{{ $data['link'] }}" style="background-color: #9F5A8D; color: #FFFFFF; padding: 10px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Activate Account</a></center>
            <br />
            <br />
        </div>
    </body>
</html>
