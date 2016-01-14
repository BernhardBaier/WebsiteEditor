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
    initSpecialSlider();
    initGallerySlider();
    window.setTimeout('postInit()',250);
}
function pageShowEffect(id){
    $('.flipContainer').removeClass('hover');
    document.getElementById('flipper'+id).className = 'flipContainer hover';
    if(id<5){
        window.setTimeout("pageShowEffect("+(id+1)+")",500);
    }else{
        window.setTimeout("$('.flipContainer').removeClass('hover');",500)
    }
}
function postInit(){
    //window.setTimeout('pageShowEffect(1)',150);
    if(pageId != 1){
        showPageInOverlay(pageId);
    }
    window.setTimeout('initPicViewer()',25);
    try{
        initImgSlider();
    }catch(ex){}
}
var subPageIdsKnown = [];
var subPageNamesKnown = [];
var subSubPageIdsKnown = [];
var subSubPageNamesKnown = [];
var lastMenu = "";
function in_array(needle,array){
    try{
        for(var i=0;i<array.length;i++){
            if(array[i] == needle){
                return true;
            }
        }
        return false;
    }catch(ex){
        return false;
    }
}
function setMenuItemActive(id){
    $('.menuItem').removeClass('active');
    $('#menuItem'+id).addClass('active');
}
function showPageInOverlay(id){
    var opts = {
        lines: 14,
        length: 16,
        width: 7,
        radius: 22,
        corners: 1,
        rotate: 219,
        direction: 1,
        color: '#000',
        speed: 1.2,
        trail: 75,
        shadow: false,
        hwaccel: false,
        className: 'spinner',
        zIndex: 9,
        top: 'calc(50% - 50px)',
        left: '50%'
    };
    $('.pageOverlayContent').html("");
    var target = document.getElementById('pageOverlayContent1');
    var spinner = new Spinner(opts).spin(target);
    $('.pageOverlayOuter').removeClass('hidden');
    $('.pageOverlayHider').removeClass('hidden');

    var path = 'web-content/'+lang+'/'+id+'.php';
    if(preview == true){
        path = 'content/'+lang+'/'+id+'.php';
    }
    $.ajax({
        type: 'POST',
        url: path,
        data: '',
        success: function(data) {
            if(data != ""){
                var content = data;
                if(in_array(id,subPageIdsKnown) || in_array(id,subSubPageIdsKnown)){
                    $.ajax({
                        type: 'POST',
                        url: 'getSubpages.php',
                        data: 'lang='+lang+'&action=sub&id='+id,
                        success: function(data2) {
                            if(!in_array(id,subSubPageIdsKnown)){
                                subSubPageIdsKnown = [];
                                subSubPageNamesKnown = [];
                            }
                            while(data2.search('{;}') > -1){
                                subSubPageIdsKnown.push(data2.substr(0,data2.search('{;}')));
                                data2 = data2.substr(data2.search('{;}')+3);
                            }
                            while(data2.search('{#}') > -1){
                                subSubPageNamesKnown.push(data2.substr(0,data2.search('{#}')));
                                data2 = data2.substr(data2.search('{#}')+3);
                            }
                            var subMenu = "<div class='subMenu'>";
                            for(var i=0;i<subSubPageIdsKnown.length;i++){
                                subMenu += "<div class='menuItem' id='menuItem"+subSubPageIdsKnown[i]+"' onclick='showPageInOverlay("+subSubPageIdsKnown[i]+")'>"+subSubPageNamesKnown[i]+'</div>';
                            }
                            subMenu += "</div>";
                            if(subMenu == "<div class='subMenu'></div>"){
                                subMenu = "";
                            }
                            var menu = "<div class='menuItem topItem' onclick='showPageInOverlay(3)'>Projekte</div>";
                            for(var i=0;i<subPageIdsKnown.length;i++){
                                if(id == subPageIdsKnown[i]){
                                    menu += "<div class='menuItem' id='menuItem"+subPageIdsKnown[i]+"' onclick='showPageInOverlay("+subPageIdsKnown[i]+")'>"+subPageNamesKnown[i]+'</div>'+subMenu;
                                }else{
                                    menu += "<div class='menuItem' id='menuItem"+subPageIdsKnown[i]+"' onclick='showPageInOverlay("+subPageIdsKnown[i]+")'>"+subPageNamesKnown[i]+'</div>';
                                }
                            }
                            if(in_array(id,subSubPageIdsKnown)){
                                menu = lastMenu;
                            }else{
                                lastMenu = menu;
                            }
                            content = "<div class='menu'><div class='menuInner'>"+menu+"</div></div><div class='pageLeft'>"+content+"</div>";
                            window.setTimeout('setMenuItemActive('+id+')',100);
                            $('.pageOverlayContent').html(content);
                            window.setTimeout('initPicViewer()',500);
                        }
                    });
                }else if(id == 3){
                    $.ajax({
                        type: 'POST',
                        url: 'getSubpages.php',
                        data: 'lang='+lang+'&id='+id,
                        success: function(data2) {
                            if(data2.search('{;}') > -1){
                                subPageIdsKnown = [];
                            }
                            while(data2.search('{;}') > -1){
                                subPageIdsKnown.push(data2.substr(0,data2.search('{;}')));
                                data2 = data2.substr(data2.search('{;}')+3);
                            }
                            while(data2.search('{#}') > -1){
                                subPageNamesKnown.push(data2.substr(0,data2.search('{#}')));
                                data2 = data2.substr(data2.search('{#}')+3);
                            }
                            var menu = "";
                            for(var i=0;i<subPageIdsKnown.length;i++){
                                menu += "<div class='menuItem' id='menuItem"+subPageIdsKnown[i]+"' onclick='showPageInOverlay("+subPageIdsKnown[i]+")'>"+subPageNamesKnown[i]+'</div>';
                            }
                            content = "<div class='menu'><div class='menuInner'>"+menu+"</div></div><div class='pageLeft'>"+content+"</div>";
                            $('.pageOverlayContent').html(content);
                            window.setTimeout('initPicViewer()',500);
                        }
                    });
                }else{
                    $('.pageOverlayContent').html(content);
                    if(id == 5){
                        loadFrame(0,'karten1');
                    }
                    window.setTimeout('initPicViewer()',500);
                }
            }else{
                $('.pageOverlayContent').html("Upps diese Seite ist nicht verf&uuml;gbar!");
            }
        }
    });//*/
}
function closePageInOverlay(){
    $('.pageOverlayOuter').addClass('hidden');
    $('.pageOverlayHider').addClass('hidden');
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

function loadFrame(id,name){
    switch (id){
        case 0:
            document.getElementById(name).src = "http://leo.ntz.de/online/buchung.jsp?dateID=102597";
            break;
        case 1:
            document.getElementById(name).src = "http://leo.ntz.de/online/buchung.jsp?dateID=102598";
            break;
        case 2:
            document.getElementById(name).src = "http://leo.ntz.de/online/buchung.jsp?dateID=102599";
            break;
    }
    $('#'+name).height($('.pageOverlayContent').height()-150+'px');
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