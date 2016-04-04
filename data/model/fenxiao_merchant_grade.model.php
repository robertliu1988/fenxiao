<?php
/**
 * 分销商等级模型
 *
 * 
 *
 *
 * by xiaolong
 */
defined('InShopNC') or exit('Access Invalid!');

class fenxiao_merchant_gradeModel{
	/**
	 * 列表
	 *
	 * @param array $condition 检索条件
	 * @return array 数组结构的返回结果
	 */
	public function getGradeList($condition = array()){
		$condition_str = $this->_condition($condition);
		$param = array();
		$param['table'] = 'fenxiao_merchant_grade';
		$param['where'] = $condition_str;
		//$param['order'] = 'fmg_id';
		$param['order'] = $condition['order']?$condition['order']:'fmg_id';
		$result = Db::select($param);
		return $result;
	}
	/**
	 * 构造检索条件
	 *
	 * @param int $id 记录ID
	 * @return string 字符串类型的返回结果
	 */
	private function _condition($condition){
		$condition_str = '';
		
		if ($condition['like_sg_name'] != ''){
			$condition_str .= " and sg_name like '%". $condition['like_sg_name'] ."%'";
		}
		if ($condition['no_fmg_id'] != ''){
			$condition_str .= " and fmg_id != '". intval($condition['no_fmg_id']) ."'";
		}
		if ($condition['sg_name'] != ''){
			$condition_str .= " and sg_name = '". $condition['sg_name'] ."'";
		}
		if ($condition['fmg_id'] != ''){
			$condition_str .= " and fenxiao_merchant_grade.fmg_id = '". $condition['fmg_id'] ."'";
		}
		/*if($condition['store_id'] != '') {
			$condition_str .= " and store.store_id=".$condition['store_id'];
		}*/
		if(isset($condition['store_id'])) {
			$condition_str .= " and store.store_id = '{$condition['store_id']}' ";
		}
		if (isset($condition['sg_sort'])){
			if ($condition['sg_sort'] == ''){
				$condition_str .= " and sg_sort = '' ";
			}else {
				$condition_str .= " and sg_sort = '{$condition['sg_sort']}'";
			}
		}
		return $condition_str;
	}
	
	/**
	 * 取单个内容
	 *
	 * @param int $id 分类ID
	 * @return array 数组类型的返回结果
	 */
	public function getOneGrade($id){
		if (intval($id) > 0){
			$param = array();
			$param['table'] = 'fenxiao_merchant_grade';
			$param['field'] = 'fmg_id';
			$param['value'] = intval($id);
			$result = Db::getRow($param);
			return $result;
		}else {
			return false;
		}
	}
	
	/**
	 * 新增
	 *
	 * @param array $param 参数内容
	 * @return bool 布尔类型的返回结果
	 */
	public function add($param){
		if (empty($param)){
			return false;
		}
		if (is_array($param)){
			$tmp = array();
			foreach ($param as $k => $v){
				$tmp[$k] = $v;
			}
			$result = Db::insert('fenxiao_merchant_grade',$tmp);
			return $result;
		}else {
			return false;
		}
	}
	
	/**
	 * 更新信息
	 *
	 * @param array $param 更新数据
	 * @return bool 布尔类型的返回结果
	 */
	public function update($param){
		if (empty($param)){
			return false;
		}
		if (is_array($param)){
			$tmp = array();
			foreach ($param as $k => $v){
				$tmp[$k] = $v;
			}
			$where = " fmg_id = '{$param['fmg_id']}'";
			$result = Db::update('fenxiao_merchant_grade',$tmp,$where);
			return $result;
		}else {
			return false;
		}
	}
	
	/**
	 * 删除分类
	 *
	 * @param int $id 记录ID
	 * @return bool 布尔类型的返回结果
	 */
	public function del($id){
		if (intval($id) > 0){
			$where = " fmg_id = '". intval($id) ."'";
			$result = Db::delete('fenxiao_merchant_grade',$where);
			return $result;
		}else {
			return false;
		}
	}
	
	
	/**
	 * 等级对应的店铺列表
	 *
	 * @param array $condition 检索条件
	 * @param obj $page 分页
	 * @return array 数组结构的返回结果
	 */
	public function getGradeShopList($condition,$page=''){
		$condition_str = $this->_condition($condition);
		$param = array(
					'table'=>'fenxiao_merchant_grade,store',
					'field'=>'fenxiao_merchant_grade.*,store.*',
					'where'=>$condition_str,
					'join_type'=>'left join',
					'join_on'=>array(
						'fenxiao_merchant_grade.fmg_id = store.grade_id',
					)
				);		
		$result = Db::select($param,$page);
		return $result;
	}
}