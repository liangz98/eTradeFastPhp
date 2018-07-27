<?php

class Zend_View_Helper_ShowOrderContract4Factoring extends Shop_View_Helper {

    function ShowOrderContract4Factoring($Arr, $accountID, $key, $client, $hasIDCertificate) {
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

                $IMG .= '<div class="col-xs-8">';
                $IMG .= '<em class="new_upicon" style="background:url(\'/ky/ico/pdf.png\') no-repeat;background-size: 20px;"></em>';
                $IMG .= '<p class="new_uptitle">' . $v['contractName'] . '</p>';
                $IMG .= '</div>';
                $IMG .= '<input type="hidden" value="' . $v['attachType'] . '" />';

                $IMG .= '<input type="hidden" id="contractAttachUrl_'.$v['contractID'].'" value="' . $pdfUrl . '">';
                $IMG .= '<input type="hidden" id="contractID_'.$v['contractID'].'" value="'.$v['contractID'].'" />';
                $IMG .= '<input type="hidden" id="contractName_'.$v['contractID'].'" value="'.$v['contractName'].'" />';
                $IMG .= '<input type="hidden" id="ext_'.$v['contractID'].'" value="'.$v['ext'].'" />';

                // 是否企业签
                $isESigned = True;  // 企业是否已经签了
                $isPSigned = True;  // 个人是否已经签了

                if ($v['firstParty'] != null && $v['firstParty'] == $accountID) {
                    // 企业
                    if ($v['firstPartySignMode'] == "E" || $v['firstPartySignMode'] == "B") {
                        if ($v['firstPartySigningDate'] == null) {
                            $isESigned = false;
                        }
                    }

                    // 个人
                    if ($v['firstPartySignMode'] == "P" || $v['firstPartySignMode'] == "B") {
                        // 签署人是否当前用户
                        if ($this->view->userID == $v['firstPartyPrincipal']) {
                            // 是否未签
                            if ($v['firstPartyPrincipalSigningDate'] == null) {
                                $isPSigned = false;
                            }
                        }
                    }
                } elseif ($v['secondParty'] != null && $v['secondParty'] == $accountID) {
                    // 企业
                    if ($v['secondPartySignMode'] == "E" || $v['secondPartySignMode'] == "B") {
                        if ($v['secondPartySigningDate'] == null) {
                            $isESigned = false;
                        }
                    }

                    // 个人
                    if ($v['secondPartySignMode'] == "P" || $v['secondPartySignMode'] == "B") {
                        // 签署人是否当前用户
                        if ($this->view->userID == $v['secondPartyPrincipal']) {
                            // 是否未签
                            if ($v['secondPartyPrincipalSigningDate'] == null) {
                                $isPSigned = false;
                            }
                        }
                    }
                } elseif ($v['thirdParty'] != null && $v['thirdParty'] == $accountID) {
                    // 企业
                    if ($v['thirdPartySignMode'] == "E" || $v['thirdPartySignMode'] == "B") {
                        if ($v['thirdPartySigningDate'] == null) {
                            $isESigned = false;
                        }
                    }

                    // 个人
                    if ($v['thirdPartySignMode'] == "P" || $v['thirdPartySignMode'] == "B") {
                        // 签署人是否当前用户
                        if ($this->view->userID == $v['thirdPartyPrincipal']) {
                            // 是否未签
                            if ($v['thirdPartyPrincipalSigningDate'] == null) {
                                $isPSigned = false;
                            }
                        }
                    }
                }

                $IMG .= '<input type="hidden" id="isESigned_'.$v['contractID'].'" value="'.$isESigned.'" />';
                $IMG .= '<input type="hidden" id="isPSigned_'.$v['contractID'].'" value="'.$isPSigned.'" />';

                $IMG .= '<div class="col-xs-2">';
                // 电子签
                if ($v['isEContract']) {
                    if (!$isESigned || !$isPSigned) {
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr">签署</a>';
                    } else {
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">已签署</a>';
                    }
                } else {
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
                $IMG .= '</div>';
            } else {
                $IMG .= '<div class="col-xs-10 col-md-offset-2">';
                $IMG .= '<label>暂无数据！</label>';
                $IMG .= '</div>';
            }
        }
        $IMG .= '<input type="hidden" id="hasNoEContract" value="'.$hasNoEContract.'" />';
        return $IMG;
    }

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
}
