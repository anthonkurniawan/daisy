<?php
require 'init.php';
include "headerast.php";
?>
<table cellpadding="0" cellspacing="1" border="0">
  <tr valign="middle" bgcolor="#FF0000">
    <td width="80">
      <b>Rincian Kerusakan</b>
      <input name="asetgrup2[]" type="hidden" value="" id="asetgrup2" />
      <?php //}  ?>
    </td>
    <td width="30">
      <?php
      $res_cat = $db->get_results("SELECT group_name,asset_group_id FROM category GROUP BY group_name ORDER BY asset_group_id ASC");
      ?>
      <select name="asetgrup0" id="group_cat1" onchange="assetgroupchange('asetgrup0')">
        <option value="">-Category-</option>
        <?php
        foreach ($res_cat as $cat):
          ?>
          <option value="<?= $cat->asset_group_id ?>" <?= ($cat->group_name == $_POST['cat1'] ? 'selected="selected"' : '') ?>>
            <?= $cat->group_name ?> </option>
        <?php endforeach; ?>
        <option value=0>Other</option>
      </select>
    </td>
    <td width="65%">

    </td>
  <tr>
    <td width="50">
    </td>
    <td width="20">
      <div id="catgr0"></div>
    </td>

  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">
      <div id="merk_typegr0"> </div>
    </td>
  </tr>

  <tr class="even">
    <td>
      <button name="add" type="button" onclick="loadpartial()" id="add_partial" value="1">Add</button>
    </td>
    <td colspan="2"> </td>
  </tr>
  <tr>
    <td colspan="3">
      <div id="partial_add"></div>
    </td>
  </tr>

</table>
<input type="hidden" value="0" id="partValue" />
<script type="text/javascript">
  function loadpartial() {
    var ni = document.getElementById('partial_add');
    var numi = document.getElementById('partValue');
    var num = (document.getElementById('partValue').value - 1) + 2;
    numi.value = num;
    var newdiv = document.createElement('div');
    var divIdName = 'my' + num + 'Div';
    newdiv.setAttribute('id', divIdName);
    ni.appendChild(newdiv);

    $("#" + divIdName).load('./partial_add.php?add_partial=' + $('#add_partial').val() + '&num=' + num);
  };
</script>
<script type="text/javascript">
  function assetgroupchange(objname) {
    var group = document.getElementById('group_cat1').value;
    var groups = document.getElementById('group_cat1');
    name = objname;
    var index;
    index = name.substr(8);
    $('#catgr' + index).load('./cat1a.php?catId=' + $('#group_cat1').val() + '&anum=' + index);
    $("input[id=asetgrup2]").val(0);
  }
</script>