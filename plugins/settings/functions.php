<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 15.04.14
 * Time: 19:28
 */
error_reporting(E_ERROR);
if(!file_exists('../access.crypt')){
    if(!copy('../../access.crypt','../access.crypt')){
        die('authentication failed');
    }
}
if(!file_exists('../auth.php')){
    if(!copy('../../auth.php','../auth.php')){
        die('authentication failed');
    }
}
include "access.php";
function updatePlugin($id){
    global $sql,$sqlBase,$output;
    $que = "SELECT * FROM plugins WHERE id=$id";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        $path = '../../'.$row['location'];
    }
    if(file_exists($path.'script.js')){
        unlink($path.'script.js');
    }
    $handle = opendir($path);
    while($file = readdir($handle)){
        if(!is_dir($path.$file)){
            if(substr($file,strrpos($file,'.')) == '.plg'){
                $path = substr($path,-1)=='/'?$path.$file:$path.'/'.$file;
            }
        }
    }
    if(!strpos($path,'.plg') > -1){
        echo('Fatal Error: found no plugin at this location!');
        exit;
    }
    $datei = fopen($path,'r');
    $input = fread($datei,filesize($path));
    fclose($datei);
    $path = substr($path,0,strrpos($path,'/')+1);
    $input = substr($input,strpos($input,'#name#')+6);
    $name = substr($input,0,strpos($input,'#'));
    $input = substr($input,strpos($input,'#copy#')+6);
    while(strpos($input,'#file#') < strpos($input,'#required#')){
        $input = substr($input,strpos($input,'#file#')+6);
        $file = substr($input,0,strpos($input,'#'));
        if(!file_exists('../'.$file)){
            $output .= "fatal error could not copy file $file!</br>";
        }else{
            if(file_exists($path.$file)){
                unlink($path.$file);
            }
            copy('../'.$file,$path.$file);
        }
    }
    $input = substr($input,strpos($input,'#required#')+10);
    while(strpos($input,'#file#') < strpos($input,'#includes#') && strpos($input,'#file#') > -1){
        $input = substr($input,strpos($input,'#file#')+6);
        $file = substr($input,0,strpos($input,'#'));
        if(!file_exists($path.$file)){
            $output .= "could not find file $file!</br>";
        }
    }

    $input = substr($input,strpos($input,'#includes#')+10);
    $includes = [];
    while(strpos($input,'#file#') < strpos($input,'#end#') && strpos($input,'#file#') > -1){
        $input = substr($input,strpos($input,'#file#')+6);
        $file = substr($input,0,strpos($input,'#'));
        if(file_exists($path.$file)){
            array_push($includes,str_replace('../','',$path).$file);
        }
    }
    if($output!=""){
        echo("Plugin seams to be incomplete!</br>$output Check it again after fixing the file problem!");
    }else {
        $path = str_replace('../plugins/', '', $path);
        $path = str_replace('../', '', $path);
        $filesWithIncludes = ['../../html5.php', '../../mobile.php'];
        for ($j = 0; $j < sizeof($filesWithIncludes); $j++) {
            $file = fopen($filesWithIncludes[$j], 'r');
            $infile = fread($file, filesize($filesWithIncludes[$j]));
            fclose($file);
            if (strpos($infile, '<!--#style for plugins#-->') > -1) {
                $start = substr($infile, 0, strpos($infile, '<!--#style for plugins#-->') + 26);
                $end = substr($infile, strpos($infile, '<!--#end#-->'));
                $styles = substr($infile, 0, strpos($infile, '<!--#end#-->'));
                $styles = substr($styles, strpos($infile, '<!--#style for plugins#-->') + 26);
                for ($i = 0; $i < sizeof($includes); $i++) {
                    if (substr($includes[$i], -4) == '.css') {
                        if (!(strpos($styles, "href='" . $includes[$i] . "'") > -1)) {
                            $styles .= "
<link href='" . $includes[$i] . "' rel='stylesheet' />";
                        }
                        $file = fopen($filesWithIncludes[$j], 'w');
                        fwrite($file, $start . $styles . $end);
                        fclose($file);
                    } else if (substr($includes[$i], -3) == '.js') {
                        if (!(strpos($styles, "src='" . $includes[$i] . "'") > -1)) {
                            $styles .= "
<script src='" . $includes[$i] . "'></script>";
                        }
                        $file = fopen($filesWithIncludes[$j], 'w');
                        fwrite($file, $start . $styles . $end);
                        fclose($file);
                    }
                }
            }
        }
        $que = "UPDATE $sqlBase.plugins SET name='$name', location='plugins/$path',includes='" . serialize($includes) . "' WHERE id=$id;";
        $erg = mysqli_query($sql, $que) or die(mysqli_error($sql));
    }
    return true;
}
if($authLevel == '1111'){
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    $function = $_POST['function'];
    switch($function){
        case 'searchPlugins':
            $que = "SELECT * FROM plugins WHERE 1";
            $erg = mysqli_query($sql,$que);
            $rows = [];
            $count = 0;
            while($row = mysqli_fetch_array($erg)){
                $rows[$count][0] = $row['name'];
                $rows[$count][1] = $row['id'];
                $rows[$count++][2] = $row['location'];
            }
            mysqli_free_result($erg);
            $titles = ['name','id','location','function'];
            echo('Avaliable Plugins:<div class="pluginSettingsOuter">');
            for($j=0;$j<4;$j++){
                echo("<div class='pluginSettingsSection'>");
                for($i=0;$i<$count;$i++){
                    if($i==0){
                        echo('<div class="pluginSettingsItem">'.$titles[$j].'</div>');
                    }
                    if($j<3){
                        echo('<div class="pluginSettingsItem">'.$rows[$i][$j].'</div>');
                    }else{
                        echo("<div class='pluginSettingsRow pluginSettingsItem'><div onclick='updatePlugin(".$rows[$i][1].")'>update</div><div onclick='removePlugin(".$rows[$i][1].")'>remove</div></div>");
                    }
                }
                echo("</div>");
            }
            echo("</div>");
            break;
        case 'updateAllPlugins':
            $que = "SELECT * FROM plugins WHERE 1";
            $erg = mysqli_query($sql,$que);
            $rows = [];
            $count = 0;
            $return = '1';
            while($row = mysqli_fetch_array($erg)){
                $rows[$count++] = $row['id'];
            }
            mysqli_free_result($erg);
            for($i=0;$i<$count;$i++){
                $return = updatePlugin($rows[$i]) == true?$return:$return.'fatal error while updating plugin with id '.$rows[$i];
            }
            if($return == true || $return == ''){
                $return = '1';
            }
            echo($return);
            break;
        case 'updatePlugin':
            $id = $_POST['id'];
            if(updatePlugin($id)){
                echo('1');
            }else{
                echo('fatal error while updating plugin with id '.$id);
            }
            break;
        case 'removePlugin':
            $id = $_POST['id'];
            $que = "SELECT * FROM plugins WHERE id=$id";
            $erg = mysqli_query($sql,$que);
            while($row = mysqli_fetch_array($erg)){
                $path = '../../'.$row['location'];
            }
            mysqli_free_result($erg);
            if(file_exists($path.'script.js')){
                unlink($path.'script.js');
            }
            $handle = opendir($path);
            while($file = readdir($handle)){
                if(!is_dir($path.$file)){
                    if(substr($file,strrpos($file,'.')) == '.plg'){
                        $path = substr($path,-1)=='/'?$path.$file:$path.'/'.$file;
                    }
                }
            }
            if(!strpos($path,'.plg') > -1){
                echo('Fatal Error: found no plugin at this location!');
                exit;
            }
            $datei = fopen($path,'r');
            $input = fread($datei,filesize($path));
            fclose($datei);
            $path = substr($path,0,strrpos($path,'/')+1);

            $input = substr($input,strpos($input,'#name#')+6);
            $name = substr($input,0,strpos($input,'#'));

            $input = substr($input,strpos($input,'#copy#')+6);
            while(strpos($input,'#file#') < strpos($input,'#required#')){
                $input = substr($input,strpos($input,'#file#')+6);
                $file = substr($input,0,strpos($input,'#'));
                if(file_exists($path.$file)){
                    unlink($path.$file);
                }
            }
            $que = "DELETE FROM $sqlBase.plugins WHERE id=$id;";
            $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
            echo("1");
            break;
        default:
            echo('error');
            break;
    }
}else{
    echo('Error: this can only be done by an admin!');
}
