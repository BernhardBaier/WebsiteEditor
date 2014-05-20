<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 18.01.14
 * Time: 10:19
 */
error_reporting(E_ERROR);
if(basename($_SERVER["SCRIPT_FILENAME"]) != 'index.php'){
    header('Location: index.php');
}
$id = $_GET['id'];
if($id == ""){
    $id = 1;
}
$lang = $_GET['lang'];
if($lang == ""){
    $lang = 'de';
}
include 'access.php';
$base = $sqlBase;
$table = 'pages_'.$lang;
$hostname = $_SERVER['HTTP_HOST'];
$host = $hostname == 'localhost'?$hostname:'rdbms.strato.de';
$sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
$que = "SELECT * FROM settings WHERE parameter='pageTitle'";
$erg = mysqli_query($sql,$que);
while($row = mysqli_fetch_array($erg)){
    $pageTitle = $row['value'];
}
function printMenu($sql,$n_parent=0,$level=0){
    global $table,$parents,$lang;
    $que = "SELECT * FROM ".$table." WHERE parent=$n_parent";
    $erg = mysqli_query($sql,$que);
    $rows = array();
    while($help = mysqli_fetch_array($erg)){
        $rows[$help['rank']] = $help;
    }
    $output = "";
    for($i=1;$i<=sizeof($rows);$i++){
        $row = $rows[$i];
        $pid = $row['id'];
        $parent = $row['parent'];
        $extra = $row['extra'];
        $name = $row['name'];
        $childCount = $row['childCount'];
        if($extra == "1"){
            if($parent == 0){
                $classToAdd = findInArray($parents,$pid)>-1?' active':'';
                $level++;
                if($childCount > 0){
                    $output .= '<div class="menuItem topItem'.$classToAdd.'" onmouseover="this.childNodes[1].className=\'subMenu\'" onmouseout="this.childNodes[1].className=\'subMenu menuOut\'"><a href="index.php?id='.$pid.'&lang='.$lang.'">'.replaceUml($name).'</a><div class="subMenu menuOut">';
                }else{
                    $output .= '<div class="menuItem topItem'.$classToAdd.'"><a href="index.php?id='.$pid.'&lang='.$lang.'">'.replaceUml($name).'</a>';
                }
            }else{
                $classToAdd = $i==sizeof($rows)?' lastItem':'';
                $levelText = $level>3?' subItemLeft':'';
                if(findInArray($parents,$pid)>-1){
                    $classToAdd .= " active";
                }
                if($childCount > 0){
                    $output .= '<div class="menuItem subItem'.$classToAdd.'" onmouseover="this.childNodes[2].className=\'subMenu2'.$levelText.'\'" onmouseout="this.childNodes[2].className=\'subMenu2'.$levelText.' menuOut\'">
                    <a href="index.php?id='.$pid.'&lang='.$lang.'">'.replaceUml($name).'</a><div class="subMenu2'.$levelText.' menuOut">';
                }else{
                    $output .= '<div class="menuItem subItem'.$classToAdd.'"><a href="index.php?id='.$pid.'&lang='.$lang.'">'.replaceUml($name).'</a>';
                }
            }
            if($childCount > 0){
                $output .= printMenu($sql,$pid,$level) . '</div>';
            }
            $output .= '</div>';
        }
    }
    mysqli_free_result($erg);
    if($output == ''){
        $output = 'Your website is up and running. It seams as if no page is visible at the moment. Please use the admin panel to add content.';
    }
    return $output;
}
function getAllParentsById($id,$sql){
    global $lang;
    $que = "SELECT * FROM pages_$lang WHERE id=$id";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        $parent = $row['parent'];
        if($parent != 0){
            $parent .= ';'.getAllParentsById($parent,$sql);
        }
    }
    mysqli_free_result($erg);
    return $parent;
}
function findInArray($array,$needle){
    for($i=0;$i<sizeof($array);$i++){
        if($array[$i] == $needle){
            return $i;
        }
    }
    return -1;
}
function replaceUml($text){
    $umlaute = [['ä','ö','ü','Ä','Ö','Ü','ß'],['&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;']];
    for($i=0;$i<sizeof($umlaute[0]);$i++){
        $text = str_replace($umlaute[0][$i],$umlaute[1][$i],$text);
    }
    return $text;
}
$ktxt = getAllParentsById($id,$sql);
$parents = [$id];
$pos = strpos($ktxt,';');
while($pos > -1){
    array_push($parents,substr($ktxt,0,$pos));
    $ktxt = substr($ktxt,$pos+1);
    $pos = strpos($ktxt,';');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo($pageTitle);?></title>
    <script>
        var pageId = <?php echo($id);?>;
        var correctRightBar = false;
        <?php
            if($browser == 'Chrome' || $browser == 'Opera'){
                echo("correctRightBar = true;");
            }
        ?>
    </script>
    <link rel="stylesheet" href="styleHTML5.css" />
    <link rel="stylesheet" href="commonStyle.css"/>
    <link rel="SHORTCUT ICON" href="images/logo.png"/>
    <!-- todo: remove this in final!--><script src="jquery-1.9.1.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <script src="scriptHTML5.js"></script>

    <script src="picViewer/picViewer.js"></script>
</head>
<body onload="init()">
<div class="pageOuter">
    <div class="container" align="center">
        <div class="header">
            <img src="images/logo.png" class="pageLogo" />
            <div class="pageTitle"><?php echo($pageTitle);?></div>
            <div class="menu">
                <?php echo(printMenu($sql)); ?>
            </div>
        </div>
        <div class="contentOuter">
            <div class="content">
                <div class="contentInner">
                    <?php
                    $preview = $_GET['preview'];
                    $pagePath = $preview=='true'?'content':'web-content';
                    if(file_exists("web-content/$lang/$id.php")){
                        $einsatz = $_GET['einsatz'];
                        if($einsatz > 0){
                            if(file_exists("$pagePath/einsatz/$lang/$id/$einsatz.php")){
                                include("$pagePath/einsatz/$lang/$id/$einsatz.php");
                            }
                        }else{
                            include("$pagePath/$lang/$id.php");
                        }
                    }else{
                        echo('Upps this page is not avaliable!');
                    }
                    ?>
                </div>
                <div class="rightBar">
                    <div class="rightBarInner">
                        <div class="calendarSide"></div>
                        <div class="rightItem">Item</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            Impressum
        </div>
        <span class="copyRight">Copyright &copy; 2012 - <?php echo(date('Y').' '.$pageTitle);?></span>
     </div>
</div>
</body>
</html>