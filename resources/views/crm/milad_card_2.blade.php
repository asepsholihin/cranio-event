<html>
    <head>
        <style type="text/css">
            @font-face {
                font-family: 'Mulish-ExtraBold';
                src: url({{ storage_path('private_assets/fonts/Mulish-ExtraBold.ttf') }}) format("truetype");
            }
            .wrapper {
                position: relative;
                width: 800px;
                height: 1422px;
            }
            .background {
                z-index: -1;
                position: absolute;
                top: 0;
                width: 800px;
                height: 1422px;
                background: url({{ storage_path('private_assets/images/milad_card_umroh.png') }});
            }
            .name {
                z-index: 1;
                position: absolute;
                top: 720px;
                left: 0;
                right:0;
                font-family: 'Mulish-ExtraBold';
                font-weight: bold;
                font-size: 2em;
                padding: 0 36px;
            }

            .wrap-avatar {
                z-index: -2;
                position: absolute;
                width: 410px;
                height: 350px;
                margin: auto;
                top: 470px;
                left: 0;
                right: 0;
            }
            .container-svg { 
                position: absolute;
                display: table;
                width: 410px;
                height: 350px;
            }
            .subcontainer-svg {
                display: table-cell;
                vertical-align: middle;
                text-align: center;
            }
            .img-svg {
                height: auto;
                max-width: 410px;
                max-height: 350px;
            }

        </style>
    </head>
<body>
<div class="wrapper">
    <img class="background" src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/milad_card_umroh_without_photo.jpg')))}}" alt="">
    <div class="name">
        <center>{{$title}} {{$participant->front_title ?? ''}} {{ucwords(strtolower($participant->name))}} {{$participant->back_title ?? ''}}</center>
    </div>
</div>
</body>
</html>
