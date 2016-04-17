<?php
/**
 * 分销员申请管理
 *
 *
 *
 **by xiaolong*/


defined('InShopNC') or exit ('Access Invalid!');
class store_goods_fenxiao_memberControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct ();
        Language::read ('member_store_goods_index');
    }
    public function indexOp() {
        $this->apply_listOp();
    }


    /**
     * 分销申请列表
     */
    public function apply_listOp() {
        $model_goods = Model('goods');
        $model_member = Model('member');
        $model_fenxiao_goods_member = Model('fenxiao_goods_member');

        $where = array();
        $where['store_id'] = $_SESSION['store_id'];

        if (isset($_GET['goods_commonid'])){

            $condition = array();
            $condition['goods_commonid'] = $_GET['goods_commonid'];
            $goods_list = $model_goods->getGoodsList($condition,'goods_id');

            $goods_arr = array();
            foreach ($goods_list as $goods) {
                $goods_arr[] = $goods['goods_id'];
            }

            $where['goods_id'] = array('in',$goods_arr);
        }

//        if (intval($_GET['stc_id']) > 0) {
//            $where['goods_stcids'] = array('like', '%,' . intval($_GET['stc_id']) . ',%');
//        }
//        if (trim($_GET['keyword']) != '') {
//            switch ($_GET['search_type']) {
//                case 0:
//                    $where['goods_name'] = array('like', '%' . trim($_GET['keyword']) . '%');
//                    break;
//                case 1:
//                    $where['goods_serial'] = array('like', '%' . trim($_GET['keyword']) . '%');
//                    break;
//                case 2:
//                    $where['goods_commonid'] = intval($_GET['keyword']);
//                    break;
//            }
//        }

//        var_dump($where);
//        exit;

        $apply_list = $model_fenxiao_goods_member->getList($where);
//        var_dump($apply_list);
//        exit;

        $final_list = array();
        foreach ($apply_list as $apply) {
            $condition = array();
            $condition['goods_id'] = $apply['goods_id'];
            $goods_info = $model_goods->getGoodsInfo($condition);

            $member_info = $model_member->getMemberInfoByID($apply['member_id']);

            $apply['goods_name'] = $goods_info['goods_name'];
            $apply['goods_image'] = $goods_info['goods_image'];
            $apply['member_name'] = $member_info['member_name'].'/'.$member_info['member_truename'];

            $model_grade = Model('fenxiao_member_grade');
            $grade_list = $model_grade->getGradeList();
            $fenxiao_points = $member_info['fenxiao_points'];
            foreach ($grade_list as $grade) {
                if (intval($fenxiao_points) >= $grade['fmg_points'])
                    $level = $grade['fmg_name'];
            }
            $apply['member_level'] = $level;

            if ($apply['status'] == 1)
                $apply['status'] = '通过';
            else if ($apply['status'] == 0)
                $apply['status'] = '待审核';
            else
                $apply['status'] = '未通过';

            //            $apply['apply_time'] = date("Y-m-d H:i:s",$apply['apply_time']);

            $final_list[] = $apply;
        }

        Tpl::output('show_page', $model_fenxiao_goods_member->showpage());
        Tpl::output('apply_list', $final_list);

        $this->profile_menu('goods_list', 'goods_list');

        Tpl::showpage('store_goods_fenxiao_member');
    }


    /**
     * 通过审核
     */
    public function verifyOp() {
        $goods_id = $_GET['goods_id'];
        $member_id = $_GET['member_id'];

        if ($goods_id <= 0 || $member_id <= 0) {
            showMessage(L('wrong_argument'), '', 'html', 'error');
        }
        $model_fenxiao_goods_member = Model('fenxiao_goods_member');
        $update_arr = array('status'=>1);

        $condition = array();
        $condition['goods_id'] = $goods_id;
        $condition['member_id'] = $member_id;

        $goodscommon_info = $model_fenxiao_goods_member->modify($update_arr,$condition);

        if ($goodscommon_info) {
            // 添加操作日志
            showDialog('审核成功', 'reload', 'succ');
        } else {
            showDialog('审核失败', '', 'error');
        }
    }

    /**
     * 删除
     */
    public function deleteOp() {
        $goods_id = $_GET['goods_id'];
        $member_id = $_GET['member_id'];

        if ($goods_id <= 0 || $member_id <= 0) {
            showMessage(L('wrong_argument'), '', 'html', 'error');
        }
        $model_fenxiao_goods_member = Model('fenxiao_goods_member');

        $condition = array();
        $condition['goods_id'] = $goods_id;
        $condition['member_id'] = $member_id;

        $info = $model_fenxiao_goods_member->drop($condition);

        if ($info) {
            // 添加操作日志
            showDialog('删除成功', 'reload', 'succ');
        } else {
            showDialog('删除失败', '', 'error');
        }
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string $menu_type 导航类型
     * @param string $menu_key 当前导航的menu_key
     * @param boolean $allow_promotion
     * @return
     */
    private function profile_menu($menu_type,$menu_key, $allow_promotion = array()) {
        $menu_array = array();
        switch ($menu_type) {
            case 'goods_list':
                $menu_array = array(
                   array('menu_key' => 'goods_list',    'menu_name' => '分销员申请', 'menu_url' => urlShop('store_goods_online', 'index'))
                );
                break;
            case 'edit_detail':
                if ($allow_promotion['lock'] === false) {
                    $menu_array = array(
                        array('menu_key' => 'edit_detail',  'menu_name' => '编辑商品', 'menu_url' => urlShop('store_goods_online', 'edit_goods', array('commonid' => $_GET['commonid'], 'ref_url' => $_GET['ref_url']))),
                        array('menu_key' => 'edit_image',   'menu_name' => '编辑图片', 'menu_url' => urlShop('store_goods_online', 'edit_image', array('commonid' => $_GET['commonid'], 'ref_url' => ($_GET['ref_url'] ? $_GET['ref_url'] : getReferer())))),
                    );
                }
                if ($allow_promotion['gift']) {
                    $menu_array[] = array('menu_key' => 'add_gift', 'menu_name' => '赠送赠品', 'menu_url' => urlShop('store_goods_online', 'add_gift', array('commonid' => $_GET['commonid'], 'ref_url' => ($_GET['ref_url'] ? $_GET['ref_url'] : getReferer()))));
                }
                if ($allow_promotion['combo']) {
                    $menu_array[] = array('menu_key' => 'add_combo', 'menu_name' => '推荐组合', 'menu_url' => urlShop('store_goods_online', 'add_combo', array('commonid' => $_GET['commonid'], 'ref_url' => ($_GET['ref_url'] ? $_GET['ref_url'] : getReferer()))));
                }
                break;
        }
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }


}
