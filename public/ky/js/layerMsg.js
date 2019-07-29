/**
 * 弹窗提示, 配合
 * @param content
 * @param title
 */
function showUploadMsg(content, title) {
    var defaultTitle = '操作失败';
    if (title === undefined || title === '') {
        title = defaultTitle;
    }
    layer.open({
        type: 1
        ,title: false //不显示标题栏
        ,closeBtn: false
        ,area: '500px;'
        ,shade: 0.8
        ,id: 'LAY_layDoAuthConfirm' //设定一个id，防止重复弹出
        ,btn: ['确认']
        ,btnAlign: 'c'
        ,moveType: 1 //拖拽模式，0或者1
        ,content: '<div style="padding: 50px; line-height: 22px; background-color: #eee; font-weight: 300;"><h3>' + title + '！</h3><br><p>' + content + '</p></div>'
    });
}
