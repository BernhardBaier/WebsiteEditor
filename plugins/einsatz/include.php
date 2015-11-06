<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 29.03.14
 * Time: 11:54
 */
if(substr($authLevel,0,1) == "1"){
$que2 = "SELECT * FROM plugins WHERE name='einsatz';";
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
    setPluginEinsatzHTML();
}
function setPluginEinsatzHTML(){
    $.ajax({
        type: 'POST',
        url: '$location/functions.php',
        data: 'lang='+lang+'&function=getEinsatzs',
        success: function(data) {
            $('.pluginInner').html('<div class=\"einsatzPageChooser hidden\"></div><div class=\"einsatzTitle\">Einsatz admin</div>Seiten mit Eins&auml;tzen:<br>'+data+'<br><div class=\"pluginEinsatzButton\" onclick=\"showAddEinsatz()\">Add a page to list</div><div class=\"pluginEinsatzButton\" onclick=\"showAddEinsatzToPage()\">Add this to page</div><hr/><a target=\"_blank\" href=\"plugins/einsatz/einsatz.php?lang='+lang+'\">open editor now</a>');
        }
    });
}
function showAddEinsatz(){
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=clickAbleMenu:setNewEinsatz(\$pid):'+lang,
        success: function(data) {
            if(data != '1'){
                $(\".einsatzPageChooser\").html(\"<img src='images/close.png' style='position:absolute;right:-15px;top:-15px;cursor:pointer' title='hide' height='22' onclick=\\\"$('.einsatzPageChooser').addClass('hidden')\\\" />\"+data).removeClass(\"hidden\");
            }
        }
    });
}
function setNewEinsatz(id){
    $.ajax({
        type: 'POST',
        url: '$location/functions.php',
        data: 'lang='+lang+'&function=setNewEinsatz&id='+id,
        success: function(data) {
            if(data != '1'){
                alert(data);
            }else{
                setPluginEinsatzHTML();
            }
        }
    });
}
function removeEinsatz(id){
    $.ajax({
        type: 'POST',
        url: '$location/functions.php',
        data: 'lang='+lang+'&function=removeEinsatz&id='+id,
        success: function(data) {
            if(data != '1'){
                alert(data);
            }else{
                setPluginEinsatzHTML();
            }
        }
    });
}

function showAddEinsatzToPage(){
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=clickAbleMenu:addEinsatzToPage(\$pid):'+lang,
        success: function(data) {
            if(data != '1'){
                $(\".einsatzPageChooser\").html(\"<img src='images/close.png' style='position:absolute;right:-15px;top:-15px;cursor:pointer' title='hide' height='22' onclick=\\\"$('.einsatzPageChooser').addClass('hidden')\\\" /><li class='clickAbleMenuItem'><img src='images/listicon.png' height='15'><span onclick='addEinsatzToPage(\"+pageId+\")'> active page</span></li>\"+data).removeClass(\"hidden\");
            }
        }
    });
}
function addEinsatzToPage(id){
	var textToInsert = '{#insertPluginEinsatz'+id+'/'+lang+'#}';
	if(id == pageId){
		var editorContent = getCurrentHTML();
		if(editorContent.search(textToInsert) == -1){
			insertHTMLatCursor(textToInsert);
			saveText('content/'+lang+'/'+id+'.php',false);
		}
	}else{
		$.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'lang='+lang+'&function=insertHTMLatEndOfPage:'+id+':'+textToInsert,
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
<link rel='stylesheet' href='$location/stylePluginEinsatz.min.css' />
");
}