<?php defined('InShopNC') or exit('Access Invalid!');?>
<script>
var PURL = '<?php echo $output['purl'];?>';

$(document).ready(function(){
    $('#area_info').nc_region();
});
</script>

<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/store_list.css" rel="stylesheet" type="text/css">
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jcarousel/skins/tango/skin.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/layout.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/seller_center.css" rel="stylesheet" type="text/css">

<style type="text/css">
.sticky #main-nav { width: 1198px;}
/*.jcarousel-skin-tango .jcarousel-prev-horizontal, .jcarousel-skin-tango .jcarousel-next-horizontal { margin-top: -60px;}*/
.jcarousel-skin-tango .jcarousel-clip-horizontal { width: 1000px !important; height: 225px !important;}
.jcarousel-skin-tango .jcarousel-item { height: 225px !important;}
.jcarousel-skin-tango .jcarousel-container-horizontal { width: 1000px !important;}
</style>

<script type="text/javascript" src="<?php echo SHOP_RESOURCE_SITE_URL.'/js/Jquery.Query.js';?>" charset="utf-8"></script>
<script type="text/javascript">
//<!CDATA[
/* 替换参数 */
function ss_replaceParam(key, value)
{
    location.assign($.query.set('key', key).set('order', value));
}

/* 替换参数 */
function ss_dropParam(key1, key2)
{
	location.assign($.query.REMOVE(key1).REMOVE(key2));
}

/* 替换参数 */
function ss_dropParam2(key1)
{
	location.assign($.query.REMOVE(key1));
}

/* 替换参数 */
function ss_replaceParam2(key, value)
{
    location.assign($.query.set(key, value, value));
}

$(function (){
    var order = '<?php echo $_GET['order'];?>';
    var arrow = '';
    var class_val = 'sort_desc';

    switch (order){
        case 'store_credit desc' : order = 'store_credit asc';  class_val = 'sort_desc'; break;
        case 'store_credit asc'  : order = 'store_credit desc'; class_val = 'sort_asc' ; break;
        default : order = 'store_credit asc';
    }
    $('#credit_grade').addClass(class_val);
    $('#credit_grade').click(function(){query('order', order);return false;});
}
);

function query(name, value){
    $("input[name='"+name+"']").val(value);
    $('#searchStore').submit();
}

//]]>
</script>

<div class="content nch-container wrapper">
<div class="nch-all-menu">
    <ul class="tab-bar">
	  <li><a href="<?php echo urlShop('fenxiao_goods', 'index');?>">全部分销商品</a></li>
      <li class="current"><a href="<?php echo urlShop('fenxiao_store', 'index');?>">全部分销商户</a></li>
      <li><a href="<?php echo urlShop('fenxiao_user', 'index');?>">全部分销员</a></li>
    </ul>
  </div>
  
  <div class="left">
    <!-- E 推荐展位 -->
    <div class="nch-module"><?php echo loadadv(37,'html');?></div>
  
  </div>

<div class="right">

<ul class="nc-store-list">
<?php if(!empty($output['store_list']) && is_array($output['store_list'])){?>
<?php foreach($output['store_list'] as $skey => $store){?>
    <li class="item">
      <dl class="shop-info">
        <dt class="shop-name"><a href="<?php echo urlShop('show_store','', array('store_id'=>$store['store_id']),$store['store_domain']);?>" target="_blank"><?php echo $store['store_name'];?></a></dt>
        <dd class="shop-pic"><a href="<?php echo urlShop('show_store','', array('store_id'=>$store['store_id']),$store['store_domain']);?>" title="" target="_blank"><span class="size72"><img src="<?php echo getStoreLogo($store['store_avatar']);?>"  alt="<?php echo $store['store_name'];?>" title="<?php echo $store['store_name'];?>" class="size72" /></span></a></dd>
        <dd class="main-runs" title="<?php echo $store['store_zy']?>"><?php echo $lang['store_class_index_store_zy'].$lang['nc_colon'];?><?php echo $store['store_zy']?></dd>
        <dd class="shopkeeper"><?php echo $lang['store_class_index_owner'].$lang['nc_colon'];?><?php echo $store['member_name'];?><a target="_blank" class="message" href="index.php?act=member_message&op=sendmsg&member_id=<?php echo $store['member_id'];?>"></a><span>
        <?php if(!empty($store['store_qq'])){?>
          <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $store['store_qq'];?>&site=qq&menu=yes" title="QQ: <?php echo $store['store_qq'];?>"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $store['store_qq'];?>:52" style=" vertical-align: middle;"/></a>
        <?php }?>
        <?php if(!empty($store['store_ww'])){?>
          <a target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid=<?php echo $store['store_ww'];?>&site=cntaobao&s=2&charset=<?php echo CHARSET;?>" ><img border="0" src="http://amos.im.alisoft.com/online.aw?v=2&uid=<?php echo $store['store_ww'];?>&site=cntaobao&s=2&charset=<?php echo CHARSET;?>" alt="Wang Wang" style=" vertical-align: middle;" /></a>
        <?php }?></span></dd>
      </dl>
      <dl class="w200">
        <dd><?php echo ($tmp = $store['goods_count']) ? $lang['store_class_index_goods_amount'].$tmp.$lang['piece'] : $lang['nc_common_goods_null'];?></dd>
        <dd><?php echo ($tmp = $store['num_sales_jq']) ? $lang['store_class_index_deal'].$tmp.$lang['store_class_index_jian'] : $lang['nc_common_sell_null'];?></dd>
        <?php if (!empty($store['search_list_goods'])){?>
        <dd class="more-on" attr='morep' nc_type='<?php echo $skey;?>'><span><?php echo $lang['store_class_index_goods_hiden'];?></span><i></i></dd>
        <?php }?>
      </dl>
      <dl class="w150">
      	<!-- 店铺信用度 -->
        <dd><?php if (empty($store['store_credit_average'])){ echo $lang['nc_common_credit_null']; }else {?>
          <?php echo $lang['store_class_index_credit_value'].$lang['nc_colon'];?>
          <span class="seller-heart level-<?php echo $store['store_credit_average']; ?>"></span>
          <?php }?>
        </dd>
        <!-- 店铺好评率 -->
        <dd>
        <?php if (empty($store['store_credit_percent'])){?>
        	<?php echo $lang['nc_common_rate_null'];?>
        <?php }else{?>	
        	<?php echo $lang['store_class_index_praise_rate'].$lang['nc_colon'].$store['store_credit_percent'];?>%
        <?php }?>
        </dd>
        <!-- 店铺动态评分 -->
        <dd class="shop-rate" nc_type="shop-rate" store_id='<?php echo $store['store_id'];?>'><?php echo $lang['store_class_index_shop_rate'].$lang['nc_colon'];?><span><i></i></span>
          <div class="shop-rate-con">
              <div class="arrow"></div>
              <dl class="rate">
                <?php  foreach ($store['store_credit'] as $key=>$value) {?>
                  <dt><?php echo $value['text'].$lang['nc_colon'];?></dt>
                  <dd class="rate-star"><em><i style=" width: <?php echo @round($value['credit']/5*100,2);?>%;"></i></em><span><?php echo $value['credit'];?><?php echo $lang['store_class_index_grade'];?></span></dd>
                <?php } ?>
              </dl>
          </div>
          </dd>
      </dl>
    </li>

<?php }?>

<?php }else{?>
<div id="no_results"><?php echo $lang['store_class_index_no_record'];?></div>
<?php }?>
</ul>

<table class="ncsc-default-table">
  <thead>
    <tr nc_type="table_header">
      <th class="w30">&nbsp;</th>
      <th class="w50">&nbsp;</th>
      <th coltype="editable" column="goods_name" checker="check_required" inputwidth="230px"><?php echo $lang['store_goods_index_goods_name'];?></th>
      <th class="w100"><?php echo $lang['store_goods_index_price'];?></th>
      <th class="w100"><?php echo $lang['store_goods_index_stock'];?></th>
      <th class="w100"><?php echo $lang['store_goods_index_add_time'];?></th>
      <th class="w120"><?php echo $lang['nc_handle'];?></th>
    </tr>
    
  </thead>
  <tbody>
    <?php if (!empty($output['goods_list'])) { ?>
    <?php foreach ($output['goods_list'] as $val) { ?>

    <tr>
      <td class="trigger"></td>
      <td><div class="pic-thumb"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $output['storage_array'][$val['goods_commonid']]['goods_id']));?>" target="_blank"><img src="<?php echo thumb($val, 60);?>"/></a></div></td>
      <td class="tl"><dl class="goods-name">
          <dt style="max-width: 350px !important;">
            <?php if ($val['is_virtual'] ==1) {?>
            <span class="type-virtual" title="虚拟兑换商品">虚拟</span>
            <?php }?>
            <?php if ($val['is_fcode'] ==1) {?>
            <span class="type-fcode" title="F码优先购买商品">F码</span>
            <?php }?>
            <?php if ($val['is_presell'] ==1) {?>
            <span class="type-presell" title="预先发售商品">预售</span>
            <?php }?>
            <?php if ($val['is_appoint'] ==1) {?>
            <span class="type-appoint" title="预约销售提示商品">预约</span>
            <?php }?>
            <a href="<?php echo urlShop('goods', 'index', array('goods_id' => $output['storage_array'][$val['goods_commonid']]['goods_id']));?>" target="_blank"><?php echo $val['goods_name']; ?></a></dt>
          <dd><?php echo $lang['store_goods_index_goods_no'].$lang['nc_colon'];?><?php echo $val['goods_serial'];?></dd>
          <dd class="serve"> <span class="<?php if ($val['goods_commend'] == 1) { echo 'open';}?>" title="店铺推荐商品"><i class="commend">荐</i></span> 
		  <span class="<?php if ($val['is_fenxiao'] == 1) { echo 'open';}?>" title="店铺分销商品"><i class="commend">分</i></span>
		  <span class="<?php if ($val['mobile_body'] != '') { echo 'open';}?>" title="手机端商品详情"><i class="icon-tablet"></i></span> <span class="" title="商品页面二维码"><i class="icon-qrcode"></i>
            <div class="QRcode"><a target="_blank" href="<?php echo goodsQRCode(array('goods_id' => $output['storage_array'][$val['goods_commonid']]['goods_id'], 'store_id' => $_SESSION['store_id']));?>">下载标签</a>
              <p><img src="<?php echo goodsQRCode(array('goods_id' => $output['storage_array'][$val['goods_commonid']]['goods_id'], 'store_id' => $_SESSION['store_id']));?>"/></p>
            </div>
            </span>
            <?php if ($val['is_fcode'] ==1) {?>
            <span><a class="ncsc-btn-mini ncsc-btn-red" href="<?php echo urlShop('store_goods_online', 'download_f_code_excel', array('commonid' => $val['goods_commonid']));?>">下载F码</a></span>
            <?php }?>
          </dd>
        </dl></td>
      <td><span><?php echo $lang['currency'].$val['goods_price']; ?></span></td>
      <td><span <?php if ($output['storage_array'][$val['goods_commonid']]['alarm']) { echo 'style="color:red;"';}?>><?php echo $output['storage_array'][$val['goods_commonid']]['sum'].$lang['piece']; ?></span></td>
      <td class="goods-time"><?php echo @date('Y-m-d',$val['goods_addtime']);?></td>
      <td class="nscs-table-handle" style="width:220px;"><?php if ($val['goods_lock'] == 0) {?>
        <span><a href="<?php echo urlShop('store_goods_online', 'edit_goods', array('commonid' => $val['goods_commonid']));?>" class="btn-blue"><i class="icon-edit"></i>
        <p><?php echo $lang['nc_edit'];?></p>
        </a></span> <span><a href="javascript:void(0);" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('store_goods_online', 'drop_goods', array('commonid' => $val['goods_commonid']));?>');" class="btn-red"><i class="icon-trash"></i>
        <p><?php echo $lang['nc_del'];?></p>
        </a></span>

            <?php  if ($val['is_fenxiao'] == 0 || $val['is_fenxiao'] == 3) { ?>
                <span><a href="<?php echo urlShop('store_goods_fenxiao', 'edit_fenxiao', array('commonid' => $val['goods_commonid']));?>" class="btn-blue"><i class="icon-edit"></i>
                      <p>分销申请</p>
                  </a></span>
              <?php } else if ($val['is_fenxiao'] == 2){?>
			  <span><a href="<?php echo urlShop('store_goods_fenxiao', 'edit_fenxiao', array('commonid' => $val['goods_commonid']));?>" class="btn-blue"><i class="icon-edit"></i>
                      <p>分销审核中</p>
                  </a></span>
              <?php } else {?>
                  <span><a href="#" class="btn-blue"><i class="icon-edit"></i>
                          <p>分销中</p>
                      </a></span>
				                <?php } ?>


        <?php } else {?>
        <span class="tip" title="该商品参加抢购活动期间不能进行编辑及删除等操作,可以编辑赠品和推荐组合"><a href="<?php if ($val['is_virtual'] ==1 ) {echo 'javascript:void(0);';} else {echo urlShop('store_goods_online', 'add_gift', array('commonid' => $val['goods_commonid']));}?>" class="btn-orange-current"><i class="icon-lock"></i>
        <p>锁定</p>
        </a></span>
        <?php }?></td>
    </tr>
    <tr style="display:none;">
      <td colspan="20"><div class="ncsc-goods-sku ps-container"></div></td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php  if (!empty($output['goods_list'])) { ?>

    <tr>
      <td colspan="20"><div class="pagination"> <?php echo $output['show_page']; ?> </div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>

  </div>
  <div class="clear"></div>

</div>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/waypoints.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jcarousel/jquery.jcarousel.min.js"></script> 

<script type="text/javascript">
$(function(){
	//图片轮换
    $('[nc_type="jcarousel"]').jcarousel({visible: 4});
    $('[attr="morep"]').click(function(){
    	var id = $(this).attr('nc_type');
    	if($(this).attr('class')=='more-off'){
    		$(this).addClass('more-on').removeClass('more-off').html('<?php echo $lang['store_class_index_goods_hiden'];?><i></i>');
    		$('div[nc_type="goods_'+id+'"]').show();
    	}else{
    		$(this).addClass('more-off').removeClass('more-on').html('<?php echo $lang['store_class_index_goods_show'];?><i></i>');
    		$('div[nc_type="goods_'+id+'"]').hide();
    	}
    });
   
});
</script>
