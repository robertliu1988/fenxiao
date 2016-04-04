<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>分销员</h3>
      <ul class="tab-base">
        <li><a href="index.php?act=fenxiao_member&op=member" ><span><?php echo $lang['nc_manage']?></span></a></li>
        <li><a href="index.php?act=fenxiao_member&op=fenxiao_joinin" ><span>分销审核</span></a></li>
        <li><a href="index.php?act=fenxiao_member&op=grade" ><span>分销员等级</span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_edit'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="grade_form" method="post">
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
          <td colspan="2" class="required"><label for="fmg_goods_limit">可分销商品数:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['grade_array']['fmg_goods_limit'];?>" id="fmg_goods_limit" name="fmg_goods_limit" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['zero_said_no_limit'];?></td>
        </tr>
        <tr><td colspan="2" class="required"><label> 积分要求:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['grade_array']['fmg_points'];?>" id="fmg_points" name="fmg_points" class="txt"></td>
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
</script>
