<?php
/**
 * 分销商户
 *
 **by xiaolong*/

defined('InShopNC') or exit('Access Invalid!');

class fenxiao_merchantControl extends SystemControl{
	const EXPORT_SIZE = 1000;
	public function __construct(){
		parent::__construct();
		Language::read('store,store_grade');
	}

	/**
	 * 店铺
	 */
	public function storeOp(){
		$lang = Language::getLangContent();

		$model_store = Model('store');

		if(trim($_GET['owner_and_name']) != ''){
			$condition['member_name']	= array('like', '%'.$_GET['owner_and_name'].'%');
			Tpl::output('owner_and_name',$_GET['owner_and_name']);
		}
		if(trim($_GET['store_name']) != ''){
			$condition['store_name']	= array('like', '%'.trim($_GET['store_name']).'%');
			Tpl::output('store_name',$_GET['store_name']);
		}
		if(intval($_GET['grade_id']) > 0){
			$condition['grade_id']		= intval($_GET['grade_id']);
			Tpl::output('grade_id',intval($_GET['grade_id']));
		}

        if (!empty($_GET['fenxiao_status']))
            $condition['fenxiao_status'] = $_GET['fenxiao_status'];

        // 默认店铺管理不包含自营店铺
        $condition['is_own_shop'] = 0;

		//店铺列表
		$store_list = $model_store->getStoreList($condition, 10,'store_id desc');

		//店铺等级
		$model_grade = Model('store_grade');
		$grade_list = $model_grade->getGradeList($condition);
		if (!empty($grade_list)){
			$search_grade_list = array();
			foreach ($grade_list as $k => $v){
				$search_grade_list[$v['sg_id']] = $v['sg_name'];
			}
		}
        Tpl::output('search_grade_list', $search_grade_list);

        $fenxiao_status_array = $this->_get_fenxiao_status_array();

        $new_store_list = array();
        foreach ($store_list as $store) {
            $store['fenxiao_status'] = $fenxiao_status_array[intval($store['fenxiao_status'])];
            $new_store_list[] = $store;
        }

        Tpl::output('grade_list',$grade_list);
		Tpl::output('store_list',$new_store_list);
        Tpl::output('fenxiao_status', $this->_get_fenxiao_status_array());
		Tpl::output('page',$model_store->showpage('2'));
		Tpl::showpage('fenxiao_merchant.index');
	}

    private function _get_fenxiao_status_array() {
        return array(
            -1 => '关闭',
            1 => '审核中',
            2 => '开启',
            3 => '审核失败',
            4 => '封禁'
       );
    }
	/**
	 * 店铺编辑
	 */
	public function store_editOp(){
		$lang = Language::getLangContent();

		$model_store = Model('store');
		//保存
		if (chksubmit()){

			$update_array = array();
            $update_array['fenxiao_status'] = intval($_POST['fenxiao_status']);

            $result = $model_store->editStore($update_array, array('store_id' => $_POST['store_id']));
			if ($result){

			
				$url = array(
				array(
				'url'=>'index.php?act=fenxiao_merchant&op=store',
				'msg'=>$lang['back_store_list'],
				),
				array(
				'url'=>'index.php?act=fenxiao_merchant&op=store_edit&store_id='.intval($_POST['store_id']),
				'msg'=>$lang['countinue_add_store'],
				),
				);
				$this->log(L('nc_edit,store').'['.$_POST['store_name'].']',1);
				showMessage($lang['nc_common_save_succ'],$url);
			}else {
				$this->log(L('nc_edit,store').'['.$_POST['store_name'].']',1);
				showMessage($lang['nc_common_save_fail']);
			}
		}
		//取店铺信息
		$store_array = $model_store->getStoreInfoByID($_GET['store_id']);
		if (empty($store_array)){
			showMessage($lang['store_no_exist']);
		}


        Tpl::output('fenxiao_status', $this->_get_fenxiao_status_array());
		Tpl::output('store_array',$store_array);

		$joinin_detail = Model('store_joinin')->getOne(array('member_id'=>$store_array['member_id']));
        Tpl::output('joinin_detail', $joinin_detail);
		Tpl::showpage('fenxiao_merchant.edit');
	}

    /**
     * 编辑保存注册信息
     */
    public function edit_save_joininOp() {
        if (chksubmit()) {
            $member_id = $_POST['member_id'];
            if ($member_id <= 0) {
                showMessage(L('param_error'));
            }
            $param = array();
            $param['company_name'] = $_POST['company_name'];
            $param['company_province_id'] = intval($_POST['province_id']);
            $param['company_address'] = $_POST['company_address'];
            $param['company_address_detail'] = $_POST['company_address_detail'];
            $param['company_phone'] = $_POST['company_phone'];
            $param['company_employee_count'] = intval($_POST['company_employee_count']);
            $param['company_registered_capital'] = intval($_POST['company_registered_capital']);
            $param['contacts_name'] = $_POST['contacts_name'];
            $param['contacts_phone'] = $_POST['contacts_phone'];
            $param['contacts_email'] = $_POST['contacts_email'];
            $param['business_licence_number'] = $_POST['business_licence_number'];
            $param['business_licence_address'] = $_POST['business_licence_address'];
            $param['business_licence_start'] = $_POST['business_licence_start'];
            $param['business_licence_end'] = $_POST['business_licence_end'];
            $param['business_sphere'] = $_POST['business_sphere'];
            if ($_FILES['business_licence_number_electronic']['name'] != '') {
                $param['business_licence_number_electronic'] = $this->upload_image('business_licence_number_electronic');
            }
            $param['organization_code'] = $_POST['organization_code'];
            if ($_FILES['organization_code_electronic']['name'] != '') {
                $param['organization_code_electronic'] = $this->upload_image('organization_code_electronic');
            }
            if ($_FILES['general_taxpayer']['name'] != '') {
                $param['general_taxpayer'] = $this->upload_image('general_taxpayer');
            }
            $param['bank_account_name'] = $_POST['bank_account_name'];
            $param['bank_account_number'] = $_POST['bank_account_number'];
            $param['bank_name'] = $_POST['bank_name'];
            $param['bank_code'] = $_POST['bank_code'];
            $param['bank_address'] = $_POST['bank_address'];
            if ($_FILES['bank_licence_electronic']['name'] != '') {
                $param['bank_licence_electronic'] = $this->upload_image('bank_licence_electronic');
            }
            $param['settlement_bank_account_name'] = $_POST['settlement_bank_account_name'];
            $param['settlement_bank_account_number'] = $_POST['settlement_bank_account_number'];
            $param['settlement_bank_name'] = $_POST['settlement_bank_name'];
            $param['settlement_bank_code'] = $_POST['settlement_bank_code'];
            $param['settlement_bank_address'] = $_POST['settlement_bank_address'];
            $param['tax_registration_certificate'] = $_POST['tax_registration_certificate'];
            $param['taxpayer_id'] = $_POST['taxpayer_id'];
            if ($_FILES['tax_registration_certificate_electronic']['name'] != '') {
                $param['tax_registration_certificate_electronic'] = $this->upload_image('tax_registration_certificate_electronic');
            }
            $result = Model('store_joinin')->editStoreJoinin(array('member_id' => $member_id), $param);
            if ($result) {
		//好商城V3-B11 更新店铺信息
	    	$store_update = array();
		$store_update['store_company_name']=$param['company_name'];
		$store_update['area_info']=$param['company_address'];
		$store_update['store_address']=$param['company_address_detail'];
		$model_store = Model('store');
		$store_info = $model_store->getStoreInfo(array('member_id'=>$member_id));
		if(!empty($store_info)) {						
                $r=$model_store->editStore($store_update, array('member_id'=>$member_id));

		$this->log('编辑店铺信息' . '[ID:' . $r. ']', 1);
		}
                showMessage(L('nc_common_op_succ'), 'index.php?act=store&op=store');
            } else {
                showMessage(L('nc_common_op_fail'));
            }
        }
    }
    
    private function upload_image($file) {
        $pic_name = '';
        $upload = new UploadFile();
        $uploaddir = ATTACH_PATH.DS.'store_joinin'.DS;
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
    
    /**
     * 店铺经营类目管理
     */
    public function store_bind_classOp() {
        $store_id = intval($_GET['store_id']);

        $model_store = Model('store');
        $model_store_bind_class = Model('store_bind_class');
        $model_goods_class = Model('goods_class');

        $gc_list = $model_goods_class->getGoodsClassListByParentId(0);
        Tpl::output('gc_list',$gc_list);

        $store_info = $model_store->getStoreInfoByID($store_id);
        if(empty($store_info)) {
            showMessage(L('param_error'),'','','error');
        }
        Tpl::output('store_info', $store_info);

        $store_bind_class_list = $model_store_bind_class->getStoreBindClassList(array('store_id'=>$store_id,'state'=>array('in',array(1,2))), null);
        $goods_class = Model('goods_class')->getGoodsClassIndexedListAll();
        for($i = 0, $j = count($store_bind_class_list); $i < $j; $i++) {
            $store_bind_class_list[$i]['class_1_name'] = $goods_class[$store_bind_class_list[$i]['class_1']]['gc_name'];
            $store_bind_class_list[$i]['class_2_name'] = $goods_class[$store_bind_class_list[$i]['class_2']]['gc_name'];
            $store_bind_class_list[$i]['class_3_name'] = $goods_class[$store_bind_class_list[$i]['class_3']]['gc_name'];
        }
        Tpl::output('store_bind_class_list', $store_bind_class_list);

        Tpl::showpage('store_bind_class');
    }

    /**
     * 添加经营类目
     */
    public function store_bind_class_addOp() {
        $store_id = intval($_POST['store_id']);
        $commis_rate = intval($_POST['commis_rate']);
        if($commis_rate < 0 || $commis_rate > 100) {
            showMessage(L('param_error'), '');
        }
        list($class_1, $class_2, $class_3) = explode(',', $_POST['goods_class']);

        $model_store_bind_class = Model('store_bind_class');

        $param = array();
        $param['store_id'] = $store_id;
        $param['class_1'] = $class_1;
        $param['state'] = 1;
        if(!empty($class_2)) {
            $param['class_2'] = $class_2;
        }
        if(!empty($class_3)) {
            $param['class_3'] = $class_3;
        }

        // 检查类目是否已经存在
        $store_bind_class_info = $model_store_bind_class->getStoreBindClassInfo($param);
        if(!empty($store_bind_class_info)) {
            showMessage('该类目已经存在','','','error');
        }

        $param['commis_rate'] = $commis_rate;
        $result = $model_store_bind_class->addStoreBindClass($param);

        if($result) {
            $this->log('删除店铺经营类目，类目编号:'.$result.',店铺编号:'.$store_id);
            showMessage(L('nc_common_save_succ'), '');
        } else {
            showMessage(L('nc_common_save_fail'), '');
        }
    }

    /**
     * 删除经营类目
     */
    public function store_bind_class_delOp() {
        $bid = intval($_POST['bid']);

        $data = array();
        $data['result'] = true;

        $model_store_bind_class = Model('store_bind_class');
        $model_goods = Model('goods');

        $store_bind_class_info = $model_store_bind_class->getStoreBindClassInfo(array('bid' => $bid));
        if(empty($store_bind_class_info)) {
            $data['result'] = false;
            $data['message'] = '经营类目删除失败';
            echo json_encode($data);die;
        }

        // 商品下架
        $condition = array();
        $condition['store_id'] = $store_bind_class_info['store_id'];
        $gc_id = $store_bind_class_info['class_1'].','.$store_bind_class_info['class_2'].','.$store_bind_class_info['class_3'];
        $update = array();
        $update['goods_stateremark'] = '管理员删除经营类目';
        $condition['gc_id'] = array('in', rtrim($gc_id, ','));
        $model_goods->editProducesLockUp($update, $condition);

        $result = $model_store_bind_class->delStoreBindClass(array('bid'=>$bid));

        if(!$result) {
            $data['result'] = false;
            $data['message'] = '经营类目删除失败';
        }
        $this->log('删除店铺经营类目，类目编号:'.$bid.',店铺编号:'.$store_bind_class_info['store_id']);
        echo json_encode($data);die;
    }

    public function store_bind_class_updateOp() {
        $bid = intval($_GET['id']);
        if($bid <= 0) {
            echo json_encode(array('result'=>FALSE,'message'=>Language::get('param_error')));
            die;
        }
        $new_commis_rate = intval($_GET['value']);
        if ($new_commis_rate < 0 || $new_commis_rate >= 100) {
            echo json_encode(array('result'=>FALSE,'message'=>Language::get('param_error')));
            die;
        } else {
            $update = array('commis_rate' => $new_commis_rate);
            $condition = array('bid' => $bid);
            $model_store_bind_class = Model('store_bind_class');
            $result = $model_store_bind_class->editStoreBindClass($update, $condition);
            if($result) {
                $this->log('更新店铺经营类目，类目编号:'.$bid);
                echo json_encode(array('result'=>TRUE));
                die;
            } else {
                echo json_encode(array('result'=>FALSE,'message'=>L('nc_common_op_fail')));
                die;
            }
        }
    }


	/**
	 * 店铺 待审核列表
	 */
	public function fenxiao_joininOp(){
		//店铺列表
		if(!empty($_GET['owner_and_name'])) {
			$condition['member_name'] = array('like','%'.$_GET['owner_and_name'].'%');
		}
		if(!empty($_GET['store_name'])) {
			$condition['store_name'] = array('like','%'.$_GET['store_name'].'%');
		}
		if(!empty($_GET['grade_id']) && intval($_GET['grade_id']) > 0) {
			$condition['sg_id'] = $_GET['grade_id'];
		}
		if(!empty($_GET['status']) && intval($_GET['status']) > 0) {
            $condition['status'] = $_GET['status'] ;
        } else {
            $condition['status'] = array('gt',0);
        }
		$model_fenxiao_joinin = Model('fenxiao_joinin');
		$store_list = $model_fenxiao_joinin->getList($condition, 10, 'status asc');

        $model_store = Model('store');
        $store_result = array();

        foreach ($store_list as $store) {
            $store_info = $model_store->getStoreInfoByID($store['member_id']);
            $store_info['apply_reason'] = $store['apply_reason'];
            $store_info['status'] = $store['status'];
            $store_result[] = $store_info;
        }

        Tpl::output('store_list', $store_result);
        Tpl::output('joinin_state_array', $this->get_fenxiao_joinin_state());

		Tpl::output('page',$model_fenxiao_joinin->showpage('2'));
		Tpl::showpage('fenxiao_joinin');
	}

    private function get_fenxiao_joinin_state() {
        $joinin_state_array = array(
            1 => '审核中',
            2 => '审核通过',
            3 => '审核失败',
        );
        return $joinin_state_array;
    }


	/**
	 * 审核详细页
	 */
	public function fenxiao_joinin_detailOp(){
		$model_fenxiao_joinin = Model('fenxiao_joinin');
        $fenxiao_joinin_detail = $model_fenxiao_joinin->getOne(array('member_id'=>$_GET['member_id']));

        $model_store_joinin = Model('store_joinin');
        $joinin_detail = $model_store_joinin->getOne(array('member_id'=>$_GET['member_id']));

        $joinin_detail['apply_reason'] = $fenxiao_joinin_detail['apply_reason'];
        $joinin_detail['status'] = $fenxiao_joinin_detail['status'];

        $joinin_detail_title = '查看';
        if(in_array(intval($joinin_detail['status']), array(1))) {
            $joinin_detail_title = '审核';
        }

        Tpl::output('joinin_detail_title', $joinin_detail_title);
		Tpl::output('joinin_detail', $joinin_detail);
		Tpl::showpage('fenxiao_joinin.detail');
	}

	/**
	 * 审核
	 */
	public function fenxiao_joinin_verifyOp() {
        $model_fenxiao_joinin = Model('fenxiao_joinin');
        $joinin_detail = $model_fenxiao_joinin->getOne(array('member_id'=>$_POST['member_id']));

        switch (intval($joinin_detail['status'])) {
            case FENXIAO_JOIN_STATE_VERIFY:
                $this->fenxiao_joinin_verify_pass($joinin_detail);
                break;
            default:
                showMessage('参数错误','');
                break;
        }
	}

    private function fenxiao_joinin_verify_pass($joinin_detail) {
        $param = array();
        $param['status'] = $_POST['verify_type'] === 'pass' ? FENXIAO_JOIN_STATE_VERIFY_SUCCESS : FENXIAO_JOIN_STATE_VERIFY_FAIL;
        $param['joinin_message'] = $_POST['joinin_message'];

        $model_fenxiao_joinin = Model('fenxiao_joinin');
        $model_fenxiao_joinin->modify($param, array('member_id'=>$_POST['member_id']));

        $param = array();
        $model_store	= Model('store');
        $param['fenxiao_status'] = $_POST['verify_type'] === 'pass' ? FENXIAO_JOIN_STATE_VERIFY_SUCCESS : FENXIAO_JOIN_STATE_VERIFY_FAIL;
        $model_store->editStore($param, array('member_id'=>$_POST['member_id']));
        showMessage('分销审核完毕','index.php?act=fenxiao_merchant&op=fenxiao_joinin');

    }

    /**
     * 提醒续费
     */
    public function remind_renewalOp() {
        $store_id = intval($_GET['store_id']);
        $store_info = Model('store')->getStoreInfoByID($store_id);
        if (!empty($store_info) && $store_info['store_end_time'] < (TIMESTAMP + 864000) && cookie('remindRenewal'.$store_id) == null) {
            // 发送商家消息
            $param = array();
            $param['code'] = 'store_expire';
            $param['store_id'] = intval($_GET['store_id']);
            $param['param'] = array();
            QueueClient::push('sendStoreMsg', $param);

            setNcCookie('remindRenewal'.$store_id, 1, 86400 * 10);  // 十天
            showMessage('消息发送成功');
        }
            showMessage('消息发送失败');
    }
	    public function delOp()
    {
        $storeId = (int) $_GET['id'];
        $storeModel = model('store');

        $storeArray = $storeModel->field('is_own_shop,store_name')->find($storeId);

        if (empty($storeArray)) {
            showMessage('外驻店铺不存在', '', 'html', 'error');
        }

        if ($storeArray['is_own_shop']) {
            showMessage('不能在此删除自营店铺', '', 'html', 'error');
        }

        $condition = array(
            'store_id' => $storeId,
        );

        if ((int) model('goods')->getGoodsCount($condition) > 0)
            showMessage('已经发布商品的外驻店铺不能被删除', '', 'html', 'error');

        // 完全删除店铺
        $storeModel->delStoreEntirely($condition);
		
		//删除入驻相关 v3-b12
		$member_id = (int) $_GET['member_id'];
		$store_joinin = model('store_joinin');
		$condition = array(
	        'member_id' => $member_id,
        	);
		$store_joinin->drop($condition);
		
        $this->log("删除外驻店铺: {$storeArray['store_name']}");
        showMessage('操作成功', getReferer());
    }
	
	
	//删除店铺操作 v3-b12
	  public function del_joinOp()
    {
        $member_id = (int) $_GET['id'];
        $fenxiao_joinin = model('fenxiao_joinin');
        $condition = array(
            'member_id' => $member_id,
        );

        $fenxiao_joinin->drop($condition);
        $this->log("删除分销申请:".$member_id);
        showMessage('操作成功', getReferer());
    }
    public function newshop_addOp()
    {
        if (chksubmit())
        {
            $memberName = $_POST['member_name'];
            $memberPasswd = (string) $_POST['member_passwd'];

            if (strlen($memberName) < 3 || strlen($memberName) > 15
                || strlen($_POST['seller_name']) < 3 || strlen($_POST['seller_name']) > 15)
                showMessage('账号名称必须是3~15位', '', 'html', 'error');

            if (strlen($memberPasswd) < 6)
                showMessage('登录密码不能短于6位', '', 'html', 'error');

            if (!$this->checkMemberName($memberName))
                showMessage('店主账号已被占用', '', 'html', 'error');

            if (!$this->checkSellerName($_POST['seller_name']))
                showMessage('店主卖家账号名称已被其它店铺占用', '', 'html', 'error');

            try
            {
                $memberId = model('member')->addMember(array(
                    'member_name' => $memberName,
                    'member_passwd' => $memberPasswd,
                    'member_email' => '',
                ));
            }
            catch (Exception $ex)
            {
                showMessage('店主账号新增失败', '', 'html', 'error');
            }

            $storeModel = model('store');

            $saveArray = array();
            $saveArray['store_name'] = $_POST['store_name'];
            $saveArray['member_id'] = $memberId;
            $saveArray['member_name'] = $memberName;
            $saveArray['seller_name'] = $_POST['seller_name'];
            $saveArray['bind_all_gc'] = 1;
            $saveArray['store_state'] = 1;
            $saveArray['store_time'] = time();
            $saveArray['is_own_shop'] = 0;

            $storeId = $storeModel->addStore($saveArray);

            model('seller')->addSeller(array(
                'seller_name' => $_POST['seller_name'],
                'member_id' => $memberId,
                'store_id' => $storeId,
                'seller_group_id' => 0,
                'is_admin' => 1,
            ));
			model('store_joinin')->save(array(
                'seller_name' => $_POST['seller_name'],
				'store_name'  => $_POST['store_name'],
				'member_name' => $memberName,
                'member_id' => $memberId,
				'joinin_state' => 40,
				'company_province_id' => 0,
				'sc_bail' => 0,
				'joinin_year' => 1,
            ));

            // 添加相册默认
            $album_model = Model('album');
            $album_arr = array();
            $album_arr['aclass_name'] = '默认相册';
            $album_arr['store_id'] = $storeId;
            $album_arr['aclass_des'] = '';
            $album_arr['aclass_sort'] = '255';
            $album_arr['aclass_cover'] = '';
            $album_arr['upload_time'] = time();
            $album_arr['is_default'] = '1';
            $album_model->addClass($album_arr);

            //插入店铺扩展表
            $model = Model();
            $model->table('store_extend')->insert(array('store_id'=>$storeId));

            // 删除自营店id缓存
            Model('store')->dropCachedOwnShopIds();

            $this->log("新增外驻店铺: {$saveArray['store_name']}");
            showMessage('操作成功', urlAdmin('store', 'store'));
            return;
        }

        Tpl::showpage('store.newshop.add');
    }

    public function check_seller_nameOp()
    {
        echo json_encode($this->checkSellerName($_GET['seller_name'], $_GET['id']));
        exit;
    }

    private function checkSellerName($sellerName, $storeId = 0)
    {
        // 判断store_joinin是否存在记录
        $count = (int) Model('store_joinin')->getStoreJoininCount(array(
            'seller_name' => $sellerName,
        ));
        if ($count > 0)
            return false;

        $seller = Model('seller')->getSellerInfo(array(
            'seller_name' => $sellerName,
        ));

        if (empty($seller))
            return true;

        if (!$storeId)
            return false;

        if ($storeId == $seller['store_id'] && $seller['seller_group_id'] == 0 && $seller['is_admin'] == 1)
            return true;

        return false;
    }

    public function check_member_nameOp()
    {
        echo json_encode($this->checkMemberName($_GET['member_name']));
        exit;
    }

    private function checkMemberName($memberName)
    {
        // 判断store_joinin是否存在记录
        $count = (int) Model('store_joinin')->getStoreJoininCount(array(
            'member_name' => $memberName,
        ));
        if ($count > 0)
            return false;

        return ! Model('member')->getMemberCount(array(
            'member_name' => $memberName,
        ));
    }
    /**
     * 验证店铺名称是否存在
     */
    public function ckeck_store_nameOp() {
        /**
         * 实例化卖家模型
         */
        $where = array();
        $where['store_name'] = $_GET['store_name'];
        $where['store_id'] = array('neq', $_GET['store_id']);
        $store_info = Model('store')->getStoreInfo($where);
        if(!empty($store_info['store_name'])) {
            echo 'false';
        } else {
            echo 'true';
        }
    }
	    /**
     * 验证店铺名称是否存在
     */
    private function ckeckStoreName($store_name) {
    	/**
    	 * 实例化卖家模型
    	 */
    	$where = array();
    	$where['store_name'] = $store_name;
    	$store_info = Model('store')->getStoreInfo($where);
    	if(!empty($store_info['store_name'])) {
    		return false;
    	} else {
    		return true;
    	}
    }

    public function store_importOp(){
		Tpl::showpage('store_import');    
    }
    
    public function store_import_csvOp(){
    	if (isset($_POST['import'])) {
    		$file = $_FILES['csv_stores'];
    		$file_type = substr(strstr($file['name'], '.'), 1);
    		
			//上传文件存在判断
			if(empty($file['name'])){
    			showMessage('请选择要上传csv的文件!','','html','error');
			}
    		
    		// 检查文件格式
    		if ($file_type != 'csv') {
    			showMessage('文件格式不对,请重新上传!','','html','error');
    			exit;
    		}

    		$handle = fopen($file['tmp_name'], "r");
		    $result = $this->input_csv($handle); //解析csv 
		    $rows = count($result); 
		    if($rows == 0){ 
    			showMessage('没有任何数据!','','html','error');
		        exit; 
		    } 

		    $scounter = 0;
		    
		    $storeModel = model('store');
		    
    		for ($i = 1; $i < $rows; $i++) { 
    			//循环获取各字段值 
    			$store_name = iconv('gb2312', 'utf-8', $result[$i][0]);
    			$member_name = iconv('gb2312', 'utf-8', $result[$i][1]);
    			$seller_name = iconv('gb2312', 'utf-8', $result[$i][2]);
    			$password = iconv('gb2312', 'utf-8', $result[$i][3]);
    			$store_company_name = iconv('gb2312', 'utf-8', $result[$i][4]);
    			$company_name = iconv('gb2312', 'utf-8', $result[$i][5]);
    			$company_address = iconv('gb2312', 'utf-8', $result[$i][6]);
    			$store_address = iconv('gb2312', 'utf-8', $result[$i][7]);
    			$store_zip = iconv('gb2312', 'utf-8', $result[$i][8]);
    			$store_qq = iconv('gb2312', 'utf-8', $result[$i][9]);
    			
    			$store_ww = iconv('gb2312', 'utf-8', $result[$i][10]);    			
    			$store_phone = iconv('gb2312', 'utf-8', $result[$i][11]);
    			$company_employee_count = iconv('gb2312', 'utf-8', $result[$i][12]);
    			$company_registered_capital = iconv('gb2312', 'utf-8', $result[$i][13]);
    			$contacts_name = iconv('gb2312', 'utf-8', $result[$i][14]);
    			$contacts_phone = iconv('gb2312', 'utf-8', $result[$i][15]);
    			$contacts_email = iconv('gb2312', 'utf-8', $result[$i][16]);
    			$business_licence_number = iconv('gb2312', 'utf-8', $result[$i][17]);
    			$business_licence_address = iconv('gb2312', 'utf-8', $result[$i][18]);
    			$business_licence_start = iconv('gb2312', 'utf-8', $result[$i][19]);
    			
    			$business_licence_end = iconv('gb2312', 'utf-8', $result[$i][20]);    			
    			$business_sphere = iconv('gb2312', 'utf-8', $result[$i][21]);
    			$organization_code = iconv('gb2312', 'utf-8', $result[$i][22]);
    			$bank_account_name = iconv('gb2312', 'utf-8', $result[$i][23]);
    			$bank_account_number = iconv('gb2312', 'utf-8', $result[$i][24]);
    			$bank_name = iconv('gb2312', 'utf-8', $result[$i][25]);
    			$bank_code = iconv('gb2312', 'utf-8', $result[$i][26]);
    			$bank_address = iconv('gb2312', 'utf-8', $result[$i][27]);
    			$is_settlement_account = iconv('gb2312', 'utf-8', $result[$i][28]);
    			$settlement_bank_account_name = iconv('gb2312', 'utf-8', $result[$i][29]);
    			
    			$settlement_bank_account_number = iconv('gb2312', 'utf-8', $result[$i][30]);
    			$settlement_bank_name = iconv('gb2312', 'utf-8', $result[$i][31]);
    			$settlement_bank_code = iconv('gb2312', 'utf-8', $result[$i][32]);
    			$settlement_bank_address = iconv('gb2312', 'utf-8', $result[$i][33]);
    			$tax_registration_certificate = iconv('gb2312', 'utf-8', $result[$i][34]);
    			$taxpayer_id = iconv('gb2312', 'utf-8', $result[$i][35]);
    			$joinin_year = iconv('gb2312', 'utf-8', $result[$i][36]);

    			if(!$this->ckeckStoreName($store_name))
    			{
    				continue;
    			}
    			if(!$this->checkMemberName($member_name))
    			{
    				continue;    				
    			}
    			if(!$this->checkSellerName($seller_name))
    			{
    				continue;
    			}    			

    			try
    			{
    				$memberId = model('member')->addMember(array(
    						'member_name' => $member_name,
    						'member_passwd' => $password,
    						'member_email' => '',
    				));
    			}
    			catch (Exception $ex)
    			{
    				showMessage('店主账号新增失败', '', 'html', 'error');
    			}
    			
    			$storeModel = model('store');
    			
    			$saveArray = array();
    			$saveArray['store_name'] = $store_name;
    			$saveArray['grade_id'] = 1;
    			$saveArray['member_id'] = $memberId;
    			$saveArray['member_name'] = $member_name;
    			$saveArray['seller_name'] = $seller_name;
    			$saveArray['bind_all_gc'] = 0;
    			$saveArray['store_state'] = 1;
    			$saveArray['store_time'] = time();
    			$saveArray['store_company_name'] = $store_company_name;
    			$saveArray['store_address'] = $store_address;
    			$saveArray['store_zip'] = $store_zip;
    			$saveArray['store_qq'] = $store_qq;
    			$saveArray['store_ww'] = $store_ww;
    			$saveArray['store_phone'] = $store_phone;
    			
    			$storeId = $storeModel->addStore($saveArray);

	            model('seller')->addSeller(array(
	                'seller_name' => $seller_name,
	                'member_id' => $memberId,
	                'store_id' => $storeId,
	                'seller_group_id' => 0,
	                'is_admin' => 1,
	            ));

	            $store_joinModel = model('store_joinin');
    			$save_joinArray = array();
    			$save_joinArray['member_id'] = $memberId;
    			$save_joinArray['member_name'] = $member_name;
    			$save_joinArray['company_name'] = $company_name;
    			$save_joinArray['company_address'] = $company_address;
    			$save_joinArray['company_address_detail'] = $store_address;
    			$save_joinArray['company_phone'] = $store_phone;
    			$save_joinArray['company_employee_count'] = $company_employee_count;
    			$save_joinArray['company_registered_capital'] = $company_registered_capita;
    			$save_joinArray['contacts_name'] = $contacts_name;
    			$save_joinArray['contacts_phone'] = $contacts_phone;
    			$save_joinArray['contacts_email'] = $contacts_email;
    			$save_joinArray['business_licence_number'] = $business_licence_number;
    			$save_joinArray['business_licence_address'] = $business_licence_address;
    			$save_joinArray['business_licence_start'] = $business_licence_start;
    			$save_joinArray['business_licence_end'] = $business_licence_end;
    			$save_joinArray['business_sphere'] = $business_sphere;
    			$save_joinArray['organization_code'] = $organization_code;
    			$save_joinArray['general_taxpayer'] = $general_taxpayer;
    			$save_joinArray['bank_account_name'] = $bank_account_name;
    			$save_joinArray['bank_account_number'] = $bank_account_number;
    			$save_joinArray['bank_name'] = $bank_name;
    			$save_joinArray['bank_code'] = $bank_code;
    			$save_joinArray['bank_address'] = $bank_address;
    			$save_joinArray['is_settlement_account'] = $is_settlement_account;
    			if($is_settlement_account == '是')
    			{
    				//2独立
    				$save_joinArray['is_settlement_account'] = 2;
    				$save_joinArray['settlement_bank_account_name'] = $settlement_bank_account_name;
    				$save_joinArray['settlement_bank_account_number'] = $settlement_bank_account_number;
    				$save_joinArray['settlement_bank_name'] = $settlement_bank_name;
    				$save_joinArray['settlement_bank_code'] = $settlement_bank_code;    	
    				$save_joinArray['settlement_bank_address'] = $settlement_bank_address;   			
    			}
    			else 
    			{ 				
    				//1非独立
    				$save_joinArray['is_settlement_account'] = 1;
    				$save_joinArray['settlement_bank_account_name'] = $bank_account_name;
    				$save_joinArray['settlement_bank_account_number'] = $bank_account_number;
    				$save_joinArray['settlement_bank_name'] = $bank_name;
    				$save_joinArray['settlement_bank_code'] = $bank_code;    	
    				$save_joinArray['settlement_bank_address'] = $bank_address;   
    			}
    			$save_joinArray['tax_registration_certificate'] = $tax_registration_certificate;
    			$save_joinArray['taxpayer_id'] = $taxpayer_id;
    			$save_joinArray['seller_name'] = $seller_name;
    			$save_joinArray['store_name'] = $store_name;
    			$save_joinArray['joinin_state'] = 40;
    			$save_joinArray['joinin_year'] = $joinin_year;
    			$save_joinArray['company_name'] = $company_name;
    			$save_joinArray['company_name'] = $company_name;
    			
    			
    			$store_joinModel->save($save_joinArray);
	            
	            // 添加相册默认
	            $album_model = Model('album');
	            $album_arr = array();
	            $album_arr['aclass_name'] = '默认相册';
	            $album_arr['store_id'] = $storeId;
	            $album_arr['aclass_des'] = '';
	            $album_arr['aclass_sort'] = '255';
	            $album_arr['aclass_cover'] = '';
	            $album_arr['upload_time'] = time();
	            $album_arr['is_default'] = '1';
	            $album_model->addClass($album_arr);

	            //插入店铺扩展表
	            $model = Model();
	            $model->table('store_extend')->insert(array('store_id'=>$storeId));
	            
	            $scounter++;
	            
    		} 
    		//$data_values = substr($data_values,0,-1); //去掉最后一个逗号 
    		fclose($handle); //关闭指针 

            showMessage('操作成功,成功导入 '.strval($scounter).' 条数据' , urlAdmin('store', 'store'));
            return;
    		
		    /*
    		$row = 0;    		
    		while ($data = fgetcsv($handle, 10000)) {
    			$row++;
    			if ($row == 1) continue;
    			$num = count($data);
    			for ($i = 0; $i < $num; $i++) {
    				$t=iconv('gb2312', 'utf-8', $data[$i]); 
    				echo $t.
    				"<br>";
    			}
    		}
    		fclose($handle);
    		*/
    	}
    }
    
    /*
     * 解析csv
     */
    private function input_csv($handle) {
    	$out = array ();
    	$n = 0;
    	while ($data = fgetcsv($handle, 10000)) {
    		$num = count($data);
    		for ($i = 0; $i < $num; $i++) {
    			$out[$n][$i] = $data[$i];
    		}
    		$n++;
    	}
    	return $out;
    }

    /**
     * 分销员等级
     */
    public function gradeOp(){
        /**
         * 读取语言包
         */
        Language::read('store_grade,store');

        $lang	= Language::getLangContent();

        $model_grade = Model('fenxiao_merchant_grade');

        $condition = array();
        $grade_list = $model_grade->getGradeList($condition);

        Tpl::output('like_sg_name',trim($_POST['like_sg_name']));
        Tpl::output('grade_list',$grade_list);

        Tpl::showpage('fenxiao_merchant_grade.index');
    }

    /**
     * 等级编辑
     */
    public function grade_editOp(){
        Language::read('store_grade,store');

        $lang	= Language::getLangContent();

        $model_grade = Model('fenxiao_merchant_grade');
        if (chksubmit()){
            $update_array = array();
            $update_array['fmg_id'] = intval($_POST['fmg_id']);
            $update_array['fmg_name'] = trim($_POST['fmg_name']);
            $update_array['fmg_goods_limit'] = trim($_POST['fmg_goods_limit']);
            $update_array['fmg_member_limit'] = trim($_POST['fmg_member_limit']);
            $update_array['fmg_points'] = trim($_POST['fmg_points']);

            $result = $model_grade->update($update_array);
            if ($result){
                dkcache('fenxiao_merchant_grade');
                $this->log(L('nc_edit,store_grade').'['.$_POST['sg_name'].']',1);
                showMessage($lang['nc_common_save_succ']);
            }else {
                showMessage($lang['nc_common_save_fail']);
            }
        }

        $grade_array = $model_grade->getOneGrade(intval($_GET['fmg_id']));

        if (empty($grade_array)){
            showMessage($lang['illegal_parameter']);
        }

        Tpl::output('grade_array',$grade_array);
        Tpl::showpage('fenxiao_merchant_grade.edit');
    }

}
