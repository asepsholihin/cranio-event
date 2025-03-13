<html>
    <head>
        <style type="text/css">

        </style>
    </head>
<body>
<div>{!!DNS2D::getBarcodeHTML($participant->barcode, 'QRCODE')!!}</div>
<p style="font-size:12px;margin-bottom:6px"><strong>{{$event->name}}<strong></p>
<p style="font-size:14px;margin:0;margin-bottom:6px">{{$participant->name}}</p>
<p style="font-size:12px;margin:0">Nomor Meja: {{$seat_name}}</p>
</body>
</html>
