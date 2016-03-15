<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['nc_fenxiaoconfig'];?></h3>
      <ul class="tab-base"><li><a class="current"><span><?php echo $lang['nc_fenxiaoconfig'];?></span></a></li></ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title"><h5><?php echo $lang['nc_prompts'];?></h5><span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td>
        <ul>
            <li><?php echo $lang['fenxiao_config_help'];?></li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <table class="table tb-type2">
    <thead>
      <tr class="thead">
        <th><?php echo $lang['fenxiao_config_name'];?></th>
        <th class="align-center"><?php echo $lang['fenxiao_config_status'];?></th>
        <th class="align-center"><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($output['fenxiao_config']) && is_array($output['fenxiao_config'])){ foreach($output['fenxiao_config'] as $k => $v){ ?>
      <tr class="hover">
        <td><?php echo $v['config_name'];?></td>
        <td class="w25pre align-center">
          <?php echo $v['config_value'] == '1' ? $lang['fenxiao_config_enable'] : $lang['fenxiao_config_disable'];?>
        </td>
        <td class="w156 align-center"><a href="index.php?act=fenxiao_config&op=edit&config_key=<?php echo $v['config_key']; ?>"><?php echo $lang['nc_edit']?></a></td>
      </tr>
      <?php } } ?>
    </tbody>
    <tfoot>
      <tr class="tfoot">
        <td colspan="15"></td>
      </tr>
    </tfoot>
  </table>
</div>