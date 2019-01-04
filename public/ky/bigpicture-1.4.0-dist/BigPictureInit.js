$(function () {
    // 初始化 BigPicture
    function setClickHandlerByClass(className, fn) {
        var elements = document.getElementsByClassName(className);
        for(var i = 0;i < elements.length;i++){
            elements[i].onclick = fn;
        }
    }
    // 初始化 BigPicture
    setClickHandlerByClass('img-view', function (e) {
        var className = e.target.className,
            eDataTypeImg = true;

        // console.log("className: " + className);
        // console.log("tagName: " + e.target.tagName);
        // console.log(e.target.parentElement.parentElement.getAttribute("data-cls-name"));

        // 判断PDF的时候, 触发下载事件
        if (e.target.getAttribute("data-type") && e.target.getAttribute("data-type") === 'pdf') {
            eDataTypeImg = false;
            var elem_child = e.target.nextSibling.childNodes;
            for (var i = 0; i < elem_child.length; i++) {
                if (elem_child[i].nodeName === "A" && elem_child[i].getAttribute("data-type") === 'download') {
                    // console.log(elem_child[i]);
                    elem_child[i].click();
                }
            }
        }

        // 定义图片列表
        var galleryName = e.target.parentElement.parentElement.getAttribute("data-cls-name");
        (e.target.tagName === 'IMG' || e.target.className === 'background-image') && eDataTypeImg &&
        BigPicture({
            el: e.target,
            gallery: '.'+galleryName
        });
        // 视频
        ~className.indexOf('htmlvid') &&
        BigPicture({
            el: e.target,
            vidSrc: e.target.getAttribute('vidSrc')
        });
    });
});
