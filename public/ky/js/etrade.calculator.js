//var BFCY_SUPPLIER = "supplier";
//var BFCY_VENDOR = "vendor";
//var BFCY_BUYER = "buyer"
//var ADVANCE_SERVICE_BY_DEFAULT = "D";
//var ADVANCE_SERVICE_BY_DECLARATION_AMOUNT = "P";
//var ADVANCE_SERVICE_BY_INVOICE_AMOUNT_INCL_TAX = "T";
//var ADVANCE_SERVICE_BY_INVOICE_AMOUNT = "V";
//
//function getExRate(form, to, bizType, bizID) {
//	var exRate;
//	var data_ = {};
//	var url = "/cache!getBizExRate.action";//此次应该改为PHP ajax请求的URL。
//	if(bizType){
//		data_["bizType"] = bizType;
//	}
//	if(bizID){
//		data_["bizID"] = bizID;
//	}
//	data_["formCrnCode"] = form;
//	data_["toCrnCode"] = to;
//	$.ajax({
//		async : false,
//		type : 'post',
//		dataType : 'json',
//		contentType : "application/json",
//		data : $.toJSON(data_),
//		url : url,
//		error : function(xhr, textStatus, throwError) {
//			alert("error");
//		},
//		success : function(data) {
//			exRate = data.exRate;
//		}
//	});
//	return exRate;
//}

/**
 * 该接口只适用于 计价方式 按 增值税开票金额 的情况下，通过开票金额反算PO金额（即拟报关金额）
 * @param buyer          买家ID
 * @param bCrnCode       买家货币
 * @param vendor         卖家ID
 * @param vCrnCode       卖家货币
 * @param supplier       供应商ID
 * @param sCrnCode       供应商货币
 * @param pCrnCode       采购金额货币
 * @param bfcy           退税受益人ID，即当前用户的AccountID
 * @param amont          发票总金额
 * @param taxRate        商品增值税率
 * @param rebateRate     商品出口退税率
 */
function calPriceByInvoicePrice(buyer, bCrnCode, vendor, vCrnCode, supplier, sCrnCode, pCrnCode, bfcy, amont, taxRate, rebateRate) {
	try {
		var totalPrice = amont;// 销售总价
		//var advanceServiceType = ADVANCE_SERVICE_BY_DEFAULT;
		var advanceServiceRate = 0.03;
		var exRate = 1;
		if (bfcy == BFCY_SUPPLIER || (bfcy == BFCY_VENDOR && supplier == vendor)) {
			exRate = getExRate(bCrnCode, pCrnCode);
			// 默认按退税金额比例
			totalPrice = arithRound(arithDiv(arithMul(totalPurPrice, arithSub(1, arithDiv(arithSub(rebateRate, arithMul(rebateRate, advanceServiceRate)), arithAdd(1, taxRate)))), exRate), 2);
		} else {
			exRate = getExRate(bCrnCode, pCrnCode);
			totalPrice = arithRound(arithDiv(totalPurPrice, exRate), 2);
		}
	} catch (e) {
		alert(e);
	}
}
