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
      <li><a href="<?php echo urlShop('fenxiao_member', 'index');?>">全部分销员</a></li>
    </ul>
  </div>
  
  <div class="left">
    <!-- E 推荐展位 -->
    <div class="nch-module"><?php echo loadadv(37,'html');?></div>
  
  </div>

<div class="right">
<div class="">
  <div class="shop_con_list" id="main-nav-holder">
    <nav class="nc-gl-sort-bar" id="main-nav">
      <form id="store_list" method="GET" action="index.php">
        <input type="hidden" name="order" value="<?php echo $_GET['order'];?>"/>
        <input type="hidden" name="act" value="store_list"/>
        <input type="hidden" name="cate_id" value="<?php echo $_GET['cate_id'];?>"/>
        <div class="sort-bar"><!-- 排序方式S -->
          <ul class="array">
          <!-- 默认 -->
            <li <?php if(!$_get['key']){?>class="selected"<?php }?>><a href="javascript:void(0)" class="nobg" onclick="javascript:ss_dropParam('key','order');" title="<?php echo $lang['goods_class_index_default_sort'];?>">默认</a></li>
            <!-- 销量 -->
            <li <?php if($_get['key'] == 'store_sales'){?>class="selected"<?php }?>><a href="javascript:void(0)" <?php if($_get['key'] == 'store_sales'){?>class="<?php echo $_GET['order'];?>"<?php }?> onclick="javascript:ss_replaceParam('store_sales','<?php echo ($_GET['order'] == 'desc' && $_GET['key'] == 'store_sales')?'asc':'desc' ?>');" title="<?php echo ($_GET['order'] == 'desc' && $_GET['key'] == 'store_sales')?$lang['store_class_index_sold_asc']:$lang['store_class_index_sold_desc']; ?>">销量</a></li>
            <!-- 信用 -->
            <li <?php if($_get['key'] == 'store_credit'){?>class="selected"<?php }?>><a href="javascript:void(0)" <?php if($_get['key'] == 'store_credit'){?>class="<?php echo $_GET['order'];?>"<?php }?> onclick="javascript:ss_replaceParam('store_credit','<?php  echo ($_GET['order'] == 'desc' && $_GET['key'] == 'store_credit')?'asc':'desc' ?>');" title="<?php  echo ($_GET['order'] == 'desc' && $_GET['key'] == 'store_credit')?$lang['store_class_index_credit_asc']:$lang['store_class_index_credit_desc']; ?>">信用</a></li>
          </ul>
          <!-- 排序方式E -->
          <?php if (!C('fullindexer.open')){?>
          <div class="sidebox">
            <h5 class="title">店铺名称<?php echo $lang['nc_colon'];?></h5>
            <div class="selectbox">
              <input class="text" type="text" name="keyword" value="<?php echo $_GET['keyword'];?>" style=" width:150px;"/>
            </div>
          </div>
          <div class="sidebox">
            <h5><label for="area_info">店铺所在地<?php echo $lang['nc_colon'];?></label></h5>
            <div class="selectbox">
              <input id="area_info" name="area_info" type="hidden" value=""/>
            </div>
          </div>
          <div class="sidebox width5" style=" background-image: none">
            <div class="selectbox">
              <input class="btn" type="submit" value="<?php echo $lang['nc_search'];?>" />
            </div>
          </div>
          <?php }?>
        </div>
      </form>
    </nav>
  </div>
</div>
<ul class="nc-store-list">
<?php if(!empty($output['store_list']) && is_array($output['store_list'])){?>
<?php foreach($output['store_list'] as $skey => $store){?>
    <li class="item">
      <dl class="shop-info">
        <dt class="shop-name"><a href="<?php echo urlShop('fenxiao_store','goods', array('store_id'=>$store['store_id']),$store['store_domain']);?>" target="_blank"><?php echo $store['store_name'];?></a>&nbsp;&nbsp;<img src="<?php echo UPLOAD_SITE_URL."/".ATTACH_ADV."/".$store['fmg_icon']; ?>" title="<?php echo $store['fmg_name']; ?>"></dt>
        <dd class="shop-pic"><a href="<?php echo urlShop('fenxiao_store','goods', array('store_id'=>$store['store_id']),$store['store_domain']);?>" title="" target="_blank"><span class="size72"><img src="<?php echo getStoreLogo($store['store_avatar']);?>"  alt="<?php echo $store['store_name'];?>" title="<?php echo $store['store_name'];?>" class="size72" /></span></a></dd>
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
        <dd><?php echo ($tmp = $store['goods_count']) ? "分销产品：".$tmp."&nbsp;&nbsp;".$lang['piece'] : $lang['nc_common_goods_null'];?></dd>
        <dd><?php echo ($tmp = $store['num_sales_jq']) ? "成交数量：".$tmp."&nbsp;&nbsp;".$lang['piece'] : $lang['nc_common_sell_null'];?></dd>
        <dd><?php echo ($tmp = $store['money_sales_jq']) ? "返利总额：<font color='red' font-weight='bold'>".$tmp."</font>&nbsp;&nbsp;元" : "暂无返利";?></dd>
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
<div class="pagination"> <?php echo $output['show_page'];?> </div>
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
