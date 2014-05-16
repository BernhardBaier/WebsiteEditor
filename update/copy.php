<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 16.05.14
 * Time: 16:35
 */
$remotePath = $_POST['remotePath'];
$path = $_POST['path'];
if(copy($remotePath.substr($path,3),$path)){
    echo('1');
}else{
    echo("<br>error copying $remotePath".substr($path,3));
}