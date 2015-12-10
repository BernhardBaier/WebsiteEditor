<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 03.12.2015
 * Time: 21:56
 */
//error_reporting(E_ERROR);
include('auth.php');
if($authLevel != '1111'){
    die('authentification failed');
}
function replaceUml($text){
    $olds = ['<und>','<dpp>','ä','ö','ü','Ä','Ö','Ü','ß'];
    $news = ['&',':','&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;'];
    $text = str_replace($olds,$news,$text);
    return $text;
}
$action = $_POST['action'];
$return = 'failed';
if($action == 'getStyles') {
    $path = str_replace('plugins/templates/', '', replaceUml($_POST['path']));
    $path = substr($path, -1) == "/" ? $path : $path . '/';
    $file = fopen($path . 'styleMobile.min.css', 'r');
    $mobile = fread($file, filesize($path . 'styleMobile.min.css'));
    fclose($file);
    $file = fopen($path . 'styleHTML5.min.css', 'r');
    $html5 = fread($file, filesize($path . 'styleHTML5.min.css'));
    fclose($file);
    $headerMobile = substr($mobile, strpos($mobile, '.header{'));
    $headerMobile = substr($headerMobile, strpos($headerMobile, '{') + 1);
    $headerMobile = substr($headerMobile, 0, strpos($headerMobile, '}')) . ';';
    $botMobile = substr($mobile, strpos($mobile, '.footer{'));
    $botMobile = substr($botMobile, strpos($botMobile, '{') + 1);
    $botMobile = substr($botMobile, 0, strpos($botMobile, '}')) . ';';
    $headerHTML5 = substr($html5, strpos($html5, '.header{'));
    $headerHTML5 = substr($headerHTML5, strpos($headerHTML5, '{') + 1);
    $headerHTML5 = substr($headerHTML5, 0, strpos($headerHTML5, '}')) . ';';
    $topItem = substr($html5, strpos($html5, '.topItem{'));
    $topItem = substr($topItem, strpos($topItem, '{') + 1);
    $topItem = substr($topItem, 0, strpos($topItem, '}')) . ';';
    $subItem = substr($html5, strpos($html5, '.subItem{'));
    $subItem = substr($subItem, strpos($subItem, '{') + 1);
    $subItem = substr($subItem, 0, strpos($subItem, '}')) . ';';
    $active = substr($html5, strpos($html5, '.menuItem.active{'));
    $active = substr($active, strpos($active, '{') + 1);
    $active = substr($active, 0, strpos($active, '}')) . ';';
    $botHTML5 = substr($html5, strpos($html5, '.footer{'));
    $botHTML5 = substr($botHTML5, strpos($botHTML5, '{') + 1);
    $botHTML5 = substr($botHTML5, 0, strpos($botHTML5, '}')) . ';';
    $rightHTML5 = substr($html5, strpos($html5, '.rightBar{'));
    $rightHTML5 = substr($rightHTML5, strpos($rightHTML5, '{') + 1);
    $rightHTML5 = substr($rightHTML5, 0, strpos($rightHTML5, '}')) . ';';
    $return = "#html5##header#$headerHTML5#topItem#$topItem#subItem#$subItem#active#$active#rightBar#$rightHTML5#footer#$botHTML5#mobile##header#$headerMobile#footer#$botMobile#";
}else if($action == 'getWrapper'){
    $j=1;
    $return = '<div class="pluginTemplateEditorColorChooserDialogOuter hidden"><div class="pluginTemplateEditorColorChooserDialogInner"><div class="pluginTemplateEditorColorChooserDialogTitle">';
    $return .= '<img src="images/close.png" onclick="$(\'.pluginTemplateEditorColorChooserDialogOuter\').addClass(\'hidden\')" />Red</div><div class="pluginTemplateEditorColorChooserDialogLeft">';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#f44336"></div><div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#e91e63"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#9c27b0"></div><div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#673ab7"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#9e9e9e"></div><div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#607d8b"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#3f51b5"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#2196f3"></div><div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#03a9f4"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#00bcd4"></div><div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#4caf50"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#009688"></div><div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#8bc34a"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#cddc39"></div><div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#ffeb3b"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#ffc107"></div><div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#ff9800"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#ff5722"></div><div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#795548"></div>';
    $return .= '<div onclick="pluginTemplateEditorShowSheet('.$j++.')" style="background:#000"></div></div>';
    $j=1;
    $return .= '<div class="pluginTemplateEditorColorChooserDialogRight"><input type="hidden" id="pluginTemplateEditorColorChooserDialogId" /><input type="hidden" id="pluginTemplateEditorColorChooserDialogNum" />';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffebee">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffcdd2">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ef9a9a">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e57373">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ef5350">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f44336">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e53935">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#d32f2f">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#c62828">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#b71c1c">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fce4ec">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f8bbd0">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f48fb1">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f06292">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ec407a">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e91e63">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#d81b60">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#c2185b">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ad1457">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#880e4f">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f3e5f5">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e1bee7">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ce93d8">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ba68c8">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ab47bc">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#9c27b0">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#8e24aa">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#7b1fa2">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#6a1b9a">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#4a148c">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ede7f6">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#d1c4e9">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#b39ddb">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#9575cd">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#7e57c2">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#673ab7">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#5e35b1">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#512da8">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#4527a0">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#311b92">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fafafa">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f5f5f5">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#eeeeee">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e0e0e0">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#bdbdbd">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#9e9e9e">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#757575">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#616161">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#424242">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#212121">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#eceff1">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#cfd8dc">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#b0bec5">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#90a4ae">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#78909c">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#607d8b">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#546e7a">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#455a64">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#37474f">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#263238">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e8eaf6">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#c5cae9">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#9fa8da">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#7986cb">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#5c6bc0">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#3f51b5">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#3949ab">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#303f9f">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#283593">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#1a237e">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e3f2fd">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#bbdefb">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#90caf9">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#64b5f6">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#42a5f5">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#2196f3">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#1e88e5">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#1976d2">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#1565c0">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#0d47a1">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e1f5fe">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#b3e5fc">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#81d4fa">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#4fc3f7">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#29b6f6">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#03a9f4">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#039be5">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#0288d1">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#0277bd">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#01579b">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e0f7fa">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#b2ebf2">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#80deea">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#4dd0e1">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#26c6da">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#00bcd4">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#00acc1">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#0097a7">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#00838f">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#006064">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e0f2f1">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#c8e6c9">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#a5d6a7">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#81c784">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#66bb6a">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#4caf50">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#43a047">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#388e3c">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#2e7d32">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#1b5e20">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e8f5e9">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#b2dfdb">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#80cbc4">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#4db6ac">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#26a69a">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#009688">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#00897b">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#00796b">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#00695c">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#004d40">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f1f8e9">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#dcedc8">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#c5e1a5">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#aed581">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#9ccc65">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#8bc34a">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#7cb342">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#689f38">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#558b2f">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#33691e">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f9fbe7">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f0f4c3">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e6ee9c">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#dce775">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#d4e157">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#cddc39">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#c0ca33">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#afb42b">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#9e9d24">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#827717">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fffde7">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fff9c4">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fff59d">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fff176">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffee58">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffeb3b">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fdd835">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fbc02d">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f9a825">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f57f17">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fff8e1">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffecb3">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffe082">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffd54f">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffca28">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffc107">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffb300">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffa000">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ff8f00">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ff6f00">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fff3e0">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffe0b2">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffcc80">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffb74d">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffa726">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ff9800">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fb8c00">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f57c00">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ef6c00">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e65100">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#fbe9e7">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffccbc">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ffab91">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ff8a65">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ff7043">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#ff5722">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#f4511e">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#e64a19">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#d84315">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#bf360c">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#efebe9">50</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#d7ccc8">100</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#bcaaa4">200</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#a1887f">300</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#8d6e63">400</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#795548">500</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#6d4c41">600</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#5d4037">700</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#4e342e">800</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#3e2723">900</div></div>';
    $return .= '<div class="pluginTemplateEditorColorChooserColorSheet hidden" id="pluginTemplateEditorColorChooserColorSheet'.$j++.'"><div onclick="pluginTemplateEditorChooseColor(this)" style="background:#FFF">White</div>';
    $return .= '<div onclick="pluginTemplateEditorChooseColor(this)" style="background:#000;color:#fff">Black</div></div>';
    $return .= '</div></div></div>';
    $return .= '<form action="javascript:pluginTemplateEditorUpdateColors()"><div class="pluginTemplateEditorOptionsClass"><div id="pluginTemplateEditorOptionsClassTitle">HTML5</div>';
    $titles = ['','header','topItem','subItem','menu.active','rightBar','footer','header','footer'];
    for($i=1;$i<7;$i++){
        $return .= '<div class="pluginTemplateEditorColorGroup invisible" id="pluginTemplateEditorColorGroup'.$i.'">';
        $return .= '<div class="pluginTemplateEditorColorGroupTitle" onclick="pluginTemplateEditorColorShowGroup('.$i.')">';
        $return .= $titles[$i].'</div><div class="pluginTemplateEditorColorGroupLeft"><div id="pluginTemplateEditorSample'.$i.'" class="pluginTemplateEditorSample" onclick="$(this).toggleClass(\'active\')"></div>';
        $return .= '</div><div class="pluginTemplateEditorColorGroupRight">';
        $return .= '<div class="pluginTemplateEditorColorElement"><div class="pluginTemplateEditorColorElementLeft">Background</div><input type="text" id="pluginTemplateEditorColor'.$i.'" />';
        $return .= '<img class="pluginTemplateEditorColorChooserPreview" src="images/pencil.png" onclick="pluginTemplateEditorColorChooser(0,'.$i.')"></img></div>';
        $return .= '<div class="pluginTemplateEditorColorElement"><div class="pluginTemplateEditorColorElementLeft">Border</div><input type="text" id="pluginTemplateEditorBorder'.$i.'" />';
        $return .= '<img class="pluginTemplateEditorColorChooserPreview" src="images/pencil.png" onclick="pluginTemplateEditorColorChooser(1,'.$i.')"></img></div>';
        $return .= '<div class="pluginTemplateEditorColorElement"><div class="pluginTemplateEditorColorElementLeft">Box-Shadow</div><input type="text" id="pluginTemplateEditorBox'.$i.'" />';
        $return .= '<img class="pluginTemplateEditorColorChooserPreview" src="images/pencil.png" onclick="pluginTemplateEditorColorChooser(2,'.$i.')"></img></div></div></div>';
    }
    $return .= '</div>';
    $return .= '<div class="pluginTemplateEditorOptionsClass"><div id="pluginTemplateEditorOptionsClassTitle">Mobile</div>';
    for($i=7;$i<9;$i++){
        $return .= '<div class="pluginTemplateEditorColorGroup invisible" id="pluginTemplateEditorColorGroup'.$i.'">';
        $return .= '<div class="pluginTemplateEditorColorGroupTitle" onclick="pluginTemplateEditorColorShowGroup('.$i.')">';
        $return .= $titles[$i].'</div><div class="pluginTemplateEditorColorGroupLeft"><div id="pluginTemplateEditorSample'.$i.'" class="pluginTemplateEditorSample" onclick="$(this).toggleClass(\'active\')"></div>';
        $return .= '</div><div class="pluginTemplateEditorColorGroupRight">';
        $return .= '<div class="pluginTemplateEditorColorElement"><div class="pluginTemplateEditorColorElementLeft">Background</div><input type="text" id="pluginTemplateEditorColor'.$i.'" /></div>';
        $return .= '<div class="pluginTemplateEditorColorElement"><div class="pluginTemplateEditorColorElementLeft">Border</div><input type="text" id="pluginTemplateEditorBorder'.$i.'" /></div>';
        $return .= '<div class="pluginTemplateEditorColorElement"><div class="pluginTemplateEditorColorElementLeft">Box-Shadow</div><input type="text" id="pluginTemplateEditorBox'.$i.'" /></div></div></div>';
    }
    $return .= '</div>';
    $return .= '<input type="submit" class="hidden"></form>';
    $return .= '<div class="pluginTemplateEditorOptionsClass"><div id="pluginTemplateEditorOptionsClassTitle" onclick="pluginTemplateEditorSaveColors()">save</div></div>';
}else if($action == 'save'){
    $path = str_replace('plugins/templates/', '', replaceUml($_POST['path']));
    $path = substr($path, -1) == "/" ? $path : $path . '/';
    $styles = [];
    $styles[0] = $_POST['text'];
    $file = fopen($path . 'styleMobile.min.css', 'r');
    $mobile = str_replace('}',';}',fread($file, filesize($path . 'styleMobile.min.css')));
    $mobile = str_replace(';;',';',$mobile);
    fclose($file);
    $file = fopen($path . 'styleHTML5.min.css', 'r');
    $html5 = str_replace('}',';}',fread($file, filesize($path . 'styleHTML5.min.css')));
    $html5 = str_replace(';;',';',$html5);
    fclose($file);
    $titles = ['','#header#','#topItem#','#subItem#','#active#','#rightBar#','#footer#','#header#','#footer#'];
    $style = [];
    $i=1;
    $style[$i] = substr($html5, strpos($html5, '.header{'));
    $style[$i] = substr($style[$i], strpos($style[$i], '{') + 1);
    $style[$i] = str_replace(';;',';',substr($style[$i], 0, strpos($style[$i], '}')));
    $i++;
    $style[$i] = substr($html5, strpos($html5, '.topItem{'));
    $style[$i] = substr($style[$i], strpos($style[$i], '{') + 1);
    $style[$i] = str_replace(';;',';',substr($style[$i], 0, strpos($style[$i], '}')));
    $i++;
    $style[$i] = substr($html5, strpos($html5, '.subItem{'));
    $style[$i] = substr($style[$i], strpos($style[$i], '{') + 1);
    $style[$i] = str_replace(';;',';',substr($style[$i], 0, strpos($style[$i], '}')));
    $i++;
    $style[$i] = substr($html5, strpos($html5, '.menuItem.active{'));
    $style[$i] = substr($style[$i], strpos($style[$i], '{') + 1);
    $style[$i] = str_replace(';;',';',substr($style[$i], 0, strpos($style[$i], '}')));
    $i++;
    $style[$i] = substr($html5, strpos($html5, '.rightBar{'));
    $style[$i] = substr($style[$i], strpos($style[$i], '{') + 1);
    $style[$i] = str_replace(';;',';',substr($style[$i], 0, strpos($style[$i], '}')));
    $i++;
    $style[$i] = substr($html5, strpos($html5, '.footer{'));
    $style[$i] = substr($style[$i], strpos($style[$i], '{') + 1);
    $style[$i] = str_replace(';;',';',substr($style[$i], 0, strpos($style[$i], '}')));
    $i++;
    $style[$i] = substr($mobile, strpos($mobile, '.header{'));
    $style[$i] = substr($style[$i], strpos($style[$i], '{') + 1);
    $style[$i] = str_replace(';;',';',substr($style[$i], 0, strpos($style[$i], '}')));
    $i++;
    $style[$i] = substr($mobile, strpos($mobile, '.footer{'));
    $style[$i] = substr($style[$i], strpos($style[$i], '{') + 1);
    $style[$i] = str_replace(';;',';',substr($style[$i], 0, strpos($style[$i], '}')));
    for($i=1;$i<9;$i++){
        $styles[0] = substr($styles[0],strpos($styles[0],$titles[$i])+strlen($titles[$i]));
        $bc = substr($styles[0],0,strpos($styles[0],';')+1);
        $styles[0] = substr($styles[0],strpos($styles[0],';')+1);
        $bd = substr($styles[0],0,strpos($styles[0],';')+1);
        $styles[0] = substr($styles[0],strpos($styles[0],';')+1);
        $bx = substr($styles[0],0,strpos($styles[0],';')+1);
        if(strpos($style[$i],'background:') > -1) {
            $obc = substr($style[$i], strpos($style[$i], 'background:'));
            $obc = substr($obc, 0, strpos($obc, ';') + 1);
        }else{
            $obc = false;
        }
        if(strpos($style[$i],'border:') > -1){
            $obd = substr($style[$i],strpos($style[$i],'border:'));
            $obd = substr($obd,0,strpos($obd,';')+1);
        }else{
            $obd = false;
        }
        if(strpos($style[$i],'box-shadow:') > -1){
            $obx = substr($style[$i],strpos($style[$i],'box-shadow:'));
            $obx = substr($obx,0,strpos($obx,';')+1);
        }else{
            $obx = false;
        }
        if($obc !== false) {
            $styles[$i] = str_replace($obc,$bc,$style[$i]);
        }else{
            $styles[$i] = $style[$i].$bc;
        }
        if($obd !== false){
            $styles[$i] = str_replace($obd,$bd,$styles[$i]);
        }else{
            $styles[$i] .= $bd;
        }
        if($obx !== false) {
            $styles[$i] = str_replace($obx,$bx,$styles[$i]);
        }else{
            $styles[$i] .= $bx;
        }
        if($i<7){
            $html5 = str_replace($style[$i],$styles[$i],$html5);
        }else{
            $mobile = str_replace($style[$i],$styles[$i],$mobile);
        }
    }
    $file = fopen($path . 'styleMobile.min.css', 'w');
    fwrite($file,str_replace(';}','}',$mobile));
    fclose($file);
    $file = fopen($path . 'styleHTML5.min.css', 'w');
    fwrite($file,str_replace(';}','}',$html5));
    fclose($file);
    $return = '1';
}
echo($return);