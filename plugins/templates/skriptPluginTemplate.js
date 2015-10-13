/**
 * Created by Bernhard on 13.10.2015.
 */
function selectTemplate(th,id){
    $('.pluginTemplateEditorTemplate').removeClass('active');
    th.className = 'pluginTemplateEditorTemplate active';
    var path = $('#pluginTemplateEditorPath'+id).html();
    path = path.substr(0,path.lastIndexOf('/'))+'/pictures/preview.jpg';
    document.getElementById('pluginTemplateEditorPic').src = path;
    $('.pluginTemplateEditorChooser').removeClass('hidden');
}
function chooseTemplate(maxId){
    var templateId = null;
    for(var i=0;i<=maxId;i++){
        try{
            if(document.getElementById('pluginTemplateEditorTemplate'+i).className == 'pluginTemplateEditorTemplate active'){
                templateId = i;
            }
        }catch(ex){}
    }
    if(templateId == null){
        alert('error');
    }else{
        $.ajax({
            type: 'POST',
            url: 'plugins/templates/choose.php',
            data: 'path='+$('#pluginTemplateEditorPath'+templateId).html(),
            success: function(data) {
                if(data == ''){
                    window.setTimeout("updateAllPlugins('plugins/settings')",10);
                    window.setTimeout("reloadLocation('showPlugins')",500);
                }else{
                    alert(data);
                }
            }
        });
    }
}