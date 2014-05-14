/**
 * Created by Bernhard on 13.05.14.
 */
var windowHeight,windowWidth;
function init(){
    getSize();
    document.getElementById('pageOuter').style.height = Math.round(windowHeight - $('.header').height() - $('.footer').height() - $('.copyright').height() - 13)+'px';
    document.getElementById('menu').style.left = -Math.round($('.menuOuter').width()+15)+'px';
    window.setTimeout('postInit()',250);
    initPicViewerMobile();
}
function postInit(){
    getSize();
    document.getElementById('pageOuter').style.height = Math.round(windowHeight - $('.header').height() - $('.footer').height() - $('.copyright').height() - 13)+'px';
}
function toggleMenu(){
    var left = document.getElementById('menu').style.left;
    if(left=='0px'){
        document.getElementById('menu').style.left = -Math.round($('.menuOuter').width()+15)+'px';
    }else{
        document.getElementById('menu').style.left = 0;
    }
}
function getSize() {
    if( typeof( window.innerWidth ) == 'number' ) {
        windowWidth = window.innerWidth;
        windowHeight = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        windowWidth = document.documentElement.clientWidth;
        windowHeight = document.documentElement.clientHeight;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        windowWidth = document.body.clientWidth;
        windowHeight = document.body.clientHeight;
    }
}