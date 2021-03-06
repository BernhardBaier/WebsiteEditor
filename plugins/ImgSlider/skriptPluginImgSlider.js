/**
 * Created by Bernhard on 14.09.2015.
 */
var currentImgSliderId = 0;
var maxImgSliderId = 0;
var imgSliderTimer, imgSliderResetTimer,imgSliderShowStepActive;
var imgSliderSpeed = 500, imgSliderTimeout = 2000;
var imgSliderIsHovered = false;
function imgSliderHover(h){
    imgSliderIsHovered = h;
    if(h){
        $('.imgSliderNavText').addClass('hover');
        if($('.imgSliderTextText').html() != ""){
            $('.imgSliderNavTextT1').removeClass('small');
            $('.imgSliderNavTextT2').removeClass('small');
        }
    }else{
        $('.imgSliderNavText').removeClass('hover');
        $('.imgSliderNavTextT1').addClass('small');
        $('.imgSliderNavTextT2').addClass('small');
    }
}
function initImgSlider(){
    try{
        window.clearInterval(imgSliderTimer);
    }catch (ex){}
    var id = 0;
    try{
        for(id=0;id<100;id++){
            if(document.getElementById('sliderImage'+id).className == "sliderImage"){}
        }
    }catch (ex){}
    maxImgSliderId = id;
    id--;
    $('#imgSliderNavPoint0').addClass('active');
    var img = $('#sliderImage0');
    img.addClass('active');
    $('.imgSliderTextText').html($('#imgSliderText0').html());
    $('#sliderImage'+id).addClass('leftOut');
    imgSliderSettings();
    currentImgSliderId = 1;
    imgSliderTimer = window.setInterval('imgSliderShowNext()',imgSliderTimeout);
    imgSliderResetTimer = window.setTimeout('imgSliderResetLeftOut()',imgSliderSpeed);
}
function imgSliderShowStep(step){
    try{
        window.clearInterval(imgSliderTimer);
    }catch (ex){}
    try{
        window.clearTimeout(imgSliderShowStepActive);
    }catch (ex){}
    if(imgSliderResetTimer != null){
        imgSliderShowStepActive = window.setTimeout('imgSliderShowStep('+step+')',100);
    }else {
        imgSliderShowStepActive = null;
        currentImgSliderId = step;
        imgSliderShowNext();
        imgSliderTimer = window.setInterval('imgSliderShowNext()', imgSliderTimeout);
    }
}
function imgSliderShowNext(){
    if(maxImgSliderId > 1) {
        var trans = 'left ' + imgSliderSpeed + 'ms ease-in-out';
        $('.sliderImage').css({transition: trans, '-webkit-transition': trans})
        $('.leftOut').removeClass('leftOut');
        $('.active').addClass('leftOut');
        $('.sliderImage').removeClass('active');
        $('#sliderImage' + currentImgSliderId).addClass('active');
        $('.imgSliderNavPoint').removeClass('active');
        $('.imgSliderTextText').html('').addClass('width0');
        imgSliderHover(false);
        window.setTimeout('imgSliderSetTexts('+currentImgSliderId+')',500);
        $('#imgSliderNavPoint' + currentImgSliderId++).addClass('active');
        if (currentImgSliderId >= maxImgSliderId) {
            currentImgSliderId = 0;
        }
        imgSliderResetTimer = window.setTimeout('imgSliderResetLeftOut()', imgSliderSpeed);
    }
}
function imgSliderSetTexts(id){
    $('.imgSliderTextText').html($('#imgSliderText'+id).html()).removeClass('width0');;
    if(imgSliderIsHovered){
        imgSliderHover(true);
    }
}
function imgSliderResetLeftOut(){
    $('.leftOut').css({transition:'left 1ms'}).css({'-webkit-transition':'left 1ms'});
    $('.leftOut').removeClass('leftOut');
    imgSliderResetTimer = null;
}
function imgSliderHoverNav(over){
    if(over){
        $('.imgSliderNavPoint').css({width: '12px', height: '12px'});
    }else{
        $('.imgSliderNavPoint').css({width: '8px', height: '8px'});
    }
}