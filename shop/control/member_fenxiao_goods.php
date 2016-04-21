<?php
/**
 * 买家 我的分销商品
 *
 * by xiaolong
 */


defined('InShopNC') or exit('Access Invalid!');

class member_fenxiao_goodsControl extends BaseMemberControl {

    public function __construct() {
        parent::__construct();
        Language::read('member_member_index');
        Language::read ('member_store_goods_index');
    }

    /**
     * 我的分销商品
     *
     */
    public function goodsOp() {
        $model_fenxiao_goods_member = Model('fenxiao_goods_member');
        $condition = array();
        $condition['member_id'] = is_null($_SESSION['member_id'])?-1:$_SESSION['member_id'];
        $condition['status'] = 1;
        $info_list = $model_fenxiao_goods_member->getList($condition,20);

        $goods_list = array();
        $model_goods = Model('goods');

        foreach ($info_list as $info) {
            $goods_info = array();
            $goods_info = $model_goods->getGoodsInfo(array('goods_id'=>$info['goods_id']));

            $goods_info['fenxiao_fanli'] = $goods_info['fenxiao_v1']."/".$goods_info['fenxiao_v2']."/".$goods_info['fenxiao_v3']."/".$goods_info['fenxiao_v4'];
            $goods_info['goods_url'] = urlShop('goods','index',array('goods_id'=>$info['goods_id']));


            $goods_list[] = $goods_info;
        }

        Tpl::output('goods_list',$goods_list);
		Tpl::output('show_page',$model_fenxiao_goods_member->showpage());

        Tpl::showpage('member_fenxiao.goods');
    }

}
