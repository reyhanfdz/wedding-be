<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
    <body style="padding: 0px; margin: 0px;">
        <div style="padding: 10px; border-radius: 10px; background-color: #FCE5F5; color: #9F5A8D">
            <center><h3>Your QR Successfully Generated</h3></center>
            <center>
            <div style="padding: 10px; background-color: #FFFFFF; border-radius: 10px; width: 320px">
                <center><img src="{{ $data['link_qr'] }}" style="width: 300px" alt="qr"/></center>
            </div>
            </center>
            <br />
            <center><span>Use this QR code for your attendance</span></center>
            <center><span>Or Your can manual sign</span></center>
            <center><span>by sign a signature book</span></center>
            <br />
            <br />
            <center><a href="{{ $data['link_qr'] }}" style="background-color: #9F5A8D; color: #FFFFFF; padding: 10px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Open QR Code Url</a></center>
            <br />
            <br />
            <hr style="border:1px solid #9F5A8D" />
            <br />
            <center><span style="font-size: 10px">If you have problem with your QR</span></center>
            <center><span style="font-size: 10px">Or forgot your QR, you can regenerate new QR</span></center>
            <center><span style="font-size: 10px">By sending message for request New QR</center></span>
            <center><span style="font-size: 10px">to admin via <a href="mailto:mugnirusmana95@gmail.com">Email</a> or <a href="https://wa.me/+628980500453">Whatsapp</a></span></center>
            <br />
            <br />
        </div>
    </body>
</html>
