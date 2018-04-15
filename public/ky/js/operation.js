/**
 * Created by lenovo on 2016/11/22.
 */
function doDelete(AcctID,name){
    //询问框
    layer.confirm('<?php echo $this->translate('is_delete');?>', {
        title:false,
        btn: ['<?php echo $this->translate('yes');?>','<?php echo $this->translate('no');?>'] //按钮
    }, function(){
        $.post( '<?php echo $this->BaseUrl();?>/'+name+'/delete',
            {'AcctID':AcctID},
            function(data){
                if( data == true){
                    layer.msg('<?php echo $this->translate('delete_y');?>', {icon: 2});
                    location.href = '<?php echo $this->BaseUrl();?>/'+name+'/';
                }
                else{
                    layer.msg('<?php echo $this->translate('delete_n');?>', {icon: 2});
                    location.href = '<?php echo $this->BaseUrl();?>/'+name+'/';
                }
            },"json");

    }, function(){
        layer.closeAll();
    });
}