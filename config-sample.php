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
    along with viood.  If not, see <http://www.gnu.org/licenses/>.
*/

// If you run viood in a subfolder of your webserver,
// configure this here

$folder = "viood/";
$pictureDir = "photos-sample/"; //don't forget the trailing slash
$base_url = "http://".$_SERVER["HTTP_HOST"]."/viood/";

$square = true; //square or rectangle photo thumbnails?

$title = "viood - qwazix's photo gallery";

// If you want some common header bar for all your sites you can set the url relative
// to the root of your vhost here

$navbar = 'nav.php';

// Extra css for this can be added to extra.css. I promise I will not update that file.
