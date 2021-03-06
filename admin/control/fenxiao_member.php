<?php
/**
 * 分销员管理
 *
 *
 *
 **by xiaolong*/

defined('InShopNC') or exit('Access Invalid!');

class fenxiao_memberControl extends SystemControl{
	const EXPORT_SIZE = 1000;
	public function __construct(){
		parent::__construct();
        Language::read('fenxiao_member');
		Language::read('member');
        Language::read('store');
	}

	/**
	 * 会员管理
	 */
	public function memberOp(){
		$lang	= Language::getLangContent();
		$model_member = Model('member');

        if (!isset($_GET['fenxiao_status']))
            $_GET['fenxiao_status'] = 2;
		//会员级别
		$member_grade = $model_member->getMemberGradeArr();
		if ($_GET['search_field_value'] != '') {
    		switch ($_GET['search_field_name']){
    			case 'member_name':
    				$condition['member_name'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
    			case 'member_email':
    				$condition['member_email'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
				//好商 城v3- b11
				case 'member_mobile':
    				$condition['member_mobile'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
    			case 'member_truename':
    				$condition['member_truename'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
    		}
		}
		switch ($_GET['search_state']){
			case 'no_informallow':
				$condition['inform_allow'] = '2';
				break;
			case 'no_isbuy':
				$condition['is_buy'] = '0';
				break;
			case 'no_isallowtalk':
				$condition['is_allowtalk'] = '0';
				break;
			case 'no_memberstate':
				$condition['member_state'] = '0';
				break;
		}
		//会员等级
		$search_grade = intval($_GET['search_grade']);
		if ($search_grade >= 0 && $member_grade){
		    $condition['member_exppoints'] = array(array('egt',$member_grade[$search_grade]['exppoints']),array('lt',$member_grade[$search_grade+1]['exppoints']),'and');
		}
		//排序
		$order = trim($_GET['search_sort']);
		if (empty($order)) {
		    $order = 'member_id desc';
		}

        if (!empty($_GET['fenxiao_status']))
            $condition['fenxiao_status'] = $_GET['fenxiao_status'];

        $member_list = $model_member->getMemberList($condition, '*', 10, $order);
		//整理会员信息
		if (is_array($member_list)){
			foreach ($member_list as $k=> $v){
				$member_list[$k]['member_time'] = $v['member_time']?date('Y-m-d H:i:s',$v['member_time']):'';
				$member_list[$k]['member_login_time'] = $v['member_login_time']?date('Y-m-d H:i:s',$v['member_login_time']):'';
				$member_list[$k]['member_grade'] = ($t = $model_member->getOneMemberGrade($v['member_exppoints'], false, $member_grade))?$t['level_name']:'';
			}
		}

        $fenxiao_status_array = $this->_get_fenxiao_status_array();

        $new_member_list = array();
        foreach ($member_list as $member) {
            $member['fenxiao_status'] = $fenxiao_status_array[intval($member['fenxiao_status'])];
            $new_member_list[] = $member;
        }

        Tpl::output('member_grade',$member_grade);
		Tpl::output('search_sort',trim($_GET['search_sort']));
		Tpl::output('search_field_name',trim($_GET['search_field_name']));
		Tpl::output('search_field_value',trim($_GET['search_field_value']));
		Tpl::output('member_list',$new_member_list);
		Tpl::output('page',$model_member->showpage());
        Tpl::output('fenxiao_status', $this->_get_fenxiao_status_array());
        Tpl::showpage('fenxiao_member.index');
	}

	/**
	 * 会员修改
	 */
	public function member_editOp(){
		$lang	= Language::getLangContent();
		$model_member = Model('member');
		/**
		 * 保存
		 */
		if (chksubmit()){

				$update_array = array();
				$update_array['member_id']			= intval($_POST['member_id']);

				$update_array['member_truename']	= $_POST['member_truename'];
				$update_array['member_mobile'] 		= $_POST['member_mobile'];
                $update_array['business_licence_number'] 		= $_POST['business_licence_number'];
                $update_array['alipay_num'] 		= $_POST['alipay_num'];
                $update_array['weixin_num'] 		= $_POST['weixin_num'];
                $update_array['fenxiao_status'] 		= $_POST['fenxiao_status'];

				$result = $model_member->editMember(array('member_id'=>intval($_POST['member_id'])),$update_array);
				if ($result){
					$url = array(
					array(
					'url'=>'index.php?act=fenxiao_member&op=member',
					'msg'=>$lang['member_edit_back_to_list'],
					),
					array(
					'url'=>'index.php?act=fenxiao_member&op=member_edit&member_id='.intval($_POST['member_id']),
					'msg'=>$lang['member_edit_again'],
					),
					);
					$this->log(L('nc_edit,member_index_name').'[ID:'.$_POST['member_id'].']',1);
					showMessage($lang['member_edit_succ'],$url);
				}else {
					showMessage($lang['member_edit_fail']);
				}

		}
		$condition['member_id'] = intval($_GET['member_id']);
		$member_array = $model_member->getMemberInfo($condition);

		Tpl::output('member_array',$member_array);
        Tpl::output('fenxiao_status', $this->_get_fenxiao_status_array());
        Tpl::showpage('fenxiao_member.edit');
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
	 * ajax操作
	 */
	public function ajaxOp(){
		switch ($_GET['branch']){
			/**
			 * 验证会员是否重复
			 */
			case 'check_user_name':
				$model_member = Model('member');
				$condition['member_name']	= $_GET['member_name'];
				$condition['member_id']	= array('neq',intval($_GET['member_id']));
				$list = $model_member->getMemberInfo($condition);
				if (empty($list)){
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
				/**
			 * 验证邮件是否重复
			 */
			case 'check_email':
				$model_member = Model('member');
				$condition['member_email'] = $_GET['member_email'];
				$condition['member_id'] = array('neq',intval($_GET['member_id']));
				$list = $model_member->getMemberInfo($condition);
				if (empty($list)){
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
		}
	}

    /**
     * 分销员 待审核列表
     */
    public function fenxiao_joininOp(){
        if (!isset($_GET['status']))
            $_GET['status'] = 1;

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
        $model_fenxiao_joinin = Model('fenxiao_member_joinin');
        $member_list = $model_fenxiao_joinin->getList($condition, 10, 'status asc');

        $model_member = Model('member');
        $member_result = array();

        foreach ($member_list as $member) {
            $member_info = $model_member->getMemberInfoByID($member['member_id']);
            $member_info['apply_reason'] = $member['apply_reason'];
            $member_info['status'] = $member['status'];
            $member_info['member_truename'] = $member['member_truename'];
            $member_result[] = $member_info;
        }
        Tpl::output('member_list', $member_result);
        Tpl::output('joinin_state_array', $this->get_fenxiao_joinin_state());

        Tpl::output('page',$model_fenxiao_joinin->showpage('2'));
        Tpl::showpage('fenxiao_member_joinin');
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
        $model_fenxiao_joinin = Model('fenxiao_member_joinin');
        $fenxiao_joinin_detail = $model_fenxiao_joinin->getOne(array('member_id'=>$_GET['member_id']));

        $model_member = Model('member');
        $joinin_detail = $model_member->getMemberInfo(array('member_id'=>$_GET['member_id']));

        $fenxiao_joinin_detail['member_name'] = $joinin_detail['member_name'];

        $joinin_detail_title = '查看';
        if(in_array(intval($fenxiao_joinin_detail['status']), array(1))) {
            $joinin_detail_title = '审核';
        }

        Tpl::output('joinin_detail_title', $joinin_detail_title);
        Tpl::output('joinin_detail', $fenxiao_joinin_detail);
        Tpl::showpage('fenxiao_member_joinin.detail');
    }

    /**
     * 审核
     */
    public function fenxiao_joinin_verifyOp() {
        $model_fenxiao_joinin = Model('fenxiao_member_joinin');
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

        $model_fenxiao_joinin = Model('fenxiao_member_joinin');
        $model_fenxiao_joinin->modify($param, array('member_id'=>$_POST['member_id']));

        $joinin_detail = $model_fenxiao_joinin->getOne(array('member_id'=>$_POST['member_id']));

        $param = array();
        $model_member	= Model('member');
        $param['fenxiao_status'] = $_POST['verify_type'] === 'pass' ? FENXIAO_JOIN_STATE_VERIFY_SUCCESS : FENXIAO_JOIN_STATE_VERIFY_FAIL;
        $param['member_truename'] = $joinin_detail['member_truename'];
//        $param['member_mobile'] = $joinin_detail['member_mobile'];
        $param['business_licence_number'] = $joinin_detail['business_licence_number'];
//        $param['alipay_num'] = $joinin_detail['alipay_num'];
//        $param['weixin_num'] = $joinin_detail['weixin_num'];

        $model_member->editMember( array('member_id'=>$_POST['member_id']),$param);
        showMessage('分销审核完毕','index.php?act=fenxiao_member&op=fenxiao_joinin');

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

        $model_grade = Model('fenxiao_member_grade');

        $condition = array();
        $grade_list = $model_grade->getGradeList($condition);

        Tpl::output('like_sg_name',trim($_POST['like_sg_name']));
        Tpl::output('grade_list',$grade_list);
        Tpl::showpage('fenxiao_member_grade.index');
    }

    /**
     * 等级编辑
     */
    public function grade_editOp(){
        Language::read('store_grade,store');

        $lang	= Language::getLangContent();

        $model_grade = Model('fenxiao_member_grade');
        if (chksubmit()){
            $update_array = array();
            $update_array['fmg_id'] = intval($_POST['fmg_id']);
            $update_array['fmg_name'] = trim($_POST['fmg_name']);
            $update_array['fmg_goods_limit'] = trim($_POST['fmg_goods_limit']);
            $update_array['fmg_points'] = trim($_POST['fmg_points']);

            $upload		= new UploadFile();

            if($_FILES['adv_pic']['name'] != ''){
                $upload->set('default_dir',ATTACH_ADV);
                $result = $upload->upfile('adv_pic');
                if (!$result){
                    showMessage($upload->error,'','','error');
                }
                $update_array['fmg_icon'] = $upload->file_name;
            }

            $result = $model_grade->update($update_array);
            if ($result){
                dkcache('fenxiao_member_grade');
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
        Tpl::showpage('fenxiao_member_grade.edit');
    }


}
