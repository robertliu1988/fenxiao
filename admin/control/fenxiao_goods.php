<?php
/**
 * 分销商品管理
 *
 *
 *
 *by xiaolong
 */

defined('InShopNC') or exit('Access Invalid!');
class fenxiao_goodsControl extends SystemControl{
    const EXPORT_SIZE = 5000;
    public function __construct() {
        parent::__construct ();
        Language::read('goods');
    }

    /**
     * 商品设置
     */
    public function goods_setOp() {
		$model_setting = Model('setting');
		if (chksubmit()){
			$update_array = array();
			$update_array['goods_verify'] = $_POST['goods_verify'];
			$result = $model_setting->updateSetting($update_array);
			if ($result === true){
				$this->log(L('nc_edit,nc_goods_set'),1);
				showMessage(L('nc_common_save_succ'));
			}else {
				$this->log(L('nc_edit,nc_goods_set'),0);
				showMessage(L('nc_common_save_fail'));
			}
		}
		$list_setting = $model_setting->getListSetting();
		Tpl::output('list_setting',$list_setting);
        Tpl::showpage('goods.setting');
    }
    /**
     * 商品管理
     */
    public function goodsOp() {
        $model_goods = Model ( 'goods' );
        /**
         * 处理商品分类
         */
        $choose_gcid = ($t = intval($_REQUEST['choose_gcid']))>0?$t:0;
        $gccache_arr = Model('goods_class')->getGoodsclassCache($choose_gcid,3);
	    Tpl::output('gc_json',json_encode($gccache_arr['showclass']));
		Tpl::output('gc_choose_json',json_encode($gccache_arr['choose_gcid']));

        if (!isset($_GET['search_verify']))
            $_GET['search_verify'] = 1;

        /**
         * 查询条件
         */
        $where = array();
        if ($_GET['search_goods_name'] != '') {
            $where['goods_name'] = array('like', '%' . trim($_GET['search_goods_name']) . '%');
        }
        if (intval($_GET['search_commonid']) > 0) {
            $where['goods_commonid'] = intval($_GET['search_commonid']);
        }
        if ($_GET['search_store_name'] != '') {
            $where['store_name'] = array('like', '%' . trim($_GET['search_store_name']) . '%');
        }
        if (intval($_GET['b_id']) > 0) {
            $where['brand_id'] = intval($_GET['b_id']);
        }
        if ($choose_gcid > 0){
		    $where['gc_id_'.($gccache_arr['showclass'][$choose_gcid]['depth'])] = $choose_gcid;
		}
        if (in_array($_GET['search_state'], array('0','1','10'))) {
            $where['goods_state'] = $_GET['search_state'];
        }
        if (in_array($_GET['search_verify'], array('0','1','2','3'))) {
            $where['is_fenxiao'] = $_GET['search_verify'];
        }

        if ($_GET['search_order'] == 1)
            $order = 'fenxiao_time desc';
        else
            $order = 'fenxiao_time asc';

        switch ($_GET['type']) {
            // 等待审核
            case 'waitverify':
                $where['is_fenxiao'] = 2;
                $goods_list = $model_goods->getGoodsCommonList($where);
                break;
            // 终止审核
            case 'cancelverify':
                $where['cancel_status'] = 1;
                $goods_list = $model_goods->getGoodsCommonList($where);
                break;
            // 全部商品
            default:
                $goods_list = $model_goods->getGoodsCommonList($where,'*',10,$order);
                break;
        }


        $final_goods = array();
        foreach ($goods_list as $goods) {
            $goods['fenxiao_fanli'] = $goods['fenxiao_v4']."/".$goods['fenxiao_v3']."/".$goods['fenxiao_v2']."/".$goods['fenxiao_v1'];
            $goods['fenxiao_endtime'] = date("Y-m-d",$goods['fenxiao_time']);
            $final_goods[] = $goods;
        }

        Tpl::output('goods_list', $final_goods);
        Tpl::output('page', $model_goods->showpage(2));

        $storage_array = $model_goods->calculateStorage($goods_list);
        Tpl::output('storage_array', $storage_array);

        // 品牌
        $brand_list = Model('brand')->getBrandPassedList(array());

        Tpl::output('search', $_GET);
        Tpl::output('brand_list', $brand_list);

        Tpl::output('state', array('1' => '出售中', '0' => '仓库中', '10' => '违规下架'));

        Tpl::output('verify', array('0' => '未申请', '1' => '通过', '2' => '审核中', '3' => '未通过'));

        Tpl::output('order', array('0' => '到期时间升序', '1' => '到期时间降序'));

        Tpl::output('ownShopIds', array_fill_keys(Model('store')->getOwnShopIds(), true));

        switch ($_GET['type']) {
            // 等待审核
            case 'waitverify':
                Tpl::showpage('fenxiao_goods.verify');
                break;
            // 终止审核
            case 'cancelverify':
                Tpl::showpage('fenxiao_goods.cancel');
                break;
            // 全部商品
            default:
                Tpl::showpage('fenxiao_goods.index');
                break;
        }
    }

    /**
     * 违规下架
     */
    public function goods_lockupOp() {
        if (chksubmit()) {
            $commonids = $_POST['commonids'];
            $commonid_array = explode(',', $commonids);
            foreach ($commonid_array as $value) {
                if (!is_numeric($value)) {
                    showDialog(L('nc_common_op_fail'), 'reload');
                }
            }
            $update = array();
            $update['goods_stateremark'] = trim($_POST['close_reason']);

            $where = array();
            $where['goods_commonid'] = array('in', $commonid_array);

            Model('goods')->editProducesLockUp($update, $where);
            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
        }
        Tpl::output('commonids', $_GET['id']);
        Tpl::showpage('goods.close_remark', 'null_layout');
    }

    /**
     * 删除商品
     */
    public function goods_delOp() {
        $common_id = intval($_GET['goods_id']);
        if ($common_id <= 0) {
            showDialog(L('nc_common_op_fail'), 'reload');
        }
        Model('goods')->delGoodsAll(array('goods_commonid' => $common_id));
        showDialog(L('nc_common_op_succ'), 'reload', 'succ');
    }

    /**
     * 审核商品
     */
    public function goods_verifyOp(){

        if (chksubmit()) {

            $commonids = $_POST['commonids'];
            $commonid_array = explode(',', $commonids);
            foreach ($commonid_array as $value) {
                if (!is_numeric($value)) {
                    showDialog(L('nc_common_op_fail'), 'reload');
                }
            }

            $update1 = array();
            $update1['is_fenxiao'] = intval($_POST['verify_state']);

            $update2 = array();
            $update2['is_fenxiao'] = intval($_POST['verify_state']);

            $where = array();
            $where['goods_commonid'] = array('in', $commonid_array);

            $model_goods = Model('goods');
            $model_goods->editProduces($where, $update1,$update2);

            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
        }
        Tpl::output('commonids', $_GET['id']);
        Tpl::showpage('fenxiao_goods.verify_remark', 'null_layout');
    }

    public function cancel_verifyOp(){

        if (chksubmit()) {

            $commonids = $_POST['commonids'];
            $commonid_array = explode(',', $commonids);
            foreach ($commonid_array as $value) {
                if (!is_numeric($value)) {
                    showDialog(L('nc_common_op_fail'), 'reload');
                }
            }

            $update1 = array();
            $update1['cancel_status'] = 0;
            $update1['is_fenxiao'] = intval($_POST['verify_state']);

            $update2 = array();
            $update2['is_fenxiao'] = intval($_POST['verify_state']);

            $where = array();
            $where['goods_commonid'] = array('in', $commonid_array);

            $model_goods = Model('goods');
            $model_goods->editProduces($where, $update1,$update2);

            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
        }
        Tpl::output('commonids', $_GET['id']);
        Tpl::showpage('fenxiao_goods.cancel_remark', 'null_layout');
    }

    /**
     * 审核商品
     */
    public function goods_endOp(){

        $commonids = $_GET['commonids'];
        $commonid_array = explode(',', $commonids);
        foreach ($commonid_array as $value) {
            if (!is_numeric($value)) {
                showDialog(L('nc_common_op_fail'), 'reload');
            }
        }

        $update1 = array();
        $update1['is_fenxiao'] = 0; //未申请

        $where = array();
        $where['goods_commonid'] = array('in', $commonid_array);

        $model_goods = Model('goods');
        $model_goods->editProduces($where, $update1);

        showDialog(L('nc_common_op_succ'), 'reload', 'succ');
    }

    /**
     * ajax获取商品列表
     */
    public function get_goods_list_ajaxOp() {
        $commonid = $_GET['commonid'];
        if ($commonid <= 0) {
            echo 'false';exit();
        }
        $model_goods = Model('goods');
        $goodscommon_list = $model_goods->getGoodeCommonInfoByID($commonid, 'spec_name');
        if (empty($goodscommon_list)) {
            echo 'false';exit();
        }
        $goods_list = $model_goods->getGoodsList(array('goods_commonid' => $commonid), 'goods_id,goods_spec,store_id,goods_price,goods_serial,goods_storage,goods_image');
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

}
