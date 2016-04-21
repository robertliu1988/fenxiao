<?php
/**
 * 买家 我的分销
 *
 * by xiaolong
 */


defined('InShopNC') or exit('Access Invalid!');

class member_fenxiao_informationControl extends BaseMemberControl {

    public function __construct() {
        parent::__construct();
        Language::read('member_member_index');
        Language::read ('member_store_goods_index');
    }

    /**
     * 我的分销商品
     *
     */
    public function indexOp() {
        $fenxiao_config = Model('fenxiao_config');
        $fenxiao_all = $fenxiao_config->getFenxiaoConfigInfo(array('config_key'=>'fenxiao_all'));

        $model_member = Model('member');
        $member_info = $model_member->getMemberInfo(array('member_id'=>$_SESSION['member_id']));
        $fenxiao_status = $member_info['fenxiao_status'];

        $fenxiao_status_array = array(
            -1 => '关闭',
            1 => '审核中',
            2 => '开启',
            3 => '审核失败',
            4 => '封禁'
        );

        //根据分销总开关首先进行判断
        if ($fenxiao_all['config_value'] == 0)
            $member_info['fenxiao_level'] = '暂未开启';
        else if ($fenxiao_status == -1 || $fenxiao_status == 3){
            $href = urlShop('member_fenxiao_joinin', 'index', array());
            $member_info['fenxiao_level'] = "<a href='$href' target='_blank'>申请分销商</a>";
        }
        else if ($fenxiao_status == 2){
            $model_grade = Model('fenxiao_member_grade');
            $grade_list = $model_grade->getGradeList();
            $level = '未定义';
            $fenxiao_points = $member_info['fenxiao_points'];
            foreach ($grade_list as $grade) {
                if (intval($fenxiao_points) >= $grade['fmg_points'])
                    $level = $grade['fmg_name'];
            }

            $href = SHOP_SITE_URL."/index.php?act=article&op=show&article_id=42";
            $member_info['fenxiao_level'] = "<a href='$href' target='_blank'>$level</a>";
        }
        else{
            $member_info['fenxiao_level'] = $fenxiao_status_array[$fenxiao_status];
        }

        self::profile_menu('fenxiao_information','fenxiao_information');

        Tpl::output('member_info',$member_info);
        Tpl::showpage('member_fenxiao.information');
    }
	
	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_type,$menu_key=''){
		$menu_array	= array(
			array('menu_key'=>'fenxiao_information','menu_name'=>'我的分销',	'menu_url'=>'index.php?act=member_fenxiao_information&op=index'),
		);
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
    
}
