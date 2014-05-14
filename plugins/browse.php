<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 29.03.14
 * Time: 12:10
 */
$path = $_POST['path'];
if($path != ''){
    $path = substr($path,-1)=='/'?$path:$path.'/';
}
$pathUp = $path;
$pathUp = substr($pathUp,0,strrpos($pathUp,'/'));
if(strpos($pathUp,'/') >-1 ){
    $pathUp = substr($pathUp,0,strrpos($pathUp,'/')+1);
}else{
    $pathUp = '../';
}
echo('A list of avaliable plugins:</br>');
searchDir($path);
echo("<div style='display:flex;border:1px solid #555;border-radius:4px;float:left;margin:0 2px;padding:3px;'><div style='display:block;float:left;'>
<div style='display:inline-flex;cursor:pointer;' title='enter dir' onclick='browsePlugins(\"$pathUp\")'><img src='images/folderUp.png' height='15'/> up</div><hr style='margin:0;padding:0;border:0 dotted #000;border-top-width:1px' />");
$handler = opendir($path);
while($file = readdir($handler)){
    if(is_dir($path.$file) && $file != '.' && $file != '..'){
        echo("<div style='display:inline-flex;cursor:pointer;' title='enter dir' onclick='browsePlugins(\"$path$file\")'><img src='images/folder.png' height='15'/> $file</div><hr style='margin:0;padding:0;border:0 dotted #000;border-top-width:1px' />");
    }
}
closedir($handler);
echo('</div><div style="float:left;margin:0 10px">plugins:</br>');
$handler = opendir($path);
while($file = readdir($handler)){
    if(!is_dir($path.$file) && $file != '.' && $file != '..'){
        if(substr($file,-3) == 'plg'){
            echo("<div style='display:inline-flex;cursor:pointer;color:#090' title='add Plugin' onclick='addPluginPath(\"$path$file\")'>$file</div><br/>");
        }
    }
}
closedir($handler);
echo('</div></div>');
function searchDir($pfad){
    $pfad = substr($pfad,-1)=='/'?$pfad:$pfad.'/';
    $handler = opendir($pfad);
    while($file = readdir($handler)){
        if($file != '.' && $file != '..'){
            if(!is_dir($pfad.$file)){
                if(substr($file,-3) == 'plg'){
                    echo("<div style='display:inline-flex;cursor:pointer;color:#090' title='add Plugin' onclick='addPluginPath(\"$pfad$file\")'>$file</div><br/>");
                }
            }else{
                searchDir($pfad.$file);
            }
        }
    }
    closedir($handler);
}