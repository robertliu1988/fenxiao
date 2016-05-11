<?php
/**
 * 分销列表
 *
 **by xiaolong*/


defined('InShopNC') or exit('Access Invalid!');

class fenxiao_goodsControl extends BaseHomeControl {


    //每页显示商品数
    const PAGESIZE = 36;

    //模型对象
    private $_model_search;

    public function indexOp() {
        Language::read('home_goods_class_index');
        $this->_model_search = Model('search');
        //显示左侧分类
        //默认分类，从而显示相应的属性和品牌
        $default_classid = intval($_GET['cate_id']);
        if (intval($_GET['cate_id']) > 0) {
            $goods_class_array = $this->_model_search->getLeftCategory(array($_GET['cate_id']));
        } elseif ($_GET['keyword'] != '') {
            //从TAG中查找分类
            $goods_class_array = $this->_model_search->getTagCategory($_GET['keyword']);
            //取出第一个分类作为默认分类，从而显示相应的属性和品牌
            $default_classid = $goods_class_array[0];
            $goods_class_array = $this->_model_search->getLeftCategory($goods_class_array, 1);;
        }
        Tpl::output('goods_class_array', $goods_class_array);
        Tpl::output('default_classid', $default_classid);

        //优先从全文索引库里查找
        list($indexer_ids,$indexer_count) = $this->_model_search->indexerSearch($_GET,self::PAGESIZE);

        //获得经过属性过滤的商品信息 v3-b12
        list($goods_param, $brand_array, $initial_array, $attr_array, $checked_brand, $checked_attr) = $this->_model_search->getAttr($_GET, $default_classid);
        Tpl::output('brand_array', $brand_array);
        Tpl::output('initial_array', $initial_array);
        Tpl::output('attr_array', $attr_array);
        Tpl::output('checked_brand', $checked_brand);
        Tpl::output('checked_attr', $checked_attr);

        //处理排序
        $order = 'is_own_shop desc,goods_id desc';
        if (in_array($_GET['key'],array('1','2','3'))) {
            $sequence = $_GET['order'] == '1' ? 'asc' : 'desc';
            $order = str_replace(array('1','2','3'), array('goods_salenum','goods_click','goods_promotion_price'), $_GET['key']);
            $order .= ' '.$sequence;
        }
        $model_goods = Model('goods');
        // 字段
        $fields = "goods_id,goods_commonid,goods_name,goods_jingle,gc_id,store_id,store_name,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_good_star,evaluation_count,is_virtual,is_fcode,is_appoint,is_presell,have_gift,fenxiao_v1,fenxiao_v2,fenxiao_v3,fenxiao_v4";

        $condition = array();
        if (is_array($indexer_ids)) {

            //商品主键搜索
            $condition['goods_id'] = array('in',$indexer_ids);
            $goods_list = $model_goods->getGoodsOnlineList($condition, $fields, 0, $order, self::PAGESIZE, null, false);

            //如果有商品下架等情况，则删除下架商品的搜索索引信息
            if (count($goods_list) != count($indexer_ids)) {
                $this->_model_search->delInvalidGoods($goods_list, $indexer_ids);
            }

            pagecmd('setEachNum',self::PAGESIZE);
            pagecmd('setTotalNum',$indexer_count);

        } else {
            //执行正常搜索
            if (isset($goods_param['class'])) {
                $condition['gc_id_'.$goods_param['class']['depth']] = $goods_param['class']['gc_id'];
            }
            if (intval($_GET['b_id']) > 0) {
                $condition['brand_id'] = intval($_GET['b_id']);
            }
            if ($_GET['keyword'] != '') {
                $condition['goods_name|goods_jingle'] = array('like', '%' . $_GET['keyword'] . '%');
            }
            if (intval($_GET['area_id']) > 0) {
                $condition['areaid_1'] = intval($_GET['area_id']);
            }
            if ($_GET['type'] == 1) {
                $condition['is_own_shop'] = 1;
            }
            if ($_GET['gift'] == 1) {
                $condition['have_gift'] = 1;
            }
            if (isset($goods_param['goodsid_array'])){
                $condition['goods_id'] = array('in', $goods_param['goodsid_array']);
            }
	    //v3-b13 按价格搜索
            if (intval($_GET['priceMin']) >= 0) {
                $condition['goods_price'] = array('egt', intval($_GET['priceMin']));
            }
            if (intval($_GET['priceMax']) >= 0) {
                $condition['goods_price'] = array('elt', intval($_GET['priceMax']));
            }
            if (intval($_GET['priceMin']) >= 0 && intval($_GET['priceMax']) >= 0) {
                $condition['goods_price'] = array('between',array(intval($_GET['priceMin']),intval($_GET['priceMax'])));
            }
	    //v3-b13 end
            $condition['is_fenxiao'] = 1;
            $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fields, $order, self::PAGESIZE);
        }


        Tpl::output('show_page1', $model_goods->showpage(4));
        Tpl::output('show_page', $model_goods->showpage(5));

        // 商品多图
        if (!empty($goods_list)) {
            $commonid_array = array(); // 商品公共id数组
            $storeid_array = array();       // 店铺id数组
            foreach ($goods_list as $value) {
                $commonid_array[] = $value['goods_commonid'];
                $storeid_array[] = $value['store_id'];
            }
            $commonid_array = array_unique($commonid_array);
            $storeid_array = array_unique($storeid_array);

            // 商品多图
            $goodsimage_more = Model('goods')->getGoodsImageList(array('goods_commonid' => array('in', $commonid_array)));

            // 店铺
            $store_list = Model('store')->getStoreMemberIDList($storeid_array);
            //搜索的关键字
            $search_keyword = trim($_GET['keyword']);
            foreach ($goods_list as $key => $value) {
                // 商品多图
		//v3-b11 商品列表主图限制不越过5个
		$n=0;
                foreach ($goodsimage_more as $v) {
                    if ($value['goods_commonid'] == $v['goods_commonid'] && $value['store_id'] == $v['store_id'] && $value['color_id'] == $v['color_id']) {
						$n++;
						$goods_list[$key]['image'][] = $v;
						if($n>=5)break;
                    }
                }
                // 店铺的开店会员编号
                $store_id = $value['store_id'];
                $goods_list[$key]['member_id'] = $store_list[$store_id]['member_id'];
                $goods_list[$key]['store_domain'] = $store_list[$store_id]['store_domain'];
                //将关键字置红
                if ($search_keyword){
                    $goods_list[$key]['goods_name_highlight'] = str_replace($search_keyword,'<font style="color:#f00;">'.$search_keyword.'</font>',$value['goods_name']);
                } else {
                    $goods_list[$key]['goods_name_highlight'] = $value['goods_name'];
                }
            }
        }

        //判断每个商品的分销状态
        $final_goods = array();
        $model_fenxiao_goods_member	= Model('fenxiao_goods_member');
        $model_store	= Model('store');
        $model_fenxiao_fanli	= Model('fenxiao_fanli');

        foreach ($goods_list as $goods) {
            $condition = array();
            $condition['goods_id'] = $goods['goods_id'];
            $condition['member_id'] = is_null($_SESSION['member_id'])?-1:$_SESSION['member_id'];
            $info = $model_fenxiao_goods_member->getOne($condition);

            $condition = array();
            $condition['member_id'] = is_null($_SESSION['member_id'])?-1:$_SESSION['member_id'];
            $store_info = $model_store->getStoreInfo($condition);


            $condition = array();
            $condition['store_id'] = $goods['store_id'];
            $goods_store_info = $model_store->getStoreInfo($condition);

            $model_grade = Model('fenxiao_merchant_grade');
            $grade_list = $model_grade->getGradeList();
            $level = '未定义';
            $fenxiao_points = $goods_store_info['fenxiao_points'];
            foreach ($grade_list as $grade) {
                if (intval($fenxiao_points) >= $grade['fmg_points'])
                    $fmg_member_limit = $grade['fmg_member_limit'];
            }

            $common_info = $model_goods->getGoodeCommonInfoByID($goods['goods_commonid'],'fenxiao_time');
            $left_seconds = $common_info['fenxiao_time']-time();
            $left_day = intval($left_seconds/(24*3600));
            $left_hour = intval(($left_seconds%(24*3600))/3600);
            $left_minute = intval((($left_seconds%(24*3600))%3600)/60);
            $left_time = $left_day."天".$left_hour."小时".$left_minute."分";
            $goods['left_time'] = $left_time;

            $condition = array();
            $condition['goods_id'] = $goods['goods_id'];
            $goods['fenxiao_apply_num'] = $model_fenxiao_goods_member->getFenxiaoGoodsMemberCount($condition);
            $goods['fenxiao_apply_num_left'] = $fmg_member_limit - $goods['fenxiao_apply_num'];
            if ($goods['fenxiao_apply_num_left'] <= 0)
                $goods['fenxiao_apply_num_left'] = 0;

            $condition = array();
            $condition['goods_id'] = $goods['goods_id'];
            $fanli_list = $model_fenxiao_fanli->getList($condition);

            $fenxiao_num = 0;
            $fenxiao_money = 0;
            foreach ($fanli_list as $fanli) {
                $fenxiao_num += $fanli['goods_num'];
                $fenxiao_money += $fanli['fanli_money'];
            }
            $goods['fenxiao_num'] = $fenxiao_num;
            $goods['fenxiao_money'] = $fenxiao_money;

            if ($store_info['store_id'] == $goods['store_id'])
                $goods['member_fenxiao'] = -1;//无法分销
            else if (!empty($info)){
                if ($info['status'] == 1)
                    $goods['member_fenxiao'] = 2;//已分销
                else
                    $goods['member_fenxiao'] = 1;//分销审核中
            }
            else if ( $goods['fenxiao_apply_num_left'] == 0)
                $goods['member_fenxiao'] = -2;//分销已满
            else
                $goods['member_fenxiao'] = 0;//未分销


            $final_goods[] = $goods;
        }

        Tpl::output('goods_list', $final_goods);
        if ($_GET['keyword'] != ''){
            Tpl::output('show_keyword',  $_GET['keyword']);
        } else {
            Tpl::output('show_keyword',  $goods_param['class']['gc_name']);
        }

        $model_goods_class = Model('goods_class');

        // SEO
        if ($_GET['keyword'] == '') {
            $seo_class_name = $goods_param['class']['gc_name'];
            if (is_numeric($_GET['cate_id']) && empty($_GET['keyword'])) {
                $seo_info = $model_goods_class->getKeyWords(intval($_GET['cate_id']));
                if (empty($seo_info[1])) {
                    $seo_info[1] = C('site_name') . ' - ' . $seo_class_name;
                }
                Model('seo')->type($seo_info)->param(array('name' => $seo_class_name))->show();
            }
        } elseif ($_GET['keyword'] != '') {
            Tpl::output('html_title', (empty($_GET['keyword']) ? '' : $_GET['keyword'] . ' - ') . C('site_name') . L('nc_common_search'));
        }

        //分销图标
        $model_grade = Model('fenxiao_member_grade');
        $grade_list = $model_grade->getGradeList();
        Tpl::output('grade_list', $grade_list );

        // 当前位置导航
        $nav_link_list = $model_goods_class->getGoodsClassNav(intval($_GET['cate_id']));
        Tpl::output('nav_link_list', $nav_link_list );

        // 得到自定义导航信息
        $nav_id = intval($_GET['nav_id']) ? intval($_GET['nav_id']) : 0;
//        Tpl::output('index_sign', $nav_id);
        Tpl::output('index_sign', 'fenxiao');

        // 地区
        $province_array = Model('area')->getTopLevelAreas();
        Tpl::output('province_array', $province_array);

        loadfunc('search');

        // 浏览过的商品
        $viewed_goods = Model('goods_browse')->getViewedGoodsList($_SESSION['member_id'],20);
        Tpl::output('viewed_goods',$viewed_goods);
        Tpl::showpage('fenxiao_goods');
    }
	/**
     * 获得推荐商品 v3-b12
     */
    public function get_hot_goodsOp() {
        $gc_id = $_GET['cate_id'];
        if ($gc_id <= 0) {
            return false;
        }
        // 获取分类id及其所有子集分类id
        $goods_class = Model('goods_class')->getGoodsClassForCacheModel();
        if (empty($goods_class[$gc_id])) {
            return false;
        }
        $child = (!empty($goods_class[$gc_id]['child'])) ? explode(',', $goods_class[$gc_id]['child']) : array();
        $childchild = (!empty($goods_class[$gc_id]['childchild'])) ? explode(',', $goods_class[$gc_id]['childchild']) : array();
        $gcid_array = array_merge(array($gc_id), $child, $childchild);
        // 查询添加到推荐展位中的商品id
        $boothgoods_list = Model('goods')->getGoodsOnlineList(array('gc_id' => array('in', $gcid_array)), 'goods_id', 4, 'rand()');
        if (empty($boothgoods_list)) {
            return false;
        }

        $goodsid_array = array();
        foreach ($boothgoods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
        }

        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_count";
        $goods_list = Model('goods')->getGoodsOnlineList(array('goods_id' => array('in', $goodsid_array)), $fieldstr);
        if (empty($goods_list)) {
            return false;
        }

        Tpl::output('goods_list', $goods_list);
        Tpl::showpage('goods.hot', 'null_layout');
    }
		    /**
     * 获得同类商品排行
     */
    public function get_listhot_goodsOp() {
        $gc_id = $_GET['cate_id'];
        if ($gc_id <= 0) {
            return false;
        }
        // 获取分类id及其所有子集分类id
        $goods_class = Model('goods_class')->getGoodsClassForCacheModel();
        if (empty($goods_class[$gc_id])) {
            return false;
        }
        $child = (!empty($goods_class[$gc_id]['child'])) ? explode(',', $goods_class[$gc_id]['child']) : array();
        $childchild = (!empty($goods_class[$gc_id]['childchild'])) ? explode(',', $goods_class[$gc_id]['childchild']) : array();
        $gcid_array = array_merge(array($gc_id), $child, $childchild);
        // 查询添加到推荐展位中的商品id
        $boothgoods_list = Model('goods')->getGoodsOnlineList(array('gc_id' => array('in', $gcid_array)));
        if (empty($boothgoods_list)) {
            return false;
        }

        $goodsid_array = array();
        foreach ($boothgoods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
        }

        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_count";
        $goods_list = Model('goods')->getGoodsOnlineList(array('goods_id' => array('in', $goodsid_array)), $fieldstr,5,'goods_salenum desc');
        if (empty($goods_list)) {
            return false;
        }

        Tpl::output('goods_list', $goods_list);
    }

    /**
     * 获得推荐商品
     */
    public function get_booth_goodsOp() {
        $gc_id = $_GET['cate_id'];
        if ($gc_id <= 0) {
            return false;
        }
        // 获取分类id及其所有子集分类id
        $goods_class = Model('goods_class')->getGoodsClassForCacheModel();
        if (empty($goods_class[$gc_id])) {
            return false;
        }
        $child = (!empty($goods_class[$gc_id]['child'])) ? explode(',', $goods_class[$gc_id]['child']) : array();
        $childchild = (!empty($goods_class[$gc_id]['childchild'])) ? explode(',', $goods_class[$gc_id]['childchild']) : array();
        $gcid_array = array_merge(array($gc_id), $child, $childchild);
        // 查询添加到推荐展位中的商品id
        $boothgoods_list = Model('p_booth')->getBoothGoodsList(array('gc_id' => array('in', $gcid_array)), 'goods_id', 0, 4, 'rand()');
        if (empty($boothgoods_list)) {
            return false;
        }

        $goodsid_array = array();
        foreach ($boothgoods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
        }

        $fieldstr = "goods_id,goods_commonid,goods_name,goods_jingle,store_id,store_name,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_count";
        $goods_list = Model('goods')->getGoodsOnlineList(array('goods_id' => array('in', $goodsid_array)), $fieldstr);
        if (empty($goods_list)) {
            return false;
        }

        Tpl::output('goods_list', $goods_list);
        Tpl::showpage('goods.booth', 'null_layout');
    }

	public function auto_completeOp() {
	    try {
    	    require(BASE_DATA_PATH.'/api/xs/lib/XS.php');
    	    $obj_doc = new XSDocument();
    	    $obj_xs = new XS(C('fullindexer.appname'));
    	    $obj_index = $obj_xs->index;
    	    $obj_search = $obj_xs->search;
    	    $obj_search->setCharset(CHARSET);
            $corrected = $obj_search->getExpandedQuery($_GET['term']);
            if (count($corrected) !== 0) {
                $data = array();
                foreach ($corrected as $word)
                {
                    $row['id'] = $word;
                    $row['label'] = $word;
                    $row['value'] = $word;
                    $data[] = $row;
                }
                exit(json_encode($data));
            }
        } catch (XSException $e) {
            if (is_object($obj_index)) {
                $obj_index->flushIndex();
            }
//             Log::record('search\auto_complete'.$e->getMessage(),Log::RUN);
        }
	}

	/**
	 * 获得猜你喜欢
	 */
    public function get_guesslikeOp(){
        $goodslist = Model('goods_browse')->getGuessLikeGoods($_SESSION['member_id'], 20);
        if(!empty($goodslist)){
            Tpl::output('goodslist',$goodslist);
            Tpl::showpage('goods_guesslike','null_layout');
        }
    }

    /**
     * 分销申请
     */
    public function goods_applyOp(){

        if (chksubmit()) {

            $goods_id = $_POST['goods_id'];
            $member_id = $_SESSION['member_id'];

            $model_fenxiao_goods_member	= Model('fenxiao_goods_member');
            $param['goods_id'] = $goods_id;
            $param['member_id'] = $member_id;
            $param['apply_time'] = time();

            //获取商品信息
            $model_goods	= Model('goods');
            $condition = array();
            $condition['goods_id'] = $goods_id;

            $goods_info = $model_goods->getGoodsInfo($condition);
            $param['store_id'] = $goods_info['store_id'];

            $fenxiao_config = Model('fenxiao_config');
            $config_info = $fenxiao_config->getFenxiaoConfigInfo(array('config_key'=>'fenxiao_goods_member'));
            if ($config_info['config_value'] == 0){
                $param['status'] = 1;
            }
            else
                $param['status'] = 0;

            $model_fenxiao_goods_member->save($param);

            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
        }

        $member_id = $_SESSION['member_id'];

        $apply_msg = "";
        if (!is_null($member_id)){
            $model_member	= Model('member');
            $member_info = $model_member->getMemberInfoByID($member_id);
            if ($member_info['fenxiao_status'] == 2)
                $status = 1;
            else{
                $apply_msg = "您尚未成为分销员，无法申请分销，点击<a href='".urlShop('member_fenxiao_joinin','index')."' style='color:blue;' target='_blank'>这里</a>申请";
                $status = 0;
            }
        }
        else{
            $status = 0;
            $apply_msg = "您尚未登录商城，无法申请分销，点击<a href='".urlShop('login','index')."' style='color:blue' target='_blank'>这里</a>登录";
        }

        Tpl::output('apply_msg', $apply_msg);
        Tpl::output('status', $status);
        Tpl::output('goods_id', $_GET['id']);
        Tpl::showpage('fenxiao_goods.goods_apply', 'null_layout');
    }
}
