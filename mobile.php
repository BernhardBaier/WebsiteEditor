<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 13.05.14
 * Time: 20:50
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
$host = $hostname == 'localhost'?$hostname:$sqlHost;
$sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
$que = "SELECT * FROM settings WHERE parameter='pageTitle'";
$erg = mysqli_query($sql,$que);
while($row = mysqli_fetch_array($erg)){
    $pageTitle = $row['value'];
}
function printMenu($sql,$n_parent=0,$level=0){
    global $table,$parents,$childs,$lang,$id;
    $que = "SELECT * FROM ".$table." WHERE parent=$n_parent";
    $erg = mysqli_query($sql,$que);
    $rows = [];
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
        $classToAdd = $id==$pid?' active':'';
        if($extra == "1"){
            if($parent == 0){
                $output .= "<li><div class='menuItem$classToAdd'><a href='index.php?id=$pid&lang=$lang'>$name</a></div></li>";
            }else{
                $print = false;
                if($level == sizeof($parents)-1){
                    if(sizeof($childs) < 3 || $childs == '' || $childs == 'NULL'){
                        $print = true;
                    }
                }
                if(findInArray($parents,$pid) > -1 || findInArray($childs,$pid) > -1 || $print===true){
                    $output .= "<li><div class='menuItem$classToAdd'><a href='index.php?id=$pid&lang=$lang'>$name</a></div></li>";
                }
            }
            if($childCount > 0){
                $output .= '<ul>'.printMenu($sql,$pid,++$level) . '</ul>';
            }
        }
    }
    mysqli_free_result($erg);
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
$que = "SELECT * FROM pages_$lang WHERE id=$id";
$erg = mysqli_query($sql,$que);
$childs = [];
while($row = mysqli_fetch_array($erg)){
    $childs = unserialize($row['child']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo($pageTitle);?></title>
    <link rel="SHORTCUT ICON" href="images/logo.png"/>
    <link rel="stylesheet" href="styleMobile.css" />
    <script src="scriptMobile.js"></script>
    <!-- todo: remove this in final!--><script src="jquery-1.9.1.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="picViewer/picViewer.js"></script>
    <!--#style for plugins#-->
    <link href='plugins/article/stylePluginArticle.css' rel='stylesheet' />
    <!--#end#-->
</head>
<body onload="init()">
<div class="container">
    <div class="header">
        <div class="menuImg"><img src="images/menu.png" height="100%" onclick="toggleMenu()" /></div>
        <div class="pageTitle"><?php echo($pageTitle);?></div>
        <div class="menuLogo"><img src="images/logo.png" height="100%" /></div>
        <div class="menuOuter" id="menu">
            <?php
            echo(printMenu($sql));
            ?>
        </div>
    </div>
    <div class="pageOuter" id="pageOuter">
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
        </div>
    </div>
    <div class="footer">
        Footer
    </div>
    <div class="copyright">
        Copyright &copy; 2014 <?php echo($pageTitle)?>
    </div>
</div>
</body>
</html>