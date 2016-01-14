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
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo(strip_tags($pageTitle));?></title>
	<meta name=viewport content="width=device-width, initial-scale=1">
    <link rel="SHORTCUT ICON" href="images/logo.png"/>
    <link rel="stylesheet" href="styleMobile.min.css" />
    <script>
        pageId = <?php echo($id);?>;
        var lang = '<?php echo($lang);?>';
        preview = '<?php echo($preview);?>';
        var mobile = true;
    </script>
    <!-- DO NOT CHANGE THE LINES BELOW-->
    <!--#meta data#-->
    <!--#end#-->
</head>
<body onload="init()">
<div class="pageOuter">
    <div class="footer">
        <div class="footerLeft">Copyright &copy; <?php echo(date('Y ') . strip_tags($pageTitle));?></div>
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
                        <div class="styleElementText text0">Die Musikschule N<span class="specialLetter">&uuml;</span>rtingen Pr<span class="specialLetter">&auml;</span>sentiert</div>
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color0">
                        <div class="styleElementLink" onclick="showPageInOverlay(3)"><div class="styleElementLinkText">Projekte</div></div>
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color1">
                        <img class="styleElementImg" src="pictures/Pigeon_mobile.png" />
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
                        <img class="styleElementImg" src="pictures/Flower_mobile.png" />
                    </div>
                </div>
                <div class="styleElementOuter width1">
                    <div class="styleElement" onclick="showPageInOverlay(2)">
                        <div class="styleElementText text1">Hippie</div>
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
                    <div class="styleElement color2">
                        <div class="styleElementLink" onclick="showPageInOverlay(5)"><div class="styleElementLinkText row3">Karten online kaufen</div></div>
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color4">
                        <img class="styleElementImg" src="pictures/Peace_mobile.png" />
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement" onclick="showPageInOverlay(2)">
                        <div class="styleElementText text2">Life</div>
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
                    <div class="styleElement color7">
                        <div class="styleElementLink" onclick="showPageInOverlay(2)"><div class="styleElementLinkText row2">Das St<span class="specialLetter">&uuml;</span>ck</div></div>
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
                        <img class="styleElementImg" src="pictures/Fingers_mobile.png" />
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
                <div class="styleElementOuter width0">
                    <div class="styleElement color5">
                        <div class="styleElementLink" onclick="showPageInOverlay(4)"><div class="styleElementLinkText row2"><span class="specialLetter">&Uuml;</span>ber uns</div></div>
                    </div>
                </div>
                <div class="styleElementOuter width0">
                    <div class="styleElement color9">
                        <div class="styleElementLink" onclick="showPageInOverlay(6)"><div class="styleElementLinkText">Bilder</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pageSeparatorOuter hidden">
            <div class="pageSeparator"></div>
        </div>
    </div>
    <!-- TODO: remove below in final!-->
<script src="jquery-1.9.1.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="scriptMobile.min.js"></script>
<script src="spin.min.js" async></script>
<script src="picViewer/picViewer.min.js"></script>
<link href="commonStyle.min.css" rel="stylesheet" />
<!-- DO NOT CHANGE THE LINES BELOW-->
<!--#style for plugins#-->
<!--#end#-->
</body>
</html>