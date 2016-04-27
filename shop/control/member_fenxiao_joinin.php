<?php
/**
 * 分销员申请
 *
 *
 *
 **by xiaolong
 */


defined('InShopNC') or exit('Access Invalid!');

class member_fenxiao_joininControl extends BaseHomeControl {

    private $joinin_detail = NULL;

	public function __construct() {
		parent::__construct();

		Tpl::setLayout('member_fenxiao_joinin_layout');

        $this->checkLogin();

        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(array('member_id' => $_SESSION['member_id']));

//        if(!empty($seller_info)) {
//            @header('location: index.php?act=seller_login');
//		}

        if($_GET['op'] != 'check_seller_name_exist' && $_GET['op'] != 'checkname') {
            $this->check_joinin_state();
        }

        $phone_array = explode(',',C('site_phone'));
        Tpl::output('phone_array',$phone_array);
        $model_help = Model('help');
        $condition = array();
        $condition['type_id'] = '99';//默认显示入驻流程;
        $list = $model_help->getShowStoreHelpList($condition);
        Tpl::output('list',$list);//左侧帮助类型及帮助
        Tpl::output('show_sign','joinin');
        Tpl::output('html_title',C('site_name').' - '.'申请分销员');
        Tpl::output('article_list','');//底部不显示文章分类
	}

    private function check_joinin_state() {
        $model_member = Model('member');
        $joinin_info = $model_member->getMemberInfoByID($_SESSION['member_id']);

        $model_fenxiao_joinin = Model('fenxiao_member_joinin');
        $joinin_detail = $model_fenxiao_joinin->getOne(array('member_id'=>$_SESSION['member_id']));

        if(!empty($joinin_info)) {
            $this->joinin_detail = $joinin_info;
            switch (intval($joinin_info['fenxiao_status'])) {
                case FENXIAO_JOIN_STATE_VERIFY:
                    $this->show_join_message('分销申请已经提交，请等待管理员审核', FALSE, '1');
                    break;
                case FENXIAO_JOIN_STATE_VERIFY_SUCCESS:
                    $this->show_join_message('审核成功', FALSE, 2);
                    break;
                case FENXIAO_JOIN_STATE_VERIFY_FAIL:
                    if (!in_array($_GET['op'],array('step1','step2')))
                        $this->show_join_message('审核失败，审核意见：'.$joinin_detail['joinin_message'], SHOP_SITE_URL.DS.'index.php?act=member_fenxiao_joinin&op=step1','2','重新申请');
                    break;
                case FENXIAO_JOIN_STATE_FORBIDDEN:
                    $this->show_join_message('已封禁');
                    break;
            }
        }
    }

	public function indexOp() {
        $this->step0Op();
	}

    public function step0Op() {
        $model_document = Model('document');
        $document_info = $model_document->getOneByCode('member_fenxiao');
        Tpl::output('agreement', $document_info['doc_content']);
        Tpl::output('step', '0');
        Tpl::output('sub_step', 'step0');
        Tpl::showpage('member_fenxiao_joinin_apply');
        exit;
    }

    public function step1Op() {
//        $model_store_joinin = Model('fenxiao_member_joinin');
//        $joinin_detail = $model_store_joinin->getOne(array('member_id'=>$_SESSION['member_id']));
        $joinin_detail = array();
        Tpl::output('joinin_detail', $joinin_detail);

        Tpl::output('step', '1');
        Tpl::output('sub_step', 'step1');
        Tpl::showpage('member_fenxiao_joinin_apply');
        exit;
    }

    public function step2Op() {
        if(!empty($_POST)) {
            $param = array();
            $param['member_id'] = $_SESSION['member_id'];
            $param['member_truename'] = $_POST['member_truename'];
            $param['business_licence_number'] = $_POST['business_licence_number'];
            $param['apply_reason'] = $_POST['apply_reason'];
            $param['business_licence_number_electronic'] = $this->upload_image('business_licence_number_electronic');

            //暂时废弃
            $param['member_mobile'] = 0;
            $param['alipay_num'] = '';
            $param['weixin_num'] = '';

            $fenxiao_config = Model('fenxiao_config');
            $fenxiao_member = $fenxiao_config->getFenxiaoConfigInfo(array('config_key'=>'fenxiao_member'));

            if (intval($fenxiao_member['config_value']) == 0)
                $param['status'] = FENXIAO_JOIN_STATE_VERIFY_SUCCESS;
            else
                $param['status'] = FENXIAO_JOIN_STATE_VERIFY;

            $fenxiao_status = $param['status'];
            $this->step2_save_valid($param);
			
            $model_member_fenxiao_joinin = Model('fenxiao_member_joinin');
            $joinin_info = $model_member_fenxiao_joinin->getOne(array('member_id' => $_SESSION['member_id']));
            if(empty($joinin_info)) {
                $param['member_id'] = $_SESSION['member_id'];
                $model_member_fenxiao_joinin->save($param);
            } else {
                $model_member_fenxiao_joinin->modify($param, array('member_id'=>$_SESSION['member_id']));
            }

            $param = array();
            $model_member	= Model('member');
            $param['fenxiao_status'] = $fenxiao_status;
            $model_member->editMember(array('member_id'=>$_SESSION['member_id']),$param);

        }

        @header('location: index.php?act=member_fenxiao_joinin');
    }

    private function upload_image($file) {
        $pic_name = '';
        $upload = new UploadFile();
        $uploaddir = ATTACH_PATH.DS.'member_fenxiao_joinin'.DS;
        $upload->set('default_dir',$uploaddir);
        $upload->set('allow_type',array('jpg','jpeg','gif','png'));
        if (!empty($_FILES[$file]['name'])){
            $result = $upload->upfile($file);
            if ($result){
                $pic_name = $upload->file_name;
                $upload->file_name = '';
            }
        }
        return $pic_name;
    }

    private function step2_save_valid($param) {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input"=>$param['apply_reason'], "require"=>"true","message"=>"申请理由不能为空"),
        );
        $error = $obj_validate->validate();
        if ($error != ''){
            showMessage($error);
        }
    }

    public function check_seller_name_existOp() {
        $condition = array();
        $condition['seller_name'] = $_GET['seller_name'];

        $model_seller = Model('seller');
        $result = $model_seller->isSellerExist($condition);

        if($result) {
            echo 'true';
        } else {
            echo 'false';
        }
    }


    private function show_join_message($message, $btn_next = FALSE, $step = '2',$btn_msg = '下一步') {
        Tpl::output('joinin_message', $message);
        Tpl::output('btn_next', $btn_next);
        Tpl::output('btn_msg', $btn_msg);
        Tpl::output('joinin_detail', $this->joinin_detail);
        Tpl::output('step', $step);
        Tpl::output('sub_step', 'step1');
        Tpl::showpage('member_fenxiao_joinin_apply');
        exit;
    }

    /**
	 * 检查店铺名称是否存在
	 *
	 * @param
	 * @return
	 */
	public function checknameOp() {
		/**
		 * 实例化卖家模型
		 */
		$model_store	= Model('store');
		$store_name = $_GET['store_name'];
		$store_info = $model_store->getStoreInfo(array('store_name'=>$store_name));
		if(!empty($store_info['store_name']) && $store_info['member_id'] != $_SESSION['member_id']) {
			echo 'false';
		} else {
			echo 'true';
		}
	}
}
