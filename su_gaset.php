<?php
require 'init.php';
require 'priviledges.php';
include "header.php";

if($_POST['inew']){
	$db->query("INSERT INTO asset_group (`group_name`,`active`) VALUES ('".htmlentities($_POST['inew'])."','1')");
	$_SESSION['flash'] = 'Data Grup Aset  Berhasil Disimpan!';	
}
if($_POST['ega']){
	$db->query("UPDATE asset_group SET `group_name`='".htmlentities($_POST['ega'])."' WHERE `asset_group_id`='".$_POST['idga']."'");
	$_SESSION['flash'] = 'Data Grup Aset  Berhasil Diupdate!';
}

if($_GET['delete']){
	$resdel = $db->get_row("SELECT * FROM asset_group WHERE `asset_group_id`='".$_GET['delete']."'");
}
if($_GET['edit']){
	$resupd = $db->get_row("SELECT * FROM asset_group WHERE `asset_group_id`='".$_GET['edit']."'");
}
if($_GET['delok']){
	$db->query("DELETE FROM asset_group WHERE `asset_group_id`='".$_GET['delok']."'");
	$_SESSION['flash'] = 'Data Grup Aset Berhasil Dihapus!';
}
?>
<table width="800" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
		<td style="width:140px">
			<ul style="list-style:none;padding-left:5px">
				<?php include "menusuper.php" ?>
			</ul>
		</td>
		<td>
			<h3>Asset Group</h3>
			<?php
				if($_SESSION['flash'])
				{
				?>
				<div class="ui-state-highlight ui-corner-all" style="width:80%;margin:0px auto;padding: 0 .7em;"> 
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
					<?=$_SESSION['flash']; ?></p>
				</div>
				<?php	
				unset($_SESSION['flash']);
				}
			?>
			
			<?php
				if($resdel)
				{
				?>
				<div class="ui-state-highlight ui-corner-all" style="width:80%;margin:0px auto;padding: 0 .7em;"> 
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
Anda akan menghapus data asset group: <strong>"<?=$resdel->group_name?>"</strong>
<div style="text-align:right">
<a href="?delok=<?=$resdel->asset_group_id?>" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">OK</a>
<a href="?" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">Cancel</a>
</div>	</p>
				</div>
				<?php } ?>			
				<table width="98%" class="tabel">
					<tr>
						<th>Nama Grup</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>				
			<?php
				$res= $db->get_results("SELECT * FROM `asset_group` ORDER BY `asset_group_id` ASC");
				if(is_array($res)&&!empty($res))
				{?>
				<?php	$i=0;foreach($res as $ga){ ?>
				<tr class="<?=($i%2==0?'even':'odd')?>">					
					<?php if($_GET['edit']==$ga->asset_group_id){ ?>
						<td><form method="post" action="?"><input type="hidden" name="idga" value="<?=$resupd->asset_group_id?>" /><input name="ega" value="<?=$resupd->group_name?>" size="50" /><input type="submit" value="Simpan" /> <a href="?">batal</a></form></td>
						<td></td>
						<td></td>
					<?php }else{ ?>
						<td><?=$ga->group_name?></td>
						<td><a href="?edit=<?=$ga->asset_group_id?>">Edit</a></td>
						<td><?php if($ga->asset_group_id>4&&$ga->asset_group_id){ ?><a href="?delete=<?=$ga->asset_group_id?>">Delete</a><?php } ?></td>
					<? } ?>
					</tr>
					<?php $i++;} ?>
				<?php
				}
			?>	
				<tr>
					<td colspan="3">
					<hr /><strong>Input Grup Aset Baru</strong><br />
					<form method="post" action="">
					Nama Grup Aset Baru:
					<input type="text" name="inew" size="50" value="" /><input value="Simpan" type="submit"/></form></td>
				</tr>
				</table>						
		</td>
	</tr>
</table>
<?php include "footer.php"?>