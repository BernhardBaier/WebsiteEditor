<?php
include "auth.php";
if(substr($authLevel,0,1) == "1"){
    $remotePath = $_POST['remotePath'];
    $path = $_POST['path'];
    if(copy($remotePath.substr($path,3),$path)){
        echo('1');
    }else{
        echo("<br>error copying $remotePath".substr($path,3));
    }
}