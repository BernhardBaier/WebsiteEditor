<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 10.10.2015
 * Time: 17:11
 */
$id = $_POST['id'];
$id = $id == ""?'1':$id;
$width = $_POST['width'];
if($width == "" || !is_numeric($width)){
    $width = '200';
}
$height = $_POST['height'];
if($height == "" || !is_numeric($height)){
    $height = '167';
}
$margin = $_POST['margin'];
if($margin == "" || !is_numeric($margin)){
    $margin = '5';
}
$speed = $_POST['speed'];
if($speed == "" || !is_numeric($speed)){
    $speed = '500';
}
$timeout = $_POST['timeout'];
if($timeout == "" || !is_numeric($timeout)){
    $timeout = '5000';
}
$output = "function imgSliderSettings(){
    $('.imgSliderOuter').css({width: '".$width."px',height: '".$height."px',margin: '".$margin."px'});
    imgSliderSpeed = ".$speed.";
    imgSliderTimeout = ".$timeout.";
}";
$file = fopen('settings/imgSliderSettings'.$id.'.js','w');
fwrite($file,$output);
fclose($file);
echo "1";