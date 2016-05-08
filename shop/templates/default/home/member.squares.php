<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
#box {
	background: #FFF;
	width: 238px;
	height: 410px;
	margin: -390px 0 0 0;
	display: block;
	border: solid 4px #D93600;
	position: absolute;
	z-index: 999;
	opacity: .5
}
.shopMenu {
	position: fixed;
	z-index: 1;
	right: 25%;
	top: 0;
}
</style>
<div class="squares" nc_type="current_display_mode">
    <input type="hidden" id="lockcompare" value="unlock" />
  <?php if(!empty($output['member_list']) && is_array($output['member_list'])){?>
  <ul class="list_pic">
    <?php foreach($output['member_list'] as $value){?>
    <li class="item">
      <div class="goods-content-fenxiao" nctype_goods=" <?php echo $value['goods_id'];?>" nctype_store="<?php echo $value['store_id'];?>">
        <div class="goods-fenxiao-pic"><a href="#"><img src="<?php echo UPLOAD_SITE_URL;?>/shop/common/loading.gif" rel="lazy" data-url="<?php echo $value['member_avatar'];?>" title="<?php echo $value['goods_name'];?>" alt="<?php echo $value['goods_name'];?>" /></a></div>
        
        <div class="goods-info2">
          
          <div class="goods-fenxiao-name" ><a href="#" title="<?php echo $value['goods_jingle'];?>">用户名：<?php echo $value['goods_name_highlight'];?><em><?php echo $value['member_name'];?></em></a></div>
          <div class="goods-normal"> 分销等级：<img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_ADV.'/'.$value['level_icon']; ?>" title="<?php echo $value['level_name']; ?>"></div>
          <div class="goods-normal"> 已&nbsp;&nbsp;分&nbsp;&nbsp;销：<?php echo $value['num_sum'];?>&nbsp;&nbsp;件</div>
          <div class="goods-normal"> 分销总额：<?php echo $value['price_sum'];?>&nbsp;&nbsp;元</div>
          <div class="goods-normal"> 返利总额：<?php echo $value['money_sum'];?>&nbsp;&nbsp;元</div>
          <div class="goods-normal"> 注册时间：<?php echo $value['member_time'];?></div>

          
        </div>
      </div>
    </li>
    <?php }?>
    <div class="clear"></div>
  </ul>
  <?php }else{?>
  <div id="no_results" class="no-results"><i></i><?php echo $lang['index_no_record'];?></div>
  <?php }?>
</div>

