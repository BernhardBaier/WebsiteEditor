/**
 * Created by Bernhard on 13.05.14.
 */
var addedMenuClass = false;
function init(){
    window.setTimeout('postInit()',250);
    initCalendarPage();
    initGallerySlider();
    initPicViewerMobile();
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
function postInit(){
    $(".pageOuter").scroll(function (e) {
        e.preventDefault();
        var elem = $(this);
        if (elem.scrollTop() > 3){
            if(!addedMenuClass){
                addedMenuClass = true;
                $('.header').addClass('small');
                $('.pageOuter').addClass('small');
                $('.headerDivider').addClass('small');
                $('.searchIcon').addClass('small');
                $('.searchOuter').addClass('small');
            }
        }else{
            if(addedMenuClass){
                expandMenu();
            }
        }
    });
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
    }else{
        document.getElementById('menu').style.left = 0;
	    expandMenu();
    }
}
function searchNow(){
    var que = document.search.searchInput.value;
    if(que != ''){
        $.ajax({
            type: 'POST',
            url: 'search.php',
            data: 'lang='+lang+'&que='+que+'&path=web-content',
            success: function(data) {
                data = data == 'Suchergebnisse:'?data+'<br>Keine Treffer!':data;
                $('.searchResultsInner').html(data);
                $('.searchResultsOuter').removeClass('hidden opac0');
            }
        });
    }
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