/**
 * Created by Bernhard on 13.05.14.
 */
var addedMenuClass = false;
function init(){
    window.setTimeout('postInit()',250);
    initPicViewerMobile();
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
        document.getElementById('menu').style.left = -Math.round($('.menuOuter').width()+15)+'px';
    }else{
        document.getElementById('menu').style.left = 0;
	    expandMenu();
    }
}