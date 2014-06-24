/**
 * Created by Bernhard on 13.12.13.
 */
/*                  Settings                */
var picViewerClassName = '.picsClickAble'; //class or Id of the element in which the picViewerPics will be searched
var picViewerThumbnails = 'thumbs/';       //Standard Thumbnail path
var activatePreloadBar = true;             //set it to show or not to show the progress bar of the preload.
var autoResizePics = false;                //Set this to true to automatically change the height of all found picViewerPics to the value below
var autoResizeHeight = 100;                //height after resize
var picViewerExcludePics = ['loading.gif','close.png'];      //a list of picViewerPics that should not be clickable
var showPicNameAsTitle = false;            //automatically show the name of the pic as title
var picViewerIsMobile = false;                      //is the viewer used by an mobile device???

/*                    code                  */
var picViewerPics = [];//the source of the picViewerImages will be stored in here
var picViewerPicFkts = [];
var picViewerImages = [];
var picViewerPicTitles = [];
var picViewerIndex = -1;
var maxPicId = -1;
var picViewerPreloadId = -1;
var picViewerWindowHeight,picViewerWindowWidth;
var picViewerPicHeight = 25;

if (!document.getElementById('picViewerStyle')){//get the style in
    var head  = document.getElementsByTagName('head')[0];
    var link  = document.createElement('link');
    link.id   = 'picViewerStyle';
    link.rel  = 'stylesheet';
    link.type = 'text/css';
    link.href = 'picViewer/picViewer.css';
    head.appendChild(link);
}
function picViewerGetSize() {
    if( typeof( window.innerWidth ) == 'number' ) {
        picViewerWindowWidth = window.innerWidth;
        picViewerWindowHeight = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        picViewerWindowWidth = document.documentElement.clientWidth;
        picViewerWindowHeight = document.documentElement.clientHeight;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        picViewerWindowWidth = document.body.clientWidth;
        picViewerWindowHeight = document.body.clientHeight;
    }
}
function picViewerFindInArray(arr,needle){
    for(var i=0;i<arr.length;i++){
        if(arr[i] == needle){
            return i;
        }
    }
    return -1;
}
function picViewerHandlePics(){
    for(var i=0;i<picViewerImages.length;i++){
        var source = picViewerImages[i].src.replace(location.toString(),'');
        source=source.replace(picViewerThumbnails,'');
        if(picViewerFindInArray(picViewerExcludePics,source.substr(source.lastIndexOf('/')+1)) == -1){
            picViewerPics.push(source);
            picViewerImages[i].id = 'galeryImg'+i;
            picViewerImages[i].title = "Zum ansehen anklicken";
            var queElem = $('#galeryImg'+i);
            try{
                if(queElem.parent().attr('class').search('titledImg') > -1){
                    var killtext = queElem.parent().html();
                    killtext = killtext.substr(killtext.search('<div class="imgTitle">') + 22);
                    killtext = killtext.substr(0,killtext.search('</div>'));
                    picViewerPicTitles[i] = killtext;
                }else{
                    picViewerPicTitles[i] = 'NULL';
                }
            }catch (ex){
                picViewerPicTitles[i] = 'NULL';
            }
            if(queElem.width() == 0 || queElem.height() == 0){
                window.setTimeout('picViewerHandlePics()',100);
                return;
            }
            picViewerPicFkts.push(queElem.width()/queElem.height());
            picViewerPicFkts[i] = picViewerPicFkts[i]==0?1.5:picViewerPicFkts[i];
            queElem.addClass('galPic');
            if(autoResizePics){
                queElem.height(autoResizeHeight).width(autoResizeHeight*picViewerPicFkts[i]);
            }else{
                queElem.width(queElem.height()*picViewerPicFkts[i]);
            }
        }
    }
    picViewerFinishInit();
}
function initPicViewerMobile(){
    picViewerPicHeight = 55;
    picViewerIsMobile = true;
    initPicViewer();
}
function initPicViewer(){
    picViewerPics = [];
    picViewerPicFkts = [];
    picViewerIndex = -1;
    picViewerPicTitles = [];
    $('.picViewerOvLeft').addClass('picViewerHidden');
    $('.picViewerOvRight').addClass('picViewerHidden');
    if($('.pagePicViewer').width() <= 0){
        var picViewerInfo = picViewerIsMobile==true?'':'<img class="picViewerAutoImg" title="info" src="picViewer/picViewerImages/info.png" onclick="picViewerInfo()" height="'+picViewerPicHeight+'"/>';
        document.body.innerHTML = '<div class="pagePicViewer picViewerOpac0 picViewerHidden" align="center"><div class="picViewerOverlay" onclick="hidePicViewer()"></div><div class="picViewerOuter"><div class="picViewerHidden" id="picViewerPreload"></div><div class="picViewer"><div class="picViewerCloser"><img src="picViewer/picViewerImages/close.png" onclick="hidePicViewer()" title="close" height="'+picViewerPicHeight+'" /></div><div class="picViewerHider"><img width="80" class="picHiderImg" src="picViewer/picViewerImages/loading.gif" /></div><div class="picViewerTitle picViewerHidden"></div><div class="picViewerInner"><div class="picViewerOvLeft picViewerOpac0 picViewerHidden" onclick="picViewerShowPrevPic()" onmouseover="picViewerPicOver(this)" onmouseout="picViewerPicOut(this)"><img height="100" class="picViewerLeftImg" src="picViewer/picViewerImages/left.png" title="voheriges Bild" /></div><img class="picViewerImg" width="200" id="picViewerImg1" src="picViewer/picViewerImages/loading.gif" onload="picViewerPicLoaded()" /><div class="picViewerOvRight picViewerOpac0 picViewerHidden" onclick="picViewerShowNextPic()" onmouseover="picViewerPicOver(this)" onmouseout="picViewerPicOut(this)"><img height="100" class="picViewerRightImg" src="picViewer/picViewerImages/right.png" title="weiteres Bild" /></div></div></div>' +
        '<div class="picViewerAutoBar picViewerHidden"></div><div class="picViewerAuto"><img class="picViewerAutoImg picViewerHidden" title="play" src="picViewer/picViewerImages/play.png" onclick="picViewerPlay()" height="'+picViewerPicHeight+'"/>'+picViewerInfo+'</div>' +
        '<div class="picViewerInfoOuter picViewerHidden"><img  onclick="picViewerInfo()" src="picViewer/picViewerImages/close.png" width="20" style="position: absolute;top: -15px;right: -15px;cursor: pointer;" />Info</br><span style="color:#555;font-size:12px;">PicViewer Version 2.0</span><div class="picViewerInfo">- Use arrow keys to navigate</br>- press x to close</br>- press p to start auto mode</br>- use +&- keys to set speed in auto mode</br>- press i to toggle information</div></div></div></div>'+document.body.innerHTML;
    }
    picViewerGetSize();
    picViewerImages = $(picViewerClassName).find('img').map(function(){
        return this;
    }).get();
    picViewerHandlePics();
}
function picViewerFinishInit(){
    picViewerImages = null;
    maxPicId = picViewerPics.length;
    if(maxPicId > 0){
        picViewerPreloadId = 0;
        picViewerLoadNext();
    }else{
        maxPicId = -1;
    }
    if(maxPicId > 1){
        $('.picViewerOvLeft').removeClass('picViewerHidden');
        $('.picViewerOvRight').removeClass('picViewerHidden');
        $('.picViewerAutoImg').removeClass('picViewerHidden');
    }
    $('.galPic').click( function() {
        showPicViewer(this.src);
    });
}
function picViewerPicOver(ev){
    if(!picVieweIsAuto){
        ev.className = ev.className.replace('picViewerOpac0','');
    }
}
function picViewerPicOut(ev){
    ev.className += ' picViewerOpac0';
}
function hidePicViewer(){
    if(picVieweIsAuto){
        picViewerStop();
    }else{
        picViewerIndex = -1;
        document.getElementById('picViewerImg1').src = 'picViewer/picViewerImages/loading.gif';
        $('.pagePicViewer').addClass('picViewerOpac0 picViewerHidden');
        $('.picViewerHider').removeClass('picViewerOpac0 picViewerHidden');
        document.body.style.overflow = 'visible';
        document.onkeydown = function(e){};
    }
}
function showPicViewer(src){
    src = src.replace(location.toString(),'');
    src = src.replace(picViewerThumbnails,'');
    if(!picViewerIsMobile){
        document.onkeydown = function(e){
            var ev = window.event ? window.event : e;
            if(ev.keyCode == 80){
                if(!picVieweIsAuto){
                    picViewerPlay();
                }
            }else if(ev.keyCode == 39){
                if(picVieweIsAuto){
                    window.clearTimeout(picViewerTimer);
                }
                picViewerShowNextPic();
            }else if(ev.keyCode == 37){
                if(picVieweIsAuto){
                    window.clearTimeout(picViewerTimer);
                }
                picViewerShowPrevPic();
            }else if(ev.keyCode == 171){
                if(picVieweIsAuto){
                    picViewerFaster();
                }
            }else if(ev.keyCode == 173){
                if(picVieweIsAuto){
                    picViewerSlower();
                }
            }else if(ev.keyCode == 88){
                hidePicViewer();
            }else if(ev.keyCode == 73){
                picViewerInfo();
            }
        };
    }
    for(var i=0;i<picViewerPics.length;i++){
        if(picViewerPics[i] == src){
            picViewerIndex = i;
            if(picViewerIndex > -1){
                showPicViewerId();
            }
            return;
        }
    }
}
var picViewerActualWidth = 0;
function showPicViewerId(){
    picViewerGetSize();
    $('.pagePicViewer').removeClass('picViewerOpac0 picViewerHidden');
    document.getElementById('picViewerImg1').src = picViewerPics[picViewerIndex];
    var $width = 0;
    if(picVieweIsAuto){
        if(picViewerWindowWidth/picViewerWindowHeight > 1.77 && picViewerWindowWidth/picViewerWindowHeight < 1.79){
            $width = picViewerWindowWidth/picViewerWindowHeight>picViewerPicFkts[picViewerIndex]?(picViewerWindowHeight - 46)*picViewerPicFkts[picViewerIndex]:picViewerWindowWidth - 40;
        }else{
            $width = picViewerWindowWidth/picViewerWindowHeight>picViewerPicFkts[picViewerIndex]?(picViewerWindowHeight - 50)*picViewerPicFkts[picViewerIndex]:picViewerWindowWidth - 100;
        }
    }else{
        $width = picViewerWindowWidth/picViewerWindowHeight>picViewerPicFkts[picViewerIndex]?(picViewerWindowHeight - 90)*picViewerPicFkts[picViewerIndex]:picViewerWindowWidth - 170;
    }
    var $height = $width/picViewerPicFkts[picViewerIndex];
    if(showPicNameAsTitle || picViewerPicTitles[picViewerIndex] != 'NULL'){
        $height += 19;
        var killtxt;
        if(picViewerPicTitles[picViewerIndex] != 'NULL'){
            killtxt = picViewerPicTitles[picViewerIndex];
        }else{
            killtxt = picViewerPics[picViewerIndex];
            killtxt = killtxt.substr(killtxt.lastIndexOf('/')+1);
            killtxt = killtxt.substr(0,killtxt.lastIndexOf('.'));
        }
        $('.picViewerTitle').removeClass('picViewerHidden').html(killtxt);
    }else{
        $('.picViewerTitle').addClass('picViewerHidden').html('');
    }
    var $left = (picViewerWindowWidth - $width) / 2 - 10;
    $('.picViewer').height($height).width($width);
    $('.picViewerInner').height($height).width($width);
    picViewerActualWidth = $width;
    if(picVieweIsAuto){
        $('.picViewerOuter').css('left',$left).css('top',window.pageYOffset);
    }else{
        $('.picViewerOuter').css('left',$left).css('top',window.pageYOffset + 35);
    }
    $('.picViewerOverlay').css('top',window.pageYOffset);
    document.body.style.overflow = 'hidden';
    if(picVieweIsAuto){
        try{
            window.clearTimeout(picViewerTimer);
        }catch (ex){}
        $('.picViewerAutoBar').css('transition','width 0ms').css('-webkit-transition','width 0ms').width(0);
        picViewerTimer = window.setTimeout("$('.picViewerAutoBar').width(0)",5);
    }
}
function picViewerShowNextPic(){
    $('.picViewerHider').removeClass('picViewerOpac0 picViewerHidden');
    picViewerIndex++;
    if(picViewerIndex > maxPicId - 1){
        picViewerIndex = 0;
    }
    window.setTimeout('showPicViewerId()',250);
}
function picViewerShowPrevPic(){
    $('.picViewerHider').removeClass('picViewerOpac0 picViewerHidden');
    picViewerIndex-=1;
    if(picViewerIndex < 0){
        picViewerIndex = maxPicId - 1;
    }
    window.setTimeout('showPicViewerId()',250);
}
function picViewerPicLoaded(){
    $('.picViewerHider').addClass('picViewerOpac0');
    if(picVieweIsAuto){
        picViewerTimer = window.setTimeout('picViewerShowNextPic()',picViewerTime);
        window.setTimeout("$('.picViewerAutoBar').css('transition','width '+(picViewerTime-50)+'ms linear').css('-webkit-transition','width '+(picViewerTime-50)+'ms linear').width(picViewerActualWidth)",50);
    }
}
function picViewerLoadNext(){
    if(activatePreloadBar){
        if($('.picPreloadStatus').width() <= 0){
            $(picViewerClassName).html('<div class="picPreloadStatus"><div class="picPreloadText">Lade Bilder vorraus </div><div class="picPreloadBar"></div><div id="barCloser" style="float:right;cursor:pointer;"><img src="picViewer/picViewerImages/close.png" title="hide" height="20" style="margin:2px;" onclick="$(\'.picPreloadStatus\').height(0)" /></div></div>'+$(picViewerClassName).html());
            $('.picPreloadStatus').height(25);
        }
        var maxWidth = $('.picPreloadStatus').width()-$('.picPreloadText').width()-35-$('#barCloser').width();
    }
    if(picViewerPreloadId<maxPicId){
        $('#picViewerPreload').html('<img src="'+picViewerPics[picViewerPreloadId]+'" height="0" onload="picViewerLoadNext()" />');
        picViewerPreloadId++;
    }else{
        $('.picPreloadStatus').height(0);
        window.setTimeout("$('.picPreloadStatus').addClass('picViewerHidden')",300);
    }
    if(activatePreloadBar){
        $('.picPreloadBar').width((picViewerPreloadId/maxPicId)*maxWidth);
    }
}

var picViewerTime = 4500;
var picVieweIsAuto = false;
var picViewerTimer;
function picViewerPlay(){
    var elem = document.body;
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
    } else if (elem.mozRequestFullScreen) {
        elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
    }
    picVieweIsAuto = true;
    picViewerGetSize();
    var $width = picViewerWindowWidth/picViewerWindowHeight>picViewerPicFkts[picViewerIndex]?(picViewerWindowHeight - 50)*picViewerPicFkts[picViewerIndex]:picViewerWindowWidth - 100;
    var $height = $width/picViewerPicFkts[picViewerIndex];
    var $left = (picViewerWindowWidth - $width) / 2 - 10;
    $('.picViewer').height($height).width($width);
    $('.picViewerInner').height($height).width($width);
    $('.picViewerOuter').css('left',$left).css('top',window.pageYOffset);
    window.setTimeout('picViewerSetSize()',200);
    $('.picViewerCloser').addClass('picViewerHidden');
    $('.picViewerOverlay').css('opacity',1);
    $('.picViewerOvLeft').addClass('picViewerHiddenTot');
    $('.picViewerOvRight').addClass('picViewerHiddenTot');
}
function picViewerSetSize(){
    $('.picViewerAuto').html('<img class="picViewerAutoImg" title="slower" src="picViewer/picViewerImages/minus.png" onclick="picViewerSlower()" height="'+picViewerPicHeight+'"/><img class="picViewerAutoImg" title="stop" src="picViewer/picViewerImages/stop.png" onclick="picViewerStop()" height="'+picViewerPicHeight+'"/><img class="picViewerAutoImg" title="faster" src="picViewer/picViewerImages/plus.png" onclick="picViewerFaster()" height="'+picViewerPicHeight+'"/>');
    $('.picViewerAutoBar').removeClass('picViewerHidden').css('transition','width '+picViewerTime+'ms linear').css('-webkit-transition','width '+picViewerTime+'ms linear').width(picViewerActualWidth);
    picViewerGetSize();
    var $width;
    if(picViewerWindowWidth/picViewerWindowHeight > 1.77 && picViewerWindowWidth/picViewerWindowHeight < 1.79){
        $width = picViewerWindowWidth/picViewerWindowHeight>picViewerPicFkts[picViewerIndex]?(picViewerWindowHeight - 46)*picViewerPicFkts[picViewerIndex]:picViewerWindowWidth - 40;
    }else{
        $width = picViewerWindowWidth/picViewerWindowHeight>picViewerPicFkts[picViewerIndex]?(picViewerWindowHeight - 50)*picViewerPicFkts[picViewerIndex]:picViewerWindowWidth - 100;
    }
    var $height = $width/picViewerPicFkts[picViewerIndex];
    var $left = (picViewerWindowWidth - $width) / 2 - 10;
    $('.picViewer').height($height).width($width);
    $('.picViewerInner').height($height).width($width);
    $('.picViewerOuter').css('left',$left).css('top',window.pageYOffset);
    picViewerTimer = window.setTimeout('picViewerShowNextPic()',picViewerTime);
}
function picViewerStop(){
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    }
    window.clearTimeout(picViewerTimer);
    picVieweIsAuto = false;
    $('.picViewerCloser').removeClass('picViewerHidden');
    $('.picViewerOverlay').css('opacity',0.7);
    var picViewerInfo = picViewerIsMobile==true?'':'<img class="picViewerAutoImg" title="info" src="picViewer/picViewerImages/info.png" onclick="picViewerInfo()" height="'+picViewerPicHeight+'"/>';
    $('.picViewerAuto').html('<img class="picViewerAutoImg" title="play" src="picViewer/picViewerImages/play.png" onclick="picViewerPlay()" height="'+picViewerPicHeight+'"/>'+picViewerInfo);
    $('.picViewerAutoBar').addClass('picViewerHidden').css('transition','width 0ms').css('-webkit-transition','width 0ms').width(0);
    picViewerGetSize();
    var $width = picViewerWindowWidth/picViewerWindowHeight>picViewerPicFkts[picViewerIndex]?(picViewerWindowHeight - 90)*picViewerPicFkts[picViewerIndex]:picViewerWindowWidth - 175;
    var $height = $width/picViewerPicFkts[picViewerIndex];
    var $left = (picViewerWindowWidth - $width) / 2 - 10;
    $('.picViewer').height($height).width($width);
    $('.picViewerOuter').css('left',$left).css('top',window.pageYOffset + 35);
    $('.picViewerOvLeft').removeClass('picViewerHiddenTot');
    $('.picViewerOvRight').removeClass('picViewerHiddenTot');
}
function picViewerSlower(){
    picViewerTime = picViewerTime<15000?picViewerTime+500:picViewerTime;
}
function picViewerFaster(){
    picViewerTime = picViewerTime>1000?picViewerTime-500:picViewerTime;
}

function picViewerInfo(){
    $('.picViewerInfoOuter').toggleClass('picViewerHidden');
}