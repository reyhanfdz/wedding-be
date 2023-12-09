<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
    <body style="padding: 0px; margin: 0px;">
        <div style="padding: 10px; border-radius: 10px; background-color: #FCE5F5; color: #9F5A8D">
            <center><h3>Account Inactive</h3></center>
            <br />
            <center><span>Your account has been inactiveted by admin</span></center>
            <center><span>Click the button below to reactivate</span></center>
            <br />
            <br />
            <center><a href="{{ $data['link'] }}" style="background-color: #9F5A8D; color: #FFFFFF; padding: 10px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Reactivate</a></center>
            <br />
            <br />
        </div>
    </body>
</html>
