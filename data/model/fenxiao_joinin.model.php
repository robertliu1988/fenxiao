<?php
/**
 * 分销申请模型
 *
 * 
 *
 *
 * by xiaolong
 */
defined('InShopNC') or exit('Access Invalid!');
class fenxiao_joininModel extends Model{

    public function __construct(){
        parent::__construct('fenxiao_joinin');
    }

	/**
	 * 读取列表 
	 * @param array $condition
	 *
	 */
	public function getList($condition,$page='',$order='',$field='*'){
        $result = $this->table('fenxiao_joinin')->field($field)->where($condition)->page($page)->order($order)->select();
        return $result;
	}
	
	/**
	 * 分销申请数量
	 * @param unknown $condition
	 */
	public function getFenxiaoJoininCount($condition) {
	    return  $this->where($condition)->count();
	}

    /**
	 * 读取单条记录
	 * @param array $condition
	 *
	 */
    public function getOne($condition){
        $result = $this->where($condition)->find();
        return $result;
    }

	/*
	 *  判断是否存在 
	 *  @param array $condition
     *
	 */
	public function isExist($condition) {
        $result = $this->getOne($condition);
        if(empty($result)) {
            return FALSE;
        }
        else {
            return TRUE;
        }
	}

	/*
	 * 增加 
	 * @param array $param
	 * @return bool
	 */
    public function save($param){
        return $this->insert($param);	
    }
	
	/*
	 * 增加 
	 * @param array $param
	 * @return bool
	 */
    public function saveAll($param){
        return $this->insertAll($param);	
    }
	
	/*
	 * 更新
	 * @param array $update
	 * @param array $condition
	 * @return bool
	 */
    public function modify($update, $condition){
        return $this->where($condition)->update($update);
    }
	
	/*
	 * 删除
	 * @param array $condition
	 * @return bool
	 */
    public function drop($condition){
        return $this->where($condition)->delete();
    }

    /**
     * 编辑
     * @param array $condition
     * @param array $update
     * @return bool
     */
    public function editFenxiaoJoinin($condition, $update) {
        return $this->where($condition)->update($update);
    }
}
