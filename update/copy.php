<?php
error_reporting(E_ERROR);
include "auth.php";
if($authLevel == '1111'){
    $remotePath = $_POST['remotePath'];
    $path = $_POST['path'];
    $file = fopen('failedFiles.list','r');
    $input = fread($file,filesize('failedFiles.list'));
    fclose($file);
    $foldersExist = false;
    $dir = substr($path,0,strrpos($path,'/'));
    $dirsToAdd = [];
    while(strrpos($dir,'/') > -1 && !$foldersExist){
        if(!mkdir($dir)){
            array_push($dirsToAdd,$dir);
            $dir = substr($dir,0,strrpos($dir,'/')-1);
            $dir = substr($dir,0,strrpos($dir,'/'));
        }else{
            for($i=sizeof($dirsToAdd)-1;$i>=0;$i--){
                mkdir($dirsToAdd[$i]);
            }
            $foldersExist = true;
        }
    }
    if(copy($remotePath.substr($path,3),$path)){
        $input = str_replace("#file#$remotePath".substr($path,3).'#','',$input);
        echo('1');
    }else{
        $input .= "#file#$remotePath".substr($path,3).'#';
        echo("<br>error copying $remotePath".substr($path,3));
    }
    $file = fopen('failedFiles.list','w');
    fwrite($file,$input);
    fclose($file);
}