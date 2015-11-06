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
            var text = '<div class=\"pluginTemplateEditorContainer\">available templates:<div class=\"pluginTemplateEditorTop\">'+data+'</div><div class=\"pluginTemplateEditorBottom\"><div class=\"pluginTemplateEditorLeft\">';
            text += 'Preview:<div class=\"pluginTemplateEditorChooser hidden\"><img src=\"\" id=\"pluginTemplateEditorPic\" />';
            text += '<div class=\"pluginTemplateEditorAdd\" title=\"this operation cannot be undone\" onclick=\"plugin".$name."ChooseTemplate()\">Choose this template</div></div></div>';
            text += '<div class=\"pluginTemplateEditorRight\"><div class=\"pluginTemplateEditorOptions hidden\"><div class=\"pluginTemplateEditorTitle\">options:</div><';
            text += 'img src=\"$location/images/close.png\" title=\"show options\" onclick=\"plugin".$name."ShowOptions()\" /><div class=\"pluginTemplateEditorOptionsChooser\"><div onclick=\"plugin".$name."EditSource()\">edit source</div>';
            text += '<div onclick=\"plugin".$name."ChoosePrepared()\">choose prepared</div></div><div class=\"pluginTemplateEditorPrepared hidden\"></div>';
            text += '<div class=\"pluginTemplateEditorFrameWrapper hidden\"><iframe id=\"templateEditorFrame\" src=\"editor.php\"></iframe></div></div></div></div></div>';
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
function plugin".$name."SelectTemplate(th,id){
    $('.pluginTemplateEditorTemplate').removeClass('active');
    th.className = 'pluginTemplateEditorTemplate active';
    var path = $('#pluginTemplateEditorPath'+id).html();
    path = path.substr(0,path.lastIndexOf('/'))+'/pictures/preview.jpg';
    document.getElementById('pluginTemplateEditorPic').src = path;
    $('.pluginTemplateEditorChooser').removeClass('hidden');
    $('.pluginTemplateEditorOptions').removeClass('hidden');
    plugin".$name."UpdateSource();
}
var plugin".$name."Path = '';
function plugin".$name."UpdateSource(){
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
            url: 'plugins/templates/getOptions.php',
            data: 'path='+$('#pluginTemplateEditorPath'+templateId).html(),
            success: function(data) {
                if(data != null){
                    document.getElementById('templateEditorFrame').src= 'editor.php?lang=$lang&id=$location/' + data + '&forcePath=true';
                    plugin".$name."Path = '$location/' + data;
                }
            }
        });
    }
}
function plugin".$name."ChooseTemplate(){
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
                    window.setTimeout(\"reloadLocation('showPlugins&pluginId=$plugId')\",500);
                }else{
                    alert(data);
                }
            }
        });
    }
}
var plugin".$name."Title = 'Title';
var plugin".$name."Items = [];
function plugin".$name."EditSource(){
    $('.pluginTemplateEditorFrameWrapper').removeClass('hidden');
    $('.pluginTemplateEditorPrepared').addClass('hidden');
    $('.pluginTemplateEditorOptionsChooser').addClass('hidden');
    plugin".$name."UpdateSource();
}
function plugin".$name."ChoosePrepared(){
    $('.pluginTemplateEditorFrameWrapper').addClass('hidden');
    $('.pluginTemplateEditorOptionsChooser').addClass('hidden');
    $('.pluginTemplateEditorPrepared').removeClass('hidden');
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
            url: 'plugins/templates/getOptions.php',
            data: 'path='+$('#pluginTemplateEditorPath'+templateId).html()+'&echoContent=true',
            success: function(data) {
                if(data != null){
                    if(data.search('rightBarItem') > -1){
                        plugin".$name."Items = [];
                        var ktxt;
                        if(data.search('rightBarTitle') > -1){
                            ktxt = data.substr(data.search('rightBarTitle'));
                            ktxt = ktxt.substr(ktxt.search('>') + 1);
                            plugin".$name."Title = ktxt.substr(0,ktxt.search('</div>'));
                        }
                        ktxt = data;
                        while(ktxt.search('rightBarItem') > -1){
                            ktxt = ktxt.substr(ktxt.search('rightBarItem'));
                            ktxt = ktxt.substr(ktxt.search('>') + 1);
                            plugin".$name."Items.push(ktxt.substr(0,ktxt.search('</div>')));
                            ktxt = ktxt.substr(ktxt.search('</div>') + 6);
                        }
                    }else{
                        alert('this file does not contain any prepared content. The fie will be overwritten!');
                    }
                    plugin".$name."ShowPrepared();
                }
            }
        });
    }
}
function plugin".$name."ShowPrepared(){
    var text = '<div style=\"position:relative;\"><div class=\"pluginTemplateEditorTextfield hidden\"><form name=\"pluginTemplateEditorTextForm\" action=\"javascript:plugin".$name."ChangeText()\">'
    text += '<input type=\"text\" name=\"pluginTemplateEditorTextfield\" /><input type=\"hidden\" name=\"pluginTemplateEditorTextId\" /><input type=\"submit\" value=\"change\" /></form></div>';
    text += '<div class=\"rightBarTitle\" onclick=\"plugin".$name."ChangeTitle()\">' + plugin".$name."Title + '</div>';
    var i;
    for(i = 0;i < plugin".$name."Items.length;i++){
        text += '<div id=\"rightBarItem' + i + '\" class=\"rightBarItem\" onclick=\"plugin".$name."ChangeItem(' + i + ')\">' + plugin".$name."Items[i] + '</div>';
    }
    text += '<div id=\"rightBarItem' + ++i + '\" class=\"rightBarItem\" onclick=\"plugin".$name."ChangeItem(' + i + ')\">Add item</div>';
    text += '<div class=\"pluginTemplateEditorBut1\" onclick=\"plugin".$name."SearchPages()\" title=\"check if there are pages existent that are named like tis item\">Look for corresponding pages</div>';
    text += '<div class=\"pluginTemplateEditorBut2\" onclick=\"plugin".$name."SetOptions()\">Change page options</div></div>';
    $('.pluginTemplateEditorPrepared').html(text);
}
function plugin".$name."ShowOptions(){
    $('.pluginTemplateEditorFrameWrapper').addClass('hidden');
    $('.pluginTemplateEditorOptionsChooser').removeClass('hidden');
    $('.pluginTemplateEditorPrepared').addClass('hidden');
}
function plugin".$name."ChangeTitle(){
    document.pluginTemplateEditorTextForm.pluginTemplateEditorTextfield.value = $('.rightBarTitle').html();
    document.pluginTemplateEditorTextForm.pluginTemplateEditorTextId.value = 'rightBarTitle';
    $('.pluginTemplateEditorTextfield').removeClass('hidden');
}
function plugin".$name."ChangeItem(id){
    document.pluginTemplateEditorTextForm.pluginTemplateEditorTextfield.value = $('#rightBarItem' + id).html();
    document.pluginTemplateEditorTextForm.pluginTemplateEditorTextId.value = 'rightBarItem' + id;
    $('.pluginTemplateEditorTextfield').removeClass('hidden');
}
function plugin".$name."ChangeText(){
    var text = document.pluginTemplateEditorTextForm.pluginTemplateEditorTextfield.value;
    var elem = document.pluginTemplateEditorTextForm.pluginTemplateEditorTextId.value;
    $('.'+elem).html(text);
    if(elem == 'rightBarTitle'){
        plugin".$name."Title = text;
    }else{
        elem = elem.replace('rightBarItem','');
        if(elem >= plugin".$name."Items.length){
            plugin".$name."Items.push(text);
        }else{
            plugin".$name."Items[elem] = text;
        }
    }
    plugin".$name."ShowPrepared();
}
function plugin".$name."SearchPages(){
    $.ajax({
        type: 'POST',
        url: 'plugins/templates/changeOptions.php',
        data: 'name='+replaceUml(plugin".$name."Title)+'&lang='+lang,
        success: function(data) {
            if(data != '0'){
                $('.rightBarTitle').html('<a href=\"index.php?id='+data+'&lang=<?php echo(\$lang);?>\">'+plugin".$name."Title+'</a>');
                plugin".$name."Title = '<a href=\"index.php?id='+data+'&lang=<?php echo(\$lang);?>\">'+plugin".$name."Title+'</a>';
            }
            for(i = 0;i < plugin".$name."Items.length;i++){
                plugin".$name."SearchPageByName(plugin".$name."Items[i],i);
            }
        }
    });
    showNotification('links added',1500);
}
function plugin".$name."SearchPageByName(name,id){
    $.ajax({
        type: 'POST',
        url: 'plugins/templates/changeOptions.php',
        data: 'name='+replaceUml(name)+'&lang='+lang,
        success: function(data) {
            if(data != '0'){
                $('#rightBarItem'+id).html('<a href=\"index.php?id='+data+'&lang=<?php echo(\$lang);?>\">'+name+'</a>');
                plugin".$name."Items[id] = '<a href=\"index.php?id='+data+'&lang=<?php echo(\$lang);?>\">'+name+'</a>';
            }
        }
    });
}
function plugin".$name."SetOptions(){
    var path = plugin".$name."Path;
    if(path == ''){
        alert('error');
    }else{
        var text = '<div class=\"rightBarTitle\">' + plugin".$name."Title + '</div>';
        var i;
        for(i = 0;i < plugin".$name."Items.length;i++){
            text += '<div class=\"rightBarItem\">' + plugin".$name."Items[i] + '</div>';
        }
        $.ajax({
            type: 'POST',
            url: 'plugins/templates/changeOptions.php',
            data: 'path='+path+'&saveText=true&text='+replaceUml(text),
            success: function(data) {
                if(data != '0'){
                    alert(data);
                }else{
                    showNotification('options saved',1500);
                }
            }
        });
    }
}";
        $file = fopen("$location/script.js",'w');
        fwrite($file, $output);
        fclose($file);
    }
    echo("
<script src='$location/script.js'></script>
<link rel='stylesheet' href='$location/stylePluginTemplate.css' />
<link rel='stylesheet' href='$location/style.min.css' />
");
}