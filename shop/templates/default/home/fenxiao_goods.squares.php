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
  <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){?>
  <ul class="list_pic">
    <?php foreach($output['goods_list'] as $value){?>
    <li class="item item-fenxiao">
      <div class="goods-content-fenxiao-goods" nctype_goods=" <?php echo $value['goods_id'];?>" nctype_store="<?php echo $value['store_id'];?>">
        <div class="goods-pic"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$value['goods_id']));?>" target="_blank" title="<?php echo $value['goods_name'];?>"><img src="<?php echo UPLOAD_SITE_URL;?>/shop/common/loading.gif" rel="lazy" data-url="<?php echo thumb($value, 240);?>" title="<?php echo $value['goods_name'];?>" alt="<?php echo $value['goods_name'];?>" /></a></div>
        <?php if (C('groupbuy_allow') && $value['goods_promotion_type'] == 1) {?>
        <div class="goods-promotion"><span>抢购商品</span></div>
        <?php } elseif (C('promotion_allow') && $value['goods_promotion_type'] == 2)  {?>
        <div class="goods-promotion"><span>限时折扣</span></div>
        <?php }?>
        <div class="goods-info">
          <div class="goods-pic-scroll-show">
            <ul>
            <?php if(!empty($value['image'])) {?>
              <?php $i=0;foreach ($value['image'] as $val) {$i++?>
              <li<?php if($i==1) {?> class="selected"<?php }?>><a href="javascript:void(0);"><img src="<?php echo UPLOAD_SITE_URL;?>/shop/common/loading.gif" rel="lazy" data-url="<?php echo thumb($val, 60);?>"/></a></li>
              <?php }?>
            <?php } else {?>
              <li class="selected"><a href="javascript:void(0);"><img src="<?php echo thumb($value, 60);?>" /></a></li>
            <?php }?>
            </ul>
          </div>
          <div class="goods-name"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$value['goods_id']));?>" target="_blank" title="<?php echo $value['goods_jingle'];?>"><?php echo $value['goods_name_highlight'];?><em><?php echo $value['goods_jingle'];?></em></a></div>
          <div class="goods-price"> <em class="sale-price" title="<?php echo $lang['goods_class_index_store_goods_price'].$lang['nc_colon'].$lang['currency'].$value['goods_promotion_price'];?>"><?php echo ncPriceFormatForList($value['goods_promotion_price']);?></em> <em class="market-price" title="市场价：<?php echo $lang['currency'].$value['goods_marketprice'];?>"><?php echo ncPriceFormatForList($value['goods_marketprice']);?></em> <span class="raty" data-score="<?php echo $value['evaluation_good_star'];?>"></span> </div>

          <div class="fenxiao-fanli">
              <div>
                <span><img src="<?php echo UPLOAD_SITE_URL."/".ATTACH_ADV."/".$output['grade_list'][0]['fmg_icon']; ?>">&nbsp;&nbsp;&nbsp;返利：<?php echo $value['fenxiao_v1']; ?>%</span>
              <span><img src="<?php echo UPLOAD_SITE_URL."/".ATTACH_ADV."/".$output['grade_list'][1]['fmg_icon']; ?>">&nbsp;&nbsp;&nbsp;返利：<?php echo $value['fenxiao_v2']; ?>%</span>
              </div>

              <div>
                  <span><img src="<?php echo UPLOAD_SITE_URL."/".ATTACH_ADV."/".$output['grade_list'][2]['fmg_icon']; ?>">&nbsp;&nbsp;&nbsp;返利：<?php echo $value['fenxiao_v3']; ?>%</span>
                  <span><img src="<?php echo UPLOAD_SITE_URL."/".ATTACH_ADV."/".$output['grade_list'][3]['fmg_icon']; ?>">&nbsp;&nbsp;&nbsp;返利：<?php echo $value['fenxiao_v4']; ?>%</span>
              </div>
              <div>
                  已申请：
              </div>
              <div>
                  已分销：
              </div>
              <div>
                  已返利：
              </div>
              <div>
                  时&nbsp;&nbsp;&nbsp;&nbsp;效：
              </div>
          </div>

          <div class="store" style="height:25px;"><a href="<?php echo urlShop('show_store','index',array('store_id'=>$value['store_id']), $value['store_domain']);?>" title="<?php echo $value['store_name'];?>" class="name"><?php echo $value['store_name'];?></a></div>
          <div class="add-cart">
              <?php if ($value['member_fenxiao'] == 2){  ?>
                  <a href="javascript:void(0);" class="btn">分销中</a>

              <?php } else if ($value['member_fenxiao'] == 1){  ?>

                  <a href="javascript:void(0);" class="btn">申请中</a>

              <?php } else if ($value['member_fenxiao'] == -1){  ?>

                  <a href="javascript:void(0);" class="btn">无法分销自己店铺</a>

              <?php } else if ($value['member_fenxiao'] == 0){  ?>

              <a href="javascript:void(0);" class="btn" nctype="verify_batch" data-param="{goods_id:<?php echo $value['goods_id'];?>}">我要分销</a>
          <?php } ?>

          </div>
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
<form id="buynow_form" method="post" action="<?php echo SHOP_SITE_URL;?>/index.php" target="_blank">
  <input id="act" name="act" type="hidden" value="buy" />
  <input id="op" name="op" type="hidden" value="buy_step1" />
  <input id="goods_id" name="cart_id[]" type="hidden"/>
</form>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/jquery.raty.min.js"></script> 
<script type="text/javascript">
    $(document).ready(function(){
        $('.raty').raty({
            path: "<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/img",
            readOnly: true,
            width: 80,
            score: function() {
              return $(this).attr('data-score');
            }
        });
      	//初始化对比按钮
    	initCompare();

        // 分销申请处理
        $('a[nctype="verify_batch"]').click(function(){
            <?php if ($_SESSION['is_login'] !== '1'){?>
            login_dialog();
            <?php }else{?>

            eval('var data_str = ' + $(this).attr('data-param'));
            str = data_str.goods_id;
            if (str) {
                goods_verify(str);
            }
            <?php } ?>
        });
    });

    function goods_verify(ids) {
        _uri = "<?php echo SHOP_SITE_URL;?>/index.php?act=fenxiao_goods&op=goods_apply&id=" + ids;
        CUR_DIALOG = ajax_form('goods_verify', '分销申请', _uri, 350);
    }

</script> 
