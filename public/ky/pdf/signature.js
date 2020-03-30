// *************************** PDF Sign View ********************************
PDFJS.workerSrc = '/ky/pdf/pdf.worker.js';

let pdfDoc = null;
let pageNum = 1;
let pageRendering = false;
let pageNumPending = null;
let scale = 1.5;
let $downloadBtn = $(".download-button"),
    $signBtn = $(".sign-button"),
    $personSignBtn = $(".person-sign-button"),
    $signSendAuthCodeBtn = $("#signSendAuthCodeBtn");

function renderPage(num, id) {
    let canvas = document.getElementById(id);
    let ctx = canvas.getContext('2d');
    pageRendering = true;
    pdfDoc.getPage(num).then(function(page) {
        let viewport = page.getViewport(scale);
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        let renderContext = {
            canvasContext: ctx,
            viewport: viewport
        };
        let renderTask = page.render(renderContext);

        renderTask.promise.then(function() {
            pageRendering = false;
            if (pageNumPending !== null) {
                // New page rendering is pending
                renderPage(pageNumPending);
                pageNumPending = null;
            }
        });
    });

    //document.getElementById('page_num').textContent = pageNum;
}

function queueRenderPage(num) {
    if (pageRendering) {
        pageNumPending = num;
    } else {
        renderPage(num);
    }
}

function onPrevPage() {
    if (pageNum <= 1) {
        return;
    }
    pageNum--;
    queueRenderPage(pageNum);
}
//document.getElementById('prev').addEventListener('click', onPrevPage);

function onNextPage() {
    if (pageNum >= pdfDoc.numPages) {
        return;
    }
    pageNum++;
    queueRenderPage(pageNum);
}

function initPdfView(pdfUrl, obj) {
    $("#pdfPageBox").html("");
    PDFJS.getDocument(pdfUrl).then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        //document.getElementById('page_count').textContent = pdfDoc.numPages;
        for (let i=1;i<=pdfDoc.numPages;i++){
            let canvasDiv = $("<div style=\"padding-top:10px;padding-bottom:10px;\"></div>");
            let pageNum = $("<div style=\"background-color: #999999;position:absolute;float: left;height:30px;width:60px;text-align: center;line-height: 30px;color:#ffffff;opacity: 0.6;\">"+i+"/"+pdfDoc.numPages+"</div>");
            let canvas = $("<canvas></canvas>").attr("id", "the-canvas_"+i);
            canvasDiv.append(pageNum);
            canvasDiv.append(canvas);
            $("#pdfPageBox").append(canvasDiv);
            renderPage(i, "the-canvas_"+i);
        }
    });

    layer.open({
        type: 1,	//Page层类型
        area: ['1150px', '100%'],
        title: signViewTitle,
        shadeClose: true,
        shade: 0.6,	//遮罩透明度
        // anim: 1,	//0-6的动画形式，-1不开启
        scrollbar: false,
        content: $("#signViewDiv")
    });

    $downloadBtn.attr("contractID_", obj.id);

    if ($("#isESigned_" + obj.id).val() === '1') {
        $signBtn.hide();
    } else {
        $signBtn.show().attr("contractID_", obj.id);
    }

    if ($("#isPSigned_" + obj.id).val() === '1') {
        $personSignBtn.hide();
    } else {
        $personSignBtn.show().attr("contractID_", obj.id);
    }
}

//document.getElementById('prev').addEventListener('click', onPrevPage);

$downloadBtn.bind("click",function(){
    let contractID_ = $(this).attr("contractID_");
    window.location.href = $("#contractAttachUrl_" + contractID_).val();
});

// ********************   获取验证手机号码 Start  *************************************
$signBtn.bind("click",function(){
    let getSignMobile = '/user/pur/getsignmobile';
    $.ajax({
        type: "POST",
        url: getSignMobile,
        data: {

        },
        dataType: "json",
        success: function (data) {
            $("#signMobileInput").val(mobileStr + " " +data);
        }
    });

    // 企业实名认证
    let layerHeight = '420px';
    if (hasIDCertificate === '1') {
        $(".signContent").removeClass("hidden");
        $(".jumpInfoContent").addClass("hidden");
        $(".jumpPersonInfoContent").addClass("hidden");
        layerHeight = '420px';
    } else {
        $(".signContent").addClass("hidden");
        $(".jumpInfoContent").removeClass("hidden");
        $(".jumpPersonInfoContent").addClass("hidden");
        layerHeight = '220px';
    }

    layer.open({
        type: 1,
        title: signViewTitle,
        shadeClose: true,
        shade: 0.2,
        area: ['500px', layerHeight],
        content: $('#doSignPDFLayDiv')
    });
});

$personSignBtn.bind("click",function(){
    let getSignMobile = '/user/pur/getpersonsignmobile';
    $.ajax({
        type: "POST",
        url: getSignMobile,
        data: {

        },
        dataType: "json",
        success: function (data) {
            $("#signMobileInput").val(mobileStr + " " +data);
        }
    });

    // 个人实名认证
    let layerHeight = '420px';
    if (contactHasIDCertificate === '1') {
        $(".signContent").removeClass("hidden");
        $(".jumpInfoContent").addClass("hidden");
        $(".jumpPersonInfoContent").addClass("hidden");
        layerHeight = '420px';
    } else {
        $(".signContent").addClass("hidden");
        $(".jumpInfoContent").addClass("hidden");
        $(".jumpPersonInfoContent").removeClass("hidden");
        layerHeight = '220px';
    }

    layer.open({
        type: 1,
        title: signViewTitle,
        shadeClose: true,
        shade: 0.2,
        area: ['500px', layerHeight],
        content: $('#doSignPDFLayDiv')
    });
    $signSendAuthCodeBtn.attr("personSend", "true");
    $("#singConfirmBtn").attr("personSend", "true");
});
// ********************   End  *************************************

// ********************   获取短信验证码 Start  *************************************
$signSendAuthCodeBtn.click(function () {
    let sendSignAuthCode = '/user/pur/sendsignauthcode';
    if ($(this).attr("personSend") === "true") {
        sendSignAuthCode = '/user/pur/sendpersonsignauthcode';
    }

    $.ajax({
        type: "POST",
        url: sendSignAuthCode,
        data: {

        },
        dataType: "json",
        success: function (data) {
            $signSendAuthCodeBtn.text(sendingStr);
            sendSignAuthCodeCountDown();
        }
    });
});

let countdown=120;
function sendSignAuthCodeCountDown() {
    setTimeSend($signSendAuthCodeBtn);
}

function setTimeSend(obj) { //发送验证码倒计时
    if (countdown === 0) {
        obj.attr('disabled',false);
        obj.removeClass('layui-btn-disabled');
        obj.text(signSendAuthCodeStr);
        countdown = 120;
        return;
    } else {
        console.log(countdown);
        obj.attr('disabled',true);
        obj.addClass('layui-btn-disabled');
        obj.text(signAuthCodeSendingStr + "(" + countdown + ")");
        countdown--;
    }
    setTimeout(function() {
            settime(obj) }
        ,1000)
}
// ********************   获取短信验证码 End  *************************************



// ********************   获取短信验证码 Start  *************************************
$("#singConfirmBtn").click(function () {
    let doSignPDF = '/user/pur/dosignpdf';
    let contractID = $signBtn.attr("contractID_");
    if ($(this).attr("personSend") === "true") {
        doSignPDF = '/user/pur/dopersonsignpdf';
        contractID = $personSignBtn.attr("contractID_");
    }

    $.ajax({
        type: "POST",
        url: doSignPDF,
        data: {
            contractID: contractID,
            signAuthCode: $("#signAuthCode").val()
        },
        dataType: "json",
        success: function (data) {
            let signMsg = signFailStr;
            if (data != null && data !== '') {
                signMsg = signSuccessStr;
                layer.msg(signMsg);
                window.location.reload();
            } else {
                $("#signAuthCode").val('');
                layer.msg(signMsg);
            }
        }
    });
});
// ********************   获取短信验证码 End  *************************************


// ********************   显示非网签上传合同层 Start  *************************************
function initSignViewNoEContract(obj) {

    layer.open({
        type: 1,	//Page层类型
        area: ['800px', '550px'],
        title: signViewTitle,
        // shadeClose: true,
        shade: 0.6,	//遮罩透明度
        // anim: 1,	//0-6的动画形式，-1不开启
        scrollbar: false,
        content: $("#signViewNoEContractDiv")
    });


    $("#signViewNoEContractDiv tr:not(:first)").empty();
    //获取最后一行的data-id(标识行)
    let $signViewNoEContractDiv = $("#signViewNoEContractDiv tr:last"),
        rowIndex = $signViewNoEContractDiv.attr("data-row");
    if (rowIndex === "" || rowIndex == null) {
        rowIndex = parseInt(1);
    } else {
        rowIndex = parseInt(rowIndex) + 1;
    }

    let htmlList = '<tr data-row=' + rowIndex + '>';

    htmlList += '<td><a href="#" id="" onclick="doDownload(\''+obj.id+'\')">' + $("#contractName_" + obj.id).val() + '</a></td>';

    htmlList += '<td>' + $("#ext_" + obj.id).val() + '</td>';

    htmlList += '<td><a href="#" id="" onclick="doDownload(\''+obj.id+'\')" class="order_contract_sign fr">下载</a></td>';

    htmlList += '</td></tr>';
    //在行最后添加数据
    $signViewNoEContractDiv.after(htmlList);

    $("#signNoEContractConfirmBtn").attr("contractID_", obj.id);
}

function doDownload(objID) {
    window.location.href = $("#contractAttachUrl_" + objID).val();
}
// ********************   显示非网签上传合同层 End  *************************************


// ********************   非网签签署合同提交按钮 Start  *************************************
$("#signNoEContractConfirmBtn").click(function () {
    let contractID = $(this).attr("contractID_");

    let nidArr = "";
    let nid = document.getElementsByName("attachNid[]");
    for (let i = 0, j = nid.length; i < j; i++) {
        nidArr += nid[i].value + "|";
    }
    let nameArr = "";
    let name = document.getElementsByName("attachName[]");
    for (let i = 0, j = name.length; i < j; i++) {
        nameArr += name[i].value + "|";
    }
    let sizeArr = "";
    let size = document.getElementsByName("attachSize[]");
    for (let i = 0, j = size.length; i < j; i++) {
        sizeArr += size[i].value + "|";
    }
    let attachTypeArr = "";
    let attachType = document.getElementsByName("attachType[]");
    for (let i = 0, j = attachType.length; i < j; i++) {
        attachTypeArr += attachType[i].value + "|";
    }

    $.ajax({
        type: "POST",
        url: '/user/sale/agree',
        data: {
            contractID: contractID,
            name: nameArr,
            nid: nidArr,
            size: sizeArr,
            attachType: attachTypeArr
        },
        dataType: "json",
        success: function (data) {
            if (data === '1') {
                document.location.reload();
            } else {
                alert(data + 'failed,please try again!');
            }
        }
    });
});
// ********************   非网签签署合同提交按钮 End  *************************************
