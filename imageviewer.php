<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title><?=$item?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="theme-color" content="#000000" />
        <link rel="icon" type="image/png" href="<?=$base_url?>favicon.png" />
        <link rel="apple-touch-icon" href="<?=$base_url?>icon.png"/>
        <link rel="shortcut icon" href="<?=$base_url?>icon.png" />
        <script src="<?=$base_url?>general.js" type="text/javascript"></script>
        <!--<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,200,300,700' rel='stylesheet' type='text/css'>-->
        <link rel="stylesheet" href="<?=$base_url?>style.css">
        <style>
            body,html{
                width: 100%;
                height: 100%;
                text-align: center;
            }
            body{
                background-size: contain; 
                background-color: black;
                background-repeat: no-repeat;
                background-position: center center;
            }
            img{
                display: block;
                margin: 0 auto;
            }
            img#main{
                max-height: 100%;
                max-width: 100%;
                position: absolute;
                margin: auto;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
               
            }
            button.fullscreen{
                opacity: 0;
            }

        </style>
    </head>
    <body class="standalone" > 
        <img id="main" src="<?php echo $base_url.'image'.$image ?>" onclick="<?=$internal?"javascript:history.back()" : "javascript:window.location.href='".$base_url.str_replace("/imageviewer/".$pictureDir,"slideshow/",$requested_path)."'"?>"/>
        <div class="toolbar"><button class="fullscreen" onclick="fs()"><img src="<?=$base_url?>fullscreen.svg" alt="⎚"></button></div>
    </body>
</html>
