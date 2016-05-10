<?php
/**
 * 分销商品管理
 *
 *
 *
 **by xiaolong*/


defined('InShopNC') or exit ('Access Invalid!');
class store_goods_fenxiaoControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct ();
        Language::read ('member_store_goods_index');
    }
    public function indexOp() {
        $this->goods_listOp();
    }


    /**
     * 分销中的商品列表
     */
    public function goods_listOp() {
        $model_goods = Model('goods');

        $where = array();
        $where['store_id'] = $_SESSION['store_id'];
        $where['is_fenxiao'] = 1;
        if (intval($_GET['stc_id']) > 0) {
            $where['goods_stcids'] = array('like', '%,' . intval($_GET['stc_id']) . ',%');
        }
        if (trim($_GET['keyword']) != '') {
            switch ($_GET['search_type']) {
                case 0:
                    $where['goods_name'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case 1:
                    $where['goods_serial'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case 2:
                    $where['goods_commonid'] = intval($_GET['keyword']);
                    break;
            }
        }
        $goods_list = $model_goods->getGoodsCommonOnlineList($where);
        Tpl::output('show_page', $model_goods->showpage());
        Tpl::output('goods_list', $goods_list);

        // 计算库存
        $storage_array = $model_goods->calculateStorage($goods_list);
        Tpl::output('storage_array', $storage_array);

        // 商品分类
        $store_goods_class = Model('store_goods_class')->getClassTree(array('store_id' => $_SESSION['store_id'], 'stc_state' => '1'));
        Tpl::output('store_goods_class', $store_goods_class);

        $this->profile_menu('goods_list', 'goods_list');

        Tpl::showpage('store_goods_list.fenxiao');
    }

    /**
     * 编辑商品页面
     */
    public function edit_fenxiaoOp() {
        $common_id = $_GET['commonid'];

        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), '', 'html', 'error');
        }
        $model_goods = Model('goods');
        $goodscommon_info = $model_goods->getGoodeCommonInfoByID($common_id);

        if ($goodscommon_info['is_fenxiao'] == 2){
            $goodscommon_info['is_fenxiao'] = 1;
            $goodscommon_info['is_verify'] = 1;
        }

        $model_class = Model('goods_class');
        $class_array = $model_class->getGoodsClassInfoById($goodscommon_info['gc_id']);

        $fenxiao_day = array(1,3,5,7,15,30);

        Tpl::output('fenxiao_rate', $class_array['fenxiao_rate']);
        Tpl::output('goods', $goodscommon_info);

        Tpl::output('fenxiao_day', $fenxiao_day);

//        $this->profile_menu('edit_detail','edit_detail', $menu_promotion);
        Tpl::output('edit_goods_sign', true);
        Tpl::showpage('store_goods_fenxiao');
    }

    /**
     * 编辑商品保存
     */
    public function edit_save_goodsOp() {
        $_POST['is_fenxiao'] = 1;

        $common_id = intval ( $_POST ['commonid'] );
        if (!chksubmit() || $common_id <= 0) {
            showDialog(L('store_goods_index_goods_edit_fail'), urlShop('store_goods_online', 'index'));
        }

        $model_goods = Model ( 'goods' );

        $update_common = array();
        $update_common['is_fenxiao']         = $_POST['is_fenxiao'];
        $update_common['fenxiao_time']         = strtotime(date("Y-m-d"))+24*3600*(intval($_POST['fenxiao_day'])+1);
        $update_common['fenxiao_day']         = intval($_POST['fenxiao_day']);

        $fenxiao_config = Model('fenxiao_config');
        $fenxiao_goods = $fenxiao_config->getFenxiaoConfigInfo(array('config_key'=>'fenxiao_goods'));

        if ($fenxiao_goods['config_value'] && $update_common['is_fenxiao'] == 1)
            $update_common['is_fenxiao']         = 2;

        //判断返利规则是否符合要求
        $fenxiao_v1 = $_POST['fenxiao_v1'];
        $fenxiao_v2 = $_POST['fenxiao_v2'];
        $fenxiao_v3 = $_POST['fenxiao_v3'];
        $fenxiao_v4 = $_POST['fenxiao_v4'];

        $model_goods = Model('goods');
        $goodscommon_info = $model_goods->getGoodeCommonInfoByID($common_id);
        $model_class = Model('goods_class');
        $class_array = $model_class->getGoodsClassInfoById($goodscommon_info['gc_id']);

        if ($fenxiao_v1 <= 0 || $fenxiao_v2 <= 0 && $fenxiao_v3 <= 0 && $fenxiao_v4 <= 0)
            showDialog('分销申请失败，返利比例必须大于0', urlShop('store_goods_fenxiao', 'edit_fenxiao',array('commonid'=>$common_id)));

        if ($class_array['fenxiao_rate'] >= $fenxiao_v4 & $fenxiao_v4 >= $fenxiao_v3 && $fenxiao_v3 >= $fenxiao_v2 && $fenxiao_v2 >= $fenxiao_v1){
            $update_common['fenxiao_v1']         = $fenxiao_v1;
            $update_common['fenxiao_v2']         = $fenxiao_v2;
            $update_common['fenxiao_v3']         = $fenxiao_v3;
            $update_common['fenxiao_v4']         = $fenxiao_v4;
        }
        else
            showDialog('分销申请失败，返利比例符合 0<普通<铜牌<银牌<金牌<系统设置', urlShop('store_goods_fenxiao', 'edit_fenxiao',array('commonid'=>$common_id)));


        $return = $model_goods->editGoodsCommon($update_common, array('goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']));

        $update_goods = array();
        $update_goods['is_fenxiao']         = $update_common['is_fenxiao'];
        $update_goods['fenxiao_v1']         = $update_common['fenxiao_v1'];
        $update_goods['fenxiao_v2']         = $update_common['fenxiao_v2'];
        $update_goods['fenxiao_v3']         = $update_common['fenxiao_v3'];
        $update_goods['fenxiao_v4']         = $update_common['fenxiao_v4'];
        $update_goods['fenxiao_time']         = $update_common['fenxiao_time'];

        $return = $model_goods->editGoods($update_goods,array('goods_commonid'=>$common_id));

        if ($return) {
            //提交事务
            showDialog(L('nc_common_op_succ'), $_POST['ref_url'], 'succ');
        } else {
            //回滚事务
            showDialog(L('store_goods_index_goods_edit_fail'), urlShop('store_goods_online', 'index'));
        }
    }

    /**
     * 终止分销页面
     */
    public function cancelOp() {
        $common_id = $_GET['commonid'];

        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), '', 'html', 'error');
        }
        $model_goods = Model('goods');
        $goodscommon_info = $model_goods->getGoodeCommonInfoByID($common_id);

        if ($goodscommon_info['cancel_status'] == 1){
            $goodscommon_info['is_verify'] = 1;
        }

        Tpl::output('goods', $goodscommon_info);

        Tpl::output('edit_goods_sign', true);
        Tpl::showpage('store_goods_fenxiao.cancel');
    }

    /**
     * 编辑商品保存
     */
    public function cancel_save_goodsOp() {
        $_POST['cancel_status'] = 1;

        $common_id = intval ( $_POST ['commonid'] );
        if (!chksubmit() || $common_id <= 0) {
            showDialog(L('store_goods_index_goods_edit_fail'), urlShop('store_goods_online', 'index'));
        }

        $model_goods = Model ( 'goods' );

        $update_common = array();
        $update_common['cancel_status']         = $_POST['cancel_status'];
        $update_common['cancel_reason']         = $_POST['cancel_reason'];

        $return = $model_goods->editGoodsCommon($update_common, array('goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']));

        showDialog(L('nc_common_op_succ'), $_POST['ref_url'], 'succ');
    }

    /**
     * 验证commonid
     */
    private function checkRequestCommonId($common_ids) {
        if (!preg_match('/^[\d,]+$/i', $common_ids)) {
            showDialog(L('para_error'), '', 'error');
        }
        return $common_ids;
    }

    /**
     * ajax获取商品列表
     */
    public function get_goods_list_ajaxOp() {
        $common_id = $_GET['commonid'];
        if ($common_id <= 0) {
            echo 'false';exit();
        }
        $model_goods = Model('goods');
        $goodscommon_list = $model_goods->getGoodeCommonInfoByID($common_id, 'spec_name,store_id');
        if (empty($goodscommon_list) || $goodscommon_list['store_id'] != $_SESSION['store_id']) {
            echo 'false';exit();
        }
        $goods_list = $model_goods->getGoodsList(array('store_id' => $_SESSION['store_id'], 'goods_commonid' => $common_id), 'goods_id,goods_spec,store_id,goods_price,goods_serial,goods_storage_alarm,goods_storage,goods_image');
        if (empty($goods_list)) {
            echo 'false';exit();
        }

        $spec_name = array_values((array)unserialize($goodscommon_list['spec_name']));
        foreach ($goods_list as $key => $val) {
            $goods_spec = array_values((array)unserialize($val['goods_spec']));
            $spec_array = array();
            foreach ($goods_spec as $k => $v) {
                $spec_array[] = '<div class="goods_spec">' . $spec_name[$k] . L('nc_colon') . '<em title="' . $v . '">' . $v .'</em>' . '</div>';
            }
            $goods_list[$key]['goods_image'] = thumb($val, '60');
            $goods_list[$key]['goods_spec'] = implode('', $spec_array);
            $goods_list[$key]['alarm'] = ($val['goods_storage_alarm'] != 0 && $val['goods_storage'] <= $val['goods_storage_alarm']) ? 'style="color:red;"' : '';
            $goods_list[$key]['url'] = urlShop('goods', 'index', array('goods_id' => $val['goods_id']));
        }

        /**
         * 转码
         */
        if (strtoupper(CHARSET) == 'GBK') {
            Language::getUTF8($goods_list);
        }
        echo json_encode($goods_list);
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
                   array('menu_key' => 'goods_list',    'menu_name' => '分销中的商品', 'menu_url' => urlShop('store_goods_online', 'index'))
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
