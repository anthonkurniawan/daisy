<?php
require 'init.php';
include "headerast.php";
?>

<table width="80%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="8%" align="left">
      <strong>Item</strong><br />
    </td>
    <td width="92%">
      <select name="itemx" id="item_merktypex" onchange="toogleAssetCtgor2()">
        <option value="">-Item-</option>
        <?php 
				$res_item = $db->get_results("SELECT distinct(item1),(id_item) FROM `category`  WHERE `asset_group_id` = '{$_GET['catId']}' GROUP BY item1 ORDER BY item1 ASC");
        foreach ($res_item as $item): 
				?>
          <option <?= ($item->item1 == $_POST['itemx'] ? 'selected="selected"' : '') ?> value="<?= $item->item1 ?>">
            <?= $item->item1 ?> 
					</option>
        <?php endforeach;
        ?>
      </select>
    </td>
  </tr>
</table>