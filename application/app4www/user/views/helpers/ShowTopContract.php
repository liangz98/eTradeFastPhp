<?php

class Zend_View_Helper_ShowTopContract extends Shop_View_Helper {
    
    //数组转对象
    public function arrayToObject($e) {
        if (gettype($e) != 'array')
            return false;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object')
                $e[$k] = (object)arrayToObject($v);
        }
        return (object)$e;
    }
    
    //对象转数组
    public function objectToArray($e) {
        $e = (array)$e;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'resource')
                return false;
            if (gettype($v) == 'object' || gettype($v) == 'array')
                $e[$k] = (array)$this->objectToArray($v);
        }
        return $e;
    }
    
    function ShowTopContract($Arr, $accountID, $key, $client, $hasIDCertificate) {
        //$accountID;
        //$client;
        //$Arr; 附件数组
        //$key; 查询key
        // $hasIDCertificate 当前企业是否已经获取到电子证书
        $IMG = '';
        // $type = 'PDF';
    
        $hasNoEContract = "False";

        foreach ($Arr as $k => $v) {
            if (is_object($v)) {
                $v = $this->objectToArray($v);
            }
            
            if (count($v['attachmentList']) > 0) {
                $attachmentList = $v['attachmentList'];
                $pdfUrl = $this->view->seed_Setting['KyUrlex'] . '/doc/download.action?sid=' . session_id() . '&nid=' . $attachmentList[0]['attachID'] . '&vid=' . $attachmentList[0]['verifyID'];
    
                $IMG .= '<li style="width: 100%;float: left;margin: 10px 0;"><em class="new_upicon" style="background:url(\'/ky/ico/pdf.png\') no-repeat;background-size: 20px;"></em>';
                $IMG .= '<p class="new_uptitle">' . $v['contractName'] . '</p>';
                $IMG .= '<input type="hidden" value="' . $v['attachType'] . '" />';
    
                $IMG .= '<input type="hidden" id="contractAttachUrl_'.$v['contractID'].'" value="' . $pdfUrl . '">';
                $IMG .= '<input type="hidden" id="contractID_'.$v['contractID'].'" value="'.$v['contractID'].'" />';
                $IMG .= '<input type="hidden" id="contractName_'.$v['contractID'].'" value="'.$v['contractName'].'" />';
                $IMG .= '<input type="hidden" id="ext_'.$v['contractID'].'" value="'.$v['ext'].'" />';
    
    
                if ($v['isEContract']) {
                    if (!$hasIDCertificate) {
                        $IMG .= '<a href="javascript:void(0)" onclick="contractSignViewNO(\'' . $v['contractID'] . '\')" class="order_contract_sign fr">签署</a>';
                    } else {
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr">签署</a>';
                    }
                } else {
                    $hasNoEContract = "True";
                    $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initSignViewNoEContract(this)" class="order_contract_sign fr">下载</a>';
                }
                $IMG .= '</li>';
            } else {
                $IMG .= '<label>暂无数据！</label>';
            }
        }
        $IMG .= '<input type="hidden" id="hasNoEContract" value="'.$hasNoEContract.'" />';
        return $IMG;
    }
}