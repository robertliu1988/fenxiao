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
              <th><i>*</i>手执身份证照片：</th>
              <td><input name="business_licence_number_electronic" type="file" class="w200" />
                  <img border="0" alt="手执身份证照范例" src="<?php echo SHOP_TEMPLATES_URL;?>/images/example.jpg" style="width:300px;height:210px">
                  <span class="block">请确保图片清晰，身份证上文字可辨（清晰照片也可使用）。</span></td>
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
  <a id="" href="<?php echo $output['btn_next'];?>" class="btn"><?php echo $output['btn_msg']; ?></a>
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
            },
            business_licence_number: {
                required: true,
                maxlength: 20
            },
            business_licence_number_electronic: {
                required: true
            },
            member_truename: {
                required: true,
                maxlength: 20
            }
        },
        messages : {
            apply_reason: {
                required: '请输入申请理由'
            },
            business_licence_number: {
                required: '请输入身份证号',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            business_licence_number_electronic: {
                required: '请选择上传手执身份证照'
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
