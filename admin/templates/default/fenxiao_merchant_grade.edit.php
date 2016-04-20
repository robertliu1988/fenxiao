<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['fenxiao_merchant'];?></h3>
      <ul class="tab-base">
	    <li><a href="index.php?act=fenxiao_merchant&op=store" ><span><?php echo $lang['nc_manage']?></span></a></li>
        <li><a href="index.php?act=fenxiao_merchant&op=fenxiao_joinin" ><span><?php echo $lang['verify'];?></span></a></li>
        <li><a href="index.php?act=fenxiao_merchant&op=grade"><span><?php echo $lang['level'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_edit'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="grade_form" enctype="multipart/form-data" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="fmg_id" value="<?php echo $output['grade_array']['fmg_id'];?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
		<tr>
          <td colspan="2" class="required"><label for="fmg_id"><?php echo $lang['grade_sortname']; //级别?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
            <input type="text" value="<?php echo $output['grade_array']['fmg_id'];?>" id="fmg_id" name="fmg_id" class="txt" readonly="readonly">
            </td>
          <td class="vatop tips">级别不可修改</td>
        </tr>
		
        <tr>
          <td colspan="2" class="required"><label class="validation" for="fmg_name"><?php echo $lang['store_grade_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['grade_array']['fmg_name'];?>" id="fmg_name" name="fmg_name" class="txt"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="fmg_goods_limit">可发布分销商品数:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['grade_array']['fmg_goods_limit'];?>" id="fmg_goods_limit" name="fmg_goods_limit" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['zero_said_no_limit'];?></td>
        </tr>
		
		<tr>
          <td colspan="2" class="required"><label for="fmg_member_limit">分销员数:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['grade_array']['fmg_member_limit'];?>" id="fmg_member_limit" name="fmg_member_limit" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['zero_said_no_limit'];?></td>
        </tr>
		
        <tr><td colspan="2" class="required"><label> 积分要求:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['grade_array']['fmg_points'];?>" id="fmg_points" name="fmg_points" class="txt"></td>
		</tr>
		
		<tr id="adv_pic" >
          <input type="hidden" name="mark" value="0">
          <td colspan="2" class="required"><label for="file_adv_pic">图标:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
		  <span class="type-file-show"><img class="show_image" src="<?php echo ADMIN_TEMPLATES_URL;?>/images/preview.png">
<div class="type-file-preview"><img src="<?php echo UPLOAD_SITE_URL."/".ATTACH_ADV."/".$output['grade_array']['fmg_icon'];?>"></div>
            </span>
		  <span class="type-file-box">
            <input type="file" class="type-file-file" id="file_adv_pic" name="adv_pic" size="30" />
            </span>
            <input type="hidden" name="pic_ori" value="<?php echo $output['grade_array']['fmg_icon'];?>"></td>
          <td class="vatop tips">支持gif,jpg,jpeg,png </td>
        </tr>
		
        
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>

<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
     $("#grade_form").submit();
	});
});
//
$(document).ready(function(){
	$('#grade_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },

        rules : {
            sg_name : {
                required : true,
                remote   : {
                url :'index.php?act=store_grade&op=ajax&branch=check_grade_name',
                type:'get',
                data:{
                        sg_name : function(){
                        	return $('#sg_name').val();
                        },
                        sg_id  : '<?php echo $output['grade_array']['sg_id'];?>'
                    }
                }
            },
			sg_price : {
                required  : true,
                number : true,
                min : 0
            },
            sg_goods_limit : {
                digits  : true
            },
            sg_space_limit : {
                digits : true
            },
            sg_sort : {
            	required  : true,
                digits  : true,
                remote   : {
	                url :'index.php?act=store_grade&op=ajax&branch=check_grade_sort',
	                type:'get',
	                data:{
	                        sg_sort : function(){
	                        	return $('#sg_sort').val();
	                        },
	                        sg_id  : '<?php echo $output['grade_array']['sg_id']; ?>'
	                    }
                }
            }
        },
        messages : {
            sg_name : {
                required : '<?php echo $lang['store_grade_name_no_null'];?>',
                remote   : '<?php echo $lang['now_store_grade_name_is_there'];?>'
            },
			sg_price : {
                required  : "<?php echo $lang['charges_standard_no_null'];?>",
                number : "<?php echo $lang['charges_standard_no_null'];?>",
                min : "<?php echo $lang['charges_standard_no_null'];?>"
            },
            sg_goods_limit : {
                digits : '<?php echo $lang['only_lnteger'];?>'
            },
            sg_space_limit : {
                digits  : '<?php echo $lang['only_lnteger'];?>'
            },
            sg_sort  : {
            	required : '<?php echo $lang['grade_add_sort_null_error']; //级别信息不能为空?>',
                digits   : '<?php echo $lang['only_lnteger'];?>',
                remote   : '<?php echo $lang['add_gradesortexist']; //级别已经存在?>'
            }
        }
    });
});

$(function(){
	var textButton="<input type='text' name='textfield' id='textfield1' class='type-file-text' /><input type='button' name='button' id='button1' value='' class='type-file-button' />"
    $(textButton).insertBefore("#file_adv_pic");
    $("#file_adv_pic").change(function(){
	$("#textfield1").val($("#file_adv_pic").val());
    });

	var textButton="<input type='text' name='textfield' id='textfield3' class='type-file-text' /><input type='button' name='button' id='button3' value='' class='type-file-button' />"
    $(textButton).insertBefore("#file_flash_swf");
    $("#file_flash_swf").change(function(){
	$("#textfield3").val($("#file_flash_swf").val());
    });
});
</script>
