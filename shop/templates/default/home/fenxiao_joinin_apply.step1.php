<?php defined('InShopNC') or exit('Access Invalid!');?>

<?php if (is_array($output['joinin_detail']) && !empty($output['joinin_detail'])) { ?>

<div id="apply_company_info" class="apply-company-info">
	
	<table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">公司及联系人信息</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">公司名称：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['company_name'];?></td>
      </tr>
      <tr>
        <th>公司所在地：</th>
        <td><?php echo $output['joinin_detail']['company_address'];?></td>
        <th>公司详细地址：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['company_address_detail'];?></td>
      </tr>
      <tr>
        <th>公司电话：</th>
        <td><?php echo $output['joinin_detail']['company_phone'];?></td>
        <th>员工总数：</th>
        <td><?php echo $output['joinin_detail']['company_employee_count'];?>&nbsp;人</td>
        <th>注册资金：</th>
        <td><?php echo $output['joinin_detail']['company_registered_capital'];?>&nbsp;万元 </td>
      </tr>
      <tr>
        <th>联系人姓名：</th>
        <td><?php echo $output['joinin_detail']['contacts_name'];?></td>
        <th>联系人电话：</th>
        <td><?php echo $output['joinin_detail']['contacts_phone'];?></td>
        <th>电子邮箱：</th>
        <td><?php echo $output['joinin_detail']['contacts_email'];?></td>
      </tr>
    </tbody>
  </table>
	
	  <div class="alert">
    <h4>注意事项：</h4>
    申请理由请认真填写。</div>

  <form id="form_company_info" action="index.php?act=fenxiao_joinin&op=step2" method="post" enctype="multipart/form-data" >
    
    <table border="0" cellpadding="0" cellspacing="0" class="all">
      <tbody>
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
