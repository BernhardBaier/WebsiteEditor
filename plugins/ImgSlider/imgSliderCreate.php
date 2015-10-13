<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 12.10.2015
 * Time: 16:06
 */
function replaceUml($text){
    $olds = ['<und>','<dpp>','ä','ö','ü','Ä','Ö','Ü','ß'];
    $news = ['&',':','&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;'];
    $text = str_replace($olds,$news,$text);
    return $text;
}
$html = $_POST['html'];
$path = $_POST['path'];
$file = fopen($path,'w');
fwrite($file,replaceUml($html));
fclose($file);