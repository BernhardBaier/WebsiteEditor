/**
 * Created by Bernhard on 20.01.14.
 */
function setCookie(c_name,value,exdays){
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}
function getCookie(c_name){
    var c_value = document.cookie;
    var c_start = c_value.indexOf(" " + c_name + "=");
    if (c_start == -1){
        c_start = c_value.indexOf(c_name + "=");
    }
    if (c_start == -1){
        c_value = "NULL";
    }
    else{
        c_start = c_value.indexOf("=", c_start) + 1;
        var c_end = c_value.indexOf(";", c_start);
        if (c_end == -1){
            c_end = c_value.length;
        }
        c_value = unescape(c_value.substring(c_start,c_end));
    }
    return c_value;
}
function init(){
    if(correctRightBar){
        if($('.rightBar').height() < $('.content').height()){
            $('.rightBar').height($('.content').height());
        }
    }
    initSpecialSlider();
    loadCalendarSide(3);
    initCalendarPage();
    initGallerySlider();
    window.setTimeout('postInit()',250);
}
function postInit(){
    window.setTimeout('initPicViewer()',25);
    try{
        initImgSlider();
    }catch(ex){}
}
function showYear(year){
    var href = document.getElementById('calendarHref').innerHTML;
    var date = new Date();
    var now = date.getFullYear();
    if(year == 0){
        year = now;
    }
    $('.calendarLoading').removeClass('hidden');
    $.ajax({
        type: 'POST',
        url: 'plugins/calendar/calendar.php',
        data: 'year='+year+'&function=pageEvents&href='+href+'&id=1&lang='+lang,
        success: function(data) {
            $('.calendarContent').html(data);
            if(year == now){
                for(var i = 1;i <= date.getMonth();i++){
                    document.getElementsByClassName('imgRotate')[i-1].className = 'imgRotate';
                    document.getElementById('calendarGroup'+i).className = 'calendarGroup invisible';
                }
            }
            $('.calendarLoading').addClass('hidden');
            if(correctRightBar){
                if($('.rightBar').height() < $('.content').height()){
                    $('.rightBar').height($('.content').height());
                }
            }
        }
    });
    $('.calendarYearChooserTitle').html("<div class='arrow-left' onclick='showYear("+(year-1)+")'></div>"+year+"<div class='arrow-right' onclick='showYear("+(year+1)+")'></div>");
}
function initCalendarPage(){
    try{
        if($('.calendar').html() != ''){
            var opts = {
                lines: 12,
                length: 8,
                width: 4,
                radius: 12,
                corners: 1,
                rotate: 0,
                direction: 1,
                color: '#000',
                speed: 1.2,
                trail: 75,
                shadow: false,
                hwaccel: false,
                className: 'spinner',
                zIndex: 9,
                top: '50px',
                left: '50%'
            };
            var target = document.getElementById('loadingImgCal');
            var spinner = new Spinner(opts).spin(target);
            showYear(0);
        }
    }catch (ex){}
}
function loadCalendarSide(count){
    $.ajax({
        type: 'POST',
        url: 'plugins/calendar/calendar.php',
        data: 'maxCount='+count+'&function=side',
        success: function(data) {
            $('.calendarSide').html(data);
        }
    });
}
function searchNow(){
    var que = document.search.searchInput.value;
    if(que.replace(' ','') == ""){
        return;
    }
    $('.searchResultsInner').html("<div>Suchergebnisse:<br/></div><div id='loadingImg1' style='height:40px;width:40px;background:#FFF;'></div><br/>");
    $('.searchOuter').removeClass('hidden');
    var opts = {
        lines: 12,
        length: 8,
        width: 4,
        radius: 12,
        corners: 1,
        rotate: 0,
        direction: 1,
        color: '#555',
        speed: 1.2,
        trail: 75,
        shadow: false,
        hwaccel: false,
        className: 'spinner',
        zIndex: 9,
        top: '65%',
        left: '50%'
    };
    var target = document.getElementById('loadingImg1');
    var spinner = new Spinner(opts).spin(target);
    if(que != ''){
        $.ajax({
            type: 'POST',
            url: 'search.php',
            data: 'lang='+lang+'&que='+que+'&path='+preview,
            success: function(data) {
                data = data == 'Suchergebnisse:'?data+'<br>Keine Treffer!':data;
                $('.searchResultsInner').html(data);
            }
        });
    }
}
function navigateToPageById(id){
	location.href = 'index.php?id='+id+'&lang='+lang;
}
function toggleWeatherLegend(){
    $('.weatherLegendBox').toggleClass('hidden');
}
var gallerySliderImages = [],gallerySliderPos = [];
function initGallerySlider(){
    var count = 0;
    for(var i=0;i<99;i++){
        count = 0;
        while(document.getElementById('galleryPrevSliderImg' + i + '_' + (count + 1))){
            count++;
            document.getElementById('galleryPrevSliderImg' + i + '_' + count).style.top = ((150 -$('#galleryPrevSliderImg' + i + '_' + count).height()) / 2) + "px";
        }
        gallerySliderImages[i] = count + 1;
        if(count > 0){
            document.getElementById('galleryPrevSliderImg' + i + '_1').className = 'galleryPrevSliderImg';
            var link = 'gallerySliderNext('+i+')';
            gallerySliderPos[i] = 1;
            window.setInterval(link,2500);
        }else{
            i = 99;
        }
    }
    $('.galleryPrevSliderImg').removeClass('opac0');
}
function gallerySliderNext(id){
    gallerySliderPos[id]++;
    for(var i=1;i<gallerySliderImages[id];i++){
        document.getElementById('galleryPrevSliderImg' + id + '_' + i).className = 'galleryPrevSliderImg right';
    }
    if(gallerySliderPos[id] < gallerySliderImages[id]){
        document.getElementById('galleryPrevSliderImg' + id + '_' + gallerySliderPos[id]).className = 'galleryPrevSliderImg';
        document.getElementById('galleryPrevSliderImg' + id + '_' + (gallerySliderPos[id] - 1)).className = 'galleryPrevSliderImg left';
    }else{
        document.getElementById('galleryPrevSliderImg' + id + '_1').className = 'galleryPrevSliderImg';
        document.getElementById('galleryPrevSliderImg' + id + '_' + (gallerySliderImages[id] - 1)).className = 'galleryPrevSliderImg left';
        gallerySliderPos[id] = 1;
    }
}
var maxSpecialSlider = 0,currSpecialSlider = 0;
var specialSliderNavBlocked = false;
var specialSliderTimer = null;
var specialSliderTimer2 = null;
function initSpecialSlider(){
    var h = $('.imgSliderHeaderLoading');
    if(h.html() == ""){
        h.addClass('opac0');
        $('#imgSliderHeaderImg0').addClass('active');
        $('#imgSliderHeaderNav0').addClass('active');
        var id;
        try{
            for(id=0;id<100;id++){
                if(document.getElementById('imgSliderHeaderImg'+id).className == "imgSliderHeaderImages"){}
            }
        }catch (ex){}
        maxSpecialSlider = id - 1;
        specialSliderTimer = window.setInterval('specialSliderShowNext()',5500);
    }
}

function specialSliderShowNext(){
    if(!specialSliderNavBlocked){
        $('.imgSliderHeaderLoading').removeClass('opac0');
        currSpecialSlider++;
        window.setTimeout("specialSliderShowImg("+currSpecialSlider+")",550);
        if(currSpecialSlider >= maxSpecialSlider){
            currSpecialSlider = -1;
        }
        specialSliderNavBlocked = true;
    }else{
        try{
            window.clearTimeout(specialSliderTimer2);
        }catch(ex){}
        specialSliderTimer2 = window.setTimeout("specialSliderShowNext()",250);
    }
}
function specialSliderShowPic(id){
    try{
        window.clearInterval(specialSliderTimer);
    }catch(ex){}
    currSpecialSlider = id - 1;
    specialSliderShowNext();
    specialSliderTimer = window.setInterval('specialSliderShowNext()',5000);
}
function specialSliderShowImg(id){
    $('.imgSliderHeaderImages').removeClass('active');
    $('.imgSliderHeaderNavPoint').removeClass('active');
    $('#imgSliderHeaderNav'+id).addClass('active');
    $('#imgSliderHeaderImg'+id).addClass('active');
    window.setTimeout("$('.imgSliderHeaderLoading').addClass('opac0')",25);
    window.setTimeout("specialSliderNavBlocked = false",550);
}