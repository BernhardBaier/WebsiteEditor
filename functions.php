<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 14.01.14
 * Time: 22:01
 */
error_reporting(E_ERROR);
include('access.php');
function replaceUml($text){
    $olds = ['<und>','<dpp>','�','�','�','�','�','�','�'];
    $news = ['&',':','&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;'];
    $text = str_replace($olds,$news,$text);
    return $text;
}
function renamePic($o_name,$name,$path){
    if(!file_exists($path.$name)){
        if(file_exists($path.'thumbs/'.$o_name)){
            rename($path.'thumbs/'.$o_name,$path.'thumbs/'.$name);
        }
        return rename($path.$o_name,$path.$name);
    }else{
        return 'Filename is already existent! Please delete the other file first.';
    }
}
function deletePic($name,$path){
    if(file_exists($path.'thumbs/'.$name)){
        unlink($path.'thumbs/'.$name);
    }
    return unlink($path.$name);
}
function storeText($text,$path){
    global $sql,$sqlBase,$lang;
    $datei = fopen($path,'w');
    fwrite($datei,$text);
    fclose($datei);
    $id = substr($path,strrpos($path,'/')+1);
    $id = substr($id,0,strpos($id,'.'));
    if($sql){
        $que = "UPDATE $sqlBase.pages_$lang SET edit='".date('d.m.Y')."' WHERE id=$id";
        $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
        return $erg;
    }
    return true;
}
function clickAbleMenu($callback,$parent=0){
    global $sql,$lang;
    $que = "SELECT * FROM pages_$lang WHERE parent=$parent";
    $erg = mysqli_query($sql,$que);
    $rows = [];
    while($help = mysqli_fetch_array($erg)){
        $rows[$help['rank']] = $help;
    }
    $output = "";
    if($rows != []){
        for($i=1;$i<=sizeof($rows);$i++){
            $row = $rows[$i];
            $pid = $row['id'];
            $name = $row['name'];
            $childCount = $row['childCount'];
            if($childCount > 0){
                $output .= "<li class='clickAbleMenuItem'>
                <img onclick='$(\"#clickAbleMenuList$pid\").toggleClass(\"hidden\");$(this).toggleClass(\"imgRotated\")' src='images/menuOptions.png' height='15' class='imgRotate' />
                <span onclick='".str_replace('$pid',$pid,$callback)."'>".replaceUml($name).'</span>'.
                    "</li><ul id='clickAbleMenuList$pid' class='clickAbleMenuList hidden'>".clickAbleMenu($callback,$pid).'</ul>';
            }else{
                $output .= "<li class='clickAbleMenuItem'><img src='images/listicon.png' height='15' /> <span onclick='".str_replace('$pid',$pid,$callback)."'>".replaceUml($name).'</span></li>';
            }
        }
    }
    mysqli_free_result($erg);
    return $output;
}
function movePics($path,$id){
    while(strpos($path,';')>-1){
        $name = substr($path,0,strpos($path,';'));
        $name = substr($name,strrpos($name,'/')+1);
        $src = "web-images/$id/$name";
        if(file_exists($src)){
            echo("pic $src already existent in new folder!");
        }else{
            $oldId = substr($path,strpos($path,'/')+1);
            $oldId = substr($oldId,0,strpos($oldId,'/'));
            $oldSrc = "web-images/$oldId/$name";
            if(file_exists($oldSrc)){
                if(copy($oldSrc,$src)){
                    unlink($oldSrc);
                }else{
                    echo("failed to copy pic $oldSrc");
                }
                if(copy("web-images/$oldId/thumbs/$name","web-images/$id/thumbs/$name")){
                    unlink("web-images/$oldId/thumbs/$name");
                }else{
                    echo("failed to copy pic web-images/$oldId/thumbs/$name");
                }
            }else{
                echo("Error with pic $oldSrc");
            }
        }
        $path = substr($path,strpos($path,';')+1);
    }
    $name = substr($path,strrpos($path,'/')+1);
    $src = "web-images/$id/$name";
    if(file_exists($src)){
        echo("pic $src already existent in new folder!");
    }else{
        $oldId = substr($path,strpos($path,'/')+1);
        $oldId = substr($oldId,0,strpos($oldId,'/'));
        $oldSrc = "web-images/$oldId/$name";
        if(file_exists($oldSrc)){
            if(copy($oldSrc,$src)){
                unlink($oldSrc);
            }else{
                echo("failed to copy pic $oldSrc");
            }
            if(copy("web-images/$oldId/thumbs/$name","web-images/$id/thumbs/$name")){
                unlink("web-images/$oldId/thumbs/$name");
            }else{
                echo("failed to copy pic web-images/$oldId/thumbs/$name");
            }
        }else{
            echo("Error with pic $oldSrc");
        }
    }
}
function handleError($func){
    echo("missing Options for function $func!");
}
$function = $_POST['text'];
if(substr($authLevel,0,1) == '1'){
    $lang = 'de';
    $lang = $_POST['lang'];
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    if(strpos($function,':')>-1){
        $option = substr($function,strpos($function,':') + 1);
        $function = substr($function,0,strpos($function,':'));
        $i=0;
        while(strpos($option,':') > -1){
            $options[$i] =  substr($option,0,strpos($option,':'));
            $option = substr($option,strpos($option,':')+1);
            $i++;
        }
        $options[$i] = $option;
    }
    switch($function){
        case 'rename':
            if(!empty($options) && sizeof($options)>=3){
                $out = renamePic(replaceUml($options[0]),replaceUml($options[1]),$options[2]);
                if($out == '1'){
                    echo('#reload#');
                }else{
                    echo($out);
                }
            }else{
                handleError($function);
            }
            break;
        case 'delete':
            if(!empty($options) && sizeof($options)>=2){
                if(deletePic($options[0],$options[1])){
                    echo('#reload#');
                }
            }else{
                handleError($function);
            }
            break;
        case 'storeText':
            if(!empty($options) && sizeof($options)>=2){
                if(storeText(replaceUml($options[0]),$options[1])){
                    echo('#saved#'.replaceUml($options[0]));
                }
            }else{
                handleError($function);
            }
            break;
        case 'publishText':
            $id=$_POST['id'];
            $einsatz = $_POST['einsatz'];
            if($lang == 'all'){
                $in = $_POST['langs'];
                $langs = [];
                $count = 0;
                while(strpos($in,',')>-1){
                    $langs[$count++] = substr($in,0,strpos($in,','));
                    $in = substr($in,strpos($in,',')+1);
                }
                $langs[$count] = $in;
                for($i=0;$i<sizeof($langs);$i++){
                    if($einsatz > 0){
                        if(file_exists("web-content/".$langs[$i]."/$einsatz/$id.php")){
                            unlink("web-content/".$langs[$i]."/$einsatz/$id.php");
                        }
                        if(copy("content/".$langs[$i]."/$einsatz/$id.php","web-content/".$langs[$i]."/$einsatz/$id.php")){
                            echo('#published#');
                        }
                    }else{
                        if(file_exists("web-content/".$langs[$i]."/$id.php")){
                            unlink("web-content/".$langs[$i]."/$id.php");
                        }
                        if(copy("content/".$langs[$i]."/$id.php","web-content/".$langs[$i]."/$id.php")){
                            echo('#published#');
                        }
                    }
                }
            }else{
                if($einsatz > 0){
                    if(file_exists("web-content/$lang/$einsatz/$id.php")){
                        unlink("web-content/$lang/$einsatz/$id.php");
                    }
                    if(copy("content/$lang/$einsatz/$id.php","web-content/$lang/$einsatz/$id.php")){
                        echo('#published#');
                    }
                }else{
                    if(file_exists("web-content/$lang/$id.php")){
                        unlink("web-content/$lang/$id.php");
                    }
                    if(copy("content/$lang/$id.php","web-content/$lang/$id.php")){
                        echo('#published#');
                    }
                }
            }
            break;
        case 'clickAbleMenu':
            if(!empty($options) && sizeof($options)>=2){
                $options[0] = substr($options[0],-1) == ')'?$options[0]:$options[0].'()';
                $options[0] = str_replace("'",'"',$options[0]);
                $lang = $options[1];
                echo(clickAbleMenu($options[0]));
            }else{
                handleError($function);
            }
            break;
        case 'movePics':
            if(!empty($options) && sizeof($options)>=2){
                echo(movePics($options[0],$options[1]));
            }else{
                handleError($function);
            }
            break;
        default:
            echo('Error');
            break;
    }
}