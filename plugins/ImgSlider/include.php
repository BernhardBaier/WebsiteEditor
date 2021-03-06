<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 14.09.2015
 * Time: 19:03
 */
$que2 = "SELECT * FROM plugins WHERE name='ImgSlider';";
$erg2 = mysqli_query($sql,$que2);
while($row = mysqli_fetch_array($erg2)){
    $location = $row['location'];
    $location = substr($location,0,strlen($location)-1);
    $name = $row['name'];
    $plugId = $row['id'];
}
if(!is_dir($location.'/settings')){
    mkdir($location.'/settings');
}
if(!is_dir($location.'/sliders')){
    mkdir($location.'/sliders');
}
echo("<img src='$location/images/logo.png' title='$name' class='pluginNavImg' onclick='initPlugin_$plugId(this);$(this).addClass(\"active\")'/>");
mysqli_free_result($erg2);
if(!file_exists("$location/script.js") || true) {
    $output = "var maxImgCount;
function initPlugin_$plugId(th){
    if(th != 0){
        resetAllPlugins();
        th.src = th.src.substring(0,th.src.lastIndexOf('/'))+'/active.png';
    }
    $.ajax({
        type: 'POST',
        url: 'getFiles.php',
        data: 'text='+browserPath+'&gal=1',
        success: function(data) {
            var files = '';
            var akFile;
            var file = data.substr(6);
            var imgCount = 0;
            while(file.search(';') > -1){
                akFile = file.substr(0,file.search(';'));
                if(in_array(akFile,imgTypes)){
                    files += '<div class=\"galMakerImg\" id=\"sliderImg'+ imgCount +'\"><div class=\"sliderImgOptions hidden\" id=\"sliderImgOption'+imgCount+'\" onclick=\"addSliderTitle('+imgCount+')\">add a title</div>';
                    files += '<img id=\"sliderPic'+ imgCount +'\" onclick=\"toggleSliderPicSel('+ imgCount +')\" height=\"100\" src=\"' + browserPath + akFile + '\" /><div class=\"imgSliderTexts\" id=\"imgSliderTextBackup' + imgCount++ + '\"></div></div>';
                }
                file = file.substr(file.search(';')+1);
            }
            maxImgCount = imgCount;
            files = files == ''?'empty dir. Upload files in main panel.':files;
            var text = '<div class=\"sliderContainer\"><div class=\"sliderLeftPics\"><div class=\"sliderTitleAdder hidden\"><div class=\"sliderTitleAdderTitle\"></div><form name=\"sliderOptions\" action=\"javascript:addSliderTitleNow()\"><input type=\"hidden\" name=\"id\">';
            text += '<input type=\"text\" autofocus name=\"optionText\"><input type=\"submit\" value=\"OK\"></form></div><div class=\"sliderOverlay hidden\" onclick=\"hideImgSliderTitle()\"></div><div class=\"sliderContainerTitle\">Select the pictures for the slider:</div>'+files;
            text += '<div class=\"updateSlider\" title=\"generate slider preview\" onclick=\"updateSlider()\"><img src=\"$location/images/updateSlider.png\" /></div></div><div class=\"sliderRightSlider\">';
            text += '<div class=\"sliderRightSliderTop\"><div>Preview:</div><div class=\"imgSliderOuter\" onmouseover=\"imgSliderHover(true)\" onmouseout=\"imgSliderHover(false)\"></div></div><div class=\"sliderRightSliderBottom\"><div>Settings:</div>';
            text += '<form name=\"imgSliderSettings\" action=\"javascript:imgSliderGenerateSettings()\"><label>Height <input type=\"text\" name=\"height\" /></label><br>';
            text += '<label>Margin <input type=\"text\" name=\"margin\" /></label><br><label>Speed <input type=\"text\" name=\"speed\" /></label><br><label>Timeout <input type=\"text\" name=\"timeout\" /></label><br>';
            text += '<input type=\"submit\" value=\"update\" name=\"imgSliderSettingsSet\" /></form><div class=\"addPluginImgSliderToPage hidden\" onclick=\"addPluginImgSliderToPage()\">add to page</div></div></div></div>'
            $('.pluginInner').html(text);
            getExistingSliders();
        }
    });
}
function getExistingSliders(){
    $.ajax({
        type: 'POST',
        url: '$location/restoreImgSliders.php',
        data: 'path=sliders/imgSlider'+pageId+'_'+lang+'.php',
        success: function(data) {
            if(data != '1'){
                $('.sliderRightSliderTop').html('<div>Preview:</div>'+data);
                window.setTimeout('resetExistingSliders()',10);
            }
        }
    });
}
function resetExistingSliders(){
    var id = 0;
    var count = 0;
    try{
        for(id=0;id<100;id++){
            if(document.getElementById('sliderPic'+id).src == document.getElementById('sliderImage'+count).src){
                document.getElementById('sliderImg'+id).className = 'galMakerImg selected';
                document.getElementById('sliderImgOption'+id).className = 'sliderImgOptions';
                $('#imgSliderTextBackup'+id).html($('#imgSliderText'+count).html());
                count++;
            }
        }
    }catch (ex){}
    updateSlider();
}
function addSliderTitle(id){
    document.sliderOptions.id.value = id;
    document.sliderOptions.optionText.value = $('#imgSliderTextBackup'+id).html();
    $('.sliderTitleAdderTitle').html('add a title to picture '+(id+1));
    $('.sliderTitleAdder').removeClass('hidden');
    $('.sliderOverlay').removeClass('hidden');
}
function addSliderTitleNow(){
    $('#imgSliderTextBackup'+document.sliderOptions.id.value).html(document.sliderOptions.optionText.value);
    hideImgSliderTitle();
}
function hideImgSliderTitle(){
    $('.sliderOverlay').addClass('hidden');
    $('.sliderTitleAdder').addClass('hidden');
}
function toggleSliderPicSel(id){
    if(document.getElementById('sliderImg'+id).className != 'galMakerImg'){
        document.getElementById('sliderImg'+id).className = 'galMakerImg';
        document.getElementById('sliderImgOption'+id).className = 'sliderImgOptions hidden';
    }else{
        document.getElementById('sliderImg'+id).className = 'galMakerImg selected';
        document.getElementById('sliderImgOption'+id).className = 'sliderImgOptions';
    }
    updateSlider();
}
function updateSlider(){
	var sliderHTML = '';
	var count;
	var sliderNav = '';
	var sliderTexts = '';
	var id = 0;
    for(count = 0;count<maxImgCount;count++){
        if(document.getElementById('sliderImg'+count).className.search('selected') > -1){
            var src = document.getElementById('sliderPic'+count).src;
            src = src.substr(src.lastIndexOf('web-images/'));
            sliderNav += '<div id=\"imgSliderNavPoint' + id + '\" class=\"imgSliderNavPoint\" onclick=\"imgSliderShowStep(' + id + ')\"></div>';
            sliderHTML += '<img class=\"sliderImage\" id=\"sliderImage' + id + '\" src=\"'+src+'\" />';
            sliderTexts += '<div class=\"imgSliderTexts\" id=\"imgSliderText' + id++ + '\"></div>';
        }
    }
    if(sliderHTML == ''){
        sliderHTML = '<div class=\"imgSliderNavText\">select some pictures</div><img class=\"sliderImage\" id=\"sliderImage0\" src=\"$location/images/default.png\" />';
    }else{
        sliderHTML = '<div class=\"imgSliderNavText\"><div class=\"imgSliderNavTextInner\"><div class=\"imgSliderNavTextT1 small\"></div><div class=\"imgSliderNavTextT2 small\"></div><div class=\"imgSliderTextText\"></div></div></div>' + sliderHTML;
        sliderHTML = '<div class=\"imgSliderNav\" align=\"center\"onmouseover=\"imgSliderHoverNav(true)\" onmouseout=\"imgSliderHoverNav(false)\">' + sliderNav + '</div>' + sliderTexts + sliderHTML;
    }
    $('.imgSliderOuter').html(sliderHTML);
    $('.addPluginImgSliderToPage').removeClass('hidden');
    id = 0;
    for(count=0;count<maxImgCount;count++){
        if(document.getElementById('sliderImg'+count).className.search('selected') > -1){
            $('#imgSliderText'+id++).html($('#imgSliderTextBackup'+count).html())
        }
    }
    window.setTimeout('initImgSlider()',250);
    if($('.imgSliderOuter').css('height') < 25){
        window.setTimeout('document.imgSliderSettings.imgSliderSettingsSet.click()',500);
    }
}
function addPluginImgSliderToPage(){
    $('.active').removeClass('active');
    var sliderHTML = '<div class=\"imgSliderOuter\" onmouseover=\"imgSliderHover(true)\" onmouseout=\"imgSliderHover(false)\">'+$('.imgSliderOuter').html()+'</div>';
    sliderHTML = replaceUml( sliderHTML + '<script src=\"$location/settings/imgSliderSettings'+pageId+'.js\"></script>');
    var html = getCurrentHTML();
    if(html.search('{#insertPluginImgSlider') > -1){

    }else{
        insertHTMLatTheEnd('{#insertPluginImgSlider'+pageId+'_'+lang+'#}');
    }
    $.ajax({
        type: 'POST',
        url: 'plugins/ImgSlider/imgSliderCreate.php',
        data: 'html='+sliderHTML+'&path=sliders/imgSlider'+pageId+'_'+lang+'.php',
        success: function(data) {
            $.ajax({
                type: 'POST',
                url: 'functionsPlugins.php',
                data: 'path=$location/sliders/imgSlider'+pageId+'_'+lang+'.php&html={#insertPluginImgSlider'+pageId+'_'+lang+'#}&action=addHTML',
                success: function(data) {
                    if(data != '1'){
                        alert(data);
                    }
                }
            });
        }
    });
    saveText('content',false);
    initImgSlider();
}";
$file = fopen("$location/script.js",'w');
fwrite($file,$output);
fclose($file);
}
echo("
<script src='$location/script.js'></script>
<script src='$location/skriptAdmin.min.js'></script>
<script src='$location/skriptPluginImgSliderSettings.js'></script>
<link rel='stylesheet' href='$location/stylePluginImgSlider.min.css' />
<link rel='stylesheet' href='$location/style.min.css' />
");