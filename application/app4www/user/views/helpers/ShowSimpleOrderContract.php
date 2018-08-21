<?php

class Zend_View_Helper_ShowSimpleOrderContract extends Shop_View_Helper {

    /**
     * @param $contract // 合同对象
     * @param $accountID // 登录公司ID
     * @return string // 返回button
     */
    function ShowSimpleOrderContract($contract, $accountID) {
        $result = '';
        $hasNoEContract = "False";

        if (is_object($contract)) {
            $contract = $this->objectToArray($contract);
        }

        if (count($contract['attachmentList']) > 0) {
            $attachmentList = $contract['attachmentList'];
            $pdfUrl = $this->view->seed_Setting['KyUrlex'] . '/doc/download.action?sid=' . session_id() . '&nid=' . $attachmentList[0]['attachID'] . '&vid=' . $attachmentList[0]['verifyID'];

            $result .= '<input type="hidden" id="contractAttachUrl_'.$contract['contractID'].'" value="' . $pdfUrl . '">';
            $result .= '<input type="hidden" id="contractID_'.$contract['contractID'].'" value="'.$contract['contractID'].'" />';
            $result .= '<input type="hidden" id="contractName_'.$contract['contractID'].'" value="'.$contract['contractName'].'" />';
            $result .= '<input type="hidden" id="ext_'.$contract['contractID'].'" value="'.$attachmentList[0]['ext'].'" />';

            // 是否企业签
            $isESigned = True;  // 企业是否已经签了
            $isPSigned = True;  // 个人是否已经签了

            if ($contract['firstParty'] != null && $contract['firstParty'] == $accountID) {
                // 企业
                if ($contract['firstPartySignMode'] == "E" || $contract['firstPartySignMode'] == "B") {
                    if ($contract['firstPartySigningDate'] == null) {
                        $isESigned = false;
                    }
                }

                // 个人
                if ($contract['firstPartySignMode'] == "P" || $contract['firstPartySignMode'] == "B") {
                    // 签署人是否当前用户
                    if ($this->view->userID == $contract['firstPartyPrincipal']) {
                        // 是否未签
                        if ($contract['firstPartyPrincipalSigningDate'] == null) {
                            $isPSigned = false;
                        }
                    }
                }
            }

            $result .= '<input type="hidden" id="isESigned_'.$contract['contractID'].'" value="'.$isESigned.'" />';
            $result .= '<input type="hidden" id="isPSigned_'.$contract['contractID'].'" value="'.$isPSigned.'" />';

            // 电子签
            if ($contract['isEContract']) {
                if (!$isESigned || !$isPSigned) {
                    $result .= '<a href="javascript:void(0)" id="' . $contract['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="btn btn-warning btn-sm order_contract_sign fr">签署</a>';
                } else {
                    if ($contract['firstParty'] != null && $contract['firstParty'] == $accountID) {
                        // 个人
                        if ($contract['firstPartySignMode'] == "P" || $contract['firstPartySignMode'] == "B") {
                            if ($this->view->userID == $contract['firstPartyPrincipal']) {
                                $result .= '<a href="javascript:void(0)" id="' . $contract['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="btn btn-warning order_contract_sign fr">已申请</a>';
                            }
                        } else {
                            $result .= '<a href="javascript:void(0)" id="' . $contract['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="btn btn-warning order_contract_sign fr">已申请</a>';
                        }
                    }

                }
            } else {
                $hasNoEContract = "True";
                // 非网签未签署
                if ($contract['firstParty'] != null && $contract['firstParty'] == $accountID && $contract['firstPartySigningDate'] == null) {
                    $hasNoEContract = "True";
                    $result .= '<a href="javascript:void(0)" id="' . $contract['contractID'] . '" onclick="initSignViewNoEContract(this)" class="btn btn-warning order_contract_sign fr">下载</a>';
                } else {
                    $result .= '<input type="hidden" id="isSign_' . $contract['contractID'] . '" value="true">';
                    $result .= '<a href="javascript:void(0)" id="' . $contract['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="btn btn-warning order_contract_sign fr">已签署</a>';
                }
            }
        } else {
            $result .= '<span>未开始</span>';
        }


        $result .= '<input type="hidden" id="hasNoEContract" value="'.$hasNoEContract.'" />';
        return $result;
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
