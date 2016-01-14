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
function replaceUml(tIn){
    tIn = tIn.replace('&auml;','<span class="specialLetter">&auml;</span>');
    tIn = tIn.replace('&ouml;','<span class="specialLetter">&ouml;</span>');
    tIn = tIn.replace('&uuml;','<span class="specialLetter">&uuml;</span>');
    tIn = tIn.replace('&Auml;','<span class="specialLetter">&Auml;</span>');
    tIn = tIn.replace('&Ouml;','<span class="specialLetter">&Ouml;</span>');
    tIn = tIn.replace('&Uuml;','<span class="specialLetter">&Uuml;</span>');
    return tIn;
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
                if(in_array(id,subPageIdsKnown)){
                    $('.pageOverlayContent').html("<div class='pageOverlayContentSmall'>" + content + "</div><div class='menuBackElement' onclick='showPageInOverlay(3)'>zur&uuml;ck</div>");
                    window.setTimeout('initPicViewer()',500);
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
                            var menu = "<div class='pageSeparatorOuter hidden'><div class='pageSeparator'></div></div><div class='pageSeparatorOuter2'><div class='pageSeparator'>";
                            for(var i=0;i<subPageIdsKnown.length;i++){
                                if(i%3 == 0 && i != 0){
                                    menu += "</div></div><div class='pageSeparatorOuter2'><div class='pageSeparator'>";
                                }
                                var row = '';
                                if(subPageNamesKnown[i].length > 30){
                                    row = ' row3';
                                }else if(subPageNamesKnown[i].length > 17){
                                    row = ' row2';
                                }
                                menu += "<div class='styleElementOuter width20'><div class='styleElement color"+(i%10)+"'><div class='styleElementLink' onclick='showPageInOverlay(" + subPageIdsKnown[i];
                                menu += ")'><div class='styleElementLinkText2"+row+"'>"+replaceUml(subPageNamesKnown[i])+"</div></div></div></div>";
                            }
                            menu += "</div></div>";
                            $('.pageOverlayContent').html("<div class='pageOverlayContentTop'>" + content + "</div><div class='menuOuter'><div class='menu'>"+menu+"</div></div>");
                            $('.menuOuter').height($('.pageOverlayContent').height() - $('.pageOverlayContentTop').height());
                            window.setTimeout('initPicViewer()',500);
                        }
                    });
                }else{
                    $('.pageOverlayContent').html(content);
                    if(id == 5){
                        loadFrame(0,'karten1');
                    }
                    $(".menu").addClass("hidden");
                    window.setTimeout('initPicViewer()',500);
                }
            }else{
                $('.pageOverlayContent').html("Upps diese Seite ist nicht verf&uuml;gbar!");
            }
        }
    });
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
    $('#'+name).height($('.pageOverlayContent').height()-165+'px');
}