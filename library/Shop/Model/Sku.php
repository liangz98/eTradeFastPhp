<?php
/**
 * 库存规格表模型 (Shop_Model_Sku)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_Sku extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'skus';

    /**
     * 查询库存规格
     *
     * 返回固定格式的库存规格数组
     *
     * @param	int	$parent	分类ID
     * @return	array
     *
     */
    public function getSkuTree($parent = 0, $level = 0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("parent_id = ?",$parent);
            $select->order("id DESC");
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            $skuList = array();
            foreach ($rows as $k => $row){
                $skuList[$k] = array('id' => intval($row['id']), 'text' => $row['text']);
                $level++;
                $list = $this->getSkuTree($row['id'], $level);
                if ( !empty($list) || $row['parent_id'] == '0') {
                    $skuList[$k]['list'] = $list;
                }
                $level--;
            }
            return $skuList;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 根据当前库存规格查询顶级规格
     *
     * 返回顶级级库存规格数组
     *
     * @param	int	$parent	分类ID
     * @return array
     *
     */
    public function getParentNav($parent)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("id = ?",$parent);
            $sql = $select->__toString();
            $row = $this->_db->fetchRow($sql);
            if($row['parent_id'] > 0) {
                $row = $this->getParentNav($row['parent_id']);
            }
            return $row;
        } catch (Exception $e) {
            throw $e;
        }
    }
}