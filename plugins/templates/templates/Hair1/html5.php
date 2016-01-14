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
$specialContent = "";
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
mysqli_free_result($erg);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo(strip_tags($pageTitle));?></title>
    <script>
        pageId = <?php echo($id);?>;
        var lang = '<?php echo($lang);?>';
        preview = '<?php echo($preview);?>';
        var correctRightBar = false;
        <?php
            if($browser == 'Chrome' || $browser == 'Opera'){
                echo("correctRightBar = true;");
            }
        ?>
    </script>
    <link rel="stylesheet" href="styleHTML5.min.css" />
    <link rel="SHORTCUT ICON" href="images/logo.png"/>
    <!-- DO NOT CHANGE THE LINES BELOW-->
    <!--#meta data#-->
    <!--#end#-->
</head>
<body onload="init()">
<div class="pageOuter">
    <div class="footer">
        <div class="footerLeft">Copyright &copy; 2012 - <?php echo(date('Y ') . strip_tags($pageTitle));?></div>
        <div class="footerRight" onclick="showPageInOverlay('impress')">Impressum</div>
    </div>
    <div class="container" align="center">
        <div class="pageOverlayOuter hidden">
            <div class="pageOverlayHider hidden" onclick="closePageInOverlay()"></div>
            <div class="pageOverlay">
                <img src="pictures/close.png" class="pageOverlayCloser" onclick="closePageInOverlay()" />
                <div class="pageOverlayContentOuter">
                    <div class="pageOverlayContent" id="pageOverlayContent1"></div>
                </div>
            </div>
        </div>
        <div class="pageSeparatorOuter">
            <div class="pageSeparator">
                <div class="styleElementOuter width0">
                    <div class="styleElement">
                        <div class="styleElementText text0" onclick="showPageInOverlay(4)">Die Musikschule N<span class="specialLetter">&uuml;</span>rtingen Pr<span class="specialLetter">&auml;</span>sentiert</div>
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color0">
                        <div class="styleElementLink" onclick="showPageInOverlay(3)"><div class="styleElementLinkText">Projekte</div></div>
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color1">
                        <img class="styleElementImg" src="pictures/Pigeon.png" />
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color2">
                        <div class="styleElementLink" onclick="showPageInOverlay(5)"><div class="styleElementLinkText row2">Karten online kaufen</div></div>
                        <!--<div class="flipContainer" id="flipper2">
                            <div class="flipper">
                                <div class="flipper front"></div>
                                <div class="flipper back">Place for something here</div>
                            </div>
                        </div>-->
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement" onclick="showPageInOverlay(5)">
                        Termine
                        <div class="styleElementText">
                            Fr. 04.03.2016 10 Uhr<br>
                            Fr. 04.03.2016 20 Uhr<br>
                            Sa. 05.03.2016 20 Uhr<br>
                            So. 06.03.2016 17 Uhr<br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pageSeparatorOuter hidden">
            <div class="pageSeparator"></div>
        </div>
        <div class="pageSeparatorOuter">
            <div class="pageSeparator">
                <div class="styleElementOuter width0">
                    <div class="styleElement color3">
                        <img class="styleElementImg" src="pictures/Flower.png" />
                    </div>
                </div>
                <div class="styleElementOuter width1">
                    <div class="styleElement" onclick="showPageInOverlay(2)">
                        <div class="styleElementText text1">Hippie</div>
                        <div class="styleElementText text2">Life</div>
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color4">
                        <img class="styleElementImg" src="pictures/Peace.png" />
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color5">
                        <div class="styleElementLink" onclick="showPageInOverlay(4)"><div class="styleElementLinkText"><span class="specialLetter">&Uuml;</span>ber uns</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pageSeparatorOuter hidden">
            <div class="pageSeparator"></div>
        </div>
        <div class="pageSeparatorOuter">
            <div class="pageSeparator">
                <div class="styleElementOuter width0">
                    <div class="styleElement color6">
                        <img class="styleElementImg" src="pictures/Woman.png" />
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color7">
                        <div class="styleElementLink" onclick="showPageInOverlay(2)"><div class="styleElementLinkText">Das St<span class="specialLetter">&uuml;</span>ck</div></div>
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement" onclick="showPageInOverlay(2)">
                        <div class="styleElementText text3">Make love</div>
                        <div class="styleElementText text4">not war</div>
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color8">
                        <img class="styleElementImg" src="pictures/Fingers.png" />
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color9">
                        <div class="styleElementLink" onclick="showPageInOverlay(6)"><div class="styleElementLinkText">Bilder</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- TODO: remove below in final!-->
    <script src="jquery-1.9.1.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="picViewer/picViewer.min.js"></script>
    <script src="spin.min.js" async></script>
    <link href="commonStyle.min.css" rel="stylesheet" />
	<!-- DO NOT CHANGE THE LINES BELOW-->
	<!--#style for plugins#-->
    <!--#end#-->
    <script src="scriptHTML5.min.js"></script>
</div>
</body>
</html>