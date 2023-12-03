<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
    <body style="padding: 0px; margin: 0px;">
        <div style="padding: 10px; border-radius: 10px; background-color: #FCE5F5; color: #9F5A8D">
            <center><h3>Activate Account</h3></center>
            <br />
            <center><span>We have received a request to activate your account</span></center>
            <center><span>Click the button below to activated your account</span></center>
            <br />
            <br />
            <center><a href="{{ $data['link'] }}" style="background-color: #9F5A8D; color: #FFFFFF; padding: 10px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Open Url</a></center>
            <br />
            <br />
            <hr style="border:1px solid #9F5A8D" />
            <br />
            <center><span style="font-size: 10px">URL activate account valid until <b>{{ $data['valid_token_date'] }}</b></span></center>
            <br />
            <br />
        </div>
    </body>
</html>
