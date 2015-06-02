<?php

require_once './Livethumb.class.php';

$live = new Livethumb($_GET);
$live->getImage();

?>