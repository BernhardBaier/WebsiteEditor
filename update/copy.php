<?php
include "auth.php";
if($authLevel == '1111'){
    $remotePath = $_POST['remotePath'];
    $path = $_POST['path'];
    $dir = substr($path,0,strrpos($path,'/'));
    if(!is_dir($dir)){
        if(!mkdir($dir)){
            $dir2 = substr($dir2,0,strrpos($dir2,'/'));
            mkdir($dir2);
            mkdir($dir);
        }
    }
    if(copy($remotePath.substr($path,3),$path)){
        echo('1');
    }else{
        echo("<br>error copying $remotePath".substr($path,3));
    }
}