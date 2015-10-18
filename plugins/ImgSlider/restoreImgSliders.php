<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 15.10.2015
 * Time: 18:47
 */
$path = $_POST['path'];
$input = '1';
if(file_exists($path)){
    $datei = fopen($path,'r');
    $input = fread($datei,filesize($path));
    fclose($datei);
}
echo($input);