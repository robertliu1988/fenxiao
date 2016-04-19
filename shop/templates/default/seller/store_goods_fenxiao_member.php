<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
 <!--好商城V3-B11-->
  <a title="批量生成商品二维码" style="display:none;" class="ncsc-btn ncsc-btn-green" href="index.php?act=store_goods_online&amp;op=maker_qrcode" target="_blank" style="right:100px" onclick="return confirm('您确定要执行批量生成二维码吗？');">批量生成商品二维码</a>
  <a href="<?php echo urlShop('store_goods_add');?>" style="display:none;" class="ncsc-btn ncsc-btn-green" title="<?php echo $lang['store_goods_index_add_goods'];?>"> <?php echo $lang['store_goods_index_add_goods'];?></a></div>
<form method="get" action="index.php">
  <table class="search-form">
    <input type="hidden" name="act" value="store_goods_fenxiao_member" />
    <input type="hidden" name="op" value="index" />
    <tr>
      <td>&nbsp;</td>
      <th>等级</th>
      <td class="w160"><select name="grade" class="w150">
          <option value="0"><?php echo $lang['nc_please_choose'];?></option>
          <?php if(is_array($output['grade_list']) && !empty($output['grade_list'])){?>
          <?php foreach ($output['grade_list'] as $val) {?>
          <option value="<?php echo $val['fmg_id']; ?>" <?php if ($_GET['grade'] == $val['fmg_id']){ echo 'selected=selected';}?>><?php echo $val['fmg_name']; ?></option>
          <?php }?>
          <?php }?>
        </select></td>
        <th>会员名</th>
        <td class="w160"><input type="text" class="text w150" name="member_name" value="<?php echo $_GET['member_name']; ?>"/></td>
        <th>真实姓名</th>
        <td class="w160"><input type="text" class="text w150" name="member_truename" value="<?php echo $_GET['member_truename']; ?>"/></td>

        <td class="tc w70"><label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" />
        </label></td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr nc_type="table_header">
	 <th class="w150">申请人/真实姓名</th>

      <th class="w50">&nbsp;</th>
      <th coltype="editable" column="goods_name" checker="check_required" inputwidth="230px"><?php echo $lang['store_goods_index_goods_name'];?></th>
      <th class="w100">申请人等级</th>
      <th class="w150">申请时间</th>
      <th class="w150">状态</th>
      <th class="w120"><?php echo $lang['nc_handle'];?></th>
    </tr>
   
  </thead>
  <tbody>
    <?php if (!empty($output['apply_list'])) { ?>
    <?php foreach ($output['apply_list'] as $val) { ?>
    
    <tr>
	      <td><span><?php echo $val['member_name']; ?></span></td>

      <td><div class="pic-thumb"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $val['goods_id']));?>" target="_blank"><img src="<?php echo thumb($val, 60);?>"/></a></div></td>
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
            <a href="<?php echo urlShop('goods', 'index', array('goods_id' => $val['goods_id']));?>" target="_blank"><?php echo $val['goods_name']; ?></a></dt>
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
      <td><span><?php echo $val['member_level']; ?></span></td>
      <td class="goods-time"><?php echo @date('Y-m-d H:i:s',$val['apply_time']);?></td>
      <td><span><?php echo $val['status']; ?></span></td>
      <td class="nscs-table-handle" style="width:220px;">
        
		
		<span><a href="javascript:void(0);" onclick="ajax_get_confirm('确定通过审核', '<?php echo urlShop('store_goods_fenxiao_member', 'verify', array('goods_id' => $val['goods_id'],'member_id' => $val['member_id']));?>');" class="btn-blue"><i class="icon-trash"></i>
        <p>通过</p>
        </a></span>
		
		
		<span><a href="javascript:void(0);" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('store_goods_fenxiao_member', 'delete', array('goods_id' => $val['goods_id'],'member_id' => $val['member_id']));?>');" class="btn-red"><i class="icon-trash"></i>
        <p><?php echo $lang['nc_del'];?></p>
        </a></span>

            
		</td>
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
    <?php  if (!empty($output['apply_list'])) { ?>
    
    <tr>
      <td colspan="20"><div class="pagination"> <?php echo $output['show_page']; ?> </div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script> 
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/store_goods_list.js"></script> 
<script>
$(function(){
    //Ajax提示
    $('.tip').poshytip({
        className: 'tip-yellowsimple',
        showTimeout: 1,
        alignTo: 'target',
        alignX: 'center',
        alignY: 'top',
        offsetY: 5,
        allowTipHover: false
    });
    $('a[nctype="batch"]').click(function(){
        if($('.checkitem:checked').length == 0){    //没有选择
        	showDialog('请选择需要操作的记录');
            return false;
        }
        var _items = '';
        $('.checkitem:checked').each(function(){
            _items += $(this).val() + ',';
        });
        _items = _items.substr(0, (_items.length - 1));

        var data_str = '';
        eval('data_str = ' + $(this).attr('data-param'));

        if (data_str.sign == 'jingle') {
            ajax_form('ajax_jingle', '设置广告词', data_str.url + '&commonid=' + _items + '&inajax=1', '480');
        } else if (data_str.sign == 'plate') {
            ajax_form('ajax_plate', '设置关联版式', data_str.url + '&commonid=' + _items + '&inajax=1', '480');
        }
    });
});
</script>