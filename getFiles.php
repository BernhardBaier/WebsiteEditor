<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 01.01.14
 * Time: 20:27
 */
error_reporting(E_ERROR);
include('auth.php');
function showDir($path){
    global $dirs,$files;
    $handle = opendir($path);
    $except = Array('.','..','.htaccess','.gitignore');
    $count1=0;
    $count2=0;
    while($file = readdir($handle)){
        if(!in_array($file,$except)){
            if(strpos($file,'.') > -1){
                $files[$count2++] = $file;
            }else{
                $dirs[$count1++] = $file;
            }
        }
    }
}
if($authLevel != '' && $authLevel != '0000'){
    $path = $_POST['text'];
    $gal = $_POST['gal'];
    $dirs = '';
    $files = '';
    if($path != ""){
        showDir($path);
    }
    sort($files);
    if($gal == 1){
        echo('#imgs#');
        for($i=0;$i<sizeof($files);$i++){
            echo($files[$i].';');
        }
    }else{
        echo('#dirs#');
        sort($dirs);
        for($i=0;$i<sizeof($dirs);$i++){
            echo($dirs[$i].';');
        }
        echo('#files#');
        for($i=0;$i<sizeof($files);$i++){
            echo($files[$i].';');
        }
    }
}