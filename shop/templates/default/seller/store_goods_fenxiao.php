<?php defined('InShopNC') or exit('Access Invalid!');?>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.fileupload.js" charset="utf-8"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.charCount.js"></script>
<!--[if lt IE 8]>
  <script src="<?php echo RESOURCE_SITE_URL;?>/js/json2.js"></script>
<![endif]-->
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/store_goods_add.step2.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<style type="text/css">
#fixedNavBar { filter:progid:DXImageTransform.Microsoft.gradient(enabled='true',startColorstr='#CCFFFFFF', endColorstr='#CCFFFFFF');background:rgba(255,255,255,0.8); width: 90px; margin-left: 510px; border-radius: 4px; position: fixed; z-index: 999; top: 172px; left: 50%;}
#fixedNavBar h3 { font-size: 12px; line-height: 24px; text-align: center; margin-top: 4px;}
#fixedNavBar ul { width: 80px; margin: 0 auto 5px auto;}
#fixedNavBar li { margin-top: 5px;}
#fixedNavBar li a { font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 20px; background-color: #F5F5F5; color: #999; text-align: center; display: block;  height: 20px; border-radius: 10px;}
#fixedNavBar li a:hover { color: #FFF; text-decoration: none; background-color: #27a9e3;}
</style>


<?php if ($output['edit_goods_sign']) {?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<?php } else {?>
<ul class="add-goods-step">
  <li><i class="icon icon-list-alt"></i>
    <h6>STEP.1</h6>
    <h2>选择商品分类</h2>
    <i class="arrow icon-angle-right"></i> </li>
  <li class="current"><i class="icon icon-edit"></i>
    <h6>STEP.2</h6>
    <h2>填写商品详情</h2>
    <i class="arrow icon-angle-right"></i> </li>
  <li><i class="icon icon-camera-retro "></i>
    <h6>STEP.3</h6>
    <h2>上传商品图片</h2>
    <i class="arrow icon-angle-right"></i> </li>
  <li><i class="icon icon-ok-circle"></i>
    <h6>STEP.4</h6>
    <h2>商品发布成功</h2>
  </li>
</ul>
<?php }?>
<div class="item-publish">
  <form method="post" id="goods_form" action="<?php if ($output['edit_goods_sign']) { echo urlShop('store_goods_fenxiao', 'edit_save_goods');} else { echo urlShop('store_goods_add', 'save_goods');}?>">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="commonid" value="<?php echo $output['goods']['goods_commonid'];?>" />
    <input type="hidden" name="type_id" value="<?php echo $output['goods_class']['type_id'];?>" />
    <input type="hidden" name="ref_url" value="<?php echo $_GET['ref_url'] ? $_GET['ref_url'] : getReferer();?>" />
    <div class="ncsc-form-goods">

	<h3 id="demo5" nctype="virtual_null" <?php if ($output['goods']['is_virtual'] == 1) {?>style="display:none;"<?php }?>>分销设置</h3>

	<dl>
        <dt>加入分销<?php echo $lang['nc_colon'];?></dt>
        <dd>
          <ul class="ncsc-form-radio-list">
            <li>
              <input type="radio" name="is_fenxiao" id="is_appoint_1" value="1"  <?php if($output['goods']['is_verify'] == 1) {?>disabled<?php }?>  <?php if($output['goods']['is_fenxiao'] == 1) {?>checked<?php }?>>
              <label for="is_appoint_1">是</label>
            </li>
            <li>
              <input type="radio" name="is_fenxiao" id="is_appoint_0" value="0"  <?php if($output['goods']['is_verify'] == 1) {?>disabled<?php }?>  <?php if($output['goods']['is_fenxiao'] == 0) {?>checked<?php }?>>
              <label for="is_appoint_0">否</label>
            </li>
          </ul>
          <p class="hint">分销商提交产品分销后不可进行任何更改、终止操作，直至分销时效结束。设置分销返利及分销时效时请谨慎。</p>
            <p class="hint">本类商品分销最高返利比例为<span style="color:red;font-weight: bold;"><?php echo $output['fenxiao_rate']; ?>%</span>，返利比例模式为：0<普通<铜牌<银牌<金牌<系统设置，精确至小数点后一位</p>        </dd>
      </dl>

        <dl>
            <dt>分销返利（普通）：</dt>
            <dd>
                <input class="text" type="text" value="<?php echo $output['goods']['fenxiao_v1']; ?>" name="fenxiao_v1">%
                <p class="hint">高一级返利必须大于低一级返利。</p>
            </dd>
        </dl>
        <dl>
            <dt>分销返利（铜牌）：</dt>
            <dd>
                <input class="text" type="text" value="<?php echo $output['goods']['fenxiao_v2']; ?>" name="fenxiao_v2">%
            </dd>
        </dl>
        <dl>
            <dt>分销返利（银牌）：</dt>
            <dd>
                <input class="text" type="text" value="<?php echo $output['goods']['fenxiao_v3']; ?>" name="fenxiao_v3">%
            </dd>
        </dl>
        <dl>
            <dt>分销返利（金牌）：</dt>
            <dd>
                <input class="text" type="text" value="<?php echo $output['goods']['fenxiao_v4']; ?>" name="fenxiao_v4">%
            </dd>
        </dl>
        <dl>
            <dt>分销时效：</dt>
            <dd>
                <select name="fenxiao_day">
                    <option value=""><?php echo $lang['nc_please_choose'];?>...</option>
                    <?php if(!empty($output['fenxiao_day']) && is_array($output['fenxiao_day'])){ ?>
                        <?php foreach($output['fenxiao_day'] as $k){ ?>
                            <option value="<?php echo $k;?>" <?php if($output['goods']['fenxiao_day'] == $k){?>selected<?php }?>><?php echo $k;?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                天
            </dd>
        </dl>
 
    </div>
    <div class="bottom tc hr32">
      <label class="submit-border">
	  <?php if($output['goods']['is_verify'] == 1) {?>
	  
        <input type="submit" class="submit" disabled="disabled" value="审核中" />
     <?php }  else {?>
	 
	         <input type="submit" class="submit" value="提交" />

<?php }?>
	 </label>
    </div>
  </form>
</div>
<script type="text/javascript">
var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
var DEFAULT_GOODS_IMAGE = "<?php echo thumb(array(), 60);?>";
var SHOP_RESOURCE_SITE_URL = "<?php echo SHOP_RESOURCE_SITE_URL;?>";

</script> 
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/scrolld.js"></script>
<script type="text/javascript">$("[id*='Btn']").stop(true).on('click', function (e) {e.preventDefault();$(this).scrolld();})</script>
