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
                left: 0;
                bottom: 0;
                right: 0;
                margin: auto;
                width: 800px;
                height: 1422px;
                background-size: contain;
                background-position: center center;
                background-repeat: no-repeat;
            }
            .name {
                z-index: 1;
                position: absolute;
                top: 1000px;
                left: 0;
                right:0;
                font-family: 'Mulish-ExtraBold';
                font-weight: bold;
                font-size: 2em;
                padding: 0 36px;
            }
            .without_photo .name {
                top: 780px;
            }

            .wrap-avatar {
                z-index: -2;
                position: absolute;
                width: 560px;
                height: 560px;
                margin: auto;
                top: 280px;
                left: 0;
                right: 0;
            }

            .container-svg {
                position: absolute;
                display: table;
                width: 560px;
                height: 560px;
            }
            .subcontainer-svg {
                display: table-cell;
                vertical-align: middle;
                text-align: center;
            }
            .img-svg-editing {
                width: 520px;
                height: 660px;
                max-width: 560px;
            }

            .img-svg {
                height: auto;
                max-width: 560px;
                max-height: 560px;
            }
        </style>
    </head>
<body>
<div class="wrapper">
    @if($withPhoto)
        <img class="background" style="background: url({{ storage_path('private_assets/images/milad_card_umroh.png') }});">
        <div class="wrap-avatar" @if ($editing) style="top: 210px;" @endif>
            <div class="container-svg">
                <div class="subcontainer-svg">
                    <img class="@if($editing) img-svg-editing  @else img-svg @endif" src="{{$photo}}"  />
                </div>
            </div>
        </div>
    @else
        <img class="background" style="background: url({{ storage_path('private_assets/images/milad_card_umroh_without_photo.jpg') }});">
    @endif
    <div @if(!$withPhoto) class="without_photo" @endif>
        <div class="name">
            <center>{{$title}} {{$participant->front_title ?? ''}} {{ucwords(strtolower($participant->name))}} {{$participant->back_title ?? ''}}</center>
        </div>
    </div>
</div>
</body>
</html>
