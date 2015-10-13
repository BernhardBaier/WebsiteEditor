/**
 * Created by Bernhard on 01.01.14.
 */
var dirs,files;
var browserPath = '';
var imgTypes = ['.jpg','.png','.gif','.svg'];
var browserType = 'web-images/';
var browserMode = 0;
function setRequest(text,url,out) {
    $.ajax({
        type: 'POST',
        url: url,
        data: 'text='+text+'&lang='+lang,
        success: function(data) {
            if(data.search('#refresh#') > -1) {
                location.reload();
            }else if(data.search('#reload#') > -1) {
                enterPath('');
            }else if(data.search('#imgs#') > -1) {
                showGaleryEditor(data.substr(6));
            }else if(data.search('#dirs#') > -1){
                dirs = data.substr(6,data.search('#files#') - 6);
                data = data.substr(data.search('#files#') + 7);
                files = data;
                showBrowser();
            }else{
                if(data!='1'){
                    alert(data);
                }
            }
        }
    });
}
function enterPath(path){
    if(path == '../'){
       var killstr = browserPath;
       for(var i=0;i<10;i++){
            killstr = killstr.replace('../','');
        }
        if(killstr == ""){
            browserPath += path;
        }else{
            browserPath = browserPath.substr(0,browserPath.lastIndexOf('/')-1);
            browserPath = browserPath.substr(0,browserPath.lastIndexOf('/')+1);
        }
    }else{
        path = path.search('/')>-1?path:path+'/';
        path = path == '/'?'':path;
        browserPath += path;
    }
    if(browserPath.search(browserType) == -1){
        browserPath = browserType + pageId + '/';
    }
    var ktxt2 = browserPath.substr(11);
    ktxt2 = ktxt2.substr(0,ktxt2.search('/'));
    if(path != ''){
        document.getElementById('fileUpload1').src = 'fileUpload/index.php?id='+ktxt2;
    }
    setRequest(browserPath,'getFiles.php','.out');
    $('.browserPath').html(browserPath);
    if(browserMode == 1){
        var num = browserPath.substr(browserPath.search('/')+1);
        num = num.substr(0,num.search('/'));
        if(num > 0){
            var pName = jsPageNames[num];
            pName = pName == 'undefined'?"deleted page":pName;
            $('.browserPath').html(browserPath.replace(num,pName));
        }
    }
    setCookie('filePath',browserPath,5);
}
function in_array(needle,array){
    needle = needle.toLowerCase();
    for(var i=0;i<array.length;i++){
        if(needle.search(array[i]) > -1){
            return true;
        }
    }
    return false;
}
function setBrowserType(){
    switch (document.browser.type.selectedIndex){
        case 1:
            browserType = 'web-others/';
            break;
        default:
            browserType = 'web-images/';
            break;
    }
    enterPath('');
}
function showBrowserSettings(){
    $('.browserSettingsOuter').toggleClass('hidden');
}
function setBrowserMode(th){
    browserMode = th.selectedIndex;
    setCookie('browserMode',browserMode,5);
    showBrowser();
    $('.browserPath').html(browserPath);
    if(browserMode == 1){
        var num = browserPath.substr(browserPath.search('/')+1);
        num = num.substr(0,num.search('/'));
        if(num > 0){
            $('.browserPath').html(browserPath.replace(num,jsPageNames[num]));
        }
    }
}
function showBrowser(){
    hideMultipleOptions();
    var $folders = '<div class="folderOuter"><div class="fileBrowserOptions"><img title="set view mode" src="images/gear.png" height="20" onclick="showBrowserSettings()" /></div><div class="folderInner" style="display:flex" onclick="enterPath(\'../\')" title="go up"><img height="15" src="images/folderUp.png" /> up</div>';
    var $dirs = dirs == ";"?"":dirs;
    while($dirs.search(';') > -1){
        var th_name = $dirs.substr(0,$dirs.search(';'));
        var active = '';
        if(th_name == pageId){
            active = ' active';
        }
        if(browserMode == 0){
            $folders += '<div class="folderInner'+active+'" onclick="enterPath(\''+$dirs.substr(0,$dirs.search(';'))+'\')" title="enter dir"><img height="15" src="images/folder.png" /> <nobr>'+ th_name +'</nobr></div>';
        }else{
            if(th_name>0){
                th_name = jsPageNames[th_name];
            }
            $folders += '<div class="folderInner'+active+'" onclick="enterPath(\''+$dirs.substr(0,$dirs.search(';'))+'\')" title="enter dir"><img height="15" src="images/folder.png" /> <nobr>'+ th_name +'</nobr></div>';
        }
        $dirs = $dirs.substr($dirs.search(';')+1);
    }
    $folders += '</div>';
    var $files = '<div class="fileOuter">';
    var $file = files == ";"?"empty dir;":files;
    var $imgCount = 0;
    while($file.search(';') > -1){
        var $akFile = $file.substr(0,$file.search(';'));
        if(in_array($akFile,imgTypes)){
            $files += '<div class="fileInner"><div class="prevImg" id="prevImg'+$imgCount+'"><img onclick="showPicInfo(this,'+$imgCount+')" height="100" src="' + browserPath + $akFile + '" /><div id="picInfo'+ $imgCount++ +'" class="picInfo height0"></div></div></div>';
        }else if($akFile != 'empty dir'){
            $files += '<div class="fileInnerFile">'+ $akFile +'<div class="fileOptions">' +
                '&nbsp;<img src="images/plus.png" title="insert" onclick="insertBrowserFile(\''+$akFile+'\')" height="18"/> <img src="images/pencil.png" title="rename" onclick="showPicRename(\''+$akFile+'\')" height="18"/> ' +
                '<img src="images/bin.png" title="delete" onclick="showPicDelete(\''+$akFile+'\')" height="18"/>&nbsp;</div></div>';
        }else{
            $files += '<div>'+$akFile+'</div>';
        }
        $file = $file.substr($file.search(';')+1);
    }
    $files += '</div>';
    $('.leftFolders').html($folders);
    $('.rightFiles').html($files);
}
function insertBrowserFile(name){
    $('.insertFile').removeClass('hidden');
    $('.overlay').removeClass('hidden');
    positionMessage();
    $('.insertFileTitle').html(name);
    document.insertFile.name.value = name;
}
function insertFileNow(){
    switch (document.insertFile.type.selectedIndex){
        case 1:
            insertHTMLatCursor('<iframe width="250" src="' + browserPath + document.insertFile.name.value + '"></iframe>');
            break;
        default:
            insertHTMLatCursor('<a target="_blank" href="' + browserPath + document.insertFile.name.value + '">'+document.insertFile.name.value+'</a>');
            break;
    }
    hideMessages();
}
function showPicInfo(e,id){
    if($('#picInfo'+id).html() == ""){
        var name = e.src.substr(e.src.lastIndexOf('/')+1);
	    var title = e.width < name.length * 7?'<marquee>'+name+'</marquee>':name;
        $('#picInfo'+id).html('<div class="msgBoxImg"><img onclick="showPicInfo($(this).parent().parent().parent(),'+id+')" height="18" title="close" src="images/close.png"/></div><div class="picInfoTitle">'+title+'</div><div class="picHandler" onclick="showPicRename(\''+name+'\')">rename<img src="images/pencil.png" class="picHandlerImg"/></div><div class="picHandler" onclick="showPicDelete(\''+name+'\')">delete<img src="images/bin.png" class="picHandlerImg"/></div><div class="picHandler" onclick="showInsertPic(\''+browserPath+name+'\')">insert<img src="images/insert.png" class="picHandlerImg"/></div><div class="picHandler" onclick="showMovePics(\''+browserPath+name+'\')">move<img src="images/move.png" class="picHandlerImg"/></div>').removeClass('height0').width(e.width-2);
        $('#prevImg'+id).addClass('selected');
    }else{
        $('#picInfo'+id).html('').addClass('height0');
        $('#prevImg'+id).removeClass('selected');
    }
    var len = $('.prevImg.selected').length;
    if(len > 1){
        showMultipleOptions(len);
    }else{
        hideMultipleOptions();
    }
}

function showMultipleOptions(count){
    $('.multipleOptionsOuter').removeClass('hidden');
    var opts = '<div class="multipleOptionsItem" onclick="multipleDelete()">delete<img src="images/bin.png" class="picHandlerImg"/></div><div class="multipleOptionsItem" onclick="multipleInsert()">insert<img src="images/insert.png" class="picHandlerImg"/></div><div class="multipleOptionsItem" onclick="moveMultiplePics()">move<img src="images/move.png" class="picHandlerImg"/></div><div class="multipleOptionsItem" onclick="multipleDeselect()">deselect<img src="images/deselect.png" class="picHandlerImg"/></div><div class="multipleOptionsItem" onclick="multipleSelectAll()">select all<img src="images/select.png" class="picHandlerImg"/></div>';
    $('.multipleOptions').html('options for '+count+' elements:<br/>'+opts);
}
function multipleSelectAll(){
	var elem = $('.prevImg');
	for(var i=0;i<elem.length;i++){
		var id = $(elem[i]).attr('id');
		var e = document.getElementById(id);
		if(e.className == 'prevImg'){
			e.className = 'prevImg selected';
			e = e.children[0];
			id = id.substr(7);
			var name = e.src.substr(e.src.lastIndexOf('/')+1);
			var title = e.width < name.length * 7?'<marquee>'+name+'</marquee>':name;
			$('#picInfo'+id).removeClass('height0').html('<div class="msgBoxImg"><img onclick="showPicInfo($(this).parent().parent().parent(),'+id+')" height="18" title="close" src="images/close.png"/></div><div class="picInfoTitle">'+title+'</div><div class="picHandler" onclick="showPicRename(\''+name+'\')">rename</div><div class="picHandler" onclick="showPicDelete(\''+name+'\')">delete</div><div class="picHandler" onclick="showInsertPic(\''+browserPath+name+'\')">insert</div><div class="picHandler" onclick="showMovePics(\''+browserPath+name+'\')">move</div>').removeClass('height0').width(e.width-2);
		}
	}
	elem.addClass('selected');
	var len = $('.prevImg.selected').length;
	if(len > 1){
		showMultipleOptions(len);
	}else{
		hideMultipleOptions();
	}
}
function hideMultipleOptions(){
    $('.multipleOptionsOuter').addClass('hidden');
}
function showMovePics(path){
    path = replaceUml(path);
    positionMessage();
    var count = 1;
    var paths = path;
    while(paths.search(';')>0){
        paths = paths.substr(paths.search(';')+1);
        count++;
    }
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=clickAbleMenu:movePicsNow("'+path+'",$pid):'+lang,
        success: function(data) {
            $('.movePicMenu').html(data);
            $('.overlay').removeClass('hidden');
            $('.movePicOuter').removeClass('hidden');
            $('.movePicTitle').html(count);
        }
    });
}
function moveMultiplePics(){
    var elem = $('.selected');
    var arr = '';
    for(var i=0;i<elem.length;i++){
        var src=$('#'+$(elem[i]).attr('id')+' img').attr('src');
        if(src){
            arr += src+';';
        }
    }
    arr = arr.substr(0,arr.length-1);
    showMovePics(arr);
}
function movePicsNow(path,id){
    path = replaceUml(path);
    $.ajax({
        type: 'POST',
        url: 'functions.php',
        data: 'text=movePics:'+path+':'+id,
        success: function(data) {
            if(data==''){
                hideMessages();
                showNotification('Pics moved',1000);
                hideMultipleOptions();
                enterPath('');
            }else{
                alert(data);
            }
        }
    });
}
function multipleDeselect(){
    var elem = $('.fileInner').find($('.selected'));
    for(var i=0;i<elem.length;i++){
	    $('#'+$(elem[i]).attr('id')).removeClass('selected');
	    $('#'+$(elem[i]).attr('id').replace('prevImg','picInfo')).addClass('height0').html('');
    }
    hideMultipleOptions();
}
var adminPicsToDelete = [];
function multipleDelete(){
    var elem = $('.selected');
    var arr = [];
    var i;
    var count = 0;
    for(i=0;i<elem.length;i++){
        var src=$('#'+$(elem[i]).attr('id')+' img').attr('src');
        if(src){
            arr[count++] = replaceUml(src);
        }
    }
    adminPicsToDelete = arr;
    $('.deletePicMultipleTitle').html('delete '+count+' pictures?');
    $('.overlay').removeClass('hidden');
    $('.deleteMultiplePic').removeClass('hidden');
    positionMessage();
}
function deleteMultiplePicNow(index){
    $.ajax({
        type: 'POST',
        url: 'deletePic.php',
        data: 'path='+adminPicsToDelete[index],
        success: function(data) {
            if(data!='1'){
                alert(data);
            }
            if(index<adminPicsToDelete.length-1){
                deleteMultiplePicNow(index+1);
            }else{
                enterPath('');
                hideMessages();
            }
        }
    });
}
function multipleInsert(){
    var elem = $('.selected');
    var out = '';
    for(var i=0;i<elem.length;i++){
        var src = $('#'+$(elem[i]).attr('id')+' img').attr('src');
        if(src){
            out += '<img class="galPic" height="100" src="'+src+'"/>';
        }
    }
    $('.overlay').removeClass('hidden');
    $('.htmlToInsertMulti').html('<div class="pictureFrame">'+out+'</div>');
    positionMessage();
    $('.insertMultiplePic').removeClass('hidden');
}
function multipleInsertNow(){
    insertHTMLatCursor($('.htmlToInsertMulti').html());
    hideMessages();
    togglePicsClickable();
    togglePicsClickable();
}

function showPicRename(name){
    document.rename.old.value = name;
    document.rename.name.value = name.substr(0,name.lastIndexOf('.'));
    document.rename.ending.value = name.substr(name.lastIndexOf('.'));
    positionMessage();
    $('.renamePic').removeClass('hidden');
    $('.overlay').removeClass('hidden');
}
function renamePicNow(){
    setRequest('rename:'+replaceUml(document.rename.old.value)+':'+ replaceUml(document.rename.name.value)+ document.rename.ending.value+':'+browserPath,'functions.php','.out');
    hideMessages();
}
function showPicDelete(name){
    document.delete.name.value = name;
    $('.deletePicTitle').html('delete file '+name+'?');
    positionMessage();
    $('.deletePic').removeClass('hidden');
    $('.overlay').removeClass('hidden');
}
function deletePicNow(){
    setRequest('delete:'+replaceUml(document.delete.name.value)+':'+browserPath,'functions.php','.out');
    hideMessages();
}
function showInsertPic(path){
    document.insert.path.value = path;
    document.insert.type.selectedIndex = 0;
    document.insert.align.selectedIndex = 0;
    changeInsertType();
    positionMessage();
    $('.insertPic').removeClass('hidden');
    $('.overlay').removeClass('hidden');
}

function changeInsertType(){
    var pic = '';
    var classes = 'pagePic';
    var align = '';
    switch(document.insert.align.selectedIndex){
        case 0:
            align = '';
            break;
        case 1:
            align = 'left';
            break;
        case 2:
            align = 'right';
            break;
        case 3:
            align = 'center;width:100%';
            break;
    }
    switch(document.insert.type.selectedIndex){
        case 0:
            if(align!=''){
                pic = '<img width="250" style="float:'+align+'" src="'+document.insert.path.value+'" />';
            }else{
                pic = '<img width="250" src="'+document.insert.path.value+'" />';
            }
            break;
        case 1:
            if(align!=''){
                pic = '<div style="float:'+align+'" class="titledImg '+classes+'"><div class="imgTitle">Title</div><img width="250" src="'+document.insert.path.value+'" /></div>';
            }else{
                pic = '<div class="titledImg '+classes+'"><div class="imgTitle">Title</div><img width="250" src="'+document.insert.path.value+'" /></div>';
            }
            break;
        case 2:
            if(align!=''){
                pic = '<div style="float:'+align+'" class="titledImg '+classes+'"><img width="250" src="'+document.insert.path.value+'" /><div class="imgTitle">Title</div></div>';
            }else{
                pic = '<div class="titledImg '+classes+'"><img width="250" src="'+document.insert.path.value+'" /><div class="imgTitle">Title</div></div>';
            }
            break;
    }
    $('.htmlToInsert').html('<table><tr><td id="htmlToInsert">'+pic+'</td></tr></table>');
}
function insertPicNow(){
    insertHTMLatCursor($('#htmlToInsert').html()+"<p>&nbsp;</p>");
    hideMessages();
    togglePicsClickable();
    togglePicsClickable();
}

function showGaleryMaker(){
    if(browserPath.substr(-7) != 'thumbs/'){
        enterPath('thumbs/');
    }
    setRequest(browserPath+'&gal=1','getFiles.php','.out');
}
function showGaleryEditor(pics){
    hideMessages();
    var $files = '';
    var $file = pics == ";"?"empty dir;":pics;
    var $imgCount = 0;
    var images = $('.gallery').find('img').map(function(){
        return this;
    }).get();
    var galPics = [];
    for(var i=0;i<images.length;i++){
        var source = images[i].src.replace(location.toString(),'');
        source = source.substr(source.lastIndexOf('web-images/'));
        galPics.push(source);
    }
    images = null;
    while($file.search(';') > -1){
        var $akFile = $file.substr(0,$file.search(';'));
        if(in_array($akFile,imgTypes)){
            var $class = findInArray(galPics,browserPath + $akFile) > -1 ?' selected':'';
            $files += '<div class="galMakerImg'+$class+'" id="galMakerImg'+ $imgCount +'"><img id="galImg'+ $imgCount +'" onclick="toggleGalPicSel('+ $imgCount++ +')" height="100" src="' + browserPath + $akFile + '" /></div>';
        }
        $file = $file.substr($file.search(';')+1);
    }
    $('.galeryMakerInner').html($files);
    $('.overlay').removeClass('hidden');
    $('.galeryMakerOuter').css('right',0).css('width',windowWidth/2);
    if(browserPath.substr(-7) == 'thumbs/'){
        enterPath('../');
    }
}
function hideGaleryEditor(){
    $('.galeryMakerInner').html('');
    $('.galeryMakerOuter').css('right',-windowWidth/2);
}
function galeryMakerSelectAll(dir){
    var goOn = true;
    var id=0;
    try{
        while(goOn){
            if(document.getElementById('galMakerImg'+id).className.search('galMakerImg') > -1){
                if(dir){
                    document.getElementById('galMakerImg'+id).className = 'galMakerImg selected';
                }else{
                    document.getElementById('galMakerImg'+id).className = 'galMakerImg';
                }
            }else{
                goOn = false;
            }
            id++;
        }
    }catch (ex){}
}
function toggleGalPicSel(id){
    if(document.getElementById('galMakerImg'+id).className != 'galMakerImg'){
        document.getElementById('galMakerImg'+id).className = 'galMakerImg';
    }else{
        document.getElementById('galMakerImg'+id).className = 'galMakerImg selected';
    }
}
function generateGallery(){
    var goOn = true;
    var id=0;
	var html = getCurrentHTML();
	var galHTML = '';
	if(!html.search('<div class="picsClickAble">') > -1){
		togglePicsClickable();
	}
    try{
        while(goOn){
            if(document.getElementById('galMakerImg'+id).className.search('selected') > -1){
                var src = document.getElementById('galImg'+id).src;
                src = src.substr(src.lastIndexOf('web-images/'));
                galHTML += '<img class="galImg" height="100" src="'+src+'" />';
            }
            id++;
        }
    }catch (ex){}
    if(html.search('class="gallery') > -1){
        $('.gallery').html(galHTML);//oh that hurts but is easy :D
    }else{
        insertHTMLatTheEnd('<div class="gallery">' + galHTML + '</div>');
    }
    hideMessages();
}