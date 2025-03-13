<html>
    <head>
        <style type="text/css">

        </style>
    </head>
<body>
<div>{!!DNS2D::getBarcodeHTML($eventOpenSeat->barcode, 'QRCODE')!!}</div>
<center><p style="font-size:16px;">{{$eventOpenSeat->seat_name}}<br>{{$objAttendee['name']}}</p></center>
</body>
</html>
