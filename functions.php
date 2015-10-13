<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 14.01.14
 * Time: 22:01
 */
error_reporting(E_ERROR);
$sql = false;
include('access.php');
function getLoggedIn(){
    if(isset($_COOKIE['PHPSESSID'])){
        return '1';
    }
    return '0';
}
function replaceUml($text){
    $olds = ['<und>','<dpp>','ä','ö','ü','Ä','Ö','Ü','ß'];
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
    $path = replaceUml($path);
    $name = replaceUml($name);
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
function gallerySlider($id,$name,$elemCount){
    global $lang;
    if(!file_exists("web-content/$lang/$id.php")){
        return '-1';
    }
    $file = fopen("web-content/$lang/$id.php",'r');
    $input = fread($file,filesize("web-content/$lang/$id.php"));
    fclose($file);
    if(!(strpos($input,'<div class="gallery">') > -1)){
        return '0';
    }
    $input = substr($input,strpos($input,'<div class="gallery">'));
    $pos = strpos($input,'<img');
    $picPaths = [];
    $count = 0;
    while($pos>-1){
        $input = substr($input,$pos);
        $input = substr($input,strpos($input,'src=')+4);
        $tren = substr($input,0,1);
        $input = substr($input,1);
        $path = substr($input,0,strpos($input,$tren));
        if(file_exists($path)){
            if(!(strpos($path,'/thumbs/') > -1)){
                $tPath = substr($path,0,strrpos($path,'/')).'/thumbs'.substr($path,strrpos($path,'/'));
                if(file_exists($tPath)){
                    $path = $tPath;
                }
            }
        }
        $picPaths[$count++] = $path;
        if($count <= 5){
            $pos = strpos($input,'<img');
        }else{
            $pos = -1;
        }
    }
    $output = "<a href='index.php?id=$id&lang=$lang' title='zu Galerie $name wechseln'><div class='galleryPrevSliderOuter'>
    <div class='galleryPrevSliderTitle'>$name</div>
    <div class='galleryPrevSliderInner'>";
    for($i=0;$i<sizeof($picPaths);$i++){
        $class = $i==0?'opac0':' opac0 right';
        $output .="<img src='".$picPaths[$i]."' id='galleryPrevSliderImg$elemCount".'_'.($i+1)."' class='galleryPrevSliderImg$class'>";
    }
$output .="    </div>
</div></a><p>&nbsp;</p>";
    return $output;
}
function movePics($path,$id){
    $path = replaceUml($path);
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
function include2string($file) {
	ob_start();
    if(strrpos($file,'?') > -1){
        $includes = substr($file,strrpos($file,'?')+1);
        while(strpos($includes,'&') > -1){
            setcookie(substr($includes,0,strpos($includes,'=')),substr($includes,strpos($includes,'=')+1,strpos($includes,'&')-(strpos($includes,'=')+1)),time()+999);
            $includes = substr($includes,strpos($includes,'&')+1);
        }
        setcookie(substr($includes,0,strpos($includes,'=')),substr($includes,strpos($includes,'=')+1),time()+999);
        $file = substr($file,0,strrpos($file,'.php')+4);
    }
	include $file;
	return ob_get_clean();
}
function replaceTextWithPlugin($text,$sql){
    global $sqlBase;
    $output = $text;
    $que = "SELECT * FROM $sqlBase.toreplace";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    if(!$erg){
        $output.="Error in MySQL request $que!";
    }
	while($row = mysqli_fetch_array($erg)){
		$textToReplace = $row['replace'];
		if(strpos($output,$textToReplace) > -1){
			$sourceOfReplacement = $row['url'];
            $locationOfReplacement = substr($sourceOfReplacement,0,strrpos($sourceOfReplacement,'.php')+4);
            if(file_exists($locationOfReplacement)){
				$input = include2string($sourceOfReplacement);
				$output = str_replace($textToReplace,$input,$output);
			}else{
				$output = str_replace($textToReplace,'',$output);
			}
		}
	}
	return $output;
}
function copyAndReplace($source,$dest){
    global $sql;
    $input = $source;
    if(file_exists($source)) {
        $file = fopen($source, 'r');
        $input = fread($file, filesize($source));
        fclose($file);
    }
	$input = replaceTextWithPlugin($input,$sql);
	while(strpos($input,'{#insertPlugin') > -1){
		$ktxt = substr($input,strpos($input,'{#insertPlugin'));
		$ktxt = substr($ktxt,0,strpos($ktxt,'#}')+2);
		$input = str_replace($ktxt,'',$input);
	}
	$file = fopen($dest,'w');
	fwrite($file,$input);
	fclose($file);
	return true;
}
function handleError($func){
    echo("missing Options for function $func!");
}
$function = $_POST['text'];
if(isset($_POST['function'])){
	$function = $_POST['function'];
}
if(substr($authLevel,0,1) == '1'){
    $lang = $_POST['lang'];
	$lang = $lang == ''?'de':$lang;
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    if(!$sql){
        die('MySQL-Error');
    }
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
                }else{
                    echo('could not delete file '.$options[1]);
                }
            }else{
                handleError($function);
            }
            break;
        case 'storeText':
            if(!empty($options) && sizeof($options)>=2){
                if(storeText(replaceUml($options[0]),$options[1])){
	                $id=$_POST['id'];
	                if(!is_dir("content/$lang/preview/")){
		                mkdir("content/$lang/preview/");
	                }
	                if(file_exists("content/$lang/preview/$id.php")){
		                unlink("web-content/$lang/preview/$id.php");
	                }
	                if(copyAndReplace("content/$lang/$id.php","content/$lang/preview/$id.php")){
		                echo('#preview#');
	                }
                    echo('#saved#');
                }
            }else{
                handleError($function);
            }
            break;
	    case 'publishText':
		    $id=$_POST['id'];
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
				    if(file_exists("web-content/".$langs[$i]."/$id.php")){
					    unlink("web-content/".$langs[$i]."/$id.php");
				    }
				    if(copyAndReplace("content/".$langs[$i]."/$id.php","web-content/".$langs[$i]."/$id.php")){
					    echo('#published#');
				    }
			    }
		    }else{
			    if(file_exists("web-content/$lang/$id.php")){
				    unlink("web-content/$lang/$id.php");
			    }
			    if(copyAndReplace("content/$lang/$id.php","web-content/$lang/$id.php")){
				    echo('#published#');
			    }
		    }
		    break;
        case 'clickAbleMenu':
            if(!empty($options) && sizeof($options)>=1){
                $options[0] = substr($options[0],-1) == ')'?$options[0]:$options[0].'()';
                $options[0] = str_replace("'",'"',$options[0]);
                $lang = $options[1];
                echo(clickAbleMenu(replaceUml($options[0])));
            }else{
                handleError($function);
            }
            break;
        case 'gallerySlider':
            if(!empty($options) && sizeof($options)>=4){
                $lang = $options[3];
                echo(gallerySlider($options[0],$options[1],$options[2]));
            }else{
                handleError($function);
            }
            break;
        case 'movePics':
            if(!empty($options) && sizeof($options)>=2){
                movePics($options[0],$options[1]);
            }else{
                handleError($function);
            }
            break;
	    case 'insertHTMLatEndOfPage':
		    if(!empty($options) && sizeof($options)>=2){
			    $id = $options[0];;
			    $html = $options[1];
			    $path = "content/$lang/$id.php";
			    $file = fopen($path,'r');
			    $input = fread($file,filesize($path));
			    fclose($file);
			    if(!strpos($input,$html) > -1){
				    if(strpos($input,'<div class="picsClickAble">') > -1){
					    $input = substr($input,0,strrpos($input,'</div>')).$html.substr($input,strrpos($input,'</div>'));
				    }else{
					    $input = $input.$html;
				    }
				    $file = fopen($path,'w');
				    fwrite($file,$input);
				    fclose($file);
			    }
			    echo('1');
		    }else{
			    handleError($function);
		    }
		    break;
        case "getLoggedIn":
            echo(getLoggedIn());
            break;
        default:
            echo("undefined call to function $function(".serialize($options).") in functions.php");
            break;
    }
}