<?php
require 'init.php';
include "headerast.php";

$addnum = $_GET['num'];
$grupname = "grupcat" . $addnum;

echo "GET num =" . $addnum . "</br>";
?>

<table cellpadding="0" cellspacing="1" border="0" width="">
  <tr bgcolor="#FF0000">  
    <td colspan="1" align="left" width="20">
      <b>Rincian Kerusakan</b>
            <input name="asetgrup[]" type="hidden" value="<?= $_GET['num'] ?>" />    
    </td>
    <td width="20">
      <?php $res_cat = $db->get_results("SELECT group_name,asset_group_id FROM category GROUP BY group_name ORDER BY asset_group_id ASC"); ?>
      <select name="grupcat<?= $addnum ?>"  id="group_cat_b">
        <option value="">-Category-</option>
        <?php
        # onchange="toogleAssetCtgor2()"
        foreach ($res_cat as $cat):
        ?>
        <option value="<?= $cat->asset_group_id ?>" <?= ($cat->group_name == $_POST['cat1'] ? 'selected="selected"' : '') ?>> <?= $cat->group_name ?> </option>
        <?php endforeach; ?>    
        <option value=0>Other</option>			
      </select>
    </td>
  </tr>
  <tr>
    <td width="50"></td>	
    <td width="20">
      <div id="catgr<?= $addnum ?>"></div>
    </td>
  </tr>

  <tr>
    <td>&nbsp;</td>
    <td colspan="1"> 
      <div id="merk_typegr<?= $addnum ?>"> </div>
    </td>
    <td width="50"></td>
  </tr>
</table>

<div id="partial_add"></div>
<?php //}   ?>

<script type="text/javascript">
  function loadpartial2()
  {
    $('#partial_add').load('./partial_add.php?add_partial2=' + $('#add_partial2').val());			
  };

  $("select[name*=grupcat]").change(function(){			
      var name = $(this).attr("name");
      var index;
      var opt = $(this).val();
      index = name.substr(7);
      $('#catgr'+index).load('./cat1a.php?catId='+opt+'&anum='+index);
    });
</script>