<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 17:05
 */
class Kyapi_Model_order
{
    public $orderID;
    public $buyer;
    public $buyerName;
    public $buyerCrnCode;
    public $buyerContactID;
    public $buyerContactName;
    public $buyerOrderRequest;
    public $vendor;
    public $vendorName;
    public $vendorContactID;
    public $vendorContactName;
    public $vendorCrnCode;
    public $vendorOrderRequest;
    public $agentContactID;
    public $agentContactName;
    public $paymentPeriod;
    public $paymentTerm;
    public $isSelfSupport;
    public $priceTerm;
    public $quotationMode;
    public $packingMode;
    public $packingDesc;
    public $shippingMethod;
    public $clearancePlace;
    public $loadingCountry;
    public $loadingCity;
    public $dischargeCity;
    public $deliveryPort;
    public $loadingPort;
    public $dischargeCountry;
    public $deliveryCity;
    public $dischargePort;
    public $deliveryPlace;
    public $deliveryDate;
    public $shippingServiceType;
    public $sizeQuantityMap;
    public $needShipping;
    public $shippingRequest;
    public $isAssignCustomsAgency;
    public $customsAgencyName;
    public $customsAgencyCode;
    public $customClearanceRequest;
    public $needFinancing;
    public $financingRequest;
    public $financingCrnCode;
    public $financingAmount;
    public $financingType;
    public $orderItemList;
    public $attachmentList;
//    public $attachList;
    public $orderType;
    public $totalAmount;
    public $saleContractNo;
    public $saleContractID;
    public $lastUpdate;
}
