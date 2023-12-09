<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
    <body style="padding: 0px; margin: 0px;">
        <div style="padding: 10px; border-radius: 10px; background-color: #FCE5F5; color: #9F5A8D">
            <center><h3>Account Disabled</h3></center>
            <br />
            <center><span>Your account has been disabled by admin</span></center>
            <center><span>Contact admin to enable your account (email: {!! env('EMAIL_ADMIN') !!} , whatsapp: {!! env('PHONE_WHATSAPP') !!})</span></center>
            <br />
            <br />
        </div>
    </body>
</html>
