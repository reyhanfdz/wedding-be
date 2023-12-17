<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
    <body style="padding: 0px; margin: 0px;">
        <div style="padding: 10px; border-radius: 10px; background-color: #FCE5F5; color: #9F5A8D">
            <center><h3>Undangan Pernikahan</h3></center>
            <br />
            <span>Assalamualaikum Warahmatullahi Wabarakatuh</span>
            <br />
            <br />
            <span>Tanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i</span><br />
            <span>untuk menghadiri acara pernikahan kami, pada:</span>
            <br/>
            <br/>
            <span><b>Akad</b></span><br />
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tanggal: {{ $data['ceremonial']['date'] }}</span><br />
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Waktu: {{ $data['ceremonial']['start_time'] }} - {{ $data['ceremonial']['end_time'] }}</span><br />
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lokasi: {{ $data['ceremonial']['address'] }}</span><br />
            <br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ $data['ceremonial']['link'] }}" style="background-color: #9F5A8D; color: #FFFFFF; padding: 5px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Google Maps</a>
            <br/>
            <br/>
            <br/>
            <span><b>Resepsi</b></span><br />
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tanggal: {{ $data['party']['date'] }}</span><br />
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Waktu: {{ $data['party']['start_time'] }} - {{ $data['party']['end_time'] }}</span><br />
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lokasi: {{ $data['party']['address'] }}</span><br />
            <br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ $data['party']['link'] }}" style="background-color: #9F5A8D; color: #FFFFFF; padding: 5px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Google Maps</a>
            <br />
            <br />
            <br />
            <span><b>Mulung Mantu</b></span><br />
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tanggal: {{ $data['traditional']['date'] }}</span><br />
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Waktu: {{ $data['traditional']['start_time'] }} - {{ $data['traditional']['end_time'] }}</span><br />
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lokasi: {{ $data['traditional']['address'] }}</span><br />
            <br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ $data['traditional']['link'] }}" style="background-color: #9F5A8D; color: #FFFFFF; padding: 5px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Google Maps</a>
            <br />
            <br />
            <br />
            <span>Suatu kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan untuk hadir</span><br />
            <span>dan memberikan doa restu.</span><br />
            <span>Terima kasih banyak atas perhatiannya.</span>
            <br />
            <br />
            <span>Wassalamualaikum Warahmatullahi Wabarakatuh</span>
            <br />
            <br />
            <span><b>{{ $data['from'] }}</b></span>
            <br />
            <br />
            <br />
            <a href="{{ $data['link'] }}" style="background-color: #9F5A8D; color: #FFFFFF; padding: 10px; border-radius: 5px; text-decoration: none; margin-bottom: 10px">Buka Undangan</a>
            <br />
            <br />
        </div>
    </body>
</html>
