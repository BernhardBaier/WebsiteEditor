<?php
error_reporting(E_ERROR);
include 'auth.php';
if($authLevel == '1111'){
    $file = fopen('fileList.list','r');
    $in = fread($file,filesize('fileList.list'));
    fclose($file);
    $oldVersion = '4';
    if(strpos($in,'#version#')>-1){
        $in = substr($in,strpos($in,'#version#')+9);
        $oldVersion = substr($in,0,strpos($in,'#'));
        $in = substr($in,strpos($in,'#')+1);
    }
    $remotePath = false;
    if(strpos($in,'#path#')>-1){
        $in = substr($in,strpos($in,'#path#')+6);
        $remotePath = substr($in,0,strpos($in,'#'));
        $in = substr($in,strpos($in,'#')+1);
    }
    if($remotePath == false){
        die('files corrupted!');
    }
    copy($remotePath.'update/fileList.list','workList.list');
    $file = fopen('workList.list','r');
    $remoteIn = fread($file,filesize('workList.list'));
    fclose($file);
    unlink('workList.list');
    $version = '4';
    if(strpos($remoteIn,'#version#')>-1){
        $remoteIn = substr($remoteIn,strpos($remoteIn,'#version#')+9);
        $version = substr($remoteIn,0,strpos($remoteIn,'#'));
        $remoteIn = substr($remoteIn,strpos($remoteIn,'#')+1);
    }
    if($version != $oldVersion){
        echo('update;'.$version);
        exit;
    }
}
echo('0');