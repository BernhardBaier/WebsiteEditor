<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 15.10.2015
 * Time: 13:49
 */
include('auth.php');
if($authLevel != '1111'){
    die('authentification failed');
}
function searchDir($path){
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
                    $addclass = '';
                    $input = substr($input, strpos($input, '#name#') + 6);
                    $input = substr($input, 0, strpos($input, '#'));
                    if ($currentTemplate == $input) {
                        $addclass = ' active';
                    }
                    $return .= '<div class="pluginTemplateEditorTemplate' . $addclass . '" id="pluginTemplateEditorTemplate' . $templateId . '" title="select template" onclick="pluginTemplateEditorSelectTemplate(this,' . $templateId . ')">';
                    $return .= '<div class="pluginTemplateEditorTemplateTitle" id="pluginTemplateEditorTemplateTitle' . $templateId . '">' . $input . '</div><img src="plugins/templates/' . $path . 'pictures/preview.jpg" /></div><div class="hidden" id="pluginTemplateEditorPath';
                    $return .= $templateId++ . '">plugins/templates/' . $path . $file . '</div>';
                }
            } else {
                $return .= searchDir($path . $file);
            }
        }
    }
    closedir($handler);
    return $return;
}
$currentTemplate = $_POST['currentTemplate'];
$path = $_POST['path'];
$templateId = 0;
$return = searchDir($path);
$return .= '<div class="pluginTemplateEditorTemplate" id="pluginTemplateEditorTemplate' . $templateId . '" title="add template" onclick="pluginTemplateEditorSelectTemplate(this,' . $templateId . ')">';
$return .= '<div class="pluginTemplateEditorTemplateTitle">add</div><img src="images/plus.png" /></div><div class="hidden" id="pluginTemplateEditorPath';
$return .= $templateId . '"></div>';
echo($return);