<?php
require 'init.php';
$subitem = $_GET['itemId'];
$query = "SELECT type,merk FROM `category` WHERE `item1` = '{$_GET['itemId']}'";
$r = $db->get_row($query);
?>
<?php echo "itemId  :  " . $_GET['itemId'] . "<BR/>"; ?>
<?php  //echo "test site :  ".$subitem."<BR/>";  ?>
<table width="80%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td> Merk </td>
    <td>
      <select name="merk" id="merk" onchange="toogleAssetCtgor2()">
        <option value="">-merk-</option>
        <?php $res_merk = $db->get_results("SELECT distinct(merk) FROM category WHERE `id_item`= '{$_GET['itemId']}' GROUP BY merk ORDER BY merk ASC");
        foreach ($res_merk as $merk): ?>
          <option value="<?= $merk->merk ?>" <?= ($merk->merk == $_POST['merk'] ? 'selected="selected"' : '') ?>> <?= $merk->merk ?>
          </option>
        <?php endforeach;
        ?>
      </select>
    </td>

    <td nowrap="nowrap">Other</td>
    <td><input type="text" name="oth_merk" /> </td>
    <td nowrap="nowrap">
      <div class="keterangan">* Diisi jika perlu</div>
    </td>
  </tr>

  <tr>
    <td> Type </td>
    <td>
      <select name="type" id="type" onchange="toogleAssetCtgor2()">
        <option value="">-Type-</option>
        <?php $res_type = $db->get_results("SELECT distinct(type) FROM category WHERE `id_item`= '{$_GET['itemId']}' GROUP BY type ORDER BY type ASC");
        foreach ($res_type as $type): ?>
          <option value="<?= $type->type ?>" <?= ($type->type == $_POST['type'] ? 'selected="selected"' : '') ?>> <?= $type->type ?>
          </option>
        <?php endforeach;
        ?>
      </select>
    </td>

    <td nowrap="nowrap">Other</td>
    <td><input type="text" name="oth_type" /> </td>
    <td nowrap="nowrap">
      <div class="keterangan">* Diisi jika perlu</div>
    </td>
  </tr>

  <tr>
    <td>Quantity</td>
    <td><input type="text" name="quan" /> </td>
    <td nowrap="nowrap" colspan="3">
      <div class="keterangan">* Diisi jika perlu</div>
    </td>
  </tr>

  <tr>
    <td>satuan</td>
    <td><input type="text" name="sat" /> </td>
    <td nowrap="nowrap" colspan="3">
      <div class="keterangan">* Diisi jika perlu</div>
    </td>
  </tr>
</table>