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
        $model_fenxiao_member_grade = Model('fenxiao_member_grade');

        //获取等级信息
        $grade_list = $model_fenxiao_member_grade->getGradeList();

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

        if (isset($_GET['member_name']) && !empty($_GET['member_name'])){
            $condition = array();
            $condition['member_name'] = array('like','%'.$_GET['member_name'].'%');
            $member_list = $model_member->getMemberList($condition,'member_id');

            $member_arr = array();
            foreach ($member_list as $member) {
                $member_arr[] = $member['member_id'];
            }
            $where['member_id'] = array('in',$member_arr);
        }

        if (isset($_GET['member_truename']) && !empty($_GET['member_truename'])){
            $condition = array();
            $condition['member_truename'] = array('like','%'.$_GET['member_truename'].'%');
            $member_list = $model_member->getMemberList($condition,'member_id');

            $member_arr = array();
            foreach ($member_list as $member) {
                $member_arr[] = $member['member_id'];
            }
            $where['member_id'] = array('in',$member_arr);
        }

        switch ($_GET['type']) {
            // 等待审核或审核失败的商品
            case 'member_pass':
                $where['status']  = 1;
                $this->profile_menu('member_pass');
                break;
            // 仓库中的商品
            default:
                $where['status']  = 0;
                $this->profile_menu('member_verify');
                break;
        }

        $apply_list = $model_fenxiao_goods_member->getList($where);

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
            $grade_id = 0;
            foreach ($grade_list as $grade) {
                if (intval($fenxiao_points) >= $grade['fmg_points']){
                    $level = $grade['fmg_name'];
                    $grade_id = $grade['fmg_id'];
                }
            }
            $apply['member_level'] = $level;

            if (isset($_GET['grade']) &&  !empty($_GET['grade'])){
                if ($grade_id != $_GET['grade'])
                    continue;
            }

            if ($apply['status'] == 1)
                $apply['status'] = '通过';
            else if ($apply['status'] == 0)
                $apply['status'] = '待审核';
            else
                $apply['status'] = '未通过';

            //            $apply['apply_time'] = date("Y-m-d H:i:s",$apply['apply_time']);

            $final_list[] = $apply;
        }

        Tpl::output('grade_list', $grade_list);
        Tpl::output('show_page', $model_fenxiao_goods_member->showpage());
        Tpl::output('apply_list', $final_list);

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
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '') {
        $menu_array = array(
            array('menu_key' => 'member_verify',     'menu_name' => '分销员申请',    'menu_url' => urlShop('store_goods_fenxiao_member', 'index', array())),
            array('menu_key' => 'member_pass',     'menu_name' => '已审核分销员',     'menu_url' => urlShop('store_goods_fenxiao_member', 'index', array('type' => 'member_pass')))
        );
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }

}
