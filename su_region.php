<?php
require 'init.php';
require 'priviledges.php';
include "header.php";

if(!empty($_POST['reg'])&&!empty($_POST['koreg'])){
	$db->query("INSERT INTO region (`kode_region`,`region`) VALUES ('".$_POST['koreg']."','".$_POST['reg']."')");
	$_SESSION['flash'] = 'Data Region  Berhasil Disimpan!';	
}
if($_POST['idreg']&&!empty($_POST['reg'])){
	$db->query("UPDATE `region` SET `kode_region`='".$_POST['idreg']."', `region`='".htmlentities($_POST['reg'])."' WHERE `kode_region`='".$_POST['idreg']."'");
	$_SESSION['flash'] = 'Data Region Berhasil Diupdate!';
}
if($_GET['delete']){
	$resdel = $db->get_row("SELECT * FROM `region` WHERE `kode_region`='".$_GET['delete']."'");
}
if($_GET['edit']){
	$resupd = $db->get_row("SELECT * FROM `region` WHERE `kode_region`='".$_GET['edit']."'");
}
if($_GET['delok']){
	$db->query("DELETE FROM region WHERE `kode_region`='".$_GET['delok']."'");
	$_SESSION['flash'] = 'Data Regional Berhasil Dihapus!';
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
			<h3>DATA INDUK: REGION</h3>
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
Anda akan menghapus data regional: <strong>"<?=$resdel->kode_region?> - <?=$resdel->region?>"</strong>
<div style="text-align:right">
<a href="?delok=<?=$resdel->kode_region?>" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">OK</a>
<a href="?" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">Cancel</a>
</div>	</p>
				</div>
				<?php } ?>			
				<table width="98%" class="tabel">
					<tr>
						<th>Kode Region</th>
						<th>Nama Region</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>				
			<?php
				$res= $db->get_results("SELECT * FROM `region` ORDER BY `kode_region` ASC");
				if(is_array($res)&&!empty($res))
				{?>
				<?php	$i=0;foreach($res as $ga){ ?>
				<tr class="<?=($i%2==0?'even':'odd')?>">					
					<?php if($_GET['edit']==$ga->kode_region){ ?>
						<td>
							<form method="post" action="?">
								Kode: <input type="text" name="idreg" value="<?=$resupd->kode_region?>" size="4" /> Region: 
								<input type="text" name="reg" value="<?=$resupd->region?>" size="20" />
								<input type="submit" value="Simpan" /> 
								<a href="?">batal</a>
							</form>
						</td>
						<td></td>
						<td></td>
						<td></td>
					<?php }else{ ?>
						<td><?=$ga->kode_region?></td>
						<td><?=$ga->region?></td>
						<td><a href="?edit=<?=$ga->kode_region?>">Edit</a></td>
						<td><a href="?delete=<?=$ga->kode_region?>">Delete</a></td>
					<? } ?>
					</tr>
					<?php $i++;} ?>
				<?php
				}
			?>	
				<tr>
					<td colspan="4">
					<strong>Input Region Baru</strong><br />
					<form method="post" action="">
					Kode/ Nama Region: <input maxlength="4" size="4" type="text" name="koreg" size="5" value="" /> / 
					<input type="text" name="reg" size="20" value="" /><input value="Simpan region baru" type="submit"/></form></td>
				</tr>
				</table>						
		</td>
	</tr>
</table>
<?php include "footer.php"?>