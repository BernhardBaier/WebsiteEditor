<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 22.05.14
 * Time: 14:42
 */
include "auth.php";
if(substr($authLevel,1,1) == "1"){
    $id = $_POST['id'];
    $maxId = $_POST['maxId'];
    $date = $_POST['date'];
    $short = $_POST['short'];
    $lang = $_POST['lang'];
    $text = $_POST['editor1'];
    $text = str_replace('src="../../','src="',$text);
    $text = str_replace("src='../../","src='",$text);
    $output = "<div class='pluginArticleOuter'><div class='pluginArticleTitle' onclick='$(\"#pluginArticleContent$maxId\").toggleClass(\"out\")'>
    <div class='pluginArticleDate' title='date'>$date</div><div class='pluginArticleShort'>$short</div></div>";
    $output .= "<div id='pluginArticleContent$maxId' class='pluginArticleContent out'><div class='pluginArticleContentInner'>$text</div></div></div>";
    $input = '';
    if(file_exists("content/$id/$lang/article.php")){
        $file = fopen("content/$id/$lang/article.php",'r');
        $input = fread($file,filesize("content/$id/$lang/article.php"));
        fclose($file);
    }
    $input .= $output;
    $file = fopen("content/$id/$lang/article.php",'w');
    fwrite($file,$input);
    fclose($file);
    header("Location: article.php?success=true&id=$id&lang=$lang");
}