 //全局变量，触摸开始位置
            var startX = 0, startY = 0;
            
            //touchstart事件
            function touchSatrtFunc(evt) {
                try
                {
                    // evt.preventDefault(); //阻止触摸时浏览器的缩放、滚动条滚动等

                    var touch = evt.touches[0]; //获取第一个触点
                    startX = touch.pageX;
                    startY = touch.pageY;
                }
                catch (e) {
                    alert('touchSatrtFunc：' + e.message);
                }
            }

            //touchmove事件，这个事件无法获取坐标
            function touchMoveFunc(evt) {
                try
                {
                    evt.preventDefault(); //阻止触摸时浏览器的缩放、滚动条滚动等
                    var touch = evt.touches[0]; //获取第一个触点
                    var x = touch.pageX; //页面触点X坐标
                    var y = touch.pageY; //页面触点Y坐标
                    
                    var text = 'TouchMove事件触发：（' + x + ', ' + y + '）';

                    if (y - startY < -100) {
                        document.getElementById("czlwelcomeid").className="czlwelcome animgalpha2";
                        document.getElementById("bodyid").className="animgalpha";
                        bindEvent(1);
                    }
                }
                catch (e) {
                    alert('touchMoveFunc：' + e.message);
                }
            }

            //touchend事件
            function touchEndFunc(evt) {
                try {
                    evt.preventDefault(); //阻止触摸时浏览器的缩放、滚动条滚动等
                    bindEvent(0);
                }
                catch (e) {
                    alert('touchEndFunc：' + e.message);
                }
            }
            //绑定事件
            function bindEvent(f) {
                if(f==1){
                    document.removeEventListener('touchstart', touchSatrtFunc, false);
                    document.removeEventListener('touchmove', touchMoveFunc, false);
                    document.removeEventListener('touchend', touchEndFunc, false);
                }else{
                    document.addEventListener('touchstart', touchSatrtFunc, false);
                    document.addEventListener('touchmove', touchMoveFunc, false);
                    document.addEventListener('touchend', touchEndFunc, false);
                }
            }
            //判断是否支持触摸事件
            function isTouchDevice() {
                try {
                    document.createEvent("TouchEvent");
                    //alert("支持TouchEvent事件！");
                    bindEvent(); //绑定事件
                }
                catch (e) {
                    //alert("不支持TouchEvent事件！" + e.message);
                }
            }
            window.onload = isTouchDevice;