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
                
                // 是否企业签
                $needESign = false; // 是否需要企业签
                $isESigned = false; // 企业是否已经签了
                $needPSign = false; // 是否需要个人签
                $isPSigned = false;   // 个人是否已经签了
                $isPartPrincipal = false;   // 当前用户是否可以签
                
                print_r("accountID: ".$accountID.'<br />');
                print_r("userID: ".$this->view->userID.'<br />');
                
                if ($v['firstParty'] != null && $v['firstParty'] == $accountID) {
                    print_r("in First".'<br />');
                    // 企业
                    if ($v['firstPartySignMode'] == "E" || $v['firstPartySignMode'] == "B") {
                        if ($v['firstPartySigningDate'] != null) {
                            $isESigned = true;
                        }
                    }
    
                    // 个人
                    if ($v['firstPartySignMode'] == "P" || $v['firstPartySignMode'] == "B") {
                        // 签署人是否当前用户
                        if ($this->view->userID == $v['firstPartyPrincipal']) {
                            // 是否未签
                            if ($v['firstPartyPrincipalSigningDate'] != null) {
                                $isPSigned = true;
                            }
                        }
                    }
                } elseif ($v['secondParty'] != null && $v['secondParty'] == $accountID) {
                    print_r("in Second");
                    // 企业
                    if ($v['secondPartySignMode'] == "E" || $v['secondPartySignMode'] == "B") {
                        if ($v['secondPartySigningDate'] != null) {
                            $isESigned = true;
                        }
                    }
    
                    // 个人
                    if ($v['secondPartySignMode'] == "P" || $v['secondPartySignMode'] == "B") {
                        // 签署人是否当前用户
                        if ($this->view->userID == $v['secondPartyPrincipal']) {
                            // 是否未签
                            if ($v['secondPartyPrincipalSigningDate'] != null) {
                                $isPSigned = true;
                            }
                        }
                    }
                } elseif ($v['thirdParty'] != null && $v['thirdParty'] == $accountID) {
                    print_r("in Third");
                    // 企业
                    if ($v['thirdPartySignMode'] == "E" || $v['thirdPartySignMode'] == "B") {
                        if ($v['thirdPartySigningDate'] != null) {
                            $isESigned = true;
                        }
                    }
    
                    // 个人
                    if ($v['thirdPartySignMode'] == "P" || $v['thirdPartySignMode'] == "B") {
                        // 签署人是否当前用户
                        if ($this->view->userID == $v['thirdPartyPrincipal']) {
                            // 是否未签
                            if ($v['thirdPartyPrincipalSigningDate'] != null) {
                                $isPSigned = true;
                            }
                        }
                    }
                }
    
                print_r("isESigned: ".$isESigned.'<br />');
                print_r("isPSigned: ".$isPSigned.'<br />');
                print_r("so: ".(!$isESigned || !$isPSigned).'<br />');
    
                // 电子签
                if ($v['isEContract']) {
                    print_r("in EContract".'<br />');
                    if (!$isESigned || !$isPSigned) {
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr">签署</a>';
                    } else {
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">已签署</a>';
                    }
                } else {
                    print_r("in not EContract".'<br />');
                    $hasNoEContract = "True";
                    // 非网签未签署
                    if (($v['firstParty'] != null && $v['firstParty'] == $accountID && $v['firstPartySigningDate'] == null) || ($v['secondParty'] != null && $v['secondParty'] == $accountID && $v['secondPartySigningDate'] == null) || ($v['thirdParty'] != null && $v['thirdParty'] == $accountID && $v['thirdPartySigningDate'] == null)) {
                        $hasNoEContract = "True";
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initSignViewNoEContract(this)" class="order_contract_sign fr">下载</a>';
                    } else {
                        $IMG .= '<input type="hidden" id="isSign_' . $v['contractID'] . '" value="true">';
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">已签署</a>';
                    }
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