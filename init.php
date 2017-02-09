<?php

ini_set('include_path', ini_get('include_path').':'.dirname($_SERVER['PHP_SELF'])); // Include script path
ini_set('include_path', ini_get('include_path').':'.dirname(__FILE__)); // Include lib path

include_once('Util/Loader.php');
Util_Loader::register();

?>
