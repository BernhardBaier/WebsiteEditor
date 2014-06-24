<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 11.05.14
 * Time: 11:08
 */
error_reporting(E_ERROR);
$lang = $_POST['lang'];
$lang = $lang==''?'de':$lang;
$path = $_POST['path'];
$path = "$path/$lang/";
$que = strtolower($_POST['que']);
if($que == ''){
    echo('Nothing to do!');
    exit;
}
//ToDO: iterate trough page titles
$handler = opendir($path);
$files = [];
while($file = readdir($handler)){
    if(!is_dir($path.$file) && strlen($file) > 3){
        array_push($files,$path.$file);
    }
}
closedir($handler);
$max = sizeof($files);
$out = 'Search results:';
for($i=0;$i<$max;$i++){
    $file = fopen($files[$i],'r');
	$id = substr($files[$i],0,strrpos($files[$i],'.'));
	$id = substr($id,strrpos($id,'/')+1);
    $in = strtolower(strip_tags(fread($file,filesize($files[$i])),'<h1></h1>'));
    if(strpos($in,$que)>-1){
        $start = strpos($in,$que)-25;
        $length = 75;
        if($start < 0){
            $length += $start;
            $start = 0;
        }
        $out .= '<div class="searchItem" onclick="navigateToPageById('.$id.')">'.str_replace($que,'<span style="color:#f00;">'.$que.'</span>',$in).'</div>';
    }
    fclose($file);
}
echo($out);