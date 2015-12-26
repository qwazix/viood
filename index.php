<?php
/*
 *  © 2014 Michael Demetriou
 * 
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
require_once './config.php';
require_once './functions.php';
require_once './Livethumb.class.php';
//require_once './averageColor.php';

if (strstr($_SERVER["HTTP_REFERER"],$base_url)===FALSE) $internal = false; else $internal=true;
$requested_path = str_replace("/viood", "", $_SERVER['REQUEST_URI']);
$requested_path = urldecode($requested_path);
$requested_path_array = explode("/", $requested_path); 

//remove the first empty entry
array_shift($requested_path_array);

//handle the different pages
if ($requested_path_array[0]=="imageviewer"){
    $image = str_replace("imageviewer/", "", $requested_path);
    include 'imageviewer.php';
    
//slideshow    
} else if ($requested_path_array[0]=="slideshow"){ 
    $goToImage = "";
    $goToImageIndex = 0;
    $counter = 0;
    $requested_path = str_replace("/slideshow/", "", $requested_path);
    if (!is_dir($pictureDir."/".$requested_path)) { 
        $goToImage = basename($requested_path);
        $requested_path = str_replace($goToImage, "", $requested_path);
    }
    $path = $pictureDir."/".$requested_path;
    $array = recurse_dir($pictureDir."/".$requested_path, $galleries, 1);
    $galleryName = basename($requested_path);
//    echo $requested_path; die;
    include 'slideshow.php';
    
    //showcase    
} else if ($requested_path_array[0]=="showcase"){ 
    $counter = 0;
    $requested_path = str_replace("/showcase/", "", $requested_path);
    $path = $pictureDir."/".$requested_path;
    $array = flatten(recurse_dir($pictureDir."/".$requested_path, $galleries, 100, false, true));
    shuffle($array);
    $galleryName = basename($requested_path);
//    echo $requested_path; die;
    include 'showcase.php';
    
//gallery    
} else {
    $galleryInfo = getGalleryInfo($pictureDir."/".$requested_path); 
    $galleryName = "- ".$galleryInfo["name"];
    $array = recurse_dir($pictureDir."/".$requested_path, $galleries, 1);
    ksort($array);
	$hasImages = hasImages($array);
    include './gallery.php';
}