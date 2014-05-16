<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 16.05.14
 * Time: 14:52
 */
$file = fopen('fileList.list','r');
$in = fread($file,200);
fclose($file);
$in = substr($in,strpos($in,'#path#')+6);
$remotePath = substr($in,0,strpos($in,'#'));
$file = fopen($remotePath.'update/fileList.list','r');
$in = fread($file,999999);
fclose($file);
echo($in);