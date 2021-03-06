/**
 * Created by Bernhard on 13.05.14.
 */
var addedMenuClass = false;
function init(){
    alert('i');
    window.setTimeout('postInit()',250);
    document.getElementById('menu').style.left = -Math.round($('.menuOuter').width()+35)+'px';
    initCalendarPage();
    initGallerySlider();
    initPicViewerMobile();
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
var lastScrollPos = 0, scrollTimer,goingToTop = false;
function postInit(){
    scrollTimer = window.setInterval("scrollReaction()",500);
    try{
        initImgSlider();
    }catch(ex){}
}
function scrollReaction(){
    var elem = $(".pageOuter");
    var scrollPos = elem.scrollTop();
    if(lastScrollPos != scrollPos){
        if(lastScrollPos - scrollPos > 20){
            $('.topOverlay').removeClass('opac0 hidden');
        }else if(lastScrollPos - scrollPos < -10){
            $('.topOverlay').addClass('opac0 hidden');
        }
        lastScrollPos = scrollPos;
        if (lastScrollPos > 60){
            if(!addedMenuClass && !goingToTop){
                addedMenuClass = true;
                $('.header').addClass('small');
                $('.pageOuter').addClass('small');
                $('.headerDivider').addClass('small');
                $('.searchIcon').addClass('small');
                $('.searchOuter').addClass('small');
            }
        }else{
            $('.topOverlay').addClass('opac0 hidden');
            if(addedMenuClass){
                expandMenu();
            }
        }
    }
}
function goToTop(){
    try{
        goingToTop = true;
        $(".pageOuter").animate({ scrollTop: 0 }, "slow");
        $('.topOverlay').addClass('opac0 hidden');
        if(addedMenuClass){
            expandMenu();
        }
        window.setTimeout("goingToTop=false;",1000);
    }catch (ex){}
}
function expandMenu(){
    addedMenuClass = false;
    $('.header').removeClass('small');
    $('.pageOuter').removeClass('small');
    $('.headerDivider').removeClass('small');
    $('.searchIcon').removeClass('small');
    $('.searchOuter').removeClass('small');
}
function toggleMenu(){
    var left = document.getElementById('menu').style.left;
    if(left=='0px'){
        document.getElementById('menu').style.left = -Math.round($('.menuOuter').width()+35)+'px';
        window.setTimeout("document.getElementById('menu').style.left = -Math.round($('.menuOuter').width()+35)+'px';",200);
    }else{
        document.getElementById('menu').style.left = 0;
	    expandMenu();
    }
}
function searchNow(){
    var que = document.search.searchInput.value;
    if(que.replace(' ','') == ""){
        return;
    }
    $('.searchResultsInner').html("<div>Suchergebnisse:<br/></div><div id='loadingImg1' style='height:40px;width:40px;background:#FFF;'></div><br/>");
    $('.searchResultsOuter').removeClass('hidden');
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
            data: 'lang='+lang+'&que='+que+'&path=web-content',
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