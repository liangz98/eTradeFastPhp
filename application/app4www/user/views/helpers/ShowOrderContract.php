<?php

class Zend_View_Helper_ShowOrderContract extends Shop_View_Helper {

    /**
     * @param $contractList // 合同列表
     * @param $accountID    // 公司id
     * @return string
     */
    function ShowOrderContract($contractList, $accountID) {
        $IMG = '';
        $hasNoEContract = "False";

        foreach ($contractList as $k => $v) {
            if (is_object($v)) {
                $v = $this->objectToArray($v);
            }

            if (count($v['attachmentList']) > 0) {
                $attachmentList = $v['attachmentList'];
                $pdfUrl = $this->view->seed_Setting['KyUrlex'] . '/doc/download.action?sid=' . session_id() . '&nid=' . $attachmentList[0]['attachID'] . '&vid=' . $attachmentList[0]['verifyID'];

                $IMG .= '<div class="row">';
                $IMG .= '<div class="col-xs-9 col-md-offset-1">';
                $IMG .= '<em class="new_upicon showPDFIcon"></em>';
                $IMG .= '<p class="new_uptitle">' . $v['contractName'] . '</p>';
                $IMG .= '</div>';

                // 暂时注释
                //$IMG .= '<input type="hidden" value="' . $v['attachType'] . '" />';
                //$IMG .= '<input type="hidden" value="' . $attachmentList[0]['attachType'] . '" />';

                $IMG .= '<input type="hidden" id="contractAttachUrl_'.$v['contractID'].'" value="' . $pdfUrl . '">';
                $IMG .= '<input type="hidden" id="contractID_'.$v['contractID'].'" value="'.$v['contractID'].'" />';
                $IMG .= '<input type="hidden" id="contractName_'.$v['contractID'].'" value="'.$v['contractName'].'" />';
                $IMG .= '<input type="hidden" id="ext_'.$v['contractID'].'" value="'.$attachmentList[0]['ext'].'" />';

                // 是否企业签
                $isESigned = True;  // 企业是否已经签了
                $isPSigned = True;  // 个人是否已经签了

                if (!empty($v['firstParty']) && $v['firstParty'] == $accountID) {
                    // 企业
                    if ($v['firstPartySignMode'] == "E" || $v['firstPartySignMode'] == "B") {
                        if (empty($v['firstPartySigningDate'])) {
                            $isESigned = false;
                            $IMG .= '<input type="hidden" id="isSignedINNN_'.$v['contractID'].'" value="true" />';
                        }
                    }

                    // 个人
                    if ($v['firstPartySignMode'] == "P" || $v['firstPartySignMode'] == "B") {
                        // 签署人是否当前用户
                        if ($this->view->userID == $v['firstPartyPrincipal']) {
                            // 是否未签
                            if (empty($v['firstPartyPrincipalSigningDate'])) {
                                $isPSigned = false;
                            }
                        }
                    }
                }

                if (!empty($v['secondParty']) && $v['secondParty'] == $accountID) {

                    // 企业
                    if ($v['secondPartySignMode'] == "E" || $v['secondPartySignMode'] == "B") {
                        if (empty($v['secondPartySignedDate'])) {
                            $isESigned = false;
                        }
                    }

                    // 个人
                    if ($v['secondPartySignMode'] == "P" || $v['secondPartySignMode'] == "B") {
                        // 签署人是否当前用户
                        if ($this->view->userID == $v['secondPartyPrincipal']) {
                            // 是否未签
                            if (empty($v['secondPartyPrincipalSigningDate'])) {
                                $isPSigned = false;
                            }
                        }
                    }
                }

                if (!empty($v['thirdParty']) && $v['thirdParty'] == $accountID) {
                    // 企业
                    if ($v['thirdPartySignMode'] == "E" || $v['thirdPartySignMode'] == "B") {
                        if (empty($v['thirdPartySignedDate'])) {
                            $isESigned = false;
                        }
                    }

                    // 个人
                    if ($v['thirdPartySignMode'] == "P" || $v['thirdPartySignMode'] == "B") {
                        // 签署人是否当前用户
                        if ($this->view->userID == $v['thirdPartyPrincipal']) {
                            // 是否未签
                            if (empty($v['thirdPartyPrincipalSigningDate'])) {
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
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr">'. $this->view->translate('attachmentSign') .'</a>';
                    } else {
                        if (!empty($v['firstParty']) && $v['firstParty'] == $accountID) {
                            // 个人
                            if ($v['firstPartySignMode'] == "P") {
                                if ($this->view->userID == $v['firstPartyPrincipal']) {
                                    $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">'. $this->view->translate('attachmentSigned') .'</a>';
                                }
                            } else {
                                $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">'. $this->view->translate('attachmentSigned') .'</a>';
                            }
                        }
                        if (!empty($v['secondParty']) && $v['secondParty'] == $accountID) {
                            if ($v['secondPartySignMode'] == "P") {
                                if ($this->view->userID == $v['secondPartyPrincipal']) {
                                    $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">'. $this->view->translate('attachmentSigned') .'</a>';
                                }
                            } else {
                                $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">'. $this->view->translate('attachmentSigned') .'</a>';
                            }
                        }
                        if (!empty($v['thirdParty']) && $v['thirdParty'] == $accountID) {
                            if ($v['thirdPartySignMode'] == "P") {
                                if ($this->view->userID == $v['thirdPartyPrincipal']) {
                                    $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">'. $this->view->translate('attachmentSigned') .'</a>';
                                }
                            } else {
                                $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">'. $this->view->translate('attachmentSigned') .'</a>';
                            }
                        }
                    }
                } else {
                    $hasNoEContract = "True";
                    // 非网签未签署
                    // 合同状态等于 '07' 的时间
                    if (($v['contractStatus'] == '07') && ((!empty($v['firstParty']) && $v['firstParty'] == $accountID && empty($v['firstPartySigningDate'])) || (!empty($v['secondParty']) && $v['secondParty'] == $accountID && empty($v['secondPartySigningDate'])) || (!empty($v['thirdParty']) && $v['thirdParty'] == $accountID && empty($v['thirdPartySigningDate'])))) {
                        $hasNoEContract = "True";
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initSignViewNoEContract(this)" class="order_contract_sign fr">'. $this->view->translate('download') .'</a>';
                        if (!empty($v['attachmentList'])) {
                            $notEContractAttachmentList = array();
                            foreach ($v['attachmentList'] as $notEContractKey => $contractAttachment) {
                                if ($contractAttachment['attachType'] == 'CRIM') {
                                    $attachment = array();
                                    $attachment['attachID'] = $contractAttachment['attachID'];
                                    $attachment['attachType'] = $contractAttachment['attachType'];
                                    $attachment['bizID'] = $contractAttachment['bizID'];
                                    $attachment['bizType'] = $contractAttachment['bizType'];
                                    $attachment['ext'] = $contractAttachment['ext'];
                                    $attachment['name'] = $contractAttachment['name'];
                                    $attachment['size'] = (int)$contractAttachment['size'];
                                    $attachment['verifyID'] = $contractAttachment['verifyID'];

                                    $notEContractAttachmentList[] = $attachment;
                                }
                            }
                            $this->view->notEContractAttachmentList = $notEContractAttachmentList;
                            $this->view->notEContractBizID = $v['bizID'];
                        } else {
                            $this->view->notEContractAttachmentList = null;
                        }

                    } else {
                        $IMG .= '<input type="hidden" id="isSign_' . $v['contractID'] . '" value="true">';
                        $IMG .= '<a href="javascript:void(0)" id="' . $v['contractID'] . '" onclick="initPdfView(\'' . $pdfUrl . '\', this)" class="order_contract_sign fr" style="background: #ccc;">'. $this->view->translate('attachmentSigned') .'</a>';
                    }
                }
                $IMG .= '</div>';
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
