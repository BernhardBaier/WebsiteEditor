/**
 * Created by Bernhard on 12.01.14.
 */
var windowHeight,windowWidth;
var pageIsIniting = true;
var showPageTour = false;
var mayCancelLogOut = true;
function reloadLocation(rel){
    window.location.href = document.location.toString().substr(0,document.location.toString().lastIndexOf('/'))+'/admin.php?id='+pageId+'&lang='+lang+'&action='+rel;
}
function confirmExit(){
	if(startHTML != replaceUml(getCurrentHTML()) && replaceUml(getCurrentHTML()) != '<p>page not existent jet.<br /><span style="color<dpp>#555;font-size<dpp>12px;">Tipp<dpp> press crtl+s to save changes.</span></p>' && mayCancelLogOut){
		return 'Save Content!';
	}else{
		document.getElementsByClassName('pageLoading')[0].className = 'pageLoading';
		document.getElementsByClassName('loadingMessage')[0].innerHTML = 'Loading';
	}
}
function getCKEPosition(){
	return false;
}
function getCurrentHTML(){
	return CKEDITOR.instances.editable.getData();
}
function setEditorHTML(html){
    CKEDITOR.instances.editable.setData(html);
}
function insertHTMLatCursor(html){
    if(html.search('width=') > -1){
        var ktxt = html;
        var killText = '';
        while(ktxt.search('width=') > -1){
            killText += ktxt.substr(0,ktxt.search('width=') + 6);
            ktxt = ktxt.substr(ktxt.search('width=') + 6);
            var tren = ktxt.substr(0,1);
            ktxt = ktxt.substr(1);
            var width = ktxt.substr(0,ktxt.search(tren));
            if(!(width.search('px') > -1 || width.search('%') > -1)){
                width += 'px';
            }
            ktxt = ktxt.substr(ktxt.search(tren));
            killText += tren + width;
        }
        killText += ktxt;
        if(html.length <= killText.length){
            html = killText;
        }
    }
	try{
        CKEDITOR.instances['editable'].insertHtml(html);
    }catch (ex){
		insertHTMLatTheEnd(html);
	}
}
function insertHTMLatTheEnd(html){
    try{
        var editorHTML = getCurrentHTML();
        var picsCA = editorHTML.search('<div class="picsClickAble">');
        if(picsCA > -1){
            var ktxt = editorHTML.substr(0,editorHTML.lastIndexOf('</div>'));
            editorHTML = ktxt + html + editorHTML.substr(editorHTML.lastIndexOf('</div>'));
        }else{
            editorHTML += html;
        }
        setEditorHTML(editorHTML);
    }catch(ex){
        alert('unable to insert!');
    }
}
function init(){
    getSize();
    printMenu();
    document.getElementById('editable').addEventListener("mouseup", function() {
        getCKEPosition()
    }, false);
    $('.content').keyup(function(ev){
        getCKEPosition();
    });
    browserMode = getCookie('browserMode');
    if(browserMode == 'NULL'){
        browserMode = 0;
    }
    document.getElementById('browserViewModeChooser').selectedIndex = browserMode;
    var path = getCookie('filePath');
    if(path.search('web-documents/') > -1){
        document.browser.type.selectedIndex = 1;
        setBrowserType();
    }else{
        if(pageId>0){
            enterPath('web-images/'+pageId+'/');
        }
    }
    if(getCookie('leftBarPos') == "1"){
	    setCookie('leftBarPos','0',5);
	    toggleLeftBar();
    }else{
	    setCookie('leftBarPos','0',5);
	    sizeMenu();
    }
    if(getCookie('fileBrowserPos') == "0"){
        toggleFileBrowser();
    }
    if(showPageTour){
        $('.pageTour').removeClass('hidden');
    }
    hideGaleryEditor();
    $('.pageMenuTitle').html(jsPageNames[pageId]);
    $('.pageName').html(jsPageNames[pageId]);
    $('#menuItemInner'+pageId).addClass('active');
    $('.loadingMessage').html('Init');
    if(showPlugIn == true){
        showPlugins();
        if(pluginId > 0){
            window.setTimeout('initPlugin_' + pluginId + '(0)',100);
        }
    }
    if(showUsers == true){
        $('.ownUserControlOuter').removeClass('hidden');
    }
    window.setTimeout('postInit()',250);
    window.onbeforeunload = confirmExit;
	document.getElementsByClassName('pageContainer')[0].style.width = 'calc(100% - 22px)';
	document.getElementsByClassName('pageContainer')[0].style.left = '24px';
	window.setTimeout("$('.pageLoading').addClass('hidden')",350);
}
function postInit(){
	setBrowserType();
    initOptions();
    $('body').keypress(function(event) {
        if(event.keyCode == 27){
            closeEverything();
            event.preventDefault();
            return false;
        }
        if (!(event.ctrlKey) && !(event.which == 19)) return true;
        if(event.which == 115){
            saveText('content', false);
            event.preventDefault();
            return false;
        }else if(event.which == 112){
            saveText('content',true)
            event.preventDefault();
            return false;
        }else if(event.which == 97){
            multipleSelectAll();
            event.preventDefault();
            return false;
        }else{
            return true;
        }
    });
    if(typeof(initUserOptions) == "function"){
        initUserOptions();
    }
    window.setTimeout('setStartHTML()',1000);
    $('.pageLoading').addClass('opac0');
    document.body.overflow = 'visible';
    $('.fileBrowser').css("transition","bottom 1s").css("-webkit-transition","bottom 1s");
    $('.leftBar').css("transition","left 1s").css("-webkit-transition","left 1s");
    $('.content').css("transition","height 1s").css("-webkit-transition","height 1s");
    $('.pageMenu').css("transition","height 1s").css("-webkit-transition","height 1s");
    $('.pageContainer').css("transition","width 1s,left 1s").css("-webkit-transition","width 1s,left 1s");
    $('.pageTour').removeClass('opac0');
    if(updateAvailable == true){
        $.ajax({
            type: 'POST',
            url: 'update/checkUpdate.php',
            data: '',
            success: function(data) {
                if(data.search('update') > -1) {
                    data = data.substr(data.search(';')+1);
                    document.getElementById('newEditorVersion').innerHTML = data;
                    $('.updateOuter').removeClass('hidden');
                }
            }
        });
    }
    window.setInterval('checkIfUserIsLoggedIn()',60000);
}
function closeEverything(){
    hideMessages();
    $('.pluginOuter').addClass('out');
    $('.userControlOuter').addClass('out');
    $('.fileUpload').addClass('hidden');
    hideGaleryEditor();
    multipleDeselect();
    hidePageTour();
}
function setStartHTML(){
	startHTML = replaceUml(CKEDITOR.instances.editable.getData());
}
function checkIfUserIsLoggedIn(){
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=getLoggedIn',
        success: function(data) {
            if(data != '1') {
                mayCancelLogOut = false;
                location.href = 'logout.php';
            }
        }
    });
}
function initPageTour(){//is used!
    showPageTour = true;
}
function showPageTourFunc(){
    $('.pageOptions').addClass('hidden').addClass('height0');
    $('.pageTour').removeClass('hidden');
    $('.pageMenuItem').removeClass('active');
    pageTourStep(0);
}
function sizeMenu(){
	var leftWidth = $('.leftBar').width();
	if(getCookie('leftBarPos') == '1'){
		document.getElementsByClassName('pageContainer')[0].style.width = 'calc(100% - 22px)';
		document.getElementsByClassName('pageContainer')[0].style.left = '24px';
		$('.leftBar').css('left',-leftWidth-15)
	}else{
		document.getElementsByClassName('pageContainer')[0].style.width = 'calc(100% - '+(leftWidth-3)+'px)';
		document.getElementsByClassName('pageContainer')[0].style.left = leftWidth - 7+'px';
	}
    if(showPageTour){
        pageTourStep(0);
    }
}
function addMainPage(name){
    name = name == ""?document.input.pageName.value:name;
    menuChange('addMainPage:'+replaceUml(name));
}
function showRename(id,name){
    document.input2.pagename.value = name;
    document.input2.id.value = id;
    $('.rename').removeClass('hidden');
    $('.overlay').removeClass('hidden');
    positionMessage();
}
function renameNow(){
    if(document.input2.pagename.value){
        menuChange('changeName:'+document.input2.id.value+':'+replaceUml(document.input2.pagename.value));
        hideMessages();
    }
}
function showDelete(id,name){
    document.input3.id.value = id;
    $('.deleteTitle').html('Delete Page '+name+'?');
    $('.delete').removeClass('hidden');
    $('.overlay').removeClass('hidden');
    positionMessage();
}
function deleteNow(){
    menuChange('deletePage:'+document.input3.id.value);
    hideMessages();
}
function setVisibility(id,visib){
    menuChange('setVisibility:'+id+':'+visib);
}
function showAddSubPage(parent,name){
    document.input4.parent.value = parent;
    $('.addSubPageTitle').html('Add sub page to '+name+':');
    $('.addSubPage').removeClass('hidden');
    $('.overlay').removeClass('hidden');
    positionMessage();
}
function addSubPage(){
    if(document.input4.pagename.value){
        menuChange('addSubPage:'+replaceUml(document.input4.pagename.value)+':'+document.input4.parent.value);
        hideMessages();
    }
}
function showAddEqualPage(parent,rank,name){
    document.input5.parent.value = parent;
    document.input5.rank.value = rank;
    $('.addEqualPageTitle').html('Add equal page to '+name+':');
    $('.addEqualPage').removeClass('hidden');
    $('.overlay').removeClass('hidden');
    positionMessage();
}
function addEqualPage(){
    if(document.input5.pagename.value){
        menuChange('addEqualPage:'+replaceUml(document.input5.pagename.value)+':'+document.input5.parent.value+':'+document.input5.rank.value);
        hideMessages();
    }
}
function positionMessage(){
    $('.msgBox').css('top',window.pageYOffset + windowHeight/5).css('left',windowWidth/2-90);
    $('.overlay').css('top',window.pageYOffset);
}
function positionMessageTop(){
    $('.notificationBox').css('top',window.pageYOffset + 15).css('right',150).css('left',$('.leftBar').width() + 20);
}
function hideMessages(){
    $('.msgBox').addClass('hidden');
    $('.overlay').addClass('hidden');
    $('.insertLinkOuter').addClass('hidden');
    hideGaleryEditor();
    $('.metaEditorOuter').addClass('hidden');
}

function hideGroupe(id){
    $('#menGrpIn'+id).removeClass('hidden');
    $('#menGrpCt'+id).addClass('hidden');
    var $pagesHidden = getCookie('pagesHidden');
    if($pagesHidden != "NULL"){
        $pagesHidden = JSON.parse($pagesHidden);
        if(findInArray($pagesHidden,id) == -1){
            $pagesHidden[$pagesHidden.length] = id;
        }
    }else{
        $pagesHidden = [id];
    }
    sizeMenu();
    setCookie('pagesHidden',JSON.stringify($pagesHidden),5);
}
function showGroupe(id){
    $('#menGrpCt'+id).removeClass('hidden');
    $('#menGrpIn'+id).addClass('hidden');
    var $pagesHidden = getCookie('pagesHidden');
    if($pagesHidden != "NULL"){
        $pagesHidden = JSON.parse($pagesHidden);
        var pos = findInArray($pagesHidden,id);
        if(pos > -1){
            $pagesHidden.splice(pos,1);
        }
        setCookie('pagesHidden',JSON.stringify($pagesHidden),5);
    }
    sizeMenu();
}
function showOptions(id,name,parent,rank){
    if(document.getElementById('menuOptions'+id).className.search('hidden') > -1){
        $('#menuOptions'+id).html('<div class="menuOptionsItem"><a title="rename page '+name+'" href="javascript:showRename('+id+',\''+name+'\')">rename<img src="images/pencil.png" class="menuOptionImg"/></a></div><div class="menuOptionsItem"><a title="delete page '+name+'" href="javascript:showDelete('+id+',\''+name+'\')">delete<img src="images/bin.png" class="menuOptionImg"/></a></div><div class="menuOptionsItem"><a title="add a sub page to '+name+'" href="javascript:showAddSubPage('+id+',\''+name+'\')">add sub page</a></div>').removeClass('hidden');
        if(parent > 0){
            document.getElementById('menuOptions'+id).innerHTML += '<div class="menuOptionsItem"><a title="add a page after '+name+'" href="javascript:showAddEqualPage('+parent+','+(rank+1)+',\''+name+'\')">add page</a></div>';
        }
       document.getElementById('menuOptionsImg'+id).src = "images/calendarHide.png";
    }else{
        $('#menuOptions'+id).addClass('hidden');
        document.getElementById('menuOptionsImg'+id).src = "images/calendarShow.png";
    }
}

function handleDragStart(e){
    if(e.target.className == 'menuItemInner'){
        $(e.target).addClass('dragElement');
    }else{
        $(e.target).parent().addClass('dragElement');
    }
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', e.target.id.replace('menuItem',''));
}

function handleDragOver(e) {
    preventDropDefault(e);
    e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.
    var id = e.target.id.replace('menuItem','');
    if(id.search('_')>-1){
        id = id.substr(0,id.length-2);
    }
    var sId = e.dataTransfer.getData('text/html');
    if(id%2 != 0){
        $('.menuItem').removeClass('visib');
        if(id != sId){
            document.getElementById('menuItem'+(parseInt(id)-1)).classList.add('visib');
            document.getElementById('menuItem'+(parseInt(id)+1)).classList.add('visib');
        }
    }
    return false;
}
function preventDropDefault(e){
    if (e.preventDefault) {
        e.preventDefault();
    }
}
function handleDragEnter(e){
    if(e.target.className == 'menuItemInner' || e.target.className == ''){
        $('.menuItemInner').removeClass('over');
    }
    $('.menuItem').removeClass('over');
    $('.menuItemLeftDrop').removeClass('over');
    $('.menuItemRightDrop').removeClass('over');
    e.target.classList.add('over');
}

function handleDragEnd(e) {
    $('.menuItemInner').css('opacity','1');
    $('.menuItem').removeClass('visib');
    $('.menuItem').removeClass('over');
    $('.menuItemInner').removeClass('over');
    $('.menuItemLeftDrop').removeClass('over');
    $('.menuItemRightDrop').removeClass('over');
    e.target.classList.remove('over');
}

function handleDrop(e) {
    preventDropDefault(e);
    var id = e.target.id.replace('menuItem','');
    var sId = id.search('_')>-1?id.substr(0,id.search('_')):id;
    if(sId%4==0){
        sId++;
    }else{
        sId--;
    }
    var info = document.getElementById('menuItemInfo'+e.dataTransfer.getData('text/html').replace('menuItem','')).innerHTML;
    var dropId = info.substr(0,info.search(';'));
    info = info.substr(info.search(';')+1);
    var dropParent = info.substr(0,info.search(';'));
    info = info.substr(info.search(';')+1);
    var dropRank = info.substr(0,info.search(';'));
    info = info.substr(info.search(';')+1);
    var dropName = info.substr(0,info.search(';'));
    info = document.getElementById('menuItemInfo'+sId).innerHTML;
    var ownId = info.substr(0,info.search(';'));
    info = info.substr(info.search(';')+1);
    var ownParent = info.substr(0,info.search(';'));
    info = info.substr(info.search(';')+1);
    var ownRank = info.substr(0,info.search(';'));
    info = info.substr(info.search(';')+1);
    var ownName = info.substr(0,info.search(';'));
    if(id.search('_')>-1){
        if(id.substr(id.search('_')+1) == '1'){//drop after
            if(dropParent == ownParent){
                if(ownRank>dropRank){
                    menuChange('moveRank:'+dropId+':'+ownRank);
                }else{
                    menuChange('moveRank:'+dropId+':'+(parseInt(ownRank)+1));
                }
            }else{
                menuChange('movePageAndRank:'+dropId+':'+ownParent+':'+(parseInt(ownRank)+1));
            }
        }else{//drop as child
            if(dropId != ownParent){
                menuChange('movePageAndRank:'+dropId+':'+ownId+':1');
            }
        }
    }else{//drop before
        if(dropParent == ownParent && ownId != dropId){
            if(ownRank>dropRank){
                menuChange('moveRank:'+dropId+':'+(parseInt(ownRank)-1));
            }else{
                menuChange('moveRank:'+dropId+':'+ownRank);
            }
        }else{
            menuChange('movePageAndRank:'+dropId+':'+ownParent+':'+ownRank);
        }
    }
    return false;
}

function drag(ev,id,parent,rank,name){
    ev.dataTransfer.setData("Id",id);
    ev.dataTransfer.setData("Parent",parent);
    $('.infoBox').removeClass('hidden');
    $('.infoBoxTitle').html('insert '+name);
}

function drop(ev,n_id,n_parent,n_rank,pos){
    ev.preventDefault();
    var id=ev.dataTransfer.getData("Id");
    var parent=ev.dataTransfer.getData("Parent");
    if(id != n_id){
        switch (pos){
            case 0:
                if(n_parent == parent && id != n_parent){
                    menuChange('moveRank:'+id+':'+n_rank);
                }else{
                    if(id != n_parent){
                        menuChange('movePageAndRank:'+id+':'+n_parent+':'+n_rank);
                    }
                }
                break;
            case 1:
                if(n_parent == parent){
                    menuChange('moveRank:'+id+':'+(n_rank));
                }else{
                    menuChange('movePageAndRank:'+id+':'+n_parent+':'+(n_rank + 1));
                }
                break;
            case 2:
                if(n_id != parent){
                    menuChange('movePageAndRank:'+id+':'+n_id+':1');
                }
                break;
            default:
                $('.dragBarBottom').css('height',0).css('top',dropBotOff+25);
                $('.dragBarTop').css('height',0).css('top',dropTopOff + 25);
                break;
        }
    }else{
        $('.dragBarBottom').css('height',0).css('top',dropBotOff+25);
        $('.dragBarTop').css('height',0).css('top',dropTopOff + 25);
    }
    hideMessages();
}
function dragOver(ev,pos,name){
    ev.preventDefault();
    switch(pos){
        case 1:
            $('.infoBoxInfo').html('before '+name);
            break;
        case 2:
            $('.infoBoxInfo').html('after '+name);
            break;
        case 3:
            $('.infoBoxInfo').html('as a child of '+name);
            break;
        default:
            pos = pos.substr(1);
            if(pos != ev.dataTransfer.getData("Id")){
                $('.dragBarBottom').css('height',0).css('top',dropBotOff+25);
                $('.dragBarTop').css('height',0).css('top',dropTopOff + 25);
                $('#dragBarBottom'+pos).css('height',25).css('top',dropBotOff);
                $('#dragBarTop'+pos).css('height',25).css('top',dropTopOff);
            }
            break;
    }
}
function dragOut(ev){
    ev.preventDefault();
    $('.dragBarBottom').css('height',0).css('top',dropBotOff+25);
    $('.dragBarTop').css('height',0).css('top',dropTopOff + 25);
}


function replaceUml(text){
    var umlaute = [['�','�','�','�','�','�','�','&',':'],['<und>auml;','<und>ouml;','<und>uuml;','<und>Auml;','<und>Ouml;','<und>Uuml;','<und>szlig;','<und>','<dpp>']];
    for(var i=0;i<umlaute[0].length;i++){
        while(text.search(umlaute[0][i]) > -1){
            text=text.replace(umlaute[0][i],umlaute[1][i]);
        }
    }
    return text;
}
function menuChange($function){
    $.ajax({
        type: 'POST',
        url: 'MySQLHandler.php',
        data: 'table=pages_'+lang+'&function='+$function,
        success: function(data) {
            if(data != 1 && data != ""){
                $('.menu').html(data);
            }else{
                window.setTimeout('printMenu()',1);
            }
        }
    });
}
function reprintMenu(){
    if($('.menu').html() == "&nbsp;&nbsp;&nbsp;&nbsp;reprint menu"){
        printMenu();
    }
}
function printMenu(){
    $.ajax({
        type: 'POST',
        url: 'MySQLHandler.php',
        data: 'table=pages_'+lang+'&function=printMenu',
        success: function(data) {
            if(pageIsIniting){
                pageIsIniting = false;
                window.setTimeout("sizeMenu()",200);
            }
            if(data == "1" || data == ""){
                data = 'database created</br><img src="images/plus.png" height="15" /> add pages.';
            }
            $('.menu').html(data+'<div class="menuSub"><form name="input" action="javascript:addMainPage(\'\')"><input name="pageName" required placeholder="create main page" type="text" /><input type="submit" value="create"/></form></div>');
            var $pagesHidden = getCookie('pagesHidden');
            if($pagesHidden != "NULL"){
                $pagesHidden = JSON.parse($pagesHidden);
                for(var i=0;i<$pagesHidden.length;i++){
                    hideGroupe($pagesHidden[i]);
                }
            }
            sizeMenu();
            $('#menGrpIn'+pageId).children().find('.menuItemHidden').first().addClass('menuGroupActive').prop('title','active page');
            $('#menGrpCt'+pageId).children().find('.menuItemInner').first().addClass('menuGroupActive').prop('title','active page');
        }
    });
}
function getNameById(id,out){
    $.ajax({
        type: 'POST',
        url: 'MySQLHandler.php',
        data: 'table=pages_'+lang+'&function=getNameById:'+id,
        success: function(data) {
            $(out).html(data);
        }
    });
}

function findInArray(arr,needle){
    try{
        for(var i=0;i<arr.length;i++){
            if(arr[i]==needle){
                return i;
            }
        }
    }catch(ex){}
    return -1;
}
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

function toggleLeftBar(){
    if(getCookie('leftBarPos') == '0'){
        $('.leftBar').css('left',-($('.leftBar').width() + 15));
	    document.getElementsByClassName('pageContainer')[0].style.width = 'calc(100% - 22px)';
	    document.getElementsByClassName('pageContainer')[0].style.left = '24px';
        setCookie('leftBarPos','1',5);
    }else{

	    var leftWidth = $('.leftBar').width();
	    document.getElementsByClassName('pageContainer')[0].style.width = 'calc(100% - '+(leftWidth-3)+'px)';
		document.getElementsByClassName('pageContainer')[0].style.left = leftWidth - 7+'px';
	    $('.leftBar').css('left',0);
        setCookie('leftBarPos','0',5);
    }
    $('.leftBarNav').toggleClass('imgRotated');
}
function toggleFileBrowser(){
    if($('.pageMenu').height() == 70){
	    document.getElementsByClassName('content')[0].style.height = 'calc(100% - 343px)';
        $('.pageMenu').css('height',33);
	    document.getElementsByClassName('fileBrowser')[0].style.bottom = '0px';
        setCookie('fileBrowserPos','1',5);
    }else{
	    document.getElementsByClassName('content')[0].style.height = 'calc(100% - 102px)';
        $('.pageMenu').css('height',70);
        $('.fileHider').html('<img src="images/fileUp.png" height="20" onclick="showFileBrowser()" title="show file Browser" />&nbsp;');
	    document.getElementsByClassName('fileBrowser')[0].style.bottom = '-277px';
        setCookie('fileBrowserPos','0',5);
    }
    $('.fileHider').toggleClass('imgRotated');
}
function showUpload(){
    $('.fileUpload').toggleClass('hidden');
    enterPath('');
    restoreUpload();
}
function restoreUpload(){
    if(document.getElementsByClassName('fileUpload')[0].className == 'fileUpload background'){
        document.getElementsByClassName('fileUpload')[0].className = 'fileUpload';
    }
    document.getElementById('fileUpload1').style.opacity = 1;
    document.getElementById('loadingImg2').className = "waitIcon hidden";
    document.getElementsByClassName('fileUploadOnBackground')[0].className = 'fileUploadOnBackground hidden';
    document.getElementsByClassName('fileUploadBackground')[0].innerHTML = 'Background';
    try{
        window.clearInterval(refreshFilesOnBackground);
    }catch (ex){}
}
var refreshFilesOnBackground;
function toggleUploadBackground(){
    if(document.getElementsByClassName('fileUpload')[0].className == 'fileUpload'){
        document.getElementsByClassName('fileUpload')[0].className = 'fileUpload background';
        document.getElementsByClassName('fileUploadOnBackground')[0].className = 'fileUploadOnBackground';
        document.getElementsByClassName('fileUploadBackground')[0].innerHTML = 'Foreground';
        document.getElementById('fileUpload1').style.opacity = 0;
        document.getElementById('loadingImg2').className = "waitIcon";
        refreshFilesOnBackground = window.setInterval("enterPath('')",5000);
    }else{
        restoreUpload();
    }
}
function showUploadError(){
    $('.overlay').removeClass('hidden');
    $('.uploadError').removeClass('hidden');
}
function saveText(path, publish){
    var elem = document.getElementsByClassName('titledImg');
    var width = $('#editable').width();
    var edited = false;
    var innerImg;
    if(elem){
        for(var j=0;j<elem.length;j++){
            var oldWidth = elem[j].style.width.search('px');
            if(oldWidth > -1){
                oldWidth = elem[j].style.width.substr(0,oldWidth);
                oldWidth = Math.round((oldWidth/width)*1000)/10;
                elem[j].style.width = oldWidth + '%';
                edited = true;
            }
            innerImg = elem[j].innerHTML.match(/<img[\w\W]+?width:[\s0-9]+?%[\w\W]+?>/gi);
            if(innerImg != null){
                innerImg = innerImg.toString();
                oldWidth = innerImg.substr(innerImg.search('width:')+6);
                oldWidth = oldWidth.substr(0,oldWidth.search('%')).replace(' ','');
                if(oldWidth != 100) {
                    var newWidth = Math.round(parseInt(width *(oldWidth / 100)) - 40);
                    elem[j].innerHTML = elem[j].innerHTML.replace(innerImg.toString(), innerImg.replace(oldWidth + '%', newWidth + 'px')).replace(/height:[\s0-9]+?px/, '').replace(/height="[0-9]+?"/, '');
                }
            }
            innerImg = elem[j].innerHTML.match(/<img[\w\W]+?width:[\s0-9]+?px[\w\W]+?>/gi);
            if(innerImg != null){
                innerImg = innerImg.toString();
                oldWidth = innerImg.substr(innerImg.search('width:')+6);
                oldWidth = oldWidth.substr(0,oldWidth.search('px')).replace(' ','');
                var newWidth = Math.round(((parseInt(oldWidth) + 8)/width)*1000)/10;
                elem[j].innerHTML = elem[j].innerHTML.replace(innerImg.toString(),innerImg.replace(oldWidth+'px','100%')).replace(/height:[\s0-9]+?px/,'').replace(/height="[0-9]+?"/,'');
                elem[j].style.width = newWidth + '%';
                edited = true;
            }
        }
    }
    var text = getCurrentHTML();
    var imgStyles = text.match(/<img[\w\W]+?>/gi);
    var styles = [];
    if(imgStyles != null){
        for(var i=0;i<imgStyles.length;i++){
            if(findInArray(innerImg,imgStyles.toString()) == -1){
                var match = imgStyles[i].match(/<img[\w\W]+?width:[\s0-9]+?px[\w\W]+?>/gi);
                if(match == null){
                    match = imgStyles[i].match(/<img[\w\W]+?width="[\s0-9]+?"[\w\W]+?>/gi);
                }
                if(match != null){
                    styles.push(match.toString())
                }
            }
        }
    }
    if(styles != null){
        for(i=0;i<styles.length;i++){
            styles[i] = styles[i].toString();
            oldWidth = styles[i].substr(styles[i].search('width') + 6);
            var pos1=oldWidth.search('px');
            var pos2=oldWidth.search('"');
            pos1 = pos1<0?1000000:pos1;
            pos2 = pos2<0?1000000:pos2;
            var tren = 'px';
            var pos = 0;
            if(pos2<pos1){
                oldWidth = oldWidth.substr(1);
                pos = oldWidth.search('"');
                tren = '';
            }
            else{
                pos = pos1;
            }
            oldWidth = oldWidth.substr(0,pos).replace(' ','');
            newWidth = Math.round(((parseInt(oldWidth))/width)*1000)/10;
            var newStyles = styles[i].replace((oldWidth+tren),newWidth+'%').replace(/height:[\s0-9]+?px/,'').replace(/height="[0-9]+?"/,'');
            text = text.replace(styles[i],newStyles);
            edited = true;
        }
    }
    if(text != getCurrentHTML()){
        setEditorHTML(text);
    }
    text = replaceUml(text);
	$.ajax({
		type: 'POST',
		url: 'functions.php',
		data: 'text=storeText:'+text+':'+path+'/'+lang+'/'+pageId+'.php&lang='+lang+'&id='+pageId,
		success: function(data) {
			if(data.search('#saved#') > -1 && data.search('#preview#') > -1) {
                startHTML = replaceUml(getCurrentHTML());
                if(publish){
                    publishPageNow();
                }else{
                    hideMessages();
                }
                if(edited){
                    showNotification('pictures resized. Changes saved.',3500);
                }else{
                    showNotification('changes saved.',1500);
                }
			}else{
				if(data!='1'){
					alert(data);
				}
			}
		}
	});
}
function publishPageNow(){
    $.ajax({
        type: 'POST',
        url: 'MySQLHandler.php',
        data: 'table=pages_'+lang+'&function=getValueById:'+pageId+':extra',
        success: function(data) {
            if(data == '1'){
                if(document.getElementById('publishPageLang').checked){
                    $.ajax({
                        type: 'POST',
                        url: 'functions.php',
                        data: 'text=publishText&lang=all&langs='+jsLanguages+'&id='+pageId,
                        success: function(data) {
                            if(data.search('#published#') != -1) {
                                hideMessages();
                                startHTML = replaceUml(getCurrentHTML());
                                showNotification('all languages published.',1500);
                            }else{
                                if(data!='1'){
                                    alert(data);
                                }
                            }
                        }
                    });
                }else{
                    $.ajax({
                        type: 'POST',
                        url: 'functions.php',
                        data: 'text=publishText&lang='+lang+'&id='+pageId,
                        success: function(data) {
                            if(data.search('#published#') != -1) {
                                hideMessages();
                                showNotification('page published.',1500);
                            }else{
                                if(data!='1'){
                                    alert(data);
                                }
                            }
                        }
                    });
                }
            }else{
                hideMessages();
                $('.publishErrorOuter').removeClass('opac0 hidden');
                $('.overlay').removeClass('opac0 hidden');
            }
        }
    });
}
function publishPageWithVisib(){
    $.ajax({
        type: 'POST',
        url: 'MySQLHandler.php',
        data: 'table=pages_'+lang+'&function=setVisibility:'+pageId+':1',
        success: function(data) {
            if(data == '1'){
                hideMessages();
                document.getElementById('visibImg'+pageId).innerHTML = "<img src='images/eye.png' height='18' title='visible' onclick='setVisibility("+pageId+",0)' />";
                publishPageNow();
            }else{
                alert(data);
                hideMessages();
            }
        }
    });
}

var notificationBoxMayHide = false;
function showNotification(text,time){
    showNotification(text,time,false);
}
function showNotification(text,time,msgType){
    positionMessageTop();
    if(msgType){
        $('.notificationBox').addClass('errorBox');
    }
	$('.notificationBox').removeClass('hidden').removeClass('opac0');
    $('.notificationBoxInner').html(text);
	notificationBoxMayHide = true;
	window.setTimeout("hideNotificationBox()",time);
}
function notificationBoxHover(){
	notificationBoxMayHide = false;
	$('.notificationBox').removeClass('opac0');
}
function notificationBoxDisHover(){
	notificationBoxMayHide = true;
	hideNotificationBox();
}
function hideNotificationBox(){
	if(notificationBoxMayHide){
		$('.notificationBox').addClass('opac0');
		window.setTimeout("hideNotificationBoxCompletely()",450);
	}
}
function hideNotificationBoxCompletely(){
	if(notificationBoxMayHide){
		$('.notificationBox').addClass('hidden').removeClass('errorBox');
	}else{
		$('.notificationBox').removeClass('opac0');
	}
}
function showPageOptions(id,th){
    $('.pageOptions').toggleClass('height0').toggleClass('hidden');
    $(th).toggleClass('active');
}

function togglePicsClickable(th){
	if(!th){
		th = document.getElementById('pageOptionItemPics');
	}
    var html = getCurrentHTML();
    if(html.search('<div class="picsClickAble">') > -1){
        html = html.substr(html.search('<div class="picsClickAble">') + 1);
        html = html.substr(html.search('>') + 1);
        html = html.substr(0,html.lastIndexOf('</div>'));
        setEditorHTML(html);
        $(th).removeClass('selected');
    }else {
        setEditorHTML('<div class="picsClickAble">' + html + '</div>');
        $(th).addClass('selected');
        if(html.search('{#insertPluginImgSlider') > -1){
            var ktxt = html.substr(html.search('{#insertPluginImgSlider'));
            ktxt = ktxt.substr(0,html.search('#}'));
            if(ktxt.length < 35){
                showNotification("imgSlider won't work when pics are clickAble!",3500,true);
            }
        }
    }
}
function initOptions(){
    if(getCurrentHTML().search('<div class="picsClickAble">') == -1){
        $('#pageOptionItemPics').removeClass('selected');
    }else{
        $('#pageOptionItemPics').addClass('selected');
    }
}
function insertPageTitle(){
    $.ajax({
        type: 'POST',
        url: 'MySQLHandler.php',
        data: 'table=pages_'+lang+'&function=getNameById:'+pageId,
        success: function(data) {
            var html = getCurrentHTML();
	        var title = '<h1>'+data+'</h1>'
	        var titleTagLenOffset = 5;
	        if(html.search('<div class="picsClickAble">') > -1){
		        if(html.search('<h1>') > -1){
			        if(html.search('<h1>') < html.search('<div class="picsClickAble">')){
				        togglePicsClickable();
				        togglePicsClickable();
				        html = getCurrentHTML();
			        }
			        titleTagLenOffset += 30;
			        if(html.search('<h1>') < titleTagLenOffset){
				        var text = html.substr(html.search('</h1>') + 5);
				        setEditorHTML('<div class="picsClickAble">' + title + text);
			        }else{
				        setEditorHTML(title + html);
			        }
		        }else{
			        if(html.search('<div class="picsClickAble">') < titleTagLenOffset){
				        html = html.substr(html.search('<div class="picsClickAble">'));
				        setEditorHTML('<div class="picsClickAble">' + title + html.substr(html.search('>') + 1));
			        }else{
				        setEditorHTML(title + html);
			        }
		        }
	        }else{
		        if(html.search('<h1>') < titleTagLenOffset && html.search('<h1>') > -1){
			        var text = html.substr(html.search('</h1>') + titleTagLenOffset);
			        setEditorHTML(title + text);
		        }else{
			        setEditorHTML(title + html);
		        }
	        }
        }
    });
}

function deleteUserNow(){
    var name = document.deleteUserForm.name.value;
    $.ajax({
        type: 'POST',
        url: 'MySQLHandler.php',
        data: 'table=users&function=deleteUser:'+name,
        success: function(data) {
            if(data == "1"){
                $.ajax({
                    type: 'POST',
                    url: 'users.php',
                    data: 'table=users',
                    success: function(data) {
                        $('.userControlInner').html(data);
                    }
                });
            }else{
                $('.userControlInner').html(data);
            }
            hideMessages();
        }
    });
}
function deleteUser(name){
    document.deleteUserForm.name.value = name;
    $('.deleteUserMsg').removeClass('hidden');
    $('.overlay').removeClass('hidden');
    positionMessage();
}

function showSetRights(name,rights){
    var elem = document.setUserRightsForm;
    elem.name.value = name;
    elem.chk1.checked = false;
    elem.chk2.checked = false;
    elem.chk3.checked = false;
    elem.chk4.checked = false;
    if(rights.substr(0,1) == '1'){
        elem.chk1.checked = true;
    }
    if(rights.substr(1,1) == '1'){
        elem.chk2.checked = true;
    }
    if(rights.substr(2,1) == '1'){
        elem.chk3.checked = true;
    }
    if(rights.substr(3,1) == '1'){
        elem.chk4.checked = true;
    }
    $('.setUserRightsTitle').html(name);
    $('.setUserRightsMsg').removeClass('hidden');
    $('.overlay').removeClass('hidden');
    positionMessage();
}
function setUserRightsNow(){
    var elem = document.setUserRightsForm;
    var name = elem.name.value;
    var rights = elem.chk1.checked?'1':'0';
    rights += elem.chk2.checked?'1':'0';
    rights += elem.chk3.checked?'1':'0';
    rights += elem.chk4.checked?'1':'0';
    $.ajax({
        type: 'POST',
        url: 'MySQLHandler.php',
        data: 'table=users&function=changeUserRights:'+name+':'+rights,
        success: function(data) {
            if(data == "1"){
                $.ajax({
                    type: 'POST',
                    url: 'users.php',
                    data: 'table=users',
                    success: function(data) {
                        $('.userControlInner').html(data);
                    }
                });
            }else{
                $('.userControlInner').html(data);
            }
            hideMessages();
        }
    });
}

function showUserControl(){
    $('.userControlOuter').toggleClass('out');
    $.ajax({
        type: 'POST',
        url: 'users.php',
        data: 'table=users',
        success: function(data) {
            $('.userControlInner').html(data);
        }
    });
}

function showPlugins(){
    $('.pluginOuter').toggleClass('out');
    $('.pageMenuItem').removeClass('active');
    $('.pageOptions').addClass('hidden').addClass('height0')
    resetAllPlugins();
    $('.pluginInner').html('Click on an Icon to see the Plugin');
}

function showInsertLink(){
    $('.insertLink').html('<lable>choose link type: <select onchange="changeInsertLinkType(this)"><option selected>Text link</option><option>Link to a gallery</option></select></lable><br/><div class="insertLinkInner"></div>');
    $('.overlay').removeClass('opac0 hidden');
    $('.insertLinkOuter').removeClass('hidden').css('left',$('.leftBar').width()+115);
    showInsertLinkToPage();
}
function showAddPictureLink(){
	$('.insertPicLink').removeClass('opac0 hidden');
	$.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=clickAbleMenu:addLinkToPicture($pid,this):'+lang,
        success: function(data) {
            $(".insertPicLinkMenue").html(data);
        }
    });
}
function hideAddPictureLink(){
    $('.insertPicLink').addClass('opac0 hidden');
}
function addLinkToPicture(id,th){
	var oldHTML = $('#htmlToInsert').html();
	var text = document.getElementById('linkTextToInsert').value;
	var newHTML = "";
	if(oldHTML.search('<a ') > -1){
		newHTML = oldHTML.substr(0,oldHTML.search('href')+6);
		newHTML += "index.php?id="+id+"&lang="+lang + oldHTML.substr(oldHTML.search(' title')-1);
		$('#htmlToInsert').html(newHTML);
		oldHTML = newHTML;
		newHTML = oldHTML.substr(0,oldHTML.search(' title')+8);
		newHTML += text + oldHTML.substr(oldHTML.search('>')-1);
		$('#htmlToInsert').html(newHTML);
		showNotification('Link replaced',1500);
	} else {
		$('#htmlToInsert').html("<a href='index.php?id="+id+"&lang="+lang+"' title='"+text+"'>"+oldHTML+"</a>");
		showNotification('link added.',1500);
        hideAddPictureLink();
        document.getElementById('insertPicLinkButton').className = 'active';
        document.getElementById('insertPicLinkButton').value = 'change link';
	}
}
function removeLinkFromPicture(){
	var oldHTML = $('#htmlToInsert').html();
	if(oldHTML.search('</a>') > -1){
		oldHTML = oldHTML.substr(oldHTML.search('>')+1);
		$('#htmlToInsert').html(oldHTML.replace("</a>",""));
		showNotification('link removed.',1500);
        hideAddPictureLink();
        document.getElementById('insertPicLinkButton').className = '';
        document.getElementById('insertPicLinkButton').value = 'add a link';
	}
}
function changeInsertLinkType(th){
    switch (th.selectedIndex){
        case 0:
            showInsertLinkToPage();
            break;
        case 1:
            showInsertLinkToGallery();
            break;
    }
}
function showInsertLinkToPage(){
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=clickAbleMenu:insertLinkToPage($pid,this):'+lang,
        success: function(data) {
            $('.insertLinkInner').html('Insert a text link to page'+data);
        }
    });
}
function insertLinkToPage(id,th){
    insertHTMLatCursor('<a href="index.php?id='+id+'&lang='+lang+'">'+th.innerHTML+'</a>');
    $('.pageOptions').addClass('height0').addClass('hidden');
    $('.pageMenuItem').removeClass('active');
    hideInsertLink();

}
function showInsertLinkToGallery(){
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=clickAbleMenu:insertLinkToGallery($pid,this):'+lang,
        success: function(data) {
            $('.insertLinkInner').html('choose page with gallery'+data);
        }
    });
}
function insertLinkToGallery(id,th){
    var count = $('.galleryPrevSliderOuter').length;
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=gallerySlider:'+id+':'+th.innerHTML+':'+count+':'+lang,
        success: function(data) {
            if(data == '-1'){
                alert('you have to publish this page first!');
            }else if(data == '0'){
                alert('this page does not contain an gallery!');
            }else{
                insertHTMLatCursor(data);
                hideInsertLink();
            }
        }
    });
}
function hideInsertLink(){
    $('.insertLinkOuter').addClass('hidden');
    $('.overlay').addClass('hidden');
}

function showPublish(){
    $('.publishOuter').removeClass('hidden');
    $('.overlay').removeClass('hidden');
    positionMessage();
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


function editMetaData(){
    $('.pageOptions').addClass('height0 hidden');
    $('.pageMenuItem').removeClass('active');
    $.ajax({
        type: 'POST',
        url: 'editMetaData.php',
        data: 'action=get',
        success: function(data) {
            if(data == 'false'){
                data = 'there is no meta data';
            }
            var des = data.search('name="description"');
            if(des > -1){
                des = data.substr(des);
                des = des.substr(des.search('content') + 9);
                des = des.substr(0,des.search('"'));
            }
            var key = data.search('name="keywords"');
            if(key > -1){
                key = data.substr(key);
                key = key.substr(key.search('content') + 9);
                key = key.substr(0,key.search('"'));
            }
            var aut = data.search('name="author"');
            if(aut > -1){
                aut = data.substr(aut);
                aut = aut.substr(aut.search('content') + 9);
                aut = aut.substr(0,aut.search('"'));
            }
            if(des != -1){
                document.getElementById('metaDescription').value = des;
            }
            if(key != -1){
                document.getElementById('metaKeywords').value = key;
            }
            if(aut != -1){
                document.getElementById('metaAuthor').value = aut;
            }$.ajax({
                type: 'POST',
                url: 'editMetaData.php',
                data: 'action=getCode',
                success: function(data) {
                    if(data == 'false'){
                        data = '';
                    }
                    $('.overlay').removeClass('hidden');
                    document.getElementById('analyticsCode').value = data;
                    $('.metaEditorOuter').removeClass('hidden');
                }
            });
        }
    });
}
function changeMetaData(){
    var des = replaceUml(document.getElementById('metaDescription').value);
    var key = replaceUml(document.getElementById('metaKeywords').value);
    var aut = replaceUml(document.getElementById('metaAuthor').value);
    $.ajax({
        type: 'POST',
        url: 'editMetaData.php',
        data: 'action=set&des='+des+'&key='+key+'&aut='+aut,
        success: function(data) {
            hideMessages();
            if(data != ''){
                alert(data);
            }else{
                showNotification('meta data changed',1500);
            }
        }
    });
}
function changeAnalyticsId(){
    var code = replaceUml(document.getElementById('analyticsCode').value);
    $.ajax({
        type: 'POST',
        url: 'editMetaData.php',
        data: 'action=setCode&code='+code,
        success: function(data) {
            hideMessages();
            if(data != ''){
                data = data=='false'?'code error':data;
                alert(data);
            }else{
                showNotification('analytics data changed',1500);
            }
        }
    });
}

function hidePageTour(){
    $('.pageTour').addClass('opac0');
    window.setTimeout("$('.pageTour').addClass('hidden')",900);
    if(document.getElementById('showTourAgain').checked){
        $.ajax({
            type: 'POST',
            url: 'tour.php',
            data: '',
            success: function(data) {
                if(data != '1'){
                    alert(data);
                }
            }
        });
    }
    document.onkeydown = function(e){};
}
function pageTourStep(step){
    document.onkeydown = function(e){
        var ev = window.event ? window.event : e;
        if(ev.keyCode == 39){
            if(step<15){
                pageTourStep(step+1);
            }else{
                pageTourStep(1);
            }
            e.preventDefault();
        }else if(ev.keyCode == 37){
            if(step>0){
                pageTourStep(step-1);
            }
            e.preventDefault();
        }
    };
    var leftWidth = $('.leftBar').width();
    var botHeight = $('.fileBrowser').height();
    switch (step){
        default:
            $('.pageTour').removeClass('white');
            $('.tourBox').addClass('hidden').width(leftWidth-7).height(windowHeight-5).css('left',0).css('top',0);
            $('.tourText').html('welcome to your tour</br>let me show you the most important functions of this editor<br/>use arrow keys to navigate<br/><div class="tourBut tourExit tourLeft" onclick="hidePageTour()">exit</div><div class="tourBut" onclick="pageTourStep(1)">proceed</div></div>').css('top',10).css('left',windowWidth*0.5-155);
            break;
        case 1:
            $('.pageTour').addClass('white');
            $('.tourBox').removeClass('hidden').width(leftWidth-12).height(windowHeight-5).css('left',0).css('top',0);
            $('.tourText').html('here we have our website menu<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>').css('top',100).css('left',leftWidth+7);
            break;
        case 2:
            $('.tourBox').width(20).height(20).css('left',0).css('top',3);
            $('.tourText').css('top',5).css('left',30).html('by clicking on this button you can hide the menu<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 3:
            $('.pageOptions').addClass('height0').addClass('hidden');
            $('.tourBox').width(windowWidth-leftWidth).height(30).css('left',leftWidth-8).css('top',0);
            $('.tourText').css('top',40).css('left',windowWidth*0.5).html('here we have the admin page menu<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 4:
            $('.pageOptions').removeClass('height0').removeClass('hidden');
            $('.tourBox').width(73).height(28).css('left',leftWidth+48).css('top',-1);
            $('.tourText').css('top',40).css('left',leftWidth+115).html('By clicking on options you can show page options like the plugins (e.g. calendar)<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 5:
            $('.pageOptions').addClass('height0').addClass('hidden');
            tourStepSave(step,leftWidth);
            break;
        case 6:
            $('.tourBox').width(windowWidth-leftWidth+2).height(windowHeight-botHeight-35).css('left',leftWidth-9).css('top',32);
            $('.tourText').css('top',windowHeight-botHeight+7).css('left',windowWidth*0.5).html('inside this area you can edit the webpages content<br/>press control + s to save or use the button in the top.<br/>Tipp: use control + p to directly publish a page.<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 7:
            $('.tourBox').width(windowWidth-leftWidth+2).height(botHeight-2).css('left',leftWidth-9).css('top',windowHeight-botHeight-3);
            $('.tourText').css('top',windowHeight-botHeight-57).css('left',windowWidth*0.5).html('down here is the file browser<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 8:
            $('.tourBox').width(20).height(20).css('left',windowWidth-28).css('top',windowHeight-botHeight);
            $('.tourText').css('top',windowHeight-botHeight-25).css('left',windowWidth-350).html('by clicking on this button you can hide the file browser<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 9:
            $('.tourBox').width(windowWidth-leftWidth-115).height(botHeight-32).css('left',leftWidth+103).css('top',windowHeight-botHeight+27);
            $('.tourText').css('top',windowHeight-botHeight-47).css('left',windowWidth*0.5).html('in here you will see the avaliable pictures (click on them to get more options)<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 10:
            $('.tourBox').width(101).height(botHeight-32).css('left',leftWidth-4).css('top',windowHeight-botHeight+27);
            $('.tourText').css('top',windowHeight-botHeight-47).css('left',leftWidth-105).html('here you can navigate through the folders (may not be needed)<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 11:
            $('.tourBox').width(windowWidth-leftWidth+2).height(27).css('left',leftWidth-9).css('top',windowHeight-botHeight-3);
            $('.tourText').css('top',windowHeight-botHeight-97).css('left',windowWidth*0.5).html('here you have the uploader menu (set the filetype on the left, upload files by clicking on upload or show the gallery editor)<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 12:
            $('.tourBox').width(leftWidth-29).height(22).css('left',10).css('top',32);
            $('.tourText').css('top',10).css('left',leftWidth-10).html('over here we have one menuitem<br/>you can change it\'s position via drag & drop<br/>by clicking on the name you can edit the page<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 13:
            $('.tourBox').width(46).height(22).css('left',leftWidth-65).css('top',32);
            $('.tourText').css('top',10).css('left',leftWidth-10).html('the eye shows the visibility of the page<br/>by clicking on the arrow you get more options for this item like rename, delete or add sup pages<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 14:
            $('.tourBox').width(67).height(27).css('left',windowWidth-330).css('top',1);
            $('.tourText').css('top',35).css('left',windowWidth-445).html('If you think that your page is ready, don\'t forget to publish the page so that everybody can see it.<br/><div class="tourBut tourLeft" onclick="pageTourStep('+(step-1)+')">go back</div><div class="tourBut" onclick="pageTourStep('+(step+1)+')">proceed</div></div>');
            break;
        case 15:
            $('.tourBox').width(63).height(27).css('left',windowWidth-255).css('top',1);
            $('.tourText').css('top',35).css('left',windowWidth-380).html('don\'t forget to logout when finished<br/>you now know the most important features of this editor<br/>have fun with using it<br/><div class="tourBut tourExit tourLeft" onclick="hidePageTour()">exit</div><input type="checkbox" id="showTourAgain"/>don\'t show this anymore<div class="tourBut" onclick="pageTourStep(0)">restart</div></div>');
            break;
    }
}

