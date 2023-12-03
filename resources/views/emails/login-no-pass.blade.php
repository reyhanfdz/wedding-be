<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
    <body style="padding: 0px; margin: 0px;">
        <div style="padding: 10px; border-radius: 10px; background-color: #FCE5F5; color: #9F5A8D">
            <center><h3>Login Without Password</h3></center>
            <br />
            <center><span>We have received a request to login without password</span></center>
            <center><span>Copy code below to login</span></center>
            <br />
            <br />
            <center><h1>{{ $data['code'] }}</h1></center>
            <br />
            <br />
            <hr style="border:1px solid #9F5A8D" />
            <br />
            <center><span style="font-size: 10px">Code login without password valid until <b>{{ $data['valid_token_date'] }}</b></span></center>
            <br />
            <br />
        </div>
    </body>
</html>
