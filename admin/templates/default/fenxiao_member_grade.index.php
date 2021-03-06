<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['fenxiao_member'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=fenxiao_member&op=member" ><span><?php echo $lang['nc_manage']?></span></a></li>
        <li><a href="index.php?act=fenxiao_member&op=fenxiao_joinin" ><span><?php echo $lang['verify'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['level'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>

  <form id="form_grade" method='post' name="">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th><?php echo $lang['grade_sortname']; ?></th>
          <th><?php echo $lang['store_grade_name'];?></th>
          <th class="align-center">可分销商品数</th>
          <th class="align-center">积分要求</th>
          <th class="align-center">图标</th>
          <th><?php echo $lang['nc_handle'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['grade_list']) && is_array($output['grade_list'])){ ?>
        <?php foreach($output['grade_list'] as $k => $v){ ?>
        <tr class="hover">
          
          <td class="w36"><?php echo $v['fmg_id'];?></td>
          <td><?php echo $v['fmg_name'];?></td>
          <td class="align-center"><?php echo $v['fmg_goods_limit'];?></td>
          <td class="align-center"><?php echo $v['fmg_points'];?></td>
         <td class="align-center"><img src="<?php echo UPLOAD_SITE_URL."/".ATTACH_ADV."/".$v['fmg_icon'];?>"></td>
          <td class="w270"><a href="index.php?act=fenxiao_member&op=grade_edit&fmg_id=<?php echo $v['fmg_id'];?>"><?php echo $lang['nc_edit'];?></a></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="10"><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </form>
</div>
