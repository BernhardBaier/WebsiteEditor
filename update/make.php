<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 16.05.14
 * Time: 15:10
 */
function findInArray($array,$needle){
    for($i=0;$i<sizeof($array);$i++){
        if($array[$i] == $needle){
            return $i;
        }
    }
    return -1;
}
include 'access.php';
if(substr($authLevel,0,1) == '1'){
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    $que = "SELECT * FROM settings WHERE parameter='editorVersion'";
    $erg = mysqli_query($sql,$que);
    $editorVersion = '4.0';
    while($row = mysqli_fetch_array($erg)){
        $editorVersion = $row['value'];
    }
    mysqli_free_result($erg);
    $number = -1;
    $version = substr($editorVersion,0,strpos($editorVersion,'.'));
    $editorVersion = substr($editorVersion,strpos($editorVersion,'.')+1);
    if(strpos($editorVersion,'.')>-1){
        $revision = substr($editorVersion,0,strpos($editorVersion,'.'));
        $editorVersion = substr($editorVersion,strpos($editorVersion,'.')+1);
        $number = $editorVersion;
    }else{
        $revision = $editorVersion;
    }
    $number++;
    $file = fopen('files.list','r');
    $in = fread($file,9999);
    fclose($file);
    $in  = substr($in,2);
    $in  = substr($in,strpos($in,'#')+1);
    $updatePath = substr($in,0,strpos($in,'#'));
    $ending = substr($in,strpos($in,'#endings')+3);
    $ending = substr($ending,strpos($ending,'#'));
    $endings = [];
    while(strpos($ending,'#')>-1){
        $ending = substr($ending,strpos($ending,'#')+1);
        array_push($endings,substr($ending,0,strpos($ending,'#')));
        $ending = substr($ending,strpos($ending,'#')+1);
    }
    $dirs = substr($in,strpos($in,'#dirs')+4);
    $dirs = substr($dirs,strpos($dirs,'#'),strpos($dirs,'#endings'));
    $files = "#version#$version.$revision.$number#
#path#$updatePath#";
    while(strpos($dirs,'#')>-1){
        $dirs = substr($dirs,strpos($dirs,'#')+1);
        $path = '../'.substr($dirs,0,strpos($dirs,'#'));
        $handler = opendir($path);
        while($file = readdir($handler)){
            if(strpos($path,'plugins')>-1){
                if($file != 'script.js'){
                    if(findInArray($endings,substr($file,strrpos($file,'.'))) > -1){
                        $files .= '
#file#'.$path.$file.'#';
                    }
                }
            }else{
                if(findInArray($endings,substr($file,strrpos($file,'.'))) > -1){
                    $files .= '
#file#'.$path.$file.'#';
                }
            }
        }
        $dirs = substr($dirs,strpos($dirs,'#')+1);
    }
    $file = fopen('fileList.list','r');
    $in = fread($file,filesize('fileList.list'));
    fclose($file);
    if(substr($in,strpos($in,'#path')) != substr($files,strpos($files,'#path'))){
        $file = fopen('fileList.list','w');
        fwrite($file,$files);
        fclose($file);
        echo('Updated file list:<br>'.$files);
    }else{
        echo('nothing to do.');
    }
}