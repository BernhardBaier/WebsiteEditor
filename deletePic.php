<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 17.02.14
 * Time: 00:03
 */
include "auth.php";
if($authLevel != '' && $authLevel != '0000'){
    $path = $_POST['path'];
    if(file_exists($path)){
        $success = true;
        $file=substr($path,0,strrpos($path,'/')+1).'thumbs'.substr($path,strrpos($path,'/'));
        if(file_exists($file)){
            $success = unlink($file);
        }
        $success &= unlink($path);
        if($success){
            echo('1');
        }else{
            echo('unable to delete file!');
        }
    }
}