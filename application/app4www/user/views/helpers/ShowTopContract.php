<?php

class Zend_View_Helper_ShowTopContract extends Shop_View_Helper {
    
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
            echo "in".is_array($v); exit;
    
            // if (is_object($valueObject)) {
            //     $attachmentList = $valueObject->attachmentList;
            // } else if (is_array($valueObject)) {
            //     $attachmentList = $valueObject["attachmentList"];
            // }
            
            // var_dump($valueObject); exit;
            
            if (count($v['attachmentList']) > 0) {
                $attachmentList = $v['attachmentList'];
                $pdfUrl = $this->view->seed_Setting['KyUrlex'] . '/doc/download.action?sid=' . session_id() . '&nid=' . $attachmentList[0]['attachID'] . '&vid=' . $attachmentList[0]['verifyID'];
    
                //if(!empty($client)&&$accountID!=$client){continue;}
                $IMG .= '<li style="width: 100%;float: left;margin: 10px 0;"><em class="new_upicon" style="background:url(\'/ky/ico/pdf.png\') no-repeat;background-size: 20px;"></em>';
                $IMG .= '<p class="new_uptitle">' . $v['contractName'] . '</p>';
                // $IMG .= '<div class="new_xian"><p class="new_desc">' . $v['description '] . '</p></div>';
                $IMG .= '<input type="hidden" value="' . $v['attachType'] . '" />';
    
    
                // $IMG .= '<a href="';
                // $IMG .= $this->view->seed_Setting['KyUrlex'].'/doc/download.action?sid='.session_id().'&nid='.$attachmentList[0]['attachID'];
                // $IMG .='&vid='.$attachmentList[0]['verifyID'].'" download>下载</a></span>';
    
                $IMG .= '<input type="hidden" id="contractAttachUrl_'.$v['contractID'].'" value="' . $pdfUrl . '">';
                $IMG .= '<input type="hidden" id="contractID_'.$v['contractID'].'" value="'.$v['contractID'].'" />';
                $IMG .= '<input type="hidden" id="contractName_'.$v['contractID'].'" value="'.$v['contractName'].'" />';
                $IMG .= '<input type="hidden" id="ext_'.$v['contractID'].'" value="'.$v['ext'].'" />';
    
                if (!$v['isDeleted'] && !$v['isCancel'] && !$v['isClosed']) {
                    if (($v['firstParty'] != null && $v['firstParty'] == $accountID && $v['firstPartySigningDate'] == null) || ($v['secondParty'] != null && $v['secondParty'] == $accountID && $v['secondPartySigningDate'] == null) || ($v['thirdParty'] != null && $v['thirdParty'] == $accountID && $v['thirdPartySigningDate'] == null)) {
                        if (!$hasIDCertificate) {
                            $IMG .= '<a href="#" onclick="contractSignViewNO(\'' . $v['contractID'] . '\')" class="order_contract_sign fr">签署</a>';
                        } else {
                            if ($v['isEContract']) {
                                $IMG .= '<a href="#" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr">签署</a>';
                            } else {
                                $hasNoEContract = "True";
                                $IMG .= '<a href="#" id="' . $v['contractID'] . '" onclick="initSignViewNoEContract(this)" class="order_contract_sign fr">下载</a>';
                            }
                        }
                    }
                    if (!$v['isDeleted'] && !$v['isCancel'] && !$v['isClosed']) {
                        if (($v['firstParty'] != null && $v['firstParty'] == $accountID && $v['firstPartySigningDate'] != null) || ($v['secondParty'] != null && $v['secondParty'] == $accountID && $v['secondPartySigningDate'] != null) || ($v['thirdParty'] != null && $v['thirdParty'] == $accountID && $v['thirdPartySigningDate'] != null)) {
                
                            $IMG .= '<input type="hidden" id="isSign_' . $v['contractID'] . '" value="true">';
                            $IMG .= '<a href="#" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">已签署</a>';
                        }
                    }
                    $IMG .= '</li>';
        
                }
            }
        }
        $IMG .= '<input type="hidden" id="hasNoEContract" value="'.$hasNoEContract.'" />';
        return $IMG;
    }
}