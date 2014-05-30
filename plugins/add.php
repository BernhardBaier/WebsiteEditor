<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 29.03.14
 * Time: 12:37
 */
error_reporting(E_ERROR);
function findInArray($array,$needle){
    for($i=0;$i<sizeof($array);$i++){
        if($array[$i] == $needle){
            return $i;
        }
    }
    return -1;
}
include('access.php');
$path = $_POST['path'];
$datei = fopen($path,'r');
$input = fread($datei,filesize($path));
fclose($datei);
$path = substr($path,0,strrpos($path,'/')+1);
$path = str_replace('../plugins/','',$path);
$handler = opendir($path);
$files = [];
while($file = readdir($handler)){
    if(!is_dir($path.$file) && strlen($file) > 3 && $file != '.gitignore'){
        array_push($files,$file);
    }
}
$input = substr($input,strpos($input,'#name#')+6);
$name = substr($input,0,strpos($input,'#'));

$added = false;

$hostname = $_SERVER['HTTP_HOST'];
$host = $hostname == 'localhost'?$hostname:$sqlHost;
$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
if($sql){
    $que = "SELECT * FROM plugins WHERE name ='$name'";
    $erg = mysqli_query($sql,$que);
    $existant = false;
    while($row = mysqli_fetch_array($erg)){
        $existant = $row['name'];
    }
    if($existant !== false){
        echo("haven't you added this plugin jet?");
        $added = true;
    }
}

$input = substr($input,strpos($input,'#copy#')+6);
while(strpos($input,'#file#') < strpos($input,'#required#')){
    $input = substr($input,strpos($input,'#file#')+6);
    $file = substr($input,0,strpos($input,'#'));
    if(!file_exists('../'.$file)){
        $output .= "fatal error could not copy file $file!</br>";
    }else{
        if(file_exists($path.$file)){
            unlink($path.$file);
        }
        copy('../'.$file,$path.$file);
    }
}

$input = substr($input,strpos($input,'#required#')+10);
while(strpos($input,'#file#') < strpos($input,'#includes#') && strpos($input,'#file#') > -1){
    $input = substr($input,strpos($input,'#file#')+6);
    $file = substr($input,0,strpos($input,'#'));
    if(!file_exists($path.$file)){
        $output .= "could not find file $file!</br>";
    }else{
        $pos = findInArray($files,$file);
        if($pos>-1){
            array_splice($files,$pos,1);
        }
    }
}
if(sizeof($files)>0){
    for($i = 0;$i < sizeof($files);$i++){
        if(substr($files[$i],-4) != '.plg'){
            unlink($path.$files[$i]);
        }
    }
}
if($added){
    echo('<br>Plugin updated. <a onclick="location.reload()" href="">refresh page</a>');
    exit;
}

$input = substr($input,strpos($input,'#includes#')+10);
$includes = [];
while(strpos($input,'#file#') < strpos($input,'#end#') && strpos($input,'#file#') > -1){
    $input = substr($input,strpos($input,'#file#')+6);
    $file = substr($input,0,strpos($input,'#'));
    if(file_exists($path.$file)){
        array_push($includes,str_replace(['../',"'"],['','"'],$path).$file);
    }
}
if($output!=""){
    echo("Plugin seams to be incomplete!</br>$output Check it again after fixing the file problem!");
}else{
    $path = str_replace('../plugins/','',$path);
    for($j=0;$j<sizeof($filesWithIncludes);$j++){
        $file = fopen($filesWithIncludes[$j],'r');
        $infile = fread($file,filesize($filesWithIncludes[$j]));
        fclose($file);
        if(strpos($infile,'<!--#style for plugins#-->') > -1){
            $start = substr($infile,0,strpos($infile,'<!--#style for plugins#-->')+26);
            $end = substr($infile,strpos($infile,'<!--#end#-->'));
            $styles = substr($infile,0,strpos($infile,'<!--#end#-->'));
            $styles = substr($styles,strpos($infile,'<!--#style for plugins#-->')+26);
            for($i=0;$i<sizeof($includes);$i++){
                if(substr($includes[$i],-4) == '.css'){
                    if(!(strpos($styles,"href='".$includes[$i]."'") > -1)){
                        $styles .= "
    <link href='".$includes[$i]."' rel='stylesheet' />".$txt;
                    }
                    $file = fopen($filesWithIncludes[$j],'w');
                    fwrite($file,$start.$styles.$end);
                    fclose($file);
                }
            }
        }
    }
    $que = "INSERT INTO $sqlBase.plugins (`name`, `location`,`includes`) VALUES ('$name','plugins/$path','".serialize($includes)."');";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    echo("1");
}