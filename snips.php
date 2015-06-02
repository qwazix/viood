<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>



<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <script src="<?=$base_url?>general.js" type="text/javascript"></script>
        <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,700' rel='stylesheet' type='text/css'>
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
                padding: 0;
                margin: 0;
            }
            img{
                display: block;
                margin: 0 auto;
            }

        </style>
        <!--<script src="masonry.pkgd.min.js"></script>-->
    </head>
    <body class="standalone" style="background-image: url('<?php echo $base_url.$image ?>'); "> <!-- TODO longpress -->
        <!--<img id="main" src="<?php echo $base_url.$image ?>"/>-->
        <button class="fullscreen" onclick="fs()">⎚</button>
        <script>
            function resize(){
                var img = document.getElementById("main")
                var w = img.width
                var h = img.height
                var ww = window.innerWidth
                var wh = window.innerHeight
                if (w/h>ww/wh) {
                    img.style.height = "auto";
//                    img.style.width = ww+"px";
                    img.style.width = "100vw";
                    img.style.marginTop = (wh-img.height)/2+"px"
                } else {
                    img.style.width = "auto";
                    img.style.marginTop = "";
//                    img.style.height = wh+"px";
                    img.style.height = "100vh";
                }
                
            }   
            function keepTheSame(){ //fixme
                var fsbutton = document.querySelector(".fullscreen")
                fsbutton.style.left = window.innerWidth-50+"px"
                fsbutton.style.top = window.innerHeight-40+"px"
            }
            function fs(){
                if (
                    document.fullscreenElement ||
                    document.webkitFullscreenElement ||
                    document.mozFullScreenElement ||
                    document.msFullscreenElement
                ) {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                } else {
                    var elem = document.getElementsByTagName("body")[0];
                    if (elem.requestFullscreen) {
                      elem.requestFullscreen();
                    } else if (elem.msRequestFullscreen) {
                      elem.msRequestFullscreen();
                    } else if (elem.mozRequestFullScreen) {
                      elem.mozRequestFullScreen();
                    } else if (elem.webkitRequestFullscreen) {
                      elem.webkitRequestFullscreen();
                    }
                }
//                setTimeout(resize,1000);
            }
//            var intval = setInterval(function(){
//                if (document.getElementsByTagName("img")[0].width > 0 ){
//                    resize()
//                    clearInterval(intval)
//                }
//            },30);
            window.addEventListener("resize",keepTheSame)
//            window.addEventListener("resize",resize)
//            window.addEventListener('orientationchange', resize);
        </script>
    </body>
</html>
