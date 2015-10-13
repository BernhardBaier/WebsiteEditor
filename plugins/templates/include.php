<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 14.09.2015
 * Time: 19:03
 */
$que2 = "SELECT * FROM plugins WHERE name='TemplateEditor';";
$erg2 = mysqli_query($sql,$que2);
$currentTemplate = 0;
while($row = mysqli_fetch_array($erg2)){
    $location = $row['location'];
    $location = substr($location,0,strlen($location)-1);
    $name = $row['name'];
    $plugId = $row['id'];
    $currentTemplate = $row['extra'];
}
$templateId = 0;
function searchDir($path){
    global $currentTemplate,$templateId;
    $path = substr($path,-1)=='/'?$path:$path.'/';
    $handler = opendir($path);
    $return = '';
    while($file = readdir($handler)){
        if($file != '.' && $file != '..'){
            if(!is_dir($path.$file)){
                if(substr($file,-4) == 'tmpl'){
                    $datei = fopen($path.$file,'r');
                    $input = fread($datei,filesize($path.$file));
                    fclose($datei);
                    $addclass = '';
                    $input = substr($input,strpos($input,'#name#')+6);
                    $input = substr($input,0,strpos($input,'#'));
                    if($currentTemplate == $input){
                        $addclass = ' active';
                    }
                    $return .= '<div class="pluginTemplateEditorTemplate'.$addclass.'" id="pluginTemplateEditorTemplate'.$templateId.'" title="select template" onclick="selectTemplate(this,'.$templateId.')">';
                    $return .= '<div class="pluginTemplateEditorTemplateTitle">'.$input.'</div><img src="'.$path.'pictures/preview.jpg" /></div><div class="hidden" id="pluginTemplateEditorPath'.$templateId++.'">'.$path.$file.'</div>';
                }
            }else{
                $return .= searchDir($path.$file);
            }
        }
    }
    closedir($handler);
    return $return;
}
echo("<img src='$location/images/logo.png' title='$name' class='pluginNavImg' onclick='initPlugin_$plugId(this);$(this).addClass(\"active\")'/>");
mysqli_free_result($erg2);
if(!file_exists("$location/script.js") || true) {
    $templates = searchDir('plugins/templates/templates');
    $output = "var maxTemplateId = ".($templateId-1).";
function initPlugin_$plugId(th){
    if(th != 0){
        resetAllPlugins();
        th.src = th.src.substring(0,th.src.lastIndexOf('/'))+'/active.png';
    }
    var text = '<div class=\"pluginTemplateEditorContainer\">available templates:<div class=\"pluginTemplateEditorTop\">$templates</div><div class=\"pluginTemplateEditorBottom\">Preview:<div class=\"pluginTemplateEditorChooser hidden\">';
    text += '<img src=\"\" id=\"pluginTemplateEditorPic\" /><div class=\"pluginTemplateEditorAdd\" title=\"this operation cannot be undone\" onclick=\"chooseTemplate('+maxTemplateId+')\">Choose this template</div></div></div></div>';
    $('.pluginInner').html(text);
}";
$file = fopen("$location/script.js",'w');
fwrite($file,$output);
fclose($file);
}
echo("
<script src='$location/script.js'></script>
<script src='$location/skriptPluginTemplate.js'></script>
<link rel='stylesheet' href='$location/stylePluginTemplate.css' />
<link rel='stylesheet' href='$location/style.css' />
");