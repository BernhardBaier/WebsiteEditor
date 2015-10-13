<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 13.10.2015
 * Time: 22:23
 */
$sql = false;
include('access.php');
$hostname = $_SERVER['HTTP_HOST'];
$host = $hostname == 'localhost'?$hostname:$sqlHost;
$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
if(!$sql){
    die('MySQL-Error');
}
if($authLevel != '1111'){
    die('authentification failed');
}
$path = $_POST['path'];
$path = substr($path,strpos($path,'/')+1);
$path = substr($path,strpos($path,'/')+1);
$file = fopen($path,'r');
$input = fread($file,filesize($path));
fclose($file);
$path = substr($path,0,strrpos($path,'/')+1);
$files = $input;

$input = substr($input,strpos($input,'#name#')+6);
$name = substr($input,0,strpos($input,'#'));

$output = '';
$input = substr($input,strpos($input,'#required#')+10);
while(strpos($input,'#file#') < strpos($input,'#end#') && strpos($input,'#file#') > -1){
    $input = substr($input,strpos($input,'#file#')+6);
    $file = substr($input,0,strpos($input,'#'));
    if(!file_exists($path.$file)){
        $output .= "fatal error could not find file $file!
";
    }
}
if($output != ''){
    die($output);
}
$input = substr($files,strpos($files,'#required#')+10);
while(strpos($input,'#file#') < strpos($input,'#end#') && strpos($input,'#file#') > -1){
    $input = substr($input,strpos($input,'#file#')+6);
    $file = substr($input,0,strpos($input,'#'));
    if(file_exists('../../'.str_replace('images','pictures',$file))){
        unlink('../../'.str_replace('images','pictures',$file));
    }
    if(!copy($path.$file,'../../'.str_replace('images','pictures',$file))){
        $output .= "fatal error could not copy file $file!
";
    }
}
$que = "UPDATE $sqlBase.plugins SET extra='$name' WHERE name='TemplateEditor';";
$erg = mysqli_query($sql, $que) or die(mysqli_error($sql));
echo($output);