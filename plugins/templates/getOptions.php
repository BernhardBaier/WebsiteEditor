<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 06.11.2015
 * Time: 16:46
 */
include('auth.php');
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
if(strpos($input,'#options') == -1){
    echo(null);
    exit;
}

$input = substr($input,strpos($input,'#options#')+9);
if(strpos($input,'#file#') < strpos($input,'#required#') && strpos($input,'#file#') > -1){
    $input = substr($input,strpos($input,'#file#')+6);
    $file = substr($input,0,strpos($input,'#'));
    if(file_exists($path.$file)){
        echo($path.$file);
    }else{
        echo(null);
    }
}