<?php defined('InShopNC') or exit('Access Invalid!');?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/styles/nyroModal.css" rel="stylesheet" type="text/css" id="cssfile2" />
<script type="text/javascript">
    $(document).ready(function(){
        $('a[nctype="nyroModal"]').nyroModal();

        $('#btn_fail').on('click', function() {
            if($('#joinin_message').val() == '') {
                $('#validation_message').text('请输入审核意见');
                $('#validation_message').show();
                return false;
            } else {
                $('#validation_message').hide();
            }
            if(confirm('确认拒绝申请？')) {
                $('#verify_type').val('fail');
                $('#form_store_verify').submit();
            }
        });
        $('#btn_pass').on('click', function() {
            var valid = true;
            $('[nctype="commis_rate"]').each(function(commis_rate) {
                rate = $(this).val();
                if(rate == '') {
                    valid = false;
                    return false;
                }

                var rate = Number($(this).val());
                if(isNaN(rate) || rate < 0 || rate >= 100) {
                    valid = false;
                    return false;
                }
            });
            if(valid) {
                $('#validation_message').hide();
                if(confirm('确认通过申请？')) {
                    $('#verify_type').val('pass');
                    $('#form_store_verify').submit();
                }
            } else {
                $('#validation_message').text('请正确填写分佣比例');
                $('#validation_message').show();
            }
        });
    });
</script>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>分销员</h3>
      <ul class="tab-base">
        <li><a href="index.php?act=fenxiao_member&op=member"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="index.php?act=fenxiao_member&op=fenxiao_joinin" ><span>等待审核</span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $output['joinin_detail_title'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
 
  <form id="form_store_verify" action="index.php?act=fenxiao_member&op=fenxiao_joinin_verify" method="post">
    <input id="verify_type" name="verify_type" type="hidden" />
    <input name="member_id" type="hidden" value="<?php echo $output['joinin_detail']['member_id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">申请信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">真实姓名</th>
          <td><?php echo $output['joinin_detail']['member_truename'];?></td>
        </tr>
		
		<tr>
          <th class="w150">联系方式</th>
          <td><?php echo $output['joinin_detail']['member_mobile'];?></td>
        </tr>
		
		<tr>
          <th class="w150">身份证</th>
          <td><?php echo $output['joinin_detail']['business_licence_number'];?></td>
        </tr>
		
		<tr>
          <th class="w150">支付宝账号</th>
          <td><?php echo $output['joinin_detail']['alipay_num'];?></td>
        </tr>
		<tr>
          <th class="w150">微信账号</th>
          <td><?php echo $output['joinin_detail']['weixin_num'];?></td>
        </tr>

          <tr>
          <th class="w150">申请理由</th>
          <td><?php echo $output['joinin_detail']['apply_reason'];?></td>
        </tr>

   <?php if(in_array(intval($output['joinin_detail']['status']), array(FENXIAO_JOIN_STATE_VERIFY))) { ?>
    <tr>
        <th>审核意见：</th>
        <td colspan="2"><textarea id="joinin_message" name="joinin_message"></textarea></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
   <?php if(in_array(intval($output['joinin_detail']['status']), array(FENXIAO_JOIN_STATE_VERIFY))) { ?>
    <div id="validation_message" style="color:red;display:none;"></div>
    <div><a id="btn_fail" class="btn" href="JavaScript:void(0);"><span>拒绝</span></a> <a id="btn_pass" class="btn" href="JavaScript:void(0);"><span>通过</span></a></div>
    <?php } ?>
  </form>
</div>
