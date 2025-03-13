<!DOCTYPE html>
<style>
    @font-face {
        font-family: 'Mulish';
        src: url({{ storage_path('private_assets/fonts/Mulish-Regular.ttf') }}) format("truetype");
    }
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0 0 .5cm;
        background-color: #ffffff;
        font: 10pt "Mulish";
    }
    .uppercase {
        text-transform: uppercase;
    }
</style>
<center>
<h2 class="uppercase">
    ABSENSI {{ $event->name }}<br>
    PT. JEJAK IMANI BERKAH BERSAMA
</h2>
</center>