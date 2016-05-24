<?php
/**
 * 我的分销
 *
 * @好商城V4 (c) 2015-2016 33hao Inc.
 * @license    http://www.33hao.com
 * @link       交流群号：216611541
 * @since      好商城提供技术支持 授权请购买shopnc授权

 */

defined('InShopNC') or exit('Access Invalid!');

class member_fenxiaoControl extends mobileMemberControl {

	public function __construct(){
		parent::__construct();
	}

    /**
     * 分销商品列表
     */
    public function fenxiao_goods_listOp() {
		$model_fenxiao_goods_member = Model('fenxiao_goods_member');

        $condition = array();
        $condition = $this->fenxiao_type_no($_POST["state_type"]);

        if (!empty($_POST['goods_id']) )
            $condition['goods_id'] = $_POST['goods_id'];

        $condition['member_id'] = $this->member_info['member_id'];
        $info_list = $model_fenxiao_goods_member->getList($condition,20);
        $inviter = base64_encode($condition['member_id']);

        $goods_list = array();
        $model_goods = Model('goods');
		$model_fenxiao_goods_member	= Model('fenxiao_goods_member');
        $model_store	= Model('store');
        $model_fenxiao_fanli	= Model('fenxiao_fanli');


        $model_member = Model('member');
        $condition = array();
        $member_id = $this->member_info['member_id'];
        $member_info = $model_member->getMemberInfoByID($member_id,'fenxiao_status');

        if ($member_info['fenxiao_status'] == 2){
            foreach ($info_list as $info) {
                $goods_info = array();
                $goods_info = $model_goods->getGoodsInfo(array('goods_id'=>$info['goods_id']));

                $goods_info['fenxiao_fanli'] = $goods_info['fenxiao_v1']."/".$goods_info['fenxiao_v2']."/".$goods_info['fenxiao_v3']."/".$goods_info['fenxiao_v4'];
                $goods_info['goods_url'] = urlShop('goods','index',array('goods_id'=>$info['goods_id']));
				$goods_info['goods_image_url'] = cthumb($goods_info['goods_image'], 360, $goods_info['store_id']);
				
				$condition = array();
				$condition['goods_id'] = $goods_info['goods_id'];
				$condition['member_id'] = $member_id;
				$info = $model_fenxiao_goods_member->getOne($condition);

				$condition = array();
				$condition['member_id'] = $member_id;
				$store_info = $model_store->getStoreInfo($condition);

				$condition = array();
				$member_info = $model_member->getMemberInfoByID($member_id);
				if ($member_id == -1)
					$member_info['fenxiao_status'] = 2;

				$condition = array();
				$condition['store_id'] = $goods_info['store_id'];
				$goods_store_info = $model_store->getStoreInfo($condition);
				$model_grade = Model('fenxiao_merchant_grade');
				$grade_list = $model_grade->getGradeList();
				$level = '未定义';
				$fenxiao_points = $goods_store_info['fenxiao_points'];
				foreach ($grade_list as $grade) {
					if (intval($fenxiao_points) >= $grade['fmg_points'])
						$fmg_member_limit = $grade['fmg_member_limit'];
				}

				$condition = array();
				$condition['goods_id'] = $goods_info['goods_id'];
				$goods['fenxiao_apply_num'] = $model_fenxiao_goods_member->getFenxiaoGoodsMemberCount($condition);
                $goods_info['fenxiao_apply_num'] = $goods['fenxiao_apply_num'];
                $goods_info['fmg_member_limit'] = $fmg_member_limit;

                $left_seconds = $goods_info['fenxiao_time']-time();
                $left_day = intval($left_seconds/(24*3600));
                $left_hour = intval(($left_seconds%(24*3600))/3600);
                $left_minute = intval((($left_seconds%(24*3600))%3600)/60);
                $left_time = $left_day."天".$left_hour."小时".$left_minute."分";
                $goods_info['left_time'] = $left_time;

                $condition = array();
                $condition['goods_id'] = $goods_info['goods_id'];
                $fanli_list = $model_fenxiao_fanli->getList($condition);
                $fenxiao_num = 0;
                $fenxiao_money = 0;
                foreach ($fanli_list as $fanli) {
                    $fenxiao_num += $fanli['goods_num'];
                    $fenxiao_money += $fanli['fanli_money'];
                }
                $goods_info['fenxiao_money'] = $fenxiao_money;
                $goods_info['fenxiao_num'] = $fenxiao_num;
                $goods_info['status'] = $info['status'];
                $goods_info['inviter'] = $inviter;

                $goods_list[] = $goods_info;
            }
        }
		
        $page_count = $model_fenxiao_goods_member->gettotalpage();
		
		//var_dump($goods_list);
		//exit;

        output_data(array('fenxiao_goods_list' => $goods_list), mobile_page($page_count));
    }
	

	private function fenxiao_type_no($stage) {
		switch ($stage){
			case 'state_verify':
				$condition['status'] = '0';
				break;
			case 'state_pass':
				$condition['status'] = '1';
				break;
            case 'state_all':
                $condition = array();
                break;
		}
		return $condition;
	}

    /**
     * 分销返利列表
     */
    public function fenxiao_fanli_listOp() {
        $model_fenxiao_fanli = Model('fenxiao_fanli');

        $condition = array();
        $condition['member_id'] = $this->member_info['member_id'];
        $info_list = $model_fenxiao_fanli->getList($condition,20);

        $goods_list = array();
        $model_goods = Model('goods');
        $model_fenxiao_goods_member	= Model('fenxiao_goods_member');
        $model_store	= Model('store');

        $inviter = base64_encode($this->member_info['member_id']);

        $goods_list = array();
        $model_goods = Model('goods');

        foreach ($info_list as $info) {
            $goods_info = array();
            $goods_info = $model_goods->getGoodsInfo(array('goods_id'=>$info['goods_id']));

            $goods_info['fenxiao_fanli'] = $goods_info['fenxiao_v1']."/".$goods_info['fenxiao_v2']."/".$goods_info['fenxiao_v3']."/".$goods_info['fenxiao_v4'];
            $goods_info['goods_url'] = urlShop('goods','index',array('goods_id'=>$info['goods_id']));
            $goods_info['pay_sn'] = $info['pay_sn'];
            $goods_info['goods_price'] = $info['goods_price'];
            $goods_info['fanli_money'] = $info['fanli_money'];
            $goods_info['goods_image_url'] = cthumb($goods_info['goods_image'], 360, $goods_info['store_id']);

            if ($info['status'] == 0)
                $status_msg = '待返利';
            else
                $status_msg = '已返利';

            $goods_info['status'] = $status_msg;
            $goods_info['inviter'] = $inviter;

            $goods_list[] = $goods_info;
        }


        $page_count = $model_fenxiao_fanli->gettotalpage();

        output_data(array('fenxiao_goods_list' => $goods_list), mobile_page($page_count));
    }

}
