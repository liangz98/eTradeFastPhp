/**
 * Created by Administrator on 2017/3/16.
 */
// JavaScript Document
$(function(){
    $(".ofh").bind('mouseover',function(){
        $(this).css("cursor","move")
    });

    var $show = $("#loader"); //进度条
    var $orderlist = $("#orderlist");
    var $list = $("#module_list");

    $list.sortable({
        opacity: 0.6,
        revert: true,
        cursor: 'move',
        handle: '.ofh',
        update: function(){
            var new_order = [];
            $list.children(".list_wrap").each(function() {
                new_order.push(this.title);
            });
            var newid = new_order.join(',');
            var oldid = $orderlist.val();
            $.ajax({
                type: "post",
                url: "/user/index",
                data: { id: newid, order: oldid },   //id:新的排列对应的ID,order：原排列顺序
                beforeSend: function() {
                    $show.html("<img src='/ky/images/load.gif' /> 正在更新");
                },
                success: function(msg) {
                    //alert(msg);
                    $show.html("");
                }
            });
        }
    });
});