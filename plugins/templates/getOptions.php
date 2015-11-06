<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 06.11.2015
 * Time: 16:46
 */
error_reporting(E_ERROR);
include('auth.php');
if($authLevel != '1111'){
    die('authentification failed');
}
$path = $_POST['path'];
$lang = $_POST['lang'];
if(strlen($lang)!=2){
    $lang = 'de';
}
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
    if(file_exists($path.$file) || file_exists(str_replace('{lang}',$lang,$path.$file))){
        if($_POST['echoContent'] == 'true'){
            $datei = fopen(str_replace('{lang}',$lang,$path.$file),'r');
            $output = fread($datei,filesize(str_replace('{lang}',$lang,$path.$file)));
            fclose($datei);
            echo($output);
        }else{
            echo(str_replace('{lang}',$lang,$path.$file));
        }
    }else{
        echo(null);
    }
}