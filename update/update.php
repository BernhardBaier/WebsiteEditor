<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 16.05.14
 * Time: 14:52
 */
include 'access.php';
if(substr($authLevel,0,1) == '1'){
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    $file = fopen('fileList.list','r');
    $in = fread($file,200);
    fclose($file);
    $in = substr($in,strpos($in,'#path#')+6);
    $remotePath = substr($in,0,strpos($in,'#'));
    $file = fopen($remotePath.'update/fileList.list','r');
    $in = fread($file,999999);
    fclose($file);
    $version = substr($in,strpos($in,'#version#')+9);
    $version = substr($version,0,strpos($version,'#'));
    $in = substr($in,strpos($in,'#file#'));
    while(strpos($in,'#')>-1){
        $in = substr($in,strpos($in,'#file#')+6);
        $path = substr($in,0,strpos($in,'#'));
        $file = fopen($remotePath.substr($path,3),'r');
        $files = fread($file,999999);
        fclose($file);
        $file = fopen($path,'w');
        fwrite($file,$files);
        fclose($file);
        $in = substr($in,strpos($in,'#')+1);
    }
    echo('All files have been updated.');
}