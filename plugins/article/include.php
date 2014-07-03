<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 29.03.14
 * Time: 11:54
 */
if(substr($authLevel,0,1) == "1"){
$que2 = "SELECT * FROM plugins WHERE name='article';";
$erg2 = mysqli_query($sql,$que2);
$location = "";
while($row = mysqli_fetch_array($erg2)){
    $location = $row['location'];
    $name = $row['name'];
    $plugId = $row['id'];
}
echo("<img src='".$location."images/logo.png' title='$name' class='pluginNavImg' onclick='initPlugin_$plugId(this);$(this).addClass(\"active\")'/>");
mysqli_free_result($erg2);
$location = substr($location,0,strlen($location)-1);
if(!file_exists("$location/script.js")){
    $output="function initPlugin_$plugId(th){
    if(th != 0){
        resetAllPlugins();
        th.src = th.src.substring(0,th.src.lastIndexOf('/'))+'/active.png';
    }
    setPluginArticleHTML();
}
function setPluginArticleHTML(){
    $.ajax({
        type: 'POST',
        url: '$location/functions.php',
        data: 'lang='+lang+'&function=getArticles',
        success: function(data) {
            $('.pluginInner').html('<div class=\"articlePageChooser hidden\"></div><div class=\"articleTitle\">Article admin</div>Pages with articles:<br>'+data+'<br><div class=\"pluginArticleButton\" onclick=\"showAddArticle()\">Add a page to article list</div><div class=\"pluginArticleButton\" onclick=\"showAddArticleToPage()\">Add article to page</div><hr/><a target=\"_blank\" href=\"plugins/article/article.php?lang='+lang+'\">open editor now</a>');
        }
    });
}
function showAddArticle(){
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=clickAbleMenu:setNewArticle(\$pid):'+lang,
        success: function(data) {
            if(data != '1'){
                $(\".articlePageChooser\").html(\"<img src='images/close.png' style='position:absolute;right:-15px;top:-15px;cursor:pointer' title='hide' height='22' onclick=\\\"$('.articlePageChooser').addClass('hidden')\\\" />\"+data).removeClass(\"hidden\");
            }
        }
    });
}
function setNewArticle(id){
    $.ajax({
        type: 'POST',
        url: '$location/functions.php',
        data: 'lang='+lang+'&function=setNewArticle&id='+id,
        success: function(data) {
            if(data != '1'){
                alert(data);
            }else{
                setPluginArticleHTML();
            }
        }
    });
}
function removeArticle(id){
    $.ajax({
        type: 'POST',
        url: '$location/functions.php',
        data: 'lang='+lang+'&function=removeArticle&id='+id,
        success: function(data) {
            if(data != '1'){
                alert(data);
            }else{
                setPluginArticleHTML();
            }
        }
    });
}

function showAddArticleToPage(){
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=clickAbleMenu:addArticleToPage(\$pid):'+lang,
        success: function(data) {
            if(data != '1'){
                $(\".articlePageChooser\").html(\"<img src='images/close.png' style='position:absolute;right:-15px;top:-15px;cursor:pointer' title='hide' height='22' onclick=\\\"$('.articlePageChooser').addClass('hidden')\\\" /><li class='clickAbleMenuItem'><img src='images/listicon.png' height='15'><span onclick='addArticleToPage(\"+pageId+\")'> active page</span></li>\"+data).removeClass(\"hidden\");
            }
        }
    });
}
function addArticleToPage(id){
	var textToInsert = '{#insertPluginArticle'+id+'/'+lang+'#}';
	if(id == pageId){
		var editorContent = getCurrentHTML();
		if(editorContent.search(textToInsert) == -1){
			insertHTMLatCursor(textToInsert);
			saveText('content/'+lang+'/'+id+'.php');
		}
	}else{
		$.ajax({
        type: 'POST',
        url: '$location/functions.php',
        data: 'lang='+lang+'&function=insertHTMLOnPage&id='+id+'&html='+textToInsert,
        success: function(data) {
            if(data != '1'){
                alert(data);
                return;
            }
        }
    });
	}
	showNotification('the Plugin has been added to the page.<br>you now have to publish it again to see the changes.',2500);
}
";
    $file = fopen("$location/script.js",'w');
    fwrite($file,$output);
    fclose($file);
}
echo("
<script src='$location/script.js'></script>
<link rel='stylesheet' href='$location/stylePluginArticle.css' />
");
}