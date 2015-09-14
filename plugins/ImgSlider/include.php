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
echo("<img src='$location/images/logo.png' title='$name' class='pluginNavImg' onclick='initPlugin_$plugId(this);$(this).addClass(\"active\")'/>");
mysqli_free_result($erg2);
if(!file_exists("$location/script.js") || true) {
    $output = "
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
            var images = $('.imgSlider').find('img').map(function(){
                return this;
            }).get();
            var sliderPics = [];
            for(var i=0;i<images.length;i++){
                var source = images[i].src.replace(location.toString(),'');
                source = source.substr(source.lastIndexOf('web-images/'));
                sliderPics.push(source);
            }
            images = null;
            while(file.search(';') > -1){
                akFile = file.substr(0,file.search(';'));
                var actClass = findInArray(sliderPics,browserPath + akFile) > -1 ?' selected':'';
                if(in_array(akFile,imgTypes)){
                    files += '<div class=\"galMakerImg' + actClass + '\" id=\"sliderImg'+ imgCount +'\"><img id=\"sliderPic'+ imgCount +'\" onclick=\"toggleSliderPicSel('+ imgCount++ +')\" height=\"100\" src=\"' + browserPath + akFile + '\" /></div>';
                }
                file = file.substr(file.search(';')+1);
            }
            files = files == ''?'empty dir':files;
            $('.pluginInner').html('Select the pictures for the slider:<div class=\"sliderContainer\"><div class=\"sliderLeftPics\">'+files+'<div class=\"updateSlider\"><a href=\"javascript:updateSlider()\">update slider</a></div></div><div class=\"sliderRightSlider\"><div class=\"imgSliderOuter\"></div></div></div>');
        }
    });
}
function toggleSliderPicSel(id){
    if(document.getElementById('sliderImg'+id).className != 'galMakerImg'){
        document.getElementById('sliderImg'+id).className = 'galMakerImg';
    }else{
        document.getElementById('sliderImg'+id).className = 'galMakerImg selected';
    }
}
function updateSlider(){
    var id=0;
	var sliderHTML = '<div class=\"imgSliderNav\"></div>';
	var count = 0;
    try{
        while(true){
            if(document.getElementById('sliderImg'+id).className.search('selected') > -1){
                var src = document.getElementById('sliderPic'+id).src;
                src = src.substr(src.lastIndexOf('web-images/'));
                sliderHTML += '<img class=\"sliderImage\" id=\"sliderImage' + count++ + '\" src=\"'+src+'\" />';
            }
            id++;
        }
    }catch (ex){}
    $('.imgSliderOuter').html(sliderHTML);
    initImgSlider();
}";
$file = fopen("$location/script.js",'w');
fwrite($file,$output);
fclose($file);
}
echo("
<script src='$location/script.js'></script>
<script src='$location/imgSlider.js'></script>
<link rel='stylesheet' href='$location/stylePluginImgSlider.css' />
<link rel='stylesheet' href='$location/style.css' />
");