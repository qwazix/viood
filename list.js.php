<?php
require_once 'config.php';
require_once 'functions.php';

header("Content-type: text/javascript");

$galleries = array();

echo "var list=\n".json_encode(recurse_dir($pictureDir, $galleries),JSON_PRETTY_PRINT);
