<?php
/**
 * 基础配置，总开关
 *
 *
 *
 **by xiaolong
 */

defined('InShopNC') or exit('Access Invalid!');
class fenxiao_configControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('fenxiao');
	}

	/**
	 * 基础配置
	 */
	public function indexOp(){
		$fenxiao_config = Model('fenxiao_config');
		$config_list = $fenxiao_config->getFenxiaoConfigList();
//        var_dump($config_list);
//        exit;
		Tpl::output('fenxiao_config',$config_list);
		Tpl::showpage('fenxiao_config.list');
	}

	/**
	 * 编辑
	 */
	public function editOp(){
        $fenxiao_config = Model('fenxiao_config');
		if (chksubmit()){
            $config_key = $_GET["config_key"];
			$data = array();
			$data['config_value'] = intval($_POST["config_value"]);

            $fenxiao_config->editFenxiaoConfig($data,array('config_key'=>$config_key));
			showMessage(Language::get('nc_common_save_succ'),'index.php?act=fenxiao_config&op=system');
		}

        $config_key = $_GET["config_key"];

		$config_info = $fenxiao_config->getFenxiaoConfigInfo(array('config_key'=>$config_key));

		Tpl::output('config_info',$config_info);
		Tpl::showpage('fenxiao_config.edit');
	}
}
