<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 18.01.14
 * Time: 10:19
 */
include('access.php');
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
if(!isset($_COOKIE['usercount'])){
    setcookie('usercount','false');
}
$users = 0;
if(file_exists('usercount.txt')){
    $file = fopen('usercount.txt','r');
    $users = fread($file,filesize('usercount.txt'));
    fclose($file);
}
if($_COOKIE['usercount'] == 'false'){
    setcookie('usercount','true',time()+70000);
    $users++;
    $file = fopen('usercount.txt','w');
    fwrite($file,$users);
    fclose($file);
}
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
        $prev = '&preview=true';
         $preview = true;
    }
}else{
    $preview = false;
    $prev = '';
}
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
function printMenu($sql,$n_parent=0,$level=0){
    global $table,$parents,$lang,$prev,$preview;
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
        if($extra == "1" || $preview == true){
            if($parent == 0){
                $classToAdd = findInArray($parents,$pid)>-1?' active':'';
                $level++;
                if($childCount > 0){
                    $output .= '<div class="menuItem topItem'.$classToAdd.'" onmouseover="this.childNodes[1].className=\'subMenu\'" onmouseout="this.childNodes[1].className=\'subMenu menuOut\'"><a href="index.php?id='.$pid.'&lang='.$lang.$prev.'">'.replaceUml($name).'</a><div class="subMenu menuOut">';
                }else{
                    $output .= '<div class="menuItem topItem'.$classToAdd.'"><a href="index.php?id='.$pid.'&lang='.$lang.$prev.'">'.replaceUml($name).'</a>';
                }
            }else{
                $classToAdd = $i==sizeof($rows)?' lastItem':'';
                $levelText = $level>3?' subItemLeft':'';
                if(findInArray($parents,$pid)>-1){
                    $classToAdd .= " active";
                }
                if($childCount > 0){
                    $output .= '<div class="menuItem subItem'.$classToAdd.'" onmouseover="this.childNodes[2].className=\'subMenu2'.$levelText.'\'" onmouseout="this.childNodes[2].className=\'subMenu2'.$levelText.' menuOut\'">
                    <a href="index.php?id='.$pid.'&lang='.$lang.$prev.'">'.replaceUml($name).'</a><div class="subMenu2'.$levelText.' menuOut">';
                }else{
                    $output .= '<div class="menuItem subItem'.$classToAdd.'"><a href="index.php?id='.$pid.'&lang='.$lang.$prev.'">'.replaceUml($name).'</a>';
                }
            }
            if($childCount > 0){
                $output .= printMenu($sql,$pid,$level) . '</div>';
            }
            $output .= '</div>';
        }
    }
    mysqli_free_result($erg);
    if($output == '' && $level == 0){
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
    <title><?php echo(strip_tags($pageTitle));?></title>
    <script>
        var pageId = <?php echo($id);?>;
        var lang = '<?php echo($lang);?>';
        var preview = '<?php echo($preview);?>';
        preview = preview=='true'?'content':'web-content';
        var correctRightBar = false;
        <?php
            if($browser == 'Chrome' || $browser == 'Opera'){
                echo("correctRightBar = true;");
            }
        ?>
    </script>
    <link rel="stylesheet" href="styleHTML5.min.css" />
    <link rel="SHORTCUT ICON" href="images/logo.png"/>
</head>
<body onload="init()">
<div class="pageOuter">
    <div class="searchOuter hidden">
        <div class="searchResults">
	        <img class="closingImg" src="pictures/close.png" title="schließen" onclick="$('.searchOuter').addClass('hidden')" />
	        <div class="searchResultsInner"></div>
        </div>
    </div>
    <div class="container" align="center">
        <div class="header">
            <div class="searchBox">
                <form name="search" action="javascript:searchNow()">
                    <input type="search" name="searchInput" placeholder="Suche" />
                    <input type="submit" value=" go " />
                </form>
            </div>
            <a href="index.php"><img src="images/logo.png" class="pageLogo" /></a>
            <div class="pageTitle"><?php echo($pageTitle);?></div>
            <div class="menu">
                <?php echo(printMenu($sql)); ?>
            </div>
        </div>
        <div class="contentOuter">
            <div class="content">
                <div class="contentInner">
                    <?php
                    $pagePath = $preview==true?'content':'web-content';
                    $preview = $preview==true?'preview/':'';
                    if(file_exists("$pagePath/$lang/".$preview."$id.php")){
                        include("$pagePath/$lang/".$preview."$id.php");
                    }else{
                        echo("Upps this page is not available!");
                    }
                    ?>
                </div>
                <div class="rightBar">
                    <div class="rightBarInner">
                        Right Bar
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <a href="index.php?id=impress&lang=<?php echo($lang);?>">Impressum</a>
            <div class="userCountOuter">
                <div class="userCountNumbers">
                <?php
                while(strlen($users) > 1){
                    echo('<span>'.substr($users,0,1).'</span>');
                    $users = substr($users,1);
                }
                echo("<span>$users</span>");
                ?>
                </div>
                Besucher waren bereits auf unserer Website.
            </div>
            <span class="copyRight">Copyright &copy; 2012 - <?php echo(date('Y').' '.$pageTitle);?></span>
        </div>
     </div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="picViewer/picViewer.min.js"></script>
    <script src="spin.min.js" async></script>
    <link rel="stylesheet" href="commonStyle.min.css"/>
	<!-- DO NOT CHANGE THE LINES BELOW-->
	<!--#style for plugins#-->
    <!--#end#-->
    <script src="scriptHTML5.min.js"></script>
</div>
</body>
</html>