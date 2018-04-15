/**
 * Created by lenovo on 2016/11/9.
 */
$(function () {
    $('#pagekey').change(function(){
        var pgkey=$('#pagekey').val();
        var stkey=$('#pagestr').val();
        if($("#pageid").length>0){
        var pageid=$('#pageid').val();
        }else{
            pageid="";
        }
        var urlkey=stkey+"-"+pgkey+".html"+pageid;
        $('#gokey').attr("href",urlkey);
    })
});