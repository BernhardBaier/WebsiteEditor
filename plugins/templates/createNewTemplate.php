<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 03.12.2015
 * Time: 17:16
 */
error_reporting(E_ERROR);
include('auth.php');
if($authLevel != '1111'){
    die('authentification failed');
}
function replaceUml($text){
    $olds = ['<und>','<dpp>','ä','ö','ü','Ä','Ö','Ü','ß'];
    $news = ['&',':','&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;'];
    $text = str_replace($olds,$news,$text);
    return $text;
}
$name = replaceUml($_POST['name']);
$copy = replaceUml($_POST['copy']);
if($name == '' || $copy == ''){
    echo('failed');
    exit;
}
mkdir('templates/'.$copy);
$path = 'templates/'.$name."/".strtolower($name).".tmpl";
$file = fopen($path,'r');
$input = fread($file,filesize($path));
fclose($file);
$tmpl = $input;
$tmpl = str_replace("#name#$name#","#name#$copy#",$tmpl);
$file = fopen('templates/'.$copy."/".strtolower($copy).'.tmpl','w');
fwrite($file,$tmpl);
fclose($file);
$input = substr($input,strpos($input,'#required#') + 9);
$i = 0;
$failedFiles = [];
while(strpos($input,'#file#') > -1){
    $input = substr($input,strpos($input,'#file#') + 6);
    $files = substr($input,0,strpos($input,'#'));
    $dir = substr($files,0,strrpos($files,'/'));
    if(!is_dir('templates/'.$copy."/".$dir)){
        mkdir('templates/'.$copy."/".$dir);
    }
    if(!copy('templates/'.$name."/".$files,'templates/'.$copy."/".$files)){
        array_push($failedFiles,'templates/'.$name."/".$files);
    }
    $input = substr($input,strpos($input,'#') + 1);
}
mkdir("templates/$copy/pictures/");
copy("templates/$name/pictures/preview.jpg","templates/$copy/pictures/preview.jpg");
if($failedFiles == []){
    echo('1');
}else{
    for($i=0;$i<sizeof($failedFiles);$i++){
        echo("failed to copy file ".$failedFiles[$i].'<br>');
    }
}