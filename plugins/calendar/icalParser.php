<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 08.08.14
 * Time: 14:53
 */
include('access.php');
if(substr($authLevel,0,1) == '1'){
    $path = $_POST['path'];
    $lang = $_POST['lang'];
    $id = $_POST['id'];
    if(!file_exists($path)){
        echo('this file does not exist!');
        exit;
    }
}