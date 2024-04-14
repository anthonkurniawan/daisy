<?php
require 'init.php';
require 'priviledges.php';
include "header.php";

if($_POST['inew']){
	$db->query("INSERT INTO cgl_vendor (`nama_vendor`) VALUES ('".htmlentities($_POST['inew'])."')");
	$_SESSION['flash'] = 'Data Vendor CGL Berhasil Disimpan!';	
}
if($_POST['ega']){
	$db->query("UPDATE cgl_vendor SET `nama_vendor`='".htmlentities($_POST['ega'])."' WHERE `id_cglv`='".$_POST['idga']."'");
	$_SESSION['flash'] = 'Data Vendor CGL Berhasil Diupdate!';
}

if($_GET['delete']){
	$resdel = $db->get_row("SELECT * FROM cgl_vendor WHERE `id_cglv`='".$_GET['delete']."'");
}
if($_GET['edit']){
	$resupd = $db->get_row("SELECT * FROM cgl_vendor WHERE `id_cglv`='".$_GET['edit']."'");
}
if($_GET['delok']){
	$db->query("DELETE FROM cgl_vendor WHERE `id_cglv`='".$_GET['delok']."'");
	$_SESSION['flash'] = 'Data Vendor CGL!';
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
			<h3>Vendor untuk CGL</h3>
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
Anda akan menghapus data vendor CGL: <strong>"<?=$resdel->nama_vendor ?>"</strong>
<div style="text-align:right">
<a href="?delok=<?=$resdel->id_cglv?>" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">OK</a>
<a href="?" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">Cancel</a>
</div>	</p>
				</div>
				<?php } ?>			
				<table width="98%" class="tabel" cellspacing="0" border="0">
					<tr>
						<th>Nama Vendor</th>
						<th>Regional</th>
						<th>Alamat</th>
						<th>Email</th>
						<th>Telepon</th>
						<th>Pimpinan</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>				
			<?php
				$res= $db->get_results("SELECT * FROM `cgl_vendor` ORDER BY `regional` ASC");
				if(is_array($res)&&!empty($res))
				{?>
				<?php	$i=0;foreach($res as $ga){ ?>
				<tr class="<?=($i%2==0?'even':'odd')?>">					
					<?php if($_GET['edit']==$ga->id_cglv){ ?>
						<td><form method="post" action="?"><input type="hidden" name="idga" value="<?=$resupd->id_cglv?>" /><input name="ega" value="<?=$resupd->nama_vendor ?>" size="50" /><input type="submit" value="Simpan" /> <a href="?">batal</a></form></td>
						<td></td>
						<td></td>
					<?php }else{ ?>
						<td><?=$ga->nama_vendor?></td>
						<td><?=$ga->regional?></td>
						<td><?=$ga->alamat?></td>
						<td><?=$ga->email?></td>
						<td><?=$ga->telp?></td>
						<td><?=$ga->pimpinan?></td>
						<td><a href="?edit=<?=$ga->id_cglv?>">Edit</a></td>
						<td><a href="?delete=<?=$ga->id_cglv?>">Delete</a></td>
					<? } ?>
					</tr>
					<?php $i++;} ?>
				<?php
				}
			?>	
				<tr>
					<td colspan="3">
					<strong>Input Vendor C G L</strong><br />
					<form method="post" action="">
					Nama Vendor:
					<input type="text" name="inew" size="50" value="" /><input value="Simpan" type="submit"/></form></td>
				</tr>
				</table>						
		</td>
	</tr>
</table>
<?php include "footer.php"?>