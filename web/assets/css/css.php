<?php
if(isset($_GET['file']) && !empty($_GET['file'])){
    $file = $_GET['file'];
}else{

    if($_GET['lang'] == 'en'){
        $file = 'base_en';
    }
    else{
        $file = 'base_ur';
    }


}

$css = file_get_contents(dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $file);

ob_start ("ob_gzhandler");
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 * 60 * 60;
$ExpStr = "Expires: " .
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);

echo $css;