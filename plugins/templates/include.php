<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 14.09.2015
 * Time: 19:03
 */
if($authLevel == '1111') {
    $que2 = "SELECT * FROM plugins WHERE name='TemplateEditor';";
    $erg2 = mysqli_query($sql, $que2);
    $currentTemplate = 0;
    while ($row = mysqli_fetch_array($erg2)) {
        $location = $row['location'];
        $location = substr($location, 0, strlen($location) - 1);
        $name = $row['name'];
        $plugId = $row['id'];
        $currentTemplate = $row['extra'];
    }
    $templateId = 0;


    echo("<img src='$location/images/logo.png' title='$name' class='pluginNavImg' onclick='initPlugin_$plugId(this);$(this).addClass(\"active\")'/>");
    mysqli_free_result($erg2);
    if (!file_exists("$location/script.js") || true) {
        $output = "var maxTemplateId = 0;
function initPlugin_$plugId(th){
    if(th != 0){
        resetAllPlugins();
        th.src = th.src.substring(0,th.src.lastIndexOf('/'))+'/active.png';
    }
    $.ajax({
        type: 'POST',
        url: 'plugins/templates/getTemplates.php',
        data: 'path=templates&currentTemplate=$currentTemplate',
        success: function(data) {
            var text = '<div class=\"pluginTemplateEditorContainer\">available templates:<div class=\"pluginTemplateEditorTop\">'+data+'</div><div class=\"pluginTemplateEditorBottom\">Preview:<div class=\"pluginTemplateEditorChooser hidden\">';
            text += '<img src=\"\" id=\"pluginTemplateEditorPic\" /><div class=\"pluginTemplateEditorAdd\" title=\"this operation cannot be undone\" onclick=\"chooseTemplate()\">Choose this template</div></div></div></div>';
            $('.pluginInner').html(text);
            var i;
            try{
                for(i=0;i<100;i++){
                    if(document.getElementById(\"pluginTemplateEditorPath\"+i).className == \"pluginTemplateEditorTemplate\"){}
                }
            }catch(ex){
                maxTemplateId = i;
            }
        }
    });
}
function selectTemplate(th,id){
    $('.pluginTemplateEditorTemplate').removeClass('active');
    th.className = 'pluginTemplateEditorTemplate active';
    var path = $('#pluginTemplateEditorPath'+id).html();
    path = path.substr(0,path.lastIndexOf('/'))+'/pictures/preview.jpg';
    document.getElementById('pluginTemplateEditorPic').src = path;
    $('.pluginTemplateEditorChooser').removeClass('hidden');
}
function chooseTemplate(){
    var templateId = null;
    for(var i=0;i<=maxTemplateId;i++){
        try{
            if(document.getElementById('pluginTemplateEditorTemplate'+i).className == 'pluginTemplateEditorTemplate active'){
                templateId = i;
            }
        }catch(ex){}
    }
    if(templateId == null){
        alert('error');
    }else{
        $.ajax({
            type: 'POST',
            url: 'plugins/templates/choose.php',
            data: 'path='+$('#pluginTemplateEditorPath'+templateId).html(),
            success: function(data) {
                if(data == ''){
                    window.setTimeout(\"updateAllPlugins('plugins/settings')\",10);
                    window.setTimeout(\"reloadLocation('showPlugins')\",500);
                }else{
                    alert(data);
                }
            }
        });
    }
}";
        $file = fopen("$location/script.js", 'w');
        fwrite($file, $output);
        fclose($file);
    }
    echo("
<script src='$location/script.js'></script>
<script src='$location/skriptPluginTemplate.js'></script>
<link rel='stylesheet' href='$location/stylePluginTemplate.css' />
<link rel='stylesheet' href='$location/style.css' />
");
}