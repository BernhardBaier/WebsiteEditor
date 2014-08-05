<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 22.05.14
 * Time: 14:56
 */
$id = $_GET['id'];
$lang = $_GET['lang'];
$id = $id==''?1:$id;
$lang = $lang==''?'de':$lang;
echo("<body onload='initPicViewer()'><link rel='stylesheet' href='stylePluginArticle.css' /><script src='../../picViewer/picViewer.js'></script>
<link href='../../picViewer/picViewer.css' rel='stylesheet' />
<script src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>");
$file = fopen("content/$id/$lang/article.php",'r');
$in  = fread($file,filesize("content/$id/$lang/article.php"));
fclose($file);
$in = str_replace('src="','src="../../',$in);
$in = str_replace("src='","src='../../",$in);
echo($in."<div><a href='article.php?success=true&id=$id&lang=$lang'>back</a></div>");
echo('</body>');