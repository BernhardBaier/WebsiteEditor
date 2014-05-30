<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 12.01.14
 * Time: 11:08
 */
error_reporting(E_ERROR);
include('access.php');
function jsMenu($parent=0){
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
    $pageTitle = 'no title';
    $editorVersion = '4.0';
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    if($sql){
        $que = "SELECT * FROM settings WHERE parameter='pageTitle'";
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
        $que = "SELECT * FROM settings WHERE parameter='languageSupport'";
        $erg = mysqli_query($sql,$que);
        $langSupport = false;
        while($row = mysqli_fetch_array($erg)){
            $langSupport = $row['value'];
        }
        mysqli_free_result($erg);
        $langSupport = $langSupport=='multi'?true:false;
        $languages = [];
        $longLanguages = [];
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
    }
    $id = $_GET['id'];
    if($id == ""){
        $id = 1;
    }
    $lang = $_GET['lang'];
    if($lang == "" || !in_array($lang,$languages)){
        $lang = 'de';
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
    $action = $_GET['action'];
    $einsatz = $_GET['einsatz'];
    $einsatz=$einsatz>0?$einsatz:0;
    ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title><?php echo($pageTitle);?> Admin</title>
    <link rel="SHORTCUT ICON" href="images/editorLogo.png"/>
    <link rel="stylesheet" href="styleAdmin.css"/>
    <link rel="stylesheet" href="commonStyle.css"/>
    <link rel="stylesheet" href="styleFileBrowser.css"/>
    <link rel="stylesheet" type="text/css" href="datepicker/jquery.datetimepicker.css" >

    <!-- todo: remove this in final!--><script src="jquery-1.9.1.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

    <script src="spin.min.js"></script>

    <script src="ckeditor/ckeditor.js"></script>
    <script>
        var startHTML = '';
        var pageId = <?php echo($id);?>;
        var lang = '<?php echo($lang);?>';
        var einsatz = <?php echo($einsatz);?>;
        var showPlugIn = false;
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
                echo('$(\'.tourBox\').width(150).height(28).css(\'left\',(leftWidth+windowWidth)*0.5-85).css(\'top\',0);
            $(\'.tourText\').css(\'top\',40).css(\'left\',(leftWidth+windowWidth)*0.5-155);
            $(\'.tourText\').html(\'The actual page name<br/><div class="tourBut tourLeft" onclick="pageTourStep(\'+(step-1)+\')">go back</div><div class="tourBut" onclick="pageTourStep(\'+(step+1)+\')">proceed</div></div>\');
            ');
            }
            ?>
        }
    </script>

    <script src="datepicker/jquery.datetimepicker.js"></script>

    <script src="scriptFileBrowser.js"></script>
    <script src="scriptAdmin.js"></script>
    <script>
        <?php
        if(strpos($_SESSION['extra'],'tour') < 0 || !$_SESSION['extra']){
            echo('initPageTour();');
            $_SESSION['extra'] .= 'tour';
        }
        ?>
    </script>
    <script src="scriptPlugin.js"></script>
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
<div class="notificationBox msgBox opac0 hidden"></div>

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
    insert picture as:</br>
    <form name="insert" action="javascript:insertPicNow()">
        <select name="type" onchange="changeInsertType()"><option>single pic</option><option>titled pic</option><option>subtitled pic</option></select><input type="button" onclick="showGaleryMaker()" value=" gallery " /></br>
        <select name="align" onchange="changeInsertType()"><option>none</option><option>left</option><option>right</option><option>center</option></select><span style="font-size:13px;"><input type="checkbox" name="border" onchange="changeInsertType()"/>with border</span>
        <div class="htmlToInsert"></div>
        <input type="submit" value=" ok " /><input type="hidden" name="path"/>
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

<div class="galeryMakerOuter">
    <div class="galeryMakerTitle"><img src="images/close.png" style="position:absolute;left:5px;top:5px;cursor:pointer;" title="close" onclick="hideMessages()" height="25" />Gallery Editor
    <img src="images/galOptions.png" class="galeryMakerImg imgRotate" height="30" onclick="$('.galeryMakerOptions').toggleClass('height0');$(this).toggleClass('imgRotated')" />
        <div class="galeryMakerOptions height0"><div onclick="galeryMakerSelectAll(1)">Select all</div><div onclick="galeryMakerSelectAll(0)">Deselect all</div></div></div>
    <div class="galeryMakerInner"></div>
    <div class="galeryMakerButton" onclick="generateGallery()">generate gallery with selected pictures</div>
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
        <input type="button" onclick="hideMessages()" value="cancel" /><input type="submit" value=" OK " onclick="publishPageNow()" />
    </div>
</div>
<?php
if(substr($authLevel,2,1) == '1'){
    echo('
<div class="userControlOuter out">
    <div class="userControlTitle">User control<img src="images/close.png" onclick="showUserControl()" height="25" style="float:right;" /></div>
    <div class="userControlInner"></div>
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
            ?>
            <img src="plugins/logo.png" class="pluginNavImg" onclick="addPlugin(this);$(this).addClass('active')" title="add plugin"/>
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
<?php
$einsatzOption = ' hidden';
if(file_exists("content/$id.php")){
    $file = fopen("content/$id.php",'r');
    $input = fread($file,filesize("content/$id.php"));
    fclose($file);
    if(strpos($input,'einsatzCount')>-1){
        $einsatzOption = '';
    }
}
?>
<div class="einsatzFoundOuter <?php echo($einsatzOption);?>">
    <div class="einsatzFoundInner">
        <img src="images/einsatzOut.png" height="20" class="einsatzFoundImg" onclick="$('.einsatzFoundOuter').toggleClass('out');$(this).toggleClass('out')" />
        <div class="einsatzFoundTitle">Eins&auml;tze gefunden</div>
        <div class="einsatzFoundContainer">zum bearbeiten anklicken.
            <?php
            if($einsatzOption != ' hidden'){
                $maxId = 0;
                while(strpos($input,'einsatzCount')>-1){
                    $ct = substr($input,strpos($input,'einsatzCount')+14);
                    $ct = substr($ct,0,strpos($ct,'<'));
                    echo("<div class='einsatzFoundItem'><a href='admin.php?id=$id&einsatz=$ct'>Einsatz $ct</a></div>");
                    $input = substr($input,strpos($input,'einsatzCount')+9);
                }
                if($einsatz>0){
                    echo("<div class='einsatzFoundItem'><a href='admin.php?id=$id'>zur&uuml;ck zur einsatz&uuml;bersicht</a></div>");
                }
            }
            ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="leftBar">
        <div class="leftBarTitle">Website menu</div>
        <div class="menu" onclick="reprintMenu()">&nbsp;&nbsp;&nbsp;&nbsp;reprint menu</div>
    </div>
    <img class="leftBarNav imgRotate" src="images/leftBar.png" title="hide menu" height="20" onclick="toggleLeftBar()" />
    <div class="pageContainer">
        <div class="pageMenu"><div class="pageMenuInner"><span class="pageMenuTitle" title="the name of the actual page"></span>
            <div class="pageMenuInfo">Website editor Version <?php echo($editorVersion);?>&nbsp;&nbsp;</br>&nbsp;Copyright &copy; 2014 Bernhard Baier&nbsp;&nbsp;</div>&nbsp;
                <div class="pageMenuItem rightItem" onclick="$('.logout').removeClass('opac0').removeClass('hidden')" title="close session and log off">Logout</div>
                <div class="pageMenuItem rightItem" onclick="showPublish()" title="publish this page">Publish</div>
                <div class="pageMenuItem"><a onclick="saveText('content')" title="save content (ctrl + s)">Save</a></div>
                <div class="pageMenuItem" onclick="showPageOptions(<?php echo($id);?>,this)" title="show the pages options"><a>Options</a></div>
                <div class="pageMenuItem">
                    <a href="index.php?id=<?php echo("$id&lang=$lang"); if($einsatz>0){echo('&einsatz='.$einsatz);}?>&preview=true" target="_blank" title="preview this page in a new tab">Preview</a>
                </div>
                <?php
                if(substr($authLevel,2,1) == '1'){
                    echo('<div class="pageMenuItem" onclick="showUserControl()" title="show registered users and set their rights">Users</div>');
                }
                if($langSupport){
                    echo("<div class='pageMenuItem' onclick=\"$('.langChooser').toggleClass('hidden')\" title='actual language'>Lang: $lang</div>");
                }
                ?>
            </div>Editing mode
        </div>
        <div class="langChooser hidden">
            <?php
            for($i=0;$i<sizeof($languages);$i++){
                echo("<a href='admin.php?id=$id&lang=".$languages[$i]."'>".$longLanguages[$i]."</a><br>");
            }
            ?>
        </div>
        <div class="pageOptions height0 hidden">Page Options:</br>
            <div class="pageOptionItem" onclick="insertPageTitle()" title="insert the Name of the page at the top">insert PageTitle</div>
            <div class="pageOptionItem" onclick="showInsertLink()" title="insert a link to another page">insert Link</div>
            <div class="pageOptionItem" onclick="togglePicsClickable(this)" id="pageOptionItemPics" title="Select if pictures shall be viewable on this page">pics clickable</div>
            <div class="pageOptionItem" onclick="showPlugins()" title="show or add plugins">plugins</div>
            <div class="pageOptionItem" onclick="location.href='setup.php?admin=true'" title="change the websites title or choose other languages and so on">setup</div>
            <div class="pageOptionItem" onclick="$('.ownUserControlOuter').removeClass('hidden')" title="show own user">user</div>
            <div class="pageOptionItem" onclick="showPageTourFunc()" title="show a tour to view the most important functions">tour</div>
            <div class="pageOptionItem"><a href="update/update.php?forceUpdate=true" title="check editor fpr updates">check updates</a></div>
        </div>
        <div class="content" contenteditable="true" id="editable">
            <?php
            if($einsatz > 0){
                if(file_exists("content/einsatz/$lang/$id/$einsatz.php")){
                    include("content/einsatz/$lang/$id/$einsatz.php");
                }
            }else{
                if(file_exists("content/$lang/$id.php")){
                    include("content/$lang/$id.php");
                }else{
                    echo('page not existent jet.</br><span style="color:#555;font-size:12px;">Tipp: press crtl+s to save changes.</span>');
                }
            }
            ?>
        </div>
        <script>
            CKEDITOR.disableAutoInline = true;
            CKEDITOR.inline( 'editable' );
        </script>
        <div class="fileBrowser">
            <div id="status"></div>
            <div class="browserType"><img class="fileHider imgRotate" src="images/fileBrowser.png" height="20" title="hide file Browser" onclick="toggleFileBrowser()" /><span class="fileBrowserInfo">Filebrowser Version 1.0&nbsp;</span>
                <div class="galeryMakerCaller" onclick="showGaleryMaker()">Gallery Editor</div><div class="uploadButton" onclick="showUpload()"><img src="images/upload.png" height="18" /> upload</div>
                <form name="browser">&nbsp;Type: <select name="type" onchange="setBrowserType()"><option>pictures</option><option>others</option></select> folder: <span class="browserPath"></span></form>
            </div><table width="100%"><tr><td>
            <div class="fileUpload hidden">
                <iframe id="fileUpload1" src="fileUpload/index.php?id=<?php echo($id);?>" width="100%" height="100%"></iframe>
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
}elseif(substr($authLevel,1,1) == "1"){
    header('Location: einsatz.php');
}elseif(substr($authLevel,3,1) == "1"){
    header('Location: plugins/Seifenkistenrennen/skrAdmin.php');
}else{
    header('Location: logout.php');
}
?>