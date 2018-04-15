<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/9
 * Time: 17:14
 */
class CacheController extends Kyapi_Controller_Action
{
    public function indexAction()
    {
        //读取数据字典
        $cacheM = new Seed_Model_Cache2File();
        $DATAlist=$cacheM->get("datatest_setting");
        $this->view->Datalist=$DATAlist;
        exit;
        //获取字典
        $kyURLt= $this->view->seed_Setting['KyUrl'].'/commApi';
        $kyoptiont = new HessianOptions();
        $kyoptiont->typeMap['requestObject'] = 'com.jtec.etrade.api.dto.RequestObject';
        $kyoptiont->typeMap['queryParams'] = 'java.util.Map';
        $userMt = new Kyapi_Controller_Common($kyURLt,$kyoptiont);
        $_requestOb=$this->_requestObject;
       // $Dcit_country= $userMt->getStaticDataListApi($_requestOb, 'COUNTRY_CODE',null,null,'zh_CN');
        $Dcit_country= $userMt->getStaticDataListApi($_requestOb, 'CURRENCY',null,null,'zh_CN');
        $exist_country =$Dcit_country->result;
        print_r($exist_country);
        exit;
        $DD_COUNTRY=$this->objectToArray( $exist_country );

        $Dcit_PROVINCE= $userMt->getStaticDataApi($_requestOb, 'PROVINCE_CODE','CN',null,'zh_CN');
        $exist_PROVINCE =$Dcit_PROVINCE->result;
        $DD_PROVINCE=$this->objectToArray( $exist_PROVINCE );
        $Dcit_CITY= $userMt->getStaticDataListApi($_requestOb, 'CITY_CODE',null,null,'zh_CN');
        $exist_CITY =$Dcit_CITY->result;
        $DD_CITY=$this->objectToArray( $exist_CITY );

        $DD=array('COUNTRY'=>$DD_COUNTRY,'PROVINCE'=>$DD_PROVINCE, 'CITY'=>$DD_CITY);;
        foreach($DD as $key=>$i )
        {
            foreach($i as$k2=>$v)

            echo $v['code'];
            echo $v['nameText'];
            echo "-";
        }
        echo '<pre>';
        print_r($DD).'\n';

        $mod_name='COUNTRY_CODE';
        $cacheM = new Seed_Model_Cache2File();
        $cacheM->save($mod_name."_setting",$DD);
        exit();


    }
}