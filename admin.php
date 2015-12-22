<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 12.01.14
 * Time: 11:08
 */
error_reporting(E_ERROR);
include('access.php');
if(!isset($_GET['id'])){
    $base = $sqlBase;
    $table = 'pages_'.$lang;
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
    $lang = $_GET['lang'];
    if($lang == ''){
        $lang = 'de';
    }
    $id = redirectToFirstPage($lang);
    header("Location: admin.php?id=$id&lang=$lang");
    exit;
}
function redirectToFirstPage($lang){
    global $sql;
    $que = "SELECT * FROM pages_$lang WHERE parent='0' and rank='1'";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    while($row = mysqli_fetch_array($erg)){
        return $row['id'];
    }
}
function findInArray($array,$needle){
    for($i=0;$i<sizeof($array);$i++){
        if($array[$i] == $needle){
            return $i;
        }
    }
    return -1;
}
function jsMenu($parent=0){
    global $sql,$lang;
    $que = "SELECT * FROM pages_$lang WHERE parent=$parent";
    $erg = mysqli_query($sql,$que);
    $rows = array();
    while($help = mysqli_fetch_array($erg)){
        $rows[$help['rank']] = $help;
    }
    $output = "";
    if(sizeof($rows) != 0){
        for($i=1;$i<=sizeof($rows);$i++){
            $row = $rows[$i];
            $pid = $row['id'];
            $name = $row['name'];
            $childCount = $row['childCount'];
            if($childCount > 0){
                $output .= "jsPageNames[$pid] = '$name';
        ".jsMenu($pid);
            }else{
                $output .= "jsPageNames[$pid] = '$name';
        ";
            }
        }
    }
    mysqli_free_result($erg);
    return $output;
}
if(substr($authLevel,0,1) == "1"){
    $browser = $_SERVER['HTTP_USER_AGENT'];
    $chrome = false;
    if(strpos($browser,'Chrome') > -1) {
        $chrome = true;
    }
    $id = $_GET['id'];
    $lang = $_GET['lang'];
    if($id == ""){
        $id = 1;
    }
    $action = $_GET['action'];
    if(isset($_GET['pluginId'])){
        $pluginId = $_GET['pluginId'];
    }
    $pageTitle = 'no title';
    $editorVersion = '4.2';
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    if($sql){
        $que = "SELECT * FROM settings WHERE parameter='languageSupport'";
        $erg = mysqli_query($sql,$que);
        $langSupport = false;
        while($row = mysqli_fetch_array($erg)){
            $langSupport = $row['value'];
        }
        mysqli_free_result($erg);
        $langSupport = $langSupport=='multi'?true:false;
        $languages = array();
        $longLanguages = array();
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
        if($lang == "" || findInArray($languages,$lang) == -1){
            $lang = 'de';
        }
        $que = "SELECT * FROM settings WHERE parameter='pageTitle_$lang'";
        $erg = mysqli_query($sql,$que);
        while($row = mysqli_fetch_array($erg)){
            $pageTitle = $row['value'];
        }
        mysqli_free_result($erg);
        $que = "SELECT * FROM settings WHERE parameter='editorVersion'";
        $erg = mysqli_query($sql,$que);
        while($row = mysqli_fetch_array($erg)){
            $editorVersion = $row['value'];
        }
        mysqli_free_result($erg);
    }

    if(!is_dir('content')){
        mkdir('content');
    }
    if(!is_dir('content/'.$lang)){
        mkdir('content/'.$lang);
    }
    if(!is_dir('web-content')){
        mkdir('web-content');
    }
    if(!is_dir('web-content/'.$lang)){
        mkdir('web-content/'.$lang);
    }
    if(!is_dir('web-images/')){
        mkdir('web-images/');
    }
    if(!is_dir('web-images/'.$id)){
        mkdir('web-images/'.$id);
        mkdir('web-images/'.$id.'/thumbs');
    }
    if(!is_dir('web-others/')){
        mkdir('web-others/');
    }
    if(!is_dir('web-others/'.$id)){
        mkdir('web-others/'.$id);
        mkdir('web-others/'.$id.'/thumbs');
    }
    ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title><?php echo(strip_tags($pageTitle));?> Admin</title>
    <link rel="SHORTCUT ICON" href="images/editorLogo.png"/>
    <link rel="stylesheet" href="styleAdmin.min.css"/>
    <link rel="stylesheet" href="commonStyle.min.css"/>
    <link rel="stylesheet" href="styleFileBrowser.min.css"/>
    <link rel="stylesheet" type="text/css" href="datepicker/jquery.datetimepicker.css" >
    <?php
    if($chrome == true){
        echo("<style>.menu li{margin:-18px 0 0 0;}</style>");
    }
    ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <script src="spin.min.js"></script>

    <script src="ckeditor/ckeditor.js"></script>
    <script>
        var startHTML = '';
        pageId = <?php if($id>0){echo($id);}else{echo("'$id'");}?>;
        lang = '<?php echo($lang);?>';
        var showPlugIn = false;
        var pluginId = 0;
        var showUsers = false;
        <?php
        switch($action){
            case 'showPlugins':
                echo('showPlugIn = true;');
                break;
            case 'showUsers':
                echo('showUsers = true;');
                break;
        }
        if(isset($pluginId)){
            echo("pluginId = $pluginId;");
        }
        ?>
        var dropTopOff = <?php echo(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') != false?-3:-26);?>;
        var dropBotOff = <?php echo(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') != false?43:20);?>;
        var jsPageNames = [];
        var jsLanguages = [];
        <?php
        echo(jsMenu());
        for($i=0;$i<sizeof($languages);$i++){
            echo("jsLanguages[$i] = '".$languages[$i]."';
        ");
        }
        ?>

        function tourStepSave(step,leftWidth){
            <?php
            if(substr($authLevel,2,1) == '1'){
                echo('$(\'.tourBox\').width(55).height(28).css(\'left\',leftWidth+208).css(\'top\',0);
            $(\'.tourText\').css(\'top\',40).css(\'left\',leftWidth+87);
            $(\'.tourText\').html(\'Here we have our user control panel<br/><div class="tourBut tourLeft" onclick="pageTourStep(\'+(step-1)+\')">go back</div><div class="tourBut" onclick="pageTourStep(\'+(step+1)+\')">proceed</div></div>\');
            ');
            }else{
                echo('$(\'.tourBox\').width(150).height(28).css(\'left\',(leftWidth+windowWidth)*0.5-95).css(\'top\',0);
            $(\'.tourText\').css(\'top\',40).css(\'left\',(leftWidth+windowWidth)*0.5-175);
            $(\'.tourText\').html(\'The current page name<br/><div class="tourBut tourLeft" onclick="pageTourStep(\'+(step-1)+\')">go back</div><div class="tourBut" onclick="pageTourStep(\'+(step+1)+\')">proceed</div></div>\');
            ');
            }
            ?>
        }
    </script>

    <script src="datepicker/jquery.datetimepicker.js"></script>

    <script src="scriptFileBrowser.min.js"></script>
    <script src="scriptAdmin.js"></script>
    <script>
        <?php
        if(strpos($_SESSION['extra'],'tour') < 0 || !$_SESSION['extra']){
            echo('initPageTour();');
            $_SESSION['extra'] .= 'tour';
        }
        ?>
    </script>
    <script src="scriptPlugin.min.js"></script>
</head>
<body onload="init()">
<div class="pageLoading"><div class="loadingImg"><div id="loadingImg1"></div><div class="loadingMessage" style="color:#FFF;">Loading</div></div></div>
        <script>
    var opts = {
        lines: 14,
        length: 16,
        width: 7,
        radius: 22,
        corners: 1,
        rotate: 219,
        direction: 1,
        color: '#FFF',
        speed: 1.2,
        trail: 75,
        shadow: false,
        hwaccel: false,
        className: 'spinner',
        zIndex: 9,
        top: '40%',
        left: '50%'
    };
    var target = document.getElementById('loadingImg1');
    var spinner = new Spinner(opts).spin(target);
</script>
<div class="pageTour opac0 hidden"><div class="tourBox hidden"></div><div class="tourText"></div></div>
<?php
include('content/user.php');
?>
<div class="logout opac0 hidden"></br><div class="logoutText">Logout?</br>
     <form action="logout.php"><input type="submit" value="logout"/><input type="button" value="cancel" onclick="$('.logout').addClass('hidden').addClass('opac0')"/></form></div>
</div>
<div class="overlay hidden" title="close" onclick="hideMessages()"></div>
<div class="rename msgBox hidden">Rename Page:</br>
    <form action="javascript:renameNow()" name="input2">
        <input type="text" name="pagename" required placeholder="page name"/><input type="hidden" name="id" /><input type="submit" value="ok"/>
    </form>
</div>
<div class="delete msgBox hidden"><span class="deleteTitle"></span></br>
    <form name="input3"><input type="hidden" name="id" /><input type="button" value=" delete " onclick="deleteNow()"/><input type="button" value=" cancel " onclick="hideMessages();"/></form>
</div>
<div class="addSubPage msgBox hidden"><span class="addSubPageTitle"></span></br>
    <form name="input4" action="javascript:addSubPage()"><input type="hidden" name="parent" /><input required type="text" name="pagename" placeholder="page name"/><input type="submit" value=" ok "/></form>
</div>
<div class="addEqualPage msgBox hidden"><span class="addEqualPageTitle"></span></br>
    <form name="input5" action="javascript:addEqualPage()">
        <input type="hidden" name="parent" /><input type="hidden" name="rank" /><input type="text" required name="pagename" placeholder="page name"/><input type="submit" value=" ok "/>
    </form>
</div>
<div class="infoBox msgBox hidden">
    <span class="infoBoxTitle"></span></br>
    <span class="infoBoxInfo"></span>
</div>
<div class="notificationBox msgBox opac0 hidden" onmouseover="notificationBoxHover()" onmouseout="notificationBoxDisHover()">
    <div class="msgBoxImg"><img onclick="hideNotificationBox()" height="20" title="close" src="images/close.png"/></div>
    <div class="notificationBoxInner"></div>
</div>

<div class="renamePic msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    Rename file:</br>
    <form name="rename" action="javascript:renamePicNow()">
        <input type="text" placeholder="name" name="name" /><input type="submit" value=" OK " /><input type="hidden" name="ending" /><input type="hidden" name="old" />
    </form>
</div>
<div class="deletePic msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    <div class="deletePicTitle"></div>
    <form name="delete" action="javascript:deletePicNow()">
        <input type="submit" value=" delete " /><input type="button" value=" cancel " onclick="hideMessages()" /><input type="hidden" name="name" />
    </form>
</div>
<div class="deleteMultiplePic msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    <div class="deletePicMultipleTitle"></div>
    <form name="deleteMultiple" action="javascript:deleteMultiplePicNow(0)">
        <input type="submit" value=" delete " /><input type="button" value=" cancel " onclick="hideMessages()" />
    </form>
</div>
<div class="insertFile msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    insert file <span class="insertFileTitle"></span> as:</br>
    <form name="insertFile" action="javascript:insertFileNow()">
        <select name="type"><option>Link</option><option>IFrame</option></select>
        <input type="submit" value=" insert " /><input type="button" value=" cancel " onclick="hideMessages()" /><input type="hidden" name="name" />
    </form>
</div>
<div class="movePicOuter msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    move <span class="movePicTitle"></span> pic(s) to page:<br>
    <div class="movePicMenu"></div>
</div>
<div class="deleteUserMsg msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    delete User?<br/>
    <form name="deleteUserForm" action="javascript:deleteUserNow()">
        <input type="submit" value=" delete " /><input type="button" value=" cancel " onclick="hideMessages()" /><input type="hidden" name="name" />
    </form>
</div>
<div class="deleteTermin msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    delete event?<br/>
    <form name="delTerminForm" action="javascript:delTerminNow()">
        <input type="submit" value=" delete " /><input type="button" value=" cancel " onclick="hideMessages()" /><input type="hidden" name="id" />
    </form>
</div>
<div class="setUserRightsMsg msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    set user rights to <span class="setUserRightsTitle"></span><br/>
    <form name="setUserRightsForm" action="javascript:setUserRightsNow()">
        <div class="setUserRightsStruct"><input type="checkbox" name="chk1"/>editor</div><div class="setUserRightsStruct"><input type="checkbox" name="chk2"/>reporter</div>
        <div class="setUserRightsStruct"><input type="checkbox" name="chk3"/>sub admin</div><div class="setUserRightsStruct"><input type="checkbox" name="chk4"/>coder</div>
        <input type="submit" value=" ok " /><input type="button" value=" cancel " onclick="hideMessages()" /><input type="hidden" name="name" />
    </form>
</div>
<div class="insertPic msgBox msgBoxBig hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
	<div class="insertPicLink opac0 hidden">
		<div class="msgBoxImg"><img onclick="hideAddPictureLink()" height="20" title="close" src="images/close.png"/></div>
		<div>link hover text: <input type="text" value="" id="linkTextToInsert"></div>
		<div class="insertPicLinkMenue"></div>
		<div><input type="button" value="remove link" onclick="removeLinkFromPicture()" /></div>
	</div>
    insert picture as:</br>
    <form name="insert" action="javascript:insertPicNow()">
        <select name="type" onchange="changeInsertType()"><option>single pic</option><option>titled pic</option><option>subtitled pic</option></select><input type="button" onclick="showGaleryMaker()" value=" gallery " /></br>
        Alignment: <select name="align" onchange="changeInsertType()"><option>none</option><option>left</option><option>right</option><option>center</option></select>
        <div class="htmlToInsert"></div>
        <input type="button" id="insertPicLinkButton" value="add a link" onclick="showAddPictureLink()" /><input type="submit" value=" ok " /><input type="hidden" name="path"/>
    </form>
</div>
<div class="insertMultiplePic msgBox msgBoxBig hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    insert picture frame:</br>
    <div class="htmlToInsertMulti"></div>
    <input type="submit" value=" ok " onclick="multipleInsertNow()"/><input type="button" value="cancel" onclick="hideMessages()"/>
</div>
<div class="uploadError msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    an error occured while uploading!</br>
    Pleace try angain.</br><input type="button" value="ok" onclick="hideMessages()" />
</div>
<div class="insertLinkOuter hidden">
    <div class="msgBoxImg"><img onclick="hideInsertLink()" height="20" title="close" src="images/close.png"/></div>
    <div class="insertLink"></div>
</div>
<div class="publishOuter msgBox hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    <div class="publishInner">
        Publish page "<span class="pageName"></span>"</br>
        <?php
        if($langSupport){
            echo('<label><input type="checkbox" id="publishPageLang" />publish all languages</label><br>');
        }else{
            echo('<div id="publishPageLang"></div>');
        }
        ?>
        <input type="button" onclick="hideMessages()" value="cancel" /><input type="submit" value=" OK " onclick="saveText('content',true)" />
    </div>
</div>
<div class="publishErrorOuter msgBox opac0 hidden">
    <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="images/close.png"/></div>
    <div class="publishErrorInner">
        This page is not visible at the moment!<br>
        Do you want me to make it visible and publish it then?<br>
        <input type="button" value="cancel" onclick="hideMessages()" /><input type="submit" value=" OK " onclick="publishPageWithVisib();" />
    </div>
</div>
<?php
if(substr($authLevel,2,1) == '1'){
    echo('
<div class="userControlOuter out">
    <div class="userControlContainer">
        <div class="userControlTitle">User control<img src="images/close.png" onclick="showUserControl()" height="25" style="float:right;" /></div>
        <div class="userControlInner"></div>
    </div>
</div>');
}
?>
<div class="pluginOuter out">
    <div class="pluginOuterTitle">Plugins<img src="images/close.png" onclick="showPlugins()" height="25" style="float:right;" /></div>
    <div class="pluginOuterInner">
        <div class="pluginNav">
            <?php
            $que = "SELECT * FROM plugins WHERE 1;";
            $erg = mysqli_query($sql,$que);
            while($row = mysqli_fetch_array($erg)){
                $location = $row['location'];
                include("$location/include.php");
            }
            mysqli_free_result($erg);
            if($authLevel == '1111'){
                echo('<img src="plugins/logo.png" class="pluginNavImg" onclick="addPlugin(this);$(this).addClass(\'active\')" title="add plugin"/>');
            }
            ?>
        </div>
        <div class="pluginInner">
            <?php
            $que = "SELECT * FROM plugins WHERE 1";
            $erg = mysqli_query($sql,$que);
            while($row = mysqli_fetch_array($erg)){
                $location = $row['location'];
                $name = $row['name'];
                $plugId = $row['id'];
                echo("<img src='".$location."active.png' class='hidden'/>");
            }
            mysqli_free_result($erg);
            ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="leftBar">
        <div class="leftBarBorder"></div>
        <div class="leftBarTitle">Website menu</div>
        <div class="menu" onclick="reprintMenu()">&nbsp;&nbsp;&nbsp;&nbsp;reprint menu</div>
    </div>
    <img class="leftBarNav imgRotate" src="images/leftBar.png" title="hide menu" height="20" onclick="toggleLeftBar()" />
	<div class="itemsGoRightOut">
		<div class="itemsGoRightOutInner">
			<div class="galeryMakerOuter">
				<div class="galeryMakerTitle"><img src="images/close.png" style="position:absolute;left:5px;top:5px;cursor:pointer;" title="close" onclick="hideMessages()" height="25" />Gallery Editor
					<img src="images/galOptions.png" class="galeryMakerImg imgRotate" height="30" onclick="$('.galeryMakerOptions').toggleClass('height0');$(this).toggleClass('imgRotated')" />
					<div class="galeryMakerOptions height0"><div onclick="galeryMakerSelectAll(1)">Select all <img height="20" src="images/select.png" /></div><div onclick="galeryMakerSelectAll(0)">Deselect all <img height="18" src="images/deselect.png" /></div></div></div>
				<div class="galeryMakerInner"></div>
				<div class="galeryMakerButton" onclick="generateGallery()">generate gallery with selected pictures</div>
			</div>
		</div>
	</div>
    <div class="pageContainer">
        <div class="pageMenu"><div class="pageMenuInner"><span class="pageMenuTitle" title="the name of the current page"></span>
            <div class="pageMenuInfo">Website editor Version <?php echo($editorVersion);?>&nbsp;&nbsp;<br>&nbsp;Copyright &copy; <?php echo(date('Y',time()));?> Bernhard Baier&nbsp;&nbsp;</div>&nbsp;
                <div class="pageMenuItem rightItem" onclick="$('.logout').removeClass('opac0').removeClass('hidden')" title="close session and log off">Logout</div>
                <div class="pageMenuItem rightItem" onclick="showPublish()" title="publish this page">Publish</div>
                <div class="pageMenuItem"><a onclick="saveText('content',false)" title="save content (ctrl + s)">Save</a></div>
                <div class="pageMenuItem" onclick="showPageOptions(<?php echo($id);?>,this)" title="show the pages options"><a>Options</a></div>
                <div class="pageMenuItem">
	                <a target="_blank" href="index.php?id=<?php echo("$id&lang=$lang");?>&preview=true" title="preview this page in a new tab">Preview</a>
                </div>
                <?php
                if($langSupport){
                    echo("<div class='pageMenuItem' onclick=\"$('.langChooser').toggleClass('hidden')\" title='current language'>Lang: $lang</div>");
                }
                if(substr($authLevel,2,1) == '1'){
                    echo('<div class="pageMenuItem" onclick="showUserControl()" title="show registered users and set their rights">Users</div>');
                }
                ?>
            </div>Text editing mode
        </div>
        <div class="langChooser hidden">
            <?php
            for($i=0;$i<sizeof($languages);$i++){
                echo("<div class='pageOptionItem'><a href='admin.php?id=$id&lang=".$languages[$i]."'>".$longLanguages[$i]."</a></div>");
            }
            ?>
        </div>
        <div class="pageOptions height0 hidden">Page Options:</br>
            <div class="pageOptionItem" onclick="insertPageTitle()" title="insert the Name of the page at the top">insert pagetitle</div>
            <div class="pageOptionItem" onclick="showInsertLink()" title="insert a link to another page">insert link</div>
            <div class="pageOptionItem" onclick="togglePicsClickable(this)" id="pageOptionItemPics" title="Select if pictures shall be viewable on this page">pics clickable</div>
            <div class="pageOptionItem" onclick="showPlugins()" title="show or add plugins">plugins</div>
            <?php
            if($authLevel == '1111'){
                echo('<div class="pageOptionItem" onclick="location.href=\'setup.php?id=settings&lang='.$lang.'\'" onmouseover="$(\'#pageSubOptionsSettings\').removeClass(\'hidden\')"
                 onmouseout="$(\'#pageSubOptionsSettings\').addClass(\'hidden\')" title="change the websites title or choose other languages and so on">setup<div id="pageSubOptionsSettings" class="pageSubOptions hidden">
                 <div class="pageOptionItem"><a href="editor.php?id=impress&lang='.$lang.'">change impress</a></div></div></div>');
            }
            ?>
            <div class="pageOptionItem" onclick="$('.ownUserControlOuter').removeClass('hidden')" title="show own user">own user</div>
            <div class="pageOptionItem" onclick="showPageTourFunc()" title="show a tour to view the most important functions">tour</div>
            <?php
            if($authLevel == '1111'){
                echo('<div class="pageOptionItem"><a href="update/update.php?forceUpdate=true" title="check editor fpr updates">check updates</a></div>');
            }
            ?>
        </div>
        <div class="content" contenteditable="true" id="editable">
            <?php
            if(file_exists("content/$lang/$id.php")){
                include("content/$lang/$id.php");
            }else{
                echo('page not existent jet.</br><span style="color:#555;font-size:12px;">Tipp: press crtl+s to save changes.</span>');
            }
            ?>
        </div>
        <script>
            CKEDITOR.disableAutoInline = true;
            CKEDITOR.inline( 'editable' );
            CKEDITOR.config.language = 'de';
        </script>
        <div class="fileBrowser">
            <div id="status"></div>
            <div class="browserType"><img class="fileHider imgRotate" src="images/fileBrowser.png" height="20" title="hide file Browser" onclick="toggleFileBrowser()" /><span class="fileBrowserInfo">Filebrowser Version 1.0&nbsp;</span>
                <div class="galeryMakerCaller" onclick="showGaleryMaker()">Gallery Editor</div><div class="uploadButton" onclick="showUpload()"><img src="images/upload.png" height="18" /> upload</div>
                <form name="browser">&nbsp;Type: <select name="type" onchange="setBrowserType()"><option>pictures</option><option>others</option></select> folder: <span class="browserPath"></span></form>
            </div><table width="100%"><tr><td>
            <div class="fileUpload hidden">
                <div class="fileUploadContainer">
                    <div class="fileUploadBackground" onclick="toggleUploadBackground()">Background</div>
                    <div class="fileUploadOnBackground hidden">
                        <div class="waitIcon hidden" id="loadingImg2" style="height:80px;width:80px;background:#FFF;position:absolute;top:50px;left:15px;"></div>
                        <div class="fileUploadNotification">files are uploaded in background...</div>
                    </div>
                    <script>
                        var opts = {
                            lines: 12,
                            length: 8,
                            width: 4,
                            radius: 12,
                            corners: 1,
                            rotate: 0,
                            direction: 1,
                            color: '#555',
                            speed: 1.2,
                            trail: 75,
                            shadow: false,
                            hwaccel: false,
                            className: 'spinner',
                            zIndex: 9,
                            top: '40%',
                            left: '50%'
                        };
                        var target = document.getElementById('loadingImg2');
                        var spinner = new Spinner(opts).spin(target);
                    </script>
                    <iframe id="fileUpload1" src="fileUpload/index.php?id=<?php echo($id);?>" width="100%" height="100%"></iframe>
                </div>
            </div>
            <div class="fileBOuter">
                <div class="fileBLeft">
                    <div class="browserSettingsOuter hidden">
                        <img class="msgBoxImg" src="images/close.png" height="20" onclick="$('.browserSettingsOuter').addClass('hidden')" />
                        set view mode:<br>
                        <select id="browserViewModeChooser" onchange="setBrowserMode(this)">
                            <option>show IDs</option>
                            <option>show names</option>
                        </select>
                    </div>
                    <div class="multipleOptionsOuter hidden">
                        <div class="multipleOptionsInner">
                            <img src="images/close.png" class="multipleOptionsImg" height="20" title="close" onclick="hideMultipleOptions()" />
                            <div class="multipleOptions"></div>
                        </div>
                    </div>
                    <div class="leftFolders"></div>
                </div>
                <div class="rightFiles"></div>
            </div>
            </td></tr></table>
        </div>
    </div>
</div>
</body>
</html>
<?php
}else{
    header('Location: logout.php');
}
?>