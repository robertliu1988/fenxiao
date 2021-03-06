<?php
/**
 * 分销店铺列表
 *
 * by xiaolong
*/


defined('InShopNC') or exit('Access Invalid!');

class fenxiao_storeControl extends BaseHomeControl {
	/**
	 * 店铺列表
	 */
	public function indexOp(){
		//读取语言包
		Language::read('home_store_class_index');	
		$lang	= Language::getLangContent();
				
		//店铺类目快速搜索
		$class_list = ($h = F('store_class')) ? $h : rkcache('store_class',true,'file');
		if (!key_exists($_GET['cate_id'],$class_list)) $_GET['cate_id'] = 0;
		Tpl::output('class_list',$class_list);

		//店铺搜索
		$model = Model();
		$condition = array();
		$keyword = trim($_GET['keyword']);
		if(C('fullindexer.open') && !empty($keyword)){
			//全文搜索
			$condition = $this->full_search($keyword);
		}else{
			if ($keyword != ''){
				$condition['store_name|store_zy'] = array('like','%'.$keyword.'%');
			}
			
			if ($_GET['user_name'] != ''){
				$condition['member_name'] = trim($_GET['user_name']);
			}
		}
		if (!empty($_GET['area_info'])){
			//v3-b12 修复店铺按地区搜索
			$tabs = preg_split("#\s+#", $_GET['area_info'], -1, PREG_SPLIT_NO_EMPTY);
			$len=count($tabs);
			$area_name=$tabs[$len-1];
			if($area_name)
			{
				$area_name=trim($area_name);
				$condition['area_info'] = array('like','%'.$area_name.'%');
			}
		}
		if ($_GET['cate_id'] > 0){
			$child = array_merge((array)$class_list[$_GET['cate_id']]['child'],array($_GET['cate_id']));
			$condition['sc_id'] = array('in',$child);
		}

		$condition['store_state'] = 1;

		if (!in_array($_GET['order'],array('desc','asc'))){
			unset($_GET['order']);
		}
		if (!in_array($_GET['key'],array('store_sales','store_credit'))){
			unset($_GET['key']);
		}

		$order = 'store_sort asc';

        if (isset($condition['store.store_id'])){
            $condition['store_id'] = $condition['store.store_id'];unset($condition['store.store_id']);
        }
        
        $model_store = Model('store');
        $store_list = $model_store->where($condition)->order($order)->page(10)->select();
        //获取店铺商品数，推荐商品列表等信息
        $store_list = $model_store->getStoreSearchListForFenxiao($store_list);

        //print_r($store_list);exit();
        //信用度排序
        if($_GET['key'] == 'store_credit') {
            if($_GET['order'] == 'desc') {
                $store_list = sortClass::sortArrayDesc($store_list, 'store_credit_average');
            }else {
                $store_list = sortClass::sortArrayAsc($store_list, 'store_credit_average');
            }
        }else if($_GET['key'] == 'store_sales') {//销量排行
            if($_GET['order'] == 'desc') {
                $store_list = sortClass::sortArrayDesc($store_list, 'num_sales_jq');
            }else {
                $store_list = sortClass::sortArrayAsc($store_list, 'num_sales_jq');
            }
        }
		Tpl::output('store_list',$store_list);
		
		Tpl::output('show_page',$model->showpage(2));

		// 页面输出
        Tpl::output('index_sign', 'fenxiao');
		//当前位置
		if (intval($_GET['cate_id']) > 0){
			$nav_link[1]['link'] = 'index.php?act=shop_search';
			$nav_link[1]['title'] = $lang['site_search_store'];
			$nav =$class_list[$_GET['cate_id']];
			//如果有父级
			if ($nav['sc_parent_id'] > 0){
				$tmp = $class_list[$nav['sc_parent_id']];
				//存入父级
				$nav_link[] = array(
					'title'=>$tmp['sc_name'],
					'link'=>"index.php?act=store_list&cate_id=".$nav['sc_parent_id']
				);
			}
			//存入当前级
			$nav_link[] = array(
				'title'=>$nav['sc_name']
			);
		}else{
			$nav_link[1]['link'] = 'index.php';
			$nav_link[1]['title'] = $lang['homepage'];
			$nav_link[2]['title'] = $lang['site_search_store'];
		}

		$purl = $this->getParam();
		Tpl::output('nav_link_list',$nav_link);
		Tpl::output('purl', urlShop($purl['act'], $purl['op'], $purl['param']));

		//SEO
		Model('seo')->type('index')->show();
		Tpl::output('html_title',(empty($_GET['keyword']) ? '' : $_GET['keyword'].' - ').C('site_name').$lang['nc_common_search']);
		
        Tpl::showpage('fenxiao_store');
	}


    /**
     * 店铺分销商品列表
     */
    public function goodsOp(){
        //读取语言包
        Language::read('home_store_class_index');
        Language::read ('member_store_goods_index');

        $lang	= Language::getLangContent();

        //店铺类目快速搜索
        $class_list = ($h = F('store_class')) ? $h : rkcache('store_class',true,'file');
        if (!key_exists($_GET['cate_id'],$class_list)) $_GET['cate_id'] = 0;
        Tpl::output('class_list',$class_list);

        //店铺搜索
        $model = Model();
        $condition = array();

        $condition['store_id'] = $_GET['store_id'];
        $model_store = Model('store');
        $store_list = $model_store->where($condition)->select();
        //获取店铺商品数，推荐商品列表等信息
        $store_list = $model_store->getStoreFenxiaoList($store_list);
//        print_r($store_list);exit();
        //信用度排序
        if($_GET['key'] == 'store_credit') {
            if($_GET['order'] == 'desc') {
                $store_list = sortClass::sortArrayDesc($store_list, 'store_credit_average');
            }else {
                $store_list = sortClass::sortArrayAsc($store_list, 'store_credit_average');
            }
        }else if($_GET['key'] == 'store_sales') {//销量排行
            if($_GET['order'] == 'desc') {
                $store_list = sortClass::sortArrayDesc($store_list, 'num_sales_jq');
            }else {
                $store_list = sortClass::sortArrayAsc($store_list, 'num_sales_jq');
            }
        }

        //分销图标
        $model_grade = Model('fenxiao_member_grade');
        $grade_list = $model_grade->getGradeList();
        Tpl::output('grade_list', $grade_list );

        $goods_list = $store_list[0]['search_list_goods'];
        $goods_id_arr = array();
        $final_goods_list = array();
        foreach ($goods_list as $goods) {
            $goods_id_arr[] = $goods['goods_id'];
            $goods['num_sales'] = 0;
            $goods['money_sales'] = 0;
            $final_goods_list[$goods['goods_id']] = $goods;
        }
        $model = Model();
        $goods_sale_array = $model->table('fenxiao_fanli')->field('goods_id,sum(goods_num) as order_count,sum(fanli_money) as money_sum')->where(array('goods_id'=>array('in',implode(',',$goods_id_arr))))->group('goods_id')->select();

        foreach ((array)$goods_sale_array as $value) {
            $final_goods_list[$value['goods_id']]['num_sales'] = $value['order_count'];
            $final_goods_list[$value['goods_id']]['money_sales'] = intval($value['money_sum']);
        }


        Tpl::output('goods_list',$final_goods_list);

        Tpl::output('store_list',$store_list);

        Tpl::output('show_page',$model->showpage(2));
        // 页面输出
        Tpl::output('index_sign', 'fenxiao');
        //当前位置
        if (intval($_GET['cate_id']) > 0){
            $nav_link[1]['link'] = 'index.php?act=shop_search';
            $nav_link[1]['title'] = $lang['site_search_store'];
            $nav =$class_list[$_GET['cate_id']];
            //如果有父级
            if ($nav['sc_parent_id'] > 0){
                $tmp = $class_list[$nav['sc_parent_id']];
                //存入父级
                $nav_link[] = array(
                    'title'=>$tmp['sc_name'],
                    'link'=>"index.php?act=store_list&cate_id=".$nav['sc_parent_id']
                );
            }
            //存入当前级
            $nav_link[] = array(
                'title'=>$nav['sc_name']
            );
        }else{
            $nav_link[1]['link'] = 'index.php';
            $nav_link[1]['title'] = $lang['homepage'];
            $nav_link[2]['title'] = $lang['site_search_store'];
        }

        $purl = $this->getParam();
        Tpl::output('nav_link_list',$nav_link);
        Tpl::output('purl', urlShop($purl['act'], $purl['op'], $purl['param']));

        //SEO
        Model('seo')->type('index')->show();
        Tpl::output('html_title',(empty($_GET['keyword']) ? '' : $_GET['keyword'].' - ').C('site_name').$lang['nc_common_search']);

        Tpl::showpage('fenxiao_store_goods');
    }

    /**
	 * 全文搜索
	 *
	 */
	private function full_search($search_txt){
		$conf = C('fullindexer');
		import('libraries.sphinx');
		$cl = new SphinxClient();
		$cl->SetServer($conf['host'], $conf['port']);
		$cl->SetConnectTimeout(1);
		$cl->SetArrayResult(true);
		$cl->SetRankingMode($conf['rankingmode']?$conf['rankingmode']:0);
		$cl->setLimits(0,$conf['querylimit']);
	
		$matchmode = $conf['matchmode'];
		$cl->setMatchMode($matchmode);
		
		//可以使用全文搜索进行状态筛选及排序，但需要经常重新生成索引，否则结果不太准，所以暂不使用。使用数据库，速度会慢些
		//		$cl->SetFilter('store_state',array(1),false);
		//		if ($_GET['key'] == 'store_credit'){
		//			$order = $_GET['order'] == 'desc' ? SPH_SORT_ATTR_DESC : SPH_SORT_ATTR_ASC;
		//			$cl->SetSortMode($order,'store_sort');
		//		}
		
		$res = $cl->Query($search_txt, $conf['index_shop']);
		if ($res){
			if (is_array($res['matches'])){
				foreach ($res['matches'] as $value) {
					$matchs_id[] = $value['id'];
				}
			}
		}
		if ($search_txt != ''){
			$condition['store.store_id'] = array('in',$matchs_id);
		}
		return $condition;
	}
	
	function getParam() {
	    $param = $_GET;
	    $purl = array();
	    $purl['act'] = $param['act'];
	    unset($param['act']);
	    $purl['op'] = $param['op'];
	    unset($param['op']); unset($param['curpage']);
	    $purl['param'] = $param;
	    return $purl;
	}
}

class sortClass{
	//升序
	public static function sortArrayAsc($preData,$sortType='store_sort'){
		$sortData = array();
		foreach ($preData as $key_i => $value_i){
			$price_i = $value_i[$sortType];
			$min_key = '';
			$sort_total = count($sortData);
			foreach ($sortData as $key_j => $value_j){
				if($price_i<$value_j[$sortType]){
					$min_key = $key_j+1;
					break;
				}
			}
			if(empty($min_key)){
				array_push($sortData, $value_i);
			}else {
				$sortData1 = array_slice($sortData, 0,$min_key-1);
				array_push($sortData1, $value_i);
				if(($min_key-1)<$sort_total){
					$sortData2 = array_slice($sortData, $min_key-1);
					foreach ($sortData2 as $value){
						array_push($sortData1, $value);
					}
				}
				$sortData = $sortData1;
			}
		}
		return $sortData;
	}
	//降序
	public static function sortArrayDesc($preData,$sortType='store_sort'){
		$sortData = array();
		foreach ($preData as $key_i => $value_i){
			$price_i = $value_i[$sortType];
			$min_key = '';
			$sort_total = count($sortData);
			foreach ($sortData as $key_j => $value_j){
				if($price_i>$value_j[$sortType]){
					$min_key = $key_j+1;
					break;
				}
			}
			if(empty($min_key)){
				array_push($sortData, $value_i);
			}else {
				$sortData1 = array_slice($sortData, 0,$min_key-1);
				array_push($sortData1, $value_i);
				if(($min_key-1)<$sort_total){
					$sortData2 = array_slice($sortData, $min_key-1);
					foreach ($sortData2 as $value){
						array_push($sortData1, $value);
					}
				}
				$sortData = $sortData1;
			}
		}
		return $sortData;
	}
}
