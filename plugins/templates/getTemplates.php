<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 15.10.2015
 * Time: 13:49
 */
error_reporting(E_ERROR);
include('auth.php');
if($authLevel != '1111'){
    die('authentification failed');
}
function searchDir($path,$option=false,$name=""){
    global $currentTemplate,$templateId;
    $path = substr($path, -1) == '/' ? $path : $path . '/';
    $handler = opendir($path);
    $return = '';
    while ($file = readdir($handler)) {
        if ($file != '.' && $file != '..') {
            if (!is_dir($path . $file)) {
                if (substr($file, -4) == 'tmpl') {
                    $datei = fopen($path . $file, 'r');
                    $input = fread($datei, filesize($path . $file));
                    fclose($datei);
                    $origin = $input;
                    $addclass = '';
                    $input = substr($input, strpos($input, '#name#') + 6);
                    $input = substr($input, 0, strpos($input, '#'));
                    if ($currentTemplate == $input) {
                        $addclass = ' active';
                    }
                    if($option == false) {
                        $return .= '<div class="pluginTemplateEditorTemplate' . $addclass . '" id="pluginTemplateEditorTemplate' . $templateId . '" title="select template" onclick="pluginTemplateEditorSelectTemplate(this,' . $templateId . ')">';
                        $return .= '<div class="pluginTemplateEditorTemplateTitle" id="pluginTemplateEditorTemplateTitle' . $templateId . '">' . $input . '</div><img src="plugins/templates/' . $path . 'pictures/preview.jpg" /></div><div class="hidden" id="pluginTemplateEditorPath';
                        $return .= $templateId++ . '">plugins/templates/' . $path . $file . '</div>';
                    }else{
                        if($input == $name){
                            $origin = substr($origin,strpos($origin,'#editAble#')+10);
                            $return = "";
                            while(strpos($origin,'#option#') > -1){
                                $origin = substr($origin,strpos($origin,'#option#')+8);
                                $return .= substr($origin,0,strpos($origin,'#')+1);
                            }
                            break;
                        }
                    }
                }
            } else {
                $return .= searchDir($path . $file,$option,$name);
            }
        }
    }
    closedir($handler);
    return $return;
}
if($_POST['action'] == 'getOptions'){
    $currentTemplate = null;
    $path = $_POST['path'];
    $templateId = 0;
    $return = "#" . searchDir($path,true,$_POST['templateName']);
    echo($return);
}else {
    $currentTemplate = $_POST['currentTemplate'];
    $path = $_POST['path'];
    $templateId = 0;
    $return = searchDir($path);
    $return .= '<div class="pluginTemplateEditorTemplate" id="pluginTemplateEditorTemplate' . $templateId . '" title="add template" onclick="pluginTemplateEditorSelectTemplate(this,' . $templateId . ')">';
    $return .= '<div class="pluginTemplateEditorTemplateTitle">add</div><img src="images/plus.png" /></div><div class="hidden" id="pluginTemplateEditorPath';
    $return .= $templateId . '"></div>';
    echo($return);
}