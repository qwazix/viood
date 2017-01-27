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

require_once 'config.php';

/**
 * Recurses into a directory and writes the structure
 * in an associative array
 * @global string $pictureDir from config.php
 * @param string $directory The directory to recurse into
 * @param array $array The array to put the results in
 * @param int $depth How deep should it go? (Default 1)
 * @return array
 * 
 */
function recurse_dir($directory, $array, $depth=1, $include_info=true, $full_path=false) {
//    echo $directory,"<br><br>";
    global $pictureDir;
    $dirName = basename($directory);
    if (!is_dir($directory)) include '404.php';
    $gallery_info = getGalleryInfo($dirName);
    //get images to be ignored from gallery.json
    if (is_array($gallery_info["ignore"])) $ignore_images = $gallery_info["ignore"]; 
    else $ignore_images = array(); 
    if ($handle = opendir($directory)) {
        //for each entry found inside $directory
        while (false !== ($entry = readdir($handle))) {
            $path  = $directory."/".$entry;
            //echo "$pictureDir -- $dirName -- $path -- $entry \n </br>";
            //ignore . and .. , write directories to the array and recurse into them
            if ($entry != "." && $entry != ".." && !in_array($entry,$ignore_images)) {
                if (is_dir($path)) { 
                    $array[$entry] = array();
                    if ($include_info) $array[$entry]["__info__"] = getGalleryInfo($path);
                    if ($depth!=0) $array[$entry] = recurse_dir($path, $array[$entry], $depth - 1, $include_info, $full_path);
                } else if(is_image($path)){ 
                    $array[$entry]= $full_path?$path:$entry;
                }
            }
        }
        closedir($handle);
    } //echo "final"; _print_r($array);
    return $array;
}

/**
 * Prints the html required to show a gallery
 * @global string $base_url from config.php
 * @global string $pictureDir from config.php
 * @param string $path the path of the gallery we're printing div's from
 * @param array the array with the directory structure
 */
function print_divs($path, $array) {
    global $base_url;
    global $pictureDir;
    $rel_path = str_replace($pictureDir, "", $path);
    foreach ($array as $name => $item) {
        if ($name == "__info__") continue;
        if (is_array($item) && count($item)) { 
            $info=array_shift($item);
            //check if hidden
            if (!isset($info['hidden']) || $info['hidden']==false){
                //check json for flagship
                if (isset($info['flagship']) && file_exists($path . "/" . $name . "/" . $info['flagship']))
                        $galleryFlagship = $info['flagship']; else $galleryFlagship = reset($item);
                ?><a class="gallery" href="<?=$name?>/"><!--
                    --><div class="galleryOverlay"><h3><?php echo $info["name"]; ?></h3><?=$info["description"]?></div>
                    <img class="gallery" src="<?= getThumb($path . "/" . $name . "/" . $galleryFlagship ) ?>"/></a><?php
            }
        } else { //sorry for the mess we must not have any spaces between inline-block items
            ?><a href="<?=$base_url."slideshow".$rel_path . $item?>"/><img class="picture" src="<?= getThumb($path . $item) ?>"/></a><?php
        }
    }
}
/**
 * Returns the mime type of a file using php fileinfo
 * @param string $file path to file
 * @return string
 */
function mimetype($file){
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimetype = finfo_file($finfo, $file);
    finfo_close($finfo);
    
    return $mimetype;
}
/**
 * Checks mime type to see if file is image
 * @param string $file the path to file to check if is image
 * @return type
 */
function is_image($file){
    return strstr(mimetype($file),"image")!==FALSE;
}

/**
 * Same as builtin print_r but wrapped in <pre> tags
 * If you have php_pdl you can uncomment two lines so that it overrides
 * the default print_r
 * @param type $array
 */
//override_function('print_r', '$array', 'return _print_r($array);');
function _print_r($array){
        echo "<pre>"; 
        print_r($array);
//        __overridden__($array); 
        echo "</pre>"; 
}
/**
 * Passes the image to liveThumb and returns the thumbnail
 * @global string $base_url
 * @param string $path The image path
 * @return string The thumbnail url 
 */
function getThumb($path){
    global $base_url;
    $lt = new Livethumb(array("thumbWidth"=>200, "thumbHeight"=>200, "fname"=>basename($path), "pathToImages"=>  str_replace(basename($path), "", $path)));
    return $base_url.$lt->getSrc();
}
/**
 * Same as getThumb() but returns a bigger image
 * @global string $base_url
 * @param string $path The image path
 * @return string The thumbnail url
 */
function getSlide($path){
    global $base_url;
    $lt = new Livethumb(array("thumbWidth"=>1980, "thumbHeight"=>1980, "fname"=>basename($path), "pathToImages"=>  str_replace(basename($path), "", $path)));
    return $base_url.$lt->getSrc();
}
/**
 * Reads gallery.json and returns the data decoded into an associative array
 * @param string $path The path to the gallery
 * @return array The gallery info as an array
 */
function getGalleryInfo($path){
    if (file_exists($path."/gallery.json"))
    return json_decode(file_get_contents($path."/gallery.json"),true);
    else return array();
}

/**
 * Checks if there are standalone images in the requested path or only other galleries
 * @param array $gallery_array The gallery
 * @return bool Whether the gallery has images or not
 */

function hasImages($gallery_array){
    foreach ($gallery_array as $item){
        if (!is_array($item)){
            return true;
        }
    }
    return false;
}

function printSlideshowIcon($requested_path){
    if (hasImages($requested_path)) { ?>
    <a href="<?= $base_url."slideshow".$requested_path ?>/">
        <img src="<?=$base_url?>slideshow.svg" alt="slideshow">
    </a>
<?php }
}

/**
 * Flattens the recursive array into just a list of images. Removes all info nodes
 * @param array $array The gallery multidimensional array
 * @return array A monodimentional array
 */
function flatten($array){
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { if ($a)$return[] = $a; });
    return $return;
}
