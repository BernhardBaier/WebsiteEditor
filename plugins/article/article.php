<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 19.02.14
 * Time: 16:24
 */
error_reporting(E_ERROR);
include("access.php");
function findInArray($array,$needle){
    for($i=0;$i<sizeof($array);$i++){
        if($array[$i] == $needle){
            return $i;
        }
    }
    return -1;
}
if(substr($authLevel,1,1) == "1"){
    $admin = false;
    if($_GET['admin'] == 'true'){
        if($authLevel == '1111'){
            $admin = true;
        }
    }
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    if(!is_dir('content/')){
        mkdir('content/');
    }
    if($sql){
        $id = 0;
        $lang = $_GET['lang'];
        $langs = ['de'];
        $que = "SELECT * FROM settings WHERE parameter='languages'";
        $erg = mysqli_query($sql,$que);
        while($row = mysqli_fetch_array($erg)){
            $langs = unserialize($row['value']);
        }
        mysqli_free_result($erg);
        if($lang == '' || !findInArray($langs,$lang)){
            $lang = 'de';
        }
        $que = "SELECT * FROM settings WHERE parameter='articleIds'";
        $erg = mysqli_query($sql,$que);
        while($row = mysqli_fetch_array($erg)){
            $articleIds = unserialize($row['value']);
        }
        mysqli_free_result($erg);
        if(!isset($_GET['id'])){

        }else{
            $id = $_GET['id'];
            if(findInArray($articleIds,$id) == -1){
                header('Location: article.php');
                exit;
            }
	        if(!is_dir("content/$id/")){
		        mkdir("content/$id/");
	        }
	        if(!is_dir("content/$id/$lang/")){
		        mkdir("content/$id/$lang/");
	        }
	        if(!is_dir("../../web-images/$id/article/")){
		        mkdir("../../web-images/$id/article/");
	        }
            $maxId = 0;
            if(file_exists("content/$id/$lang/article.php")){
                $file = fopen("content/$id/$lang/article.php",'r');
                $input = fread($file,filesize("content/$id/$lang/article.php"));
                fclose($file);
                while(strpos($input,'pluginArticleOuter') >- 1){
                    $maxId++;
                    $input = substr($input,strpos($input,'pluginArticleOuter')+9);
                }
            }
            $maxId++;
            $adminId = $maxId;
            if(isset($_POST['date'])){
                $date = $_POST['date'];
            }
            if(isset($_POST['time'])){
                $time = $_POST['time'];
            }
            if(isset($_POST['short'])){
                $short = $_POST['short'];
            }
            if(isset($_POST['editor1'])){
                $editor1 = $_POST['editor1'];
            }
            if(isset($_GET['adminId'])){
                $adminId = $_GET['adminId'];
                if($adminId>0 && $adminId<$maxId){
                    $file = fopen("content/$id/$lang/article.php",'r');
                    $input = fread($file,filesize("content/$id/$lang/article.php"));
                    fclose($file);
                    $input = substr($input,strpos($input,'#pluginArticleContent'.$adminId));
                    $input = substr($input,strpos($input,'pluginArticleDate'));
                    $input = substr($input,strpos($input,'>')+1);
                    $date = substr($input,0,strpos($input,'</div>'));
                    $date = str_replace(' ','',$date);
                    $input = substr($input,strpos($input,'pluginArticleShort'));
                    $input = substr($input,strpos($input,'>')+1);
                    $short = substr($input,0,strpos($input,'</div>'));

                    if(strpos($input,'#pluginArticleContent'.($adminId-1)) > -1){
                        $input = substr($input,0,strpos($input,'#pluginArticleContent'.($adminId-1)));
                        $input = substr($input,0,strrpos($input,'<'));
                        $input = substr($input,0,strrpos($input,'<'));
                        $editor1 = substr($input,strpos($input,'pluginArticleContentInner'));
                        $editor1 = substr($editor1,strpos($editor1,'>')+1);
                        $editor1 = substr($editor1,0,strrpos($editor1,'</div>'));
                        $editor1 = substr($editor1,0,strrpos($editor1,'</div>'));
                    }else{
                        $editor1 = substr($input,strpos($input,'pluginArticleContentInner'));
                        $editor1 = substr($editor1,strpos($editor1,'>')+1);
                        $editor1 = substr($editor1,0,strrpos($editor1,'</div>'));
                        $editor1 = substr($editor1,0,strrpos($editor1,'</div>'));
                    }
                    $editor1 = str_replace('<div class="picsClickAble">','',$editor1);
                    $editor1 = str_replace('src="web-','src="../../web-',$editor1);
                    $editor1 = str_replace("src='web-","src='../../web-",$editor1);
                }else{
                    $adminId = $maxId;
                }
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Article</title>
        <link rel="SHORTCUT ICON" href="../../images/editorLogo.png" />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="stylePluginArticle.css" />
        <link rel="stylesheet" href="../../commonStyle.css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="../../ckeditor/ckeditor.js"></script>
        <script>
            var lang = '<?php echo($lang);?>';
            var path = 'web-images/<?php echo($id);?>/article/thumbs/';
            var admin = '<?php if($authLevel == '1111'){echo('&admin=true');}?>';
            function replaceUml(text){
                var umlaute = [['�','�','�','�','�','�','�','&',':'],['<und>auml;','<und>ouml;','<und>uuml;','<und>Auml;','<und>Ouml;','<und>Uuml;','<und>szlig;','<und>','<dpp>']];
                for(var i=0;i<umlaute[0].length;i++){
                    while(text.search(umlaute[0][i]) > -1){
                        text=text.replace(umlaute[0][i],umlaute[1][i]);
                    }
                }
                return text;
            }
            function init(){
                $.ajax({
                    type: 'POST',
                    url: '../../getFiles.php',
                    data: 'text='+path+'&gal=1',
                    success: function(data) {
                        if(data!="1"){
                            data = data.substr(6);
                            var pics = '';
                            var pos = data.search(';');
                            while(pos > 1){
                                var pfad = path + data.substr(0,pos);
                                pics += '<div class="prevImgBox" onclick="$(this).children(\'div\').toggleClass(\'hidden\')"><img src="../../' + pfad + '" height="100"/><div class="prevImgOpts hidden">';
                                pics += '<div class="prevImgOptsItem" onclick="showInsertPic(\'../../' + pfad + '\')">insert</div><div class="prevImgOptsItem" onclick="showDeletePic(\'' + pfad + '\')">delete</div></div></div>';
                                data = data.substr(pos+1);
                                pos = data.search(';')
                            }
                            pics = pics==""?'none':pics;
                            $('.filePreview').html(pics);
                        }else{
                            alert(data);
                        }
                    }
                });
            }
            function showUpload(){
                $('.fileUpload').toggleClass('hidden');
                init();
            }
            function showInsertPic(path){
                var content = CKEDITOR.instances['editor1'].getData();
                if(content.search('</div>')>-1){
                    content = content.substr(0,content.lastIndexOf('</div>'))+'<img src="'+path+'" width="200" /></div>';
                }else{
                    content +='<img src="'+path+'" width="200" />';
                }
                CKEDITOR.instances['editor1'].setData(content);
            }
            function showDeletePic(name){
                document.delete.name.value = name;
                $('.deletePicTitle').html('delete file '+name+'?');
                $('.deletePic').removeClass('hidden');
                $('.overlay').removeClass('hidden');
            }
            function deletePicNow(){
                $.ajax({
                    type: 'POST',
                    url: '../../functions.php',
                    data: 'text=delete:'+replaceUml(document.delete.name.value.substr(document.delete.name.value.lastIndexOf('/')+1))+':'+path.replace('thumbs/',''),
                    success: function(data) {
                        if(data.search('#reload#') != -1) {
                            init();
                        }else{
                            alert(data);
                        }
                        hideMessages();
                    }
                });
            }
            function hideMessages(){
                $('.msgBox').addClass('hidden');
                $('.overlay').addClass('hidden');
            }
            function chooseArticle(){
                $.ajax({
                    type: 'POST',
                    url: 'functions.php',
                    data: 'function=getArticles&callback=chooseArticleNow($pid)&lang='+lang,
                    success: function(data) {
                        data = data=='none'?'This Plugin is not ready for use jet! Your admin has to activate it first!':data;
                        var count = data.match(/<\/li>/g);
                        if(count.length == 1){
                            data = data.substr(data.search('chooseArticleNow')+17);
                            data = data.substr(0,data.search("'")-1);
                            chooseArticleNow(data);
                            data = 'redirecting you now...';
                        }
                        $('.articlePageChooser2').html(data);
                    }
                });
            }
            function chooseArticleNow(id){
                location.href = 'article.php?id='+id+'&lang='+lang+admin;
            }
	        function publishArticle(){
		        $.ajax({
			        type: 'POST',
			        url: '../../functions.php',
			        data: 'function=publishText&id=<?php echo($id);?>&lang='+lang,
			        success: function(data) {
						if(data != '#published#'){
							alert(data);
						}else{
							location.href = "article.php?publish=true&id=<?php echo($id);?>&lang="+lang;
						}
			        }
		        });
	        }
            function loadOld(th){
                if(th.selectedIndex > 0 ){
                    location.href = 'article.php?id=<?php echo($id);?>&lang='+lang+admin+"&adminId="+th.selectedIndex;
                }
            }
        </script>
    </head>
    <?php
    if(isset($_GET['success'])){
        if($_GET['success'] == 'true'){
            if(isset($_GET['id'])){
                $id = $_GET['id'];
            }
            if(isset($_GET['maxId'])){
                $maxId = $_GET['maxId'];
            }
            echo('<div class="success">Article has been created<br/>it will only be visible after <div class="publish" onclick="publishArticle()">publish</div><br/><a href="preview.php?id='.$id.'&maxId='.$maxId.'&lang='.$lang.'">preview</a> | <a href="logout.php">logout</a></div>');
        }else{
            echo('<div class="success not">article could not be added<br/>(user abort)</br><a href="article.php">repeat</a> | <a href="../../logout.php">logout</a></div>');
        }
    }else if(isset($_GET['publish'])){
	    if($_GET['publish'] == 'true'){
		    echo('<div class="success published">Article has been published.<br/><a href="article.php">repeat</a> | <a href="../../logout.php">logout</a></div>');
	    }
    }else{
        if($id>0){
        ?>
        <body onload="init()">
        <div class="overlay hidden" title="close" onclick="hideMessages()"></div>
        <div class="deletePic msgBox hidden">
            <div class="msgBoxImg"><img onclick="hideMessages()" height="20" title="close" src="../../images/close.png"/></div>
            <div class="deletePicTitle"></div>
            <form name="delete" action="javascript:deletePicNow()">
                <input type="submit" value=" delete " /><input type="button" value=" cancel " onclick="hideMessages()" /><input type="hidden" name="name" />
            </form>
        </div>
        <div class="container">
            <div class="nav">
                <a href="article.php" style="float:left">choose id</a>
                <a href="logout.php" title="Logout" style="float:right;">Logout</a>
                Article editor version 1.1
            </div>
            <div class="content">
                <div class="timing">
                    <div class="fileUpload hidden">
                        <iframe src="../../fileUpload/index.php?id=<?php echo($id);?>&path=article" width="100%" height="100%"></iframe>
                    </div>
                    <form action="articleFunctions.php" method="post">
                        Add article: <input name="date" type="date" placeholder="Datum" value="<?php if($date){echo($date);}else{echo(date('d.m.Y'));}?>" required />
                        Article Id <?php echo($adminId); if($admin){echo(' <lable>edit old one<select onchange="loadOld(this)"><option>..</option>');for($i=1;$i<$maxId;$i++){echo("<option>$i</option>");}echo('</select></lable>');}?></br>
                        Short text: <textarea name="short" required placeholder="short text"><?php echo($short);?></textarea> <div class="uploadButton" onclick="showUpload()"><img src="../../images/upload.png" height="18"/>Upload</div>
                        <textarea name="editor1" id="editor1"><div class="picsClickAble"><?php if(isset($editor1)){echo($editor1);}else{echo('enter article here');}?></div></textarea>
                        <script>
                            CKEDITOR.replace( 'editor1' );
                            CKEDITOR.config.language = 'de';
                        </script>
                        <input type="hidden" name="function" value="createEvent" /><input type="hidden" name="id" value="<?php echo($id);?>" /><input type="hidden" name="maxId" value="<?php echo($maxId);?>" />
                        <input type="hidden" name="adminId" value="<?php echo($adminId);?>" /><input type="hidden" name="lang" value="<?php echo($lang);?>" /><input type="submit" value="Save" />
                    </form>
                    Available pictures: (add per Drag & Drop)
                    <div class="filePreview">none</div>
                </div>
            </div>
        </div>
        </body>
    <?php
        }else{
        ?>
            <body onload="chooseArticle()">
            <div class="container">
                <div class="nav">
                    <a href="../../logout.php" title="Logout" style="float:right;">Logout</a>
                    Article editor version 1.0
                </div>
                <div class="content">
                    <div class="timing">
                        Choose page on which you want to add an article:
                        <div class="articlePageChooser2"></div>
                    </div>
                </div>
            </div>
            </body>
        <?php
        }
    }
    ?>
    </html>
<?php
}