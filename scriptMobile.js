/**
 * Created by Bernhard on 13.05.14.
 */
function init(){
    document.getElementById('menu').style.left = -Math.round($('.menuOuter').width()+15)+'px';
}
function toggleMenu(){
    var left = document.getElementById('menu').style.left;
    if(left=='0px'){
        document.getElementById('menu').style.left = -Math.round($('.menuOuter').width()+15)+'px';
    }else{
        document.getElementById('menu').style.left = 0;
    }
}