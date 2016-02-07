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
$que = "SELECT * FROM settings WHERE parameter='pageTitle_$lang'";
$erg = mysqli_query($sql,$que);
while($row = mysqli_fetch_array($erg)){
    $pageTitle = $row['value'];
}
mysqli_free_result($erg);
$que = "SELECT * FROM settings WHERE parameter='languageSupport'";
$erg = mysqli_query($sql,$que);
$langSupport = false;
while($row = mysqli_fetch_array($erg)){
    $langSupport = $row['value'];
}
mysqli_free_result($erg);
$langSupport = $langSupport=='multi'?true:false;
if($langSupport){
    $que = "SELECT * FROM settings WHERE parameter='languages'";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        $languages = unserialize($row['value']);
    }
    mysqli_free_result($erg);
    $que = "SELECT * FROM settings WHERE parameter='languagesLong'";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        $longLanguages = unserialize($row['value']);
    }
    mysqli_free_result($erg);
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
                if(strpos($classToAdd, "active") > -1){
                    $output .= "<li><a><div class='menuItem$classToAdd'>$name</div></a></li>";
                }else{
                    $output .= "<li><a href='index.php?id=$pid&lang=$lang'><div class='menuItem$classToAdd'>$name</div></a></li>";
                }
            }else{
                if(findInArray($parents,$pid) > -1 || findInArray($childs,$pid) > -1 || findInArray($equal,$pid) > -1){
                    if(strpos($classToAdd, "active") > -1){
                        $output .= "<li><a><div class='menuItem$classToAdd'>$name</div></a></li>";
                    }else{
                        $output .= "<li><a href='index.php?id=$pid&lang=$lang'><div class='menuItem$classToAdd'>$name</div></a></li>";
                    }
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
    $umlaute = [['�','�','�','�','�','�','�'],['&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;']];
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
    <title><?php echo(strip_tags($pageTitle));?></title>
	<meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="SHORTCUT ICON" href="images/logo.png"/>
    <link rel="stylesheet" href="styleMobile.min.css" />
    <!-- DO NOT CHANGE THE LINES BELOW-->
    <!--#meta data#-->
    <!--#end#-->
    <script>
        var lang = '<?php echo($lang);?>';
        var mobile = true;
    </script>
</head>
<body onload="init()">
<!-- DO NOT CHANGE THE LINES BELOW-->
<!--#analytics data#-->
<!--#end#-->
<div class="container">
    <div class="menuOuter" id="menu">
        <?php
        echo(printMenu($sql));
        ?>
    </div>
    <div class="header">
        <div class="searchResultsOuter hidden">
            <div class="searchResults">
                <img src="pictures/close.png" title="schlie�en" onclick="$('.searchResultsOuter').addClass('hidden')" />
                <div class="searchResultsInner"></div>
            </div>
        </div>
	    <div class="headerDivider">
	        <div class="menuImg"><img src="pictures/menu.png" height="100%" onclick="toggleMenu()" /></div>
	        <div class="pageTitle"><?php echo($pageTitle);?></div>
		    <div class="menuLogo"><a href="index.php?lang=<?php echo($lang);?>"><img src="images/logo.png" height="100%" /></a></div>
		    <img class="searchIcon" onclick="expandMenu()" src="pictures/search.png" />
	    </div>
	    <div class="searchOuter">
		    <div class="searchBig">
			    <form class="searchBox" name="search" action="javascript:searchNow()">
				    <input name="searchInput" placeholder="Suche" type="search" />
				    <input value=" go " type="submit" />
			    </form>
                <?php
                if($langSupport){
                    echo("<div class='languageChooser'>");
                    if($lang == $languages[0]){
                        echo("<a href='index.php?id=".$id."&lang=".$languages[1]."' title='switch to ".$longLanguages[1]."'><img src='pictures/flag_".$languages[1].".png' /></a>");
                    }
                    if($lang == $languages[1]){
                        echo("<a href='index.php?id=".$id."&lang=".$languages[0]."' title='wechsele zu ".$longLanguages[0]."'><img src='pictures/flag_".$languages[0].".png' /></a>");
                    }
                    echo("</div>");
                }
                ?>
		    </div>
	    </div>
        <div class="roundButton topOverlay opac0 hidden">
            <div class="topOverlayInner" onclick="goToTop()">
                <img src="pictures/arrowTop.png" />
            </div>
        </div>
    </div>
    <div class="pageOuter" id="pageOuter">
        <div class="content">
            <div class="contentInner">
                <?php
                $preview = $_GET['preview'];
                if($preview == 'true') {
                    $preview = false;
                    session_start();
                    $authLevel = "";
                    $hostname = $_SERVER['HTTP_HOST'];
                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }
                    if($ip == $_SESSION['ip']){
                        $authLevel = $_SESSION['authlevel'];
                    }
                    if(substr($authLevel,0,1) == "1"){
                        $preview = 'true';
                    }
                }else{
                    $preview = false;
                }
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
        <div class="fbBox">
            <div>
                <?php include('footer_'.$lang.'.php');?>
            </div>
        </div>
        <a href="index.php?id=impress&lang=<?php echo($lang);?>" style="float: right">Impressum&nbsp;</a>
    </div>
    <div class="copyright">
        Copyright &copy; 2014  - <?php echo(date('Y').' '.$pageTitle);?>
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="scriptMobile.min.js"></script>
<script src="spin.min.js" async></script>
<script src="picViewer/picViewer.min.js"></script>
<link href='commonStyle.min.css' rel='stylesheet' />
<!-- DO NOT CHANGE THE LINES BELOW-->
<!--#style for plugins#-->
<!--#end#-->
</body>
</html>