<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 15.10.2015
 * Time: 18:47
 */
$path = $_POST['path'];
$input = '';
if(file_exists($path)){
    $datei = fopen($path,'r');
    $input = fread($datei,filesize($path));
    fclose($datei);
}else{
    echo('error');
}
echo($input);