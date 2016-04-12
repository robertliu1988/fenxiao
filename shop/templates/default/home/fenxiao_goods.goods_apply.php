<?php defined('InShopNC') or exit('Access Invalid!');?>

<style type="text/css">

.page {
    padding: 9px 20px 20px;
    text-align: left;
}

.nobdb {
    border-bottom: none;
}

.noborder, .noborder td {
    border-bottom: 0;
    border-top: 0;
}

.table {
    clear: both;
    width: 100%;
    margin-top: 8px;
}

a.btn { font-size: 14px; color: #555; font-weight: 700; line-height:18px; background: transparent url(../admin/templates/default/images/sky/bg_position.gif) no-repeat scroll 0 -280px; display: inline-block; height: 38px; padding-left: 15px; margin-right:6px; cursor: pointer;}
a.btn:hover { background-position: 0 -318px;}
a.btn:active { background-position: 0 -356px;}
a.btn span { background: #FFF url(../admin/templates/default/images/sky/bg_position.gif) no-repeat scroll 100% -280px; display: inline-block; height: 18px; padding: 10px 15px 10px 0;}
a:hover.btn span { color: #1AA3D1; background-position: 100% -318px;}
a:active.btn span { color: #63C7ED; background-position: 100% -356px;}

</style>


<div class="page">
  <form method="post" name="form1" id="form1" action="<?php echo urlShop('fenxiao_goods', 'goods_apply');?>">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" value="<?php echo $output["goods_id"];?>" name="goods_id">
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder" style="padding-bottom: 10px;
    font-size: 14px;
    color: black;">
		<?php if ($output["status"] ==1) {?>
          <td colspan="2" class="required"><label>确认申请</label></td>
		  		<?php } else {?>
          <td colspan="2" class="required"><label>无分销资格</label></td>

		  		  		<?php } ?>

        </tr>
        
      </tbody>
      <tfoot>
        <tr class="tfoot">
				<?php if ($output["status"] ==1) {?>

          <td colspan="2" style="padding-top:10px;"><a href="javascript:void(0);" class="btn" nctype="btn_submit"><span>提交</span></a></td>
		  		  		  		<?php } ?>

        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/admincp.js" charset="utf-8"></script>
<script>
$(function(){
    $('a[nctype="btn_submit"]').click(function(){
        ajaxpost('form1', '', '', 'onerror');
    });
    $('input[name="verify_state"]').click(function(){
        if ($(this).val() == 1) {
            $('tr[nctype="reason"]').hide();
        } else {
            $('tr[nctype="reason"]').show();
        }
    });
});
</script>