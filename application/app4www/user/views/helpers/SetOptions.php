<?php

class Zend_View_Helper_SetOptions extends Shop_View_Helper {

    //查询本地资源文件 列表字典结果
    /**
     * @param $arrayObject // 要放入 select 的数组,或json对象
     * @param $selectID
     * @return string
     */
    function ShowDictionaryList($arrayObject, $selectID) {
        $optionString = "";
        if ($arrayObject != null && !empty($arrayObject)) {
            if (is_object($arrayObject)) {
                $arrayObject = $this->objectToArray($arrayObject);

                foreach ($arrayObject as $key => $item) {
                    if (!empty($selectID)) {
                        if ($selectID == $item['id']) {
                            $optionString .= '<option selected="selected" value=' . $item['id'] . '>' . $item['name'] . '</option>';
                        } else {
                            $optionString .= '<option value=' . $item['id'] . '>' . $item['name'] . '</option>';
                        }
                    } else {
                        $optionString .= '<option value=' . $item['id'] . '>' . $item['name'] . '</option>';
                    }
                }
            }
        }
        return $optionString;
    }

    //对象转数组
    public function objectToArray($e) {
        $e = (array)$e;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'resource')
                return;
            if (gettype($v) == 'object' || gettype($v) == 'array')
                $e[$k] = (array)$this->objectToArray($v);
        }
        return $e;
    }

    //数组转对象
    public function arrayToObject($e) {

        if (gettype($e) != 'array')
            return;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object')
                $e[$k] = (object)arrayToObject($v);
        }
        return (object)$e;
    }
}
