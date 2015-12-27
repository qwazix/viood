<?php
require_once 'config.php';
require_once 'functions.php';

header("Content-type: text/javascript");
header("Access-Control-Allow-Origin: *");

$galleries = array();

if (isset($_GET['showcase'])) {
    $array = flatten(recurse_dir($pictureDir, $galleries, 100 , false, true));
    shuffle($array);
    echo json_encode($array);
} else {
    echo "var list=\n".json_encode(recurse_dir($pictureDir, $galleries, 100*showcase ,!$showcase, $showcase),JSON_PRETTY_PRINT);
}




//
