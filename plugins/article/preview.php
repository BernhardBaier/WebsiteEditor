<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 22.05.14
 * Time: 14:56
 */
$id = $_GET['id'];
$id = $id==''?1:$id;
echo("<link rel='stylesheet' href='stylePluginArticle.css' />");
include("content/$id/article.php");