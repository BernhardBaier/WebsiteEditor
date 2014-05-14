<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 20.02.14
 * Time: 14:48
 */
include "auth.php";
if($authLevel != '' && $authLevel != '0000'){
    $type = $_POST['type'];
    $path = $_POST['path'];
    if($type == 'folder'){
        $path = substr($path,-1)=='/'?$path:$path.'/';
        $zip_file = $path.'folder.zip';
        if(!file_exists($zip_file)){
            $handle = opendir($path);
            $zip = new ZipArchive();
            if ($zip->open($zip_file, ZIPARCHIVE::CREATE)!==true){
                exit("cannot open <$file>\n");
            }
            while($file = readdir($handle)){
                if(is_file($path.$file)){
                    $zip->addFile($path.$file,$file);
                }
            }
            $zip->close();
        }
    }
    echo('1');
}