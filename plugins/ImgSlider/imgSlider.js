/**
 * Created by Bernhard on 14.09.2015.
 */
var currentImgSliderId = 0;
var maxImgSliderId = 0;
var sliderTimer;
function initImgSlider(){
    try{
        window.clearInterval(sliderTimer);
    }catch (ex){}
    var id = 0;
    try{
        while(true){
            if(document.getElementById('sliderImage'+id).className == "sliderImage"){}
            id++;
        }
    }catch (ex){}
    maxImgSliderId = id;
    $('#sliderImage0').addClass('active');
    sliderTimer = window.setInterval('imgSliderShowNext()',2000);
}
function imgSliderShowNext(){
    $('.sliderImage').removeClass('active');
    $('#sliderImage'+currentImgSliderId++).addClass('active');
    if(currentImgSliderId>=maxImgSliderId){
        currentImgSliderId = 0;
    }
}