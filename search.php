<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 11.05.14
 * Time: 11:08
 */
$lang = $_POST['lang'];
$lang = $lang==''?'de':$lang;
$path = "web-content/$lang/";
$que = $_POST['que'];
if($que == ''){
    echo('Nothing to do!');
    exit;
}
$handler = opendir($path);
$files = [];
while($file = readdir($handler)){
    if(!is_dir($path.$file) && strlen($file) > 3){
        array_push($files,$path.$file);
    }
}
closedir($handler);
$max = sizeof($files);
for($i=0;$i<$max;$i++){
    $file = fopen($files[$i],'r');
    $in = fread($file,filesize($files[$i]));
    if(strpos($in,$que)>-1){
        echo('match in '.substr($files[$i],strrpos($files[$i],'/')+1));
    }
    fclose($file);
}