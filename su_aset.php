<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
$gres = $db->get_results("SELECT * FROM asset_group ORDER BY asset_group_id ASC");
					
if($_POST['g'] && $_POST['n']){
	$db->query("INSERT INTO asset(`asset_group_id`,`asset_name`) VALUES ('".$_POST['g']."','".htmlentities($_POST['n'])."')");
	$_SESSION['flash'] = 'Data Aset  Berhasil Disimpan!';	
}
if($_GET['delete']){
	$resdel = $db->get_row("SELECT * FROM asset WHERE `asset_id`='".$_GET['delete']."'");
}
if($_GET['edit']){
	$resupd = $db->get_row("SELECT * FROM asset WHERE `asset_id`='".$_GET['edit']."'");
}

if($_POST['ida'] && $_POST['en'] && $_POST['eg']){
	$db->query("UPDATE asset SET `asset_group_id`='".$_POST['eg']."', `asset_name`='".htmlentities($_POST['en'])."' WHERE `asset_id`='".$_POST['ida']."'");
	$_SESSION['flash'] = 'Data Aset Berhasil Diupdate!';
}

if($_GET['delok']){
	$db->query("DELETE FROM asset WHERE `asset_id`='".$_GET['delok']."'");
	$_SESSION['flash'] = 'Data Aset Berhasil Dihapus!';
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
			<h3>Data Aset</h3>
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
					Anda akan menghapus data aset: <strong>"<?=$resdel->asset_name?>"</strong>
					<div style="text-align:right">
					<a href="?delok=<?=$resdel->asset_id?>" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">OK</a>
					<a href="?" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">Cancel</a>
					</div>	</p>
				</div>
				<?php } ?>			
				<a href="#new">Input baru</a>
			
				<table width="98%" class="tabel">
					<tr>
						<th>Grup Aset</th>
						<th>Nama Aset</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>				
			<?php
				$res= $db->get_results("SELECT * FROM `asset` a JOIN `asset_group` ag ON a.asset_group_id=ag.asset_group_id ORDER BY `asset_id` ASC");
				if(is_array($res)&&!empty($res))
				{?>
				<?php	$i=0;foreach($res as $ga){ ?>
				<tr class="<?=($i%2==0?'even':'odd')?>">					
					<?php if($_GET['edit']==$ga->asset_id){ ?>
						<td colspan="2">
							<form method="post" action="?">
								<input type="hidden" name="ida" value="<?=$resupd->asset_id?>" />
								<select name="eg">
									<?php foreach($gres as $g): ?>
										<option <?php if($g->asset_group_id==$resupd->asset_group_id): ?>selected="selected"<?php endif; ?> value="<?=$g->asset_group_id?>"><?=$g->group_name?></option>
									<?php endforeach; ?>
								</select>								
								<input name="en" value="<?=$resupd->asset_name?>" size="30" />
								<input type="submit" value="Simpan" /> 
								<a href="?">batal</a>
							</form>
						</td>
						<td></td>
						<td></td>
					<?php }else{ ?>
						<td><?=$ga->group_name?></td>
						<td><?=$ga->asset_name?></td>
						<td><a href="?edit=<?=$ga->asset_id?>">Edit</a></td>
						<td><a href="?delete=<?=$ga->asset_id?>">Delete</a></td>
					<? } ?>
					</tr>
					<?php $i++;} ?>
				<?php
				}
			?>	
				<tr>
					<td colspan="3">
					<hr /><strong><a name="new">Input Aset Baru</a></strong><br />
					<form method="post" action="">
					Grup Aset: 
					<select name="g">
						<?php foreach($gres as $g): ?>
						<option value="<?=$g->asset_group_id?>"><?=$g->group_name?></option>
						<?php endforeach; ?>
					</select>
					Nama Aset :
					<input type="text" name="n" size="30" value="" /><input value="Simpan" type="submit"/></form></td>
				</tr>
				</table>						
		</td>
	</tr>
</table>
<?php include "footer.php"?>