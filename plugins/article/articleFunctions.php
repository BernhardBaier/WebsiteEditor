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
    $team = $_POST['team'];
    $short = $_POST['short'];
    $text = $_POST['editor1'];
    $text = str_replace('src="../../','src="',$text);
    $text = str_replace("src='../../","src='",$text);
    $output = "<div class='pluginArticleOuter'><div class='pluginArticleTitle'><div class='pluginArticleDate'>$date</div><div class='pluginArticleTeam'>$team</div></div>";
    $output .= "<div class='pluginArticleContent'>$text</div></div>";
    $input = '';
    if(file_exists("content/$maxId/article.php")){
        $file = fopen("content/$maxId/article.php",'r');
        $input = fread($file,filesize("content/$maxId/article.php"));
        fclose($file);
    }
    $input .= $output;
    $file = fopen("content/$maxId/article.php",'w');
    fwrite($file,$input);
    fclose($file);
    header("Location: article.php?success=true&id=$id&maxId=$maxId");
}