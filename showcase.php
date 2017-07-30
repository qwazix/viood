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
        <meta name="theme-color" content="#000000" />
        <script src="<?=$base_url?>general.js" type="text/javascript"></script>
        <link rel="icon" type="image/png" href="<?=$base_url?>favicon.png" />
        <link rel="apple-touch-icon" href="<?=$base_url?>icon.png"/>
        <link rel="shortcut icon" href="<?=$base_url?>icon.png" />
        <link rel="stylesheet" href="<?=$base_url?>style.css">
    </head>
    <body class=showcase>
        <div class=two style="background-image: url('<?=$base_url?>image/<?=$array[1]?>')"></div>
        <div class=one style="background-image: url('<?=$base_url?>image/<?=$array[0]?>')"></div>
        <script>
            var all = <?= json_encode($array)?>;
            var current = 1;
            //we change the image halfway through the slideshow so that we give it time to load
            var alternator = true;
                
            var two = document.querySelector('.showcase .two');
            var one = document.querySelector('.showcase .one');
            
            setInterval(function(){
                if(two.style.opacity == 0) {
                    if (alternator) two.style.backgroundImage = "url('<?=$base_url?>"+all[current++]+"')";
                    else two.style.opacity = 1;
                } else {
                    if (alternator) one.style.backgroundImage = "url('<?=$base_url?>"+all[current++]+"')";
                    else two.style.opacity = 0;
                }
                if(current>=all.length) current = 0;
                alternator=!alternator;
            },500000)
        </script>
    </body>
</html>