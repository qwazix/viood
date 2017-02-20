<?php
include 'config.php';

class Livethumb{
    
    //CONFIGURATION - default settings
    private $checkCache=true; //set to true to check for cached Images
    private $cacheFolder="cache/";
    private $pathToImages="photos/"; //default path to images (to avoid setting path)
    private $fname=""; // default image (can be used for image not found)
    private $color="FFFFFF";
    private $thumbWidth=0;
    private $thumbHeight=0;
    private $inside=false;
    private $crop=false;
    private $stretch=false;
    private $largertoo=false;
    private $maketransparent=false;
    private $replacebackgroundwith=false;
    private $type=false;
    private $debug=false;
    private $cachedFile="";
    private $returnSrc=false; //set that to return path , not livethumb.php
    private $query='';
    
    public function Livethumb($arr){
        global $pictureDir;
        //// get data from url - availiable parameters
        if (isset($arr['pathToImages'])) $this->pathToImages = $arr['pathToImages'];
        $this->pathToImages = $pictureDir.$this->pathToImages;
//        echo $this->pathToImages;
        if (isset($arr['cacheFolder'])) $this->cacheFolder= $arr['cacheFolder'];
       
        if (isset($arr['checkCache']) ) $this->checkCache=true;
        if (isset($arr['fname']))    $this->fname = $arr['fname'];
        if (isset($arr['thumbWidth']) && is_numeric($arr['thumbWidth']) )    $this->thumbWidth = $arr['thumbWidth'];
        if (isset($arr['thumbHeight']) && is_numeric($arr['thumbHeight']) )    $this->thumbHeight = $arr['thumbHeight'];
        if (isset($arr['inside'])) $this->inside=true;
        if (isset($arr['crop'])) $this->crop=true; 
        if (isset($arr['color'])) $this->color=$arr['color'];
        if (isset($arr['stretch'])) $this->stretch=true;
        if (isset($arr['largertoo'])) $this->largertoo=true;
        if (isset($arr['maketransparent'])) $this->maketransparent=true; 
        if (isset($arr['replacebackgroundwith'])) $this->replacebackgroundwith=$arr['replacebackgroundwith'];
        if (isset($arr['debug'])) $this->debug=true;
        if (isset($arr['returnSrc'])) $this->returnSrc=true;
        

        if(file_exists($this->pathToImages . $this->fname))
            $info = pathinfo($this->pathToImages . $this->fname);
        else {
            //TODO return image with the following text
            //echo 'Image not found!->'.$this->pathToImages.$this->fname;
            
            // Create a 100*30 image
            $im = imagecreate($this->thumbWidth, $this->thumbHeight);

            // White background and blue text
            $bg = imagecolorallocate($im, 0, 0, 0);
            $textcolor = imagecolorallocate($im, 255, 255, 255);

            // Write the string at the top left
            imagestring($im, 5, 0, 0, chunk_split('Image not found!->'.$this->pathToImages.$this->fname,$this->thumbWidth/10) , $textcolor);

            // Output the image
            header('Content-type: image/png');

            imagepng($im);
            imagedestroy($im);
            
            return; 
        }

        //// check the type of the file   -----   we are supporting png and jpg
        if(strtolower($info['extension']) == 'jpg' || strtolower($info['extension']) == 'jpeg' ){ 
            $this->type = 'jpg';
        }elseif(strtolower($info['extension']) == 'png' ){ 
            $this->type = 'png';
        }else{
           // echo 'not right type'; 
            return ;
        }
        
        $query = ''; //to find the image on cache
        foreach($arr as $k => $str){
            $query.='&'.$k.'='.$str;
        }
        // create cache dir
        if (!file_exists($this->cacheFolder)) mkdir ($this->cacheFolder);
        
        $this->query=$query;
        $this->cachedFile = $this->cacheFolder . $this->imageHash($query) . $this->fname;
        /////////////// END OF SETUP
    }
    
    public function getSrc(){
        if($this->checkCache && $this->imageInCache() ){ //
            return $this->cachedFile;
        }else{
            return 'livethumb.php?'.$this->query;
        }
    }

    public function getImage(){
        $this->run( $this->checkCache, $this->cacheFolder, $this->cachedFile, $this->pathToImages, $this->fname, $this->color, $this->thumbWidth,
            $this->thumbHeight, $this->inside, $this->crop, $this->stretch, $this->largertoo, $this->maketransparent, 
            $this->replacebackgroundwith, $this->type, $this->debug);
    }
    
    public function doWhatWeDO($thumbWidth,$thumbHeight,$width,$height,$inside){        
        $mybool = $this->smallestRatioDifference($thumbWidth,$thumbHeight,$width,$height);
        if ($inside)  $mybool = !$mybool; // match the biggest ratio difference dimension
        if ($mybool) { //height then width
           if( $width > $thumbWidth && $height > $thumbHeight ) //
               $newHeight = $thumbHeight; 
           else {
               $newHeight = $height;
           }
           $newWidth = floor($width * ( $newHeight / $height ));
        }else { //width then height
            if ($width > $thumbWidth && $height > $thumbHeight )
                $newWidth = $thumbWidth; 
            else {
                $newWidth = $width;
            }
            $newHeight = floor($height * ( $newWidth / $width ));
        }
        return array('newHeight'=>$newHeight , 'newWidth'=>$newWidth);   
    }
    
    /** i pleura pou mporei na mikrinei ligotero / 1:height 0:width */
    function smallestRatioDifference($thumbWidth,$thumbHeight,$width,$height){
        if( $width / $thumbWidth > $height / $thumbHeight ){
            return 1;
        }else return 0;
    }
    
    /** Checks if the resized image can fit inside both the given dimensions.*/
    function theResizedIsSmaller($thumbWidth,$thumbHeight,$newWidth,$newHeight){
        if($newWidth<$thumbWidth && $newHeight<$thumbHeight){
            return true;
        }else return false;
    }
    
    
    private function run($checkCache, $cacheFolder, $cachedFile, $pathToImages, $fname, $color, $thumbWidth,
                    $thumbHeight, $inside, $crop, $stretch, $largertoo, $maketransparent, $replacebackgroundwith, $type, $debug){
        if(!$debug)header("Content-type: image/$type");

        if ($this->checkCache && $this->imageInCache() ) {   //cache?  
            //echo 'saving in cache';
            $this->cachePrint();
        }

        // load image and get image size
        $img = $this->imageResource($pathToImages.$fname, $this->type);

        // calculate thumbnail size
        $newHeight=0;
        $newWidth=0;
        $width = imagesx( $img );
        $height = imagesy( $img );
        
        if ($thumbWidth > 0 && $thumbHeight > 0) { //iparxoun kai ta duo
            if($stretch){
                $newHeight=$thumbHeight;
                $newWidth=$thumbWidth;
            }elseif($crop && !$inside){
                if($debug) echo 'crop && !inside';
                $newarr = $this->doWhatWeDO($thumbWidth, $thumbHeight, $width, $height, $inside);
                $newHeight = $newarr['newHeight'];
                $newWidth = $newarr['newWidth'];
            }else{ 
                $mybool = $width / $thumbWidth > $height / $thumbHeight;
                //echo $width.' '.$thumbWidth.' '.$width / $thumbWidth.' '.$height.' '.$thumbHeight.' '.$height / $thumbHeight;die();
                ////tote einai to height oriako / i pleura pou mporei na mikrinei ligotero / 1:height 0:width
                if ($inside)  $mybool = !$mybool; // match the big dimension
                if ($mybool) { //height then width
                    //echo 'a';
                    if( $width > $thumbWidth && $height > $thumbHeight || $largertoo )
                        $newHeight = $thumbHeight; 
                    else {
                        $newHeight = $height;
                    }
                    if($crop && $inside){
                        $aspect=$thumbWidth/$thumbHeight;
                        $newWidth = $width;
                        $newHeight = floor($height * 1/($thumbWidth/$thumbHeight));
                    }else
                        $newWidth = floor($width * ( $newHeight / $height ));
                } else { //width then height
                    if ($width > $thumbWidth && $height > $thumbHeight || $largertoo)
                        $newWidth = $thumbWidth; 
                    else {
                        $newWidth = $width;
                    }
                    if($crop && $inside){
                        $aspect=$thumbHeight/$thumbWidth;
                        $newHeight = $height;
                        $newWidth = floor($width * 1/($thumbHeight/$thumbWidth));
                    }else
                        $newHeight = floor($height * ( $newWidth / $width ));
                }
            }
            if($debug)(var_dump('newW'.$newWidth , 'newH'.$newHeight ,'w'.$width ,'h'.$height ));
        }
        elseif( $thumbHeight>0 && $thumbWidth==0 ){//an uparxei thumbheight xoris thumbwidth
            if($stretch){
                $newHeight=$thumbHeight;
                $newWidth=$width;
            }elseif($crop){
                $newHeight=$thumbHeight;
                $newWidth=$width;
            }else{  
                $newHeight=$thumbHeight;
                $newWidth= floor( $width * ( $newHeight / $height ) );//keep aspect
            }
        }
        elseif( $thumbWidth>0 && $thumbHeight==0 ){//an uparxei thumbwidth xoris thumbheight
            if($stretch){
                $newHeight=$height;
                $newWidth=$thumbWidth;
            }elseif($crop){
                $newHeight=$height;
                $newWidth=$thumbWidth;
            }else{
                $newWidth=$thumbWidth;
                $newHeight= floor( $height * ( $newWidth / $width ) );//keep aspect
            }
        }
        else{ //den exei kanena
            $newHeight=$height;
            $newWidth=$width;
        }
        //if($debug)var_dump($thumbWidth ,$thumbHeight ,$newWidth,$newHeight);
        if ($crop) 
            $tmp_img = imagecreatetruecolor( $thumbWidth, $thumbHeight );
        else $tmp_img = imagecreatetruecolor( $newWidth, $newHeight );

        // transform a color from FFF or FFFFFF to an array of 3 F or 3 FF
        $len=strlen($color);
        $colors = array();
        for($i=1;$i<=3;$i++){
            $colors[$i]=substr($color, (($len/3)*$i)-$len/3, $len/3);
        }
        if($debug)print_r($colors);

        //// create a new image
        imagefill($tmp_img, 0, 0, imagecolorallocatealpha($tmp_img, "0x".$colors["1"], "0x".$colors["2"], "0x".$colors["1"], 127));
        if ( $this->type == 'png' ){ //cater for transparency
           imagealphablending($tmp_img, false);
           imagesavealpha($tmp_img, true);
        }
        // copy and resize old image into new image
        if($debug)var_dump($tmp_img, $img, ($thumbWidth-$width)/2, ($thumbHeight-$height)/2, 0, 0, $newWidth, $newHeight, $width, $height);

        if ($crop && !$inside && !$this->theResizedIsSmaller($thumbWidth, $thumbHeight, $newWidth, $newHeight)){
            imagecopyresampled( $tmp_img, $img, ($thumbWidth-$newWidth)/2, ($thumbHeight-$newHeight)/2, 0, 0, $newWidth, $newHeight, $width, $height );
        }elseif($crop && $inside && !$this->theResizedIsSmaller($thumbWidth, $thumbHeight, $newWidth, $newHeight)){
            imagecopy($tmp_img, $img, ($newWidth-$width)/2, ($newHeight-$height)/2, 0, 0, $width, $height);
        }elseif($crop && $this->theResizedIsSmaller($thumbWidth, $thumbHeight, $newWidth, $newHeight)){
            imagecopyresampled( $tmp_img, $img, ($thumbWidth-$newWidth)/2, ($thumbHeight-$newHeight)/2, 0, 0, $newWidth, $newHeight, $width, $height );
        }else{
            imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height );
        }


        //make white transparent
        if (($maketransparent || $replacebackgroundwith) && $this->type=='jpg') {
            imagesavealpha($tmp_img, true);
            imagealphablending($tmp_img, false);
            for ($i=0;$i<imagesx($tmp_img);$i++){
                for ($j=0;$j<imagesy($tmp_img);$j++){
                    $rgb = imagecolorat($tmp_img, $i, $j);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    if ($replacebackgroundwith){
                        $rbw = hexdec($replacebackgroundwith);
                        $ro = ($rbw >> 16) & 0xFF;
                        $go = ($rbw >> 8) & 0xFF;
                        $bo = $rbw & 0xFF;
                    }
                    $color = imagecolorallocatealpha($tmp_img, $ro, $go, $bo, 127);
                    $semi = imagecolorallocatealpha($tmp_img, $ro, $go, $bo, 90);
                    if ($r > 250 && $g > 250 && $b > 250) imagesetpixel($tmp_img, $i, $j, $color); else
                    if ($r > 230 && $g > 230 && $b > 230) imagesetpixel($tmp_img, $i, $j, $semi);
                }
            }
           if($maketransparent) $this->type = 'png';
           imagealphablending($tmp_img, false);
        }

        if ($checkCache) { 
             $this->imagePrint($tmp_img, $this->type, $cachedFile);
        }else $this->imagePrint($tmp_img, $this->type);

        imagedestroy($img);
        imagedestroy($tmp_img);
    }
    
    /** TRUE if a valid cached image is found.
     * Valid:file exists & is newer than the parent file. */
    private function imageInCache(){
        if (file_exists($this->cachedFile) && 
           ( filemtime($this->cachedFile)>filemtime($this->pathToImages.$this->fname)) &&
           ( filectime($this->cachedFile)>filectime($this->pathToImages.$this->fname)) ) {
            return true;
        }else return false;
    }
    
    /** Print image from cache and end script */
    private function cachePrint(){
        $img = $this->imageResource($this->cachedFile, $this->type);
        $this->imagePrint($img, $this->type);
    }

    private function imageHash($string){
        $imgHash=substr(md5($string),0,15);
        return $imgHash;
    }

    /** load image as resource */
    private function imageResource($filename,$type){ 
        if ($type == 'jpg') 
            return $img = imagecreatefromjpeg( $filename );
        else if ($type == 'png') 
            return $img = imagecreatefrompng( $filename );
        else return ;
    }

    /** Print thumbnail. If filename is given save into a file. */
    private function imagePrint($file, $type, $filename=null) {
        //echo 'image save to disk';
        global $debug;
        if($debug)return;
        if ($type == "jpg") {
            if ($filename == null) {
                imagejpeg($file);
            }else{
                imagejpeg($file, $filename);
                imagejpeg($file);
            }
        } elseif ($type == "png") {
            if ($filename == null) {
                imagepng($file);
            }else{
                imagepng($file,$filename);
                imagepng($file);
            }
        }
    }
}
?>
