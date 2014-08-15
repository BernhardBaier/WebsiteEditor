/**
 * Created by Bernhard on 20.01.14.
 */
function init(){
    if(correctRightBar){
        $('.rightBar').height($('.content').height());
    }
    loadCalendarSide(3);
    initCalendarPage();
    initGallerySlider();
    window.setTimeout('postInit()',250);
}
function initCalendarPage(){
    try{
        if($('.calendar').html() != ''){
            var date = new Date();
            for(var i = 1;i <= date.getMonth();i++){
                document.getElementsByClassName('imgRotate')[i-1].className = 'imgRotate';
                document.getElementById('calendarGroup'+i).className = 'calendarGroup invisible';
            }
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
function postInit(){
    window.setTimeout('initPicViewer()',25);
}
function searchNow(){
    var que = document.search.searchInput.value;
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