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
    $adminId = $_POST['adminId'];
    $date = $_POST['date'];
    $short = $_POST['short'];
    $lang = $_POST['lang'];
    $text = $_POST['editor1'];
    $text = str_replace('src="../../','src="',$text);
    $text = str_replace("src='../../","src='",$text);
    $output = "<div class='pluginArticleOuter'><div class='pluginArticleTitle' onclick='$(\"#pluginArticleContent$adminId\").toggleClass(\"out\")'>
    <div class='pluginArticleDate' title='date'>$date</div><div class='pluginArticleShort'>$short</div></div>";
    $output .= "<div id='pluginArticleContent$adminId' class='pluginArticleContent out'><div class='pluginArticleContentInner'>$text</div></div></div>";
    $input = '';
    if(file_exists("content/$id/$lang/article.php")){
        $file = fopen("content/$id/$lang/article.php",'r');
        $input = fread($file,filesize("content/$id/$lang/article.php"));
        fclose($file);
    }
    if($adminId != $maxId){
        $pos = strpos($input,'#pluginArticleContent'.$adminId)-300;
        $pos = $pos<0?0:$pos;
        $ktxt = substr($input,$pos);
        $ktxt = substr($ktxt,strpos($ktxt,'pluginArticleOuter')-12);
        while(strrpos($ktxt,'pluginArticleOuter') > 20){
            $ktxt = substr($ktxt,0,strrpos($ktxt,'pluginArticleOuter')-12);
        }
        $ktxt = substr($ktxt,0,strrpos($ktxt,'</div>')+6);
        $input = str_replace($ktxt,$output,$input);
    }else{
        $input = $output . $input;
    }
    $file = fopen("content/$id/$lang/article.php",'w');
    fwrite($file,$input);
    fclose($file);
    header("Location: article.php?success=true&id=$id&lang=$lang");
}