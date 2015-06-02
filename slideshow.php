<?php

/*
    This file is part of viood.

    viood is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    viood is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

//this should have 
  //an inspect button that goes to the standalone viewer DONE
  //buttons on desktop WONTFIX
  //fullscreen button DONE
  //history replace so that back always goes back to the gallery view but DONE
  //one still can link to an image
  //keyboard navigation DONE

?>
<!DOCTYPE html>
<html>
    <head>
        <title>viood - <?=$galleryName?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <script src="<?=$base_url?>general.js" type="text/javascript"></script>
        <link rel="icon" type="image/png" href="<?=$base_url?>favicon.png" />
        <link rel="apple-touch-icon" href="<?=$base_url?>icon.png"/>
        <link rel="shortcut icon" href="<?=$base_url?>icon.png" />
        <!--<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,200,300,700' rel='stylesheet' type='text/css'>-->
        <script src="<?=$base_url?>swipe.js"></script>
        <style>
            body{
                text-align: center;
            }
              .swipe {
                overflow: hidden;
                visibility: hidden;
                position: relative;
              }
              .swipe-wrap {
                overflow: hidden;
                position: relative;
              }
              .swipe-wrap > div {
                float:left;
                width:100%;
                position: relative;
              }
        </style>
        <link rel="stylesheet" href="<?=$base_url?>style.css">
    </head>
    <body>
        <div id='slider' class='swipe'><div class='swipe-wrap'>
        <?php 
        foreach ($array as $name => $item) {
            if ($name == "__info__") continue;
            if (!is_array($item)) {
                //find if we should start from specific image
                if ($goToImage == $item) $goToImageIndex = $counter; ?> 
                <div>
                   <img class="picture" src="<?= $goToImage == $item || $goToImage == $previousItem ? getSlide($path . "/" . $item) : getThumb($path . "/" . $item) ?>" 
                                        data-src="<?= getSlide($path . "/" . $item) ?>"
                                        data-thumb="<?= getThumb($path . "/" . $item) ?>"
                                        data-fullimage="<?=$base_url."imageviewer/".$path . "/" . $item?>" 
                                        data-imagename="<?=$item?>"/> 
                </div>
                <?php $counter++; $previousItem = $item;
            }
        }
        ?>
        </div></div>
        <div class="toolbar">
            <button class="fullimage" onclick="window.location.href = document.querySelector('.swipe-wrap>div:nth-child('+(mySwipe.getPos()+1)+') img').getAttribute('data-fullimage')"><img src="<?=$base_url?>fullimage.svg" alt="1:1"></button>
            <button class="fullscreen" onclick="fs()"><img src="<?=$base_url?>fullscreen.svg" alt="⎚"></button>
        </div>
        <a class="back" href="<?=$internal?"javascript:history.back()" :$base_url.$requested_path?>">◀</a>
        <script>
            window.mySwipe = new Swipe(document.getElementById('slider'), {
                startSlide: <?=$goToImageIndex?>,
                speed: 400,
                auto: false,
                continuous: true,
                disableScroll: true,
                stopPropagation: true,
                callback: function(){
                    //count all images
                    var numberOfImages = document.querySelectorAll(".swipe-wrap>div").length
                    var currentImageIndex = mySwipe.getPos();
                    
                    //change url with each slide
                    var currentImage = getImage(currentImageIndex%numberOfImages);
                    var nextImage = getImage((currentImageIndex+1)%numberOfImages);
                    var previousImage = getImage((currentImageIndex-1)%numberOfImages);
                    var imageBeforePrevious = getImage((currentImageIndex-2)%numberOfImages);
                    var imageAfterNext = getImage((currentImageIndex+2)%numberOfImages);
                    
                    history.replaceState({},"",currentImage.getAttribute("data-imagename"));
                    
                    setTimeout(function(){
                    currentImage.src = currentImage.getAttribute("data-src");
                    if (nextImage) nextImage.src = nextImage.getAttribute("data-src");
                    if (previousImage) previousImage.src = previousImage.getAttribute("data-src");
                    
                    if (imageBeforePrevious) imageBeforePrevious.src = imageBeforePrevious.getAttribute("data-thumb");
                    if (imageAfterNext) imageAfterNext.src = imageAfterNext.getAttribute("data-thumb");
                    },500)
                }
            });
            //make arrow keys navigate the slideshow
            document.addEventListener("keydown",function(e){
                e = e || window.event;
                if (e.keyCode == '37') {
                    mySwipe.prev();
                }
                else if (e.keyCode == '39') {
                    mySwipe.next();
                }
              })
              
              function getImage(index){
                  var image = document.querySelector('.swipe-wrap>div:nth-child('+(index+1)+') img');
                  return image;
              }
              
              function fitToScreen(img){
                    var w = img.width
                    var h = img.height
                    var ww = window.innerWidth
                    var wh = window.innerHeight
                    if (w/h>ww/wh) {
                        img.style.height = "auto";
    //                    img.style.width = ww+"px";
                        img.style.width = "100vw";
//                        img.style.marginTop = (wh-img.height)/2+"px"
                    } else {
                        img.style.width = "auto";
//                        img.style.marginTop = "";
    //                    img.style.height = wh+"px";
                        img.style.height = "100vh";
                    }
              }
              
              function resetSize(img){
                  img.style.width = "";
                  img.style.height = "";
              }
              
              var imageCollection = document.querySelectorAll(".swipe-wrap>div>img")
              
              for (var i in imageCollection){
                  if (typeof imageCollection[i] == "object"){
                      imageCollection[i].addEventListener("load",function(){
                          if (this.src == this.getAttribute("data-src")) 
                              resetSize(this);
                          else
                              fitToScreen(this);
                      })
//                    if (Math.abs(mySwipe.getPos()-i+1)<2)
//                      fitToScreen(imageCollection[i]);
//                    else
//                      resetSize(imageCollection[i]);
                  }
              }
              
              window.addEventListener("resize",function(){
                  for (var i in imageCollection){ 
                      if (imageCollection[i] == imageCollection[i].getAttribute("data-src")) 
                              resetSize(imageCollection[i]);
                          else
                              fitToScreen(imageCollection[i]);
                  }
              })
        </script>
    </body>
</html>