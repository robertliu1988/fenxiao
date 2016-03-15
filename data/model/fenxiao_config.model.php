<?php
/**
 * 分销配置模型
 *
 * 
 *
 *
 * by xiaolong
 */
defined('InShopNC') or exit('Access Invalid!');

class fenxiao_configModel extends Model {

    public function __construct(){
        parent::__construct('fenxiao_config');
    }

    /**
     * 取分销列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $order
     */
    public function getFenxiaoConfigList($condition = array(), $pagesize = '', $limit = '', $order = '') {
        return $this->where($condition)->order($order)->page($pagesize)->limit($limit)->select();
    }

    /**
     * 取得单条信息
     * @param unknown $condition
     */
    public function getFenxiaoConfigInfo($condition = array()) {
        return $this->where($condition)->find();
    }

    /**
     * 删除配置
     * @param unknown $condition
     */
    public function delFenxiaoConfig($condition = array()) {
        return $this->where($condition)->delete();
    }

    /**
     * 增加配置
     * @param unknown $data
     * @return boolean
     */
    public function addFenxiaoConfig($data) {
        return $this->insert($data);
    }

    /**
     * 更新配置
     * @param unknown $data
     * @param unknown $condition
     */
    public function editFenxiaoConfig($data = array(),$condition = array()) {
        return $this->where($condition)->update($data);
    }
}