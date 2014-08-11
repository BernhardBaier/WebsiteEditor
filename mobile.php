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
    global $table,$parents,$childs,$equal,$lang,$id;
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
                $output .= "<li><a href='index.php?id=$pid&lang=$lang'><div class='menuItem$classToAdd'>$name</div></a></li>";
            }else{
                if(findInArray($parents,$pid) > -1 || findInArray($childs,$pid) > -1 || findInArray($equal,$pid) > -1){
                    $output .= "<li><a href='index.php?id=$pid&lang=$lang'><div class='menuItem$classToAdd'>$name</div></a></li>";
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
    $parent = $row['parent'];
    $childs = unserialize($row['child']);
}
$que = "SELECT * FROM pages_$lang WHERE id=$parent";
$erg = mysqli_query($sql,$que);
$equal = [];
while($row = mysqli_fetch_array($erg)){
    $equal = unserialize($row['child']);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo($pageTitle);?></title>
	<meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="SHORTCUT ICON" href="images/logo.png"/>
    <link rel="stylesheet" href="styleMobile.css" />
    <script>
        var lang = '<?php echo($lang);?>';
    </script>
</head>
<body onload="init()">
<div class="container">
    <div class="menuOuter" id="menu">
        <?php
        echo(printMenu($sql));
        ?>
    </div>
    <div class="searchResultsOuter hidden">
        <div class="searchResults">
            <img src="images/close.png" title="schließen" onclick="$('.searchResultsOuter').addClass('hidden')" />
            <div class="searchResultsInner"></div>
        </div>
    </div>
    <div class="header">
	    <div class="headerDivider">
	        <div class="menuImg"><img src="images/menu.png" height="100%" onclick="toggleMenu()" /></div>
	        <div class="pageTitle"><?php echo($pageTitle);?></div>
		    <div class="menuLogo"><img src="images/logo.png" height="100%" /></div>
		    <img class="searchIcon" onclick="expandMenu()" src="images/search.png" />
	    </div>
	    <div class="searchOuter">
		    <div class="searchBig">
			    <form class="searchBox" name="search" action="javascript:searchNow()">
				    <input name="searchInput" placeholder="Suche" type="search">
				    <input value=" go " type="submit">
			    </form>
		    </div>
	    </div>
    </div>
    <div class="pageOuter" id="pageOuter">
        <div class="content">
            <div class="contentInner">
                <?php
                $preview = $_GET['preview'];
                $pagePath = $preview=='true'?'content':'web-content';
                $preview = $preview=='true'?'preview/':'';
                if(file_exists("$pagePath/$lang/".$preview."$id.php")){
	                include("$pagePath/$lang/".$preview."$id.php");
                }else{
	                echo("Upps this page is not available!");
                }
                ?>
            </div>
        </div>
    </div>
    <div class="footer">
        <a href="index.php?id=impress&lang=<?php echo($lang);?>">Impressum</a>
    </div>
    <div class="copyright">
        Copyright &copy; 2014 <?php echo($pageTitle)?>
    </div>
</div>
<script src="scriptMobile.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="picViewer/picViewer.min.js"></script>
<link href='commonStyle.css' rel='stylesheet' />
<!-- DO NOT CHANGE THE LINES BELOW-->
<!--#style for plugins#-->
<link href='plugins/article/stylePluginArticle.css' rel='stylesheet' />

    <link href='plugins/calendar/stylePluginCalendar.css' rel='stylesheet' />
    <link href='plugins/einsatz/stylePluginEinsatz.css' rel='stylesheet' /><!--#end#-->
</body>
</html>