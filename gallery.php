<!DOCTYPE html>
<!--
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
-->
<html>
    <head>
        <title>viood - qwazix's photo gallery <?=$galleryName?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="theme-color" content="#000000" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <link rel="icon" type="image/png" href="<?=$base_url?>favicon.png" />
        <link rel="apple-touch-icon" href="<?=$base_url?>icon.png"/>
        <link rel="shortcut icon" href="<?=$base_url?>icon.png" />
        <link rel="stylesheet" href="<?=$base_url?>style.css">
        <link rel="stylesheet" href="<?=$base_url?>extra.css">
        <link rel="apple-touch-startup-image" href="startup.png" />
        <script src="<?=$base_url?>general.js" type="text/javascript"></script>
        <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,200,300,700' rel='stylesheet' type='text/css'>
    </head>
    <body class="gallery">
        <header>
            <?php include '../'.$navbar ?>
            <h1><?=$galleryInfo["name"]?></h1>
        </header>
        <p><?=$galleryInfo["description"]?></p>
        <div class="gallery">
            <?php print_divs($requested_path,$array); //TODO recurse ?>
        </div>
        <?php if ($requested_path != "" && $requested_path != "/") { ?>
            <a class="back" href="<?= $internal?"javascript:history.back()" :$base_url.preg_replace('#/.*/?$#', "", $requested_path)?>">◀</a>
        <?php } ?>
	<footer>This gallery is free software - get it on <a target="_blank" href="https://github.com/qwazix/viood">github</a></footer>
    </body>
</html>
