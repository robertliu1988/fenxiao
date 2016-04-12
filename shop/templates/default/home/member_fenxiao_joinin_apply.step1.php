<?php defined('InShopNC') or exit('Access Invalid!');?>

<?php if (empty($output['joinin_detail'])) { ?>

<div id="apply_company_info" class="apply-company-info">
	
	
	  <div class="alert">
    <h4>注意事项：</h4>
    以下内容请认真填写。</div>

  <form id="form_company_info" action="index.php?act=member_fenxiao_joinin&op=step2" method="post" enctype="multipart/form-data" >
    
    <table border="0" cellpadding="0" cellspacing="0" class="all">
      <tbody>
	  <tr>
          <th><i>*</i>真实姓名：</th>
          <td><input name="member_truename" type="text" class="w100" />
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>身份证号码：</th>
          <td><input name="business_licence_number" type="text" class="w200" />
            <span></span></td>
        </tr>
		<tr>
          <th><i>*</i>联系方式：</th>
          <td><input name="member_mobile" type="text" class="w100" />
            <span></span></td>
        </tr>
 		<tr>
          <th><i>*</i>支付宝账号：</th>
          <td><input name="alipay_num" type="text" class="w200" />
            <span></span></td>
        </tr>
		<tr>
          <th><i>*</i>微信支付账号：</th>
          <td><input name="weixin_num" type="text" class="w200" />
            <span></span></td>
        </tr>
       <tr>
          <th><i>*</i>申请理由：</th>
          <td><textarea name="apply_reason" rows="3" class="w400"></textarea>
            <span></span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>


  </form>
  <div class="bottom"><a id="btn_apply_company_next" href="javascript:;" class="btn">下一步，提交申请</a></div>
</div>

<?php
} else {
?>

<div class="explain"><i></i><?php echo $output['joinin_message'];?></div>

<?php } ?>

<div class="bottom">
  <?php if($output['btn_next']) { ?>
  <a id="" href="<?php echo $output['btn_next'];?>" class="btn">下一步</a>
  <?php } ?>
</div>

<script type="text/javascript">
$(document).ready(function(){

    $('#form_company_info').validate({
        errorPlacement: function(error, element){
            element.nextAll('span').first().after(error);
        },
        rules : {
            apply_reason: {
                required: true
            }
        },
        messages : {
            apply_reason: {
                required: '请输入申请理由'
            },
        }
    });
	
	    $('#btn_apply_company_next').on('click', function() {
        if($('#form_company_info').valid()) {
            $('#form_company_info').submit();
        }
    });
});
</script> 
