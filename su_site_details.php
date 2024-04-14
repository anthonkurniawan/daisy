<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
if($_POST)
{
	if(empty($_POST['roleu'])||empty($_POST['u'])||empty($_POST['p'])||empty($_POST['kp'])||
	empty($_POST['n'])||empty($_POST['i'])||empty($_POST['r'])||empty($_POST['j'])||empty($_POST['po'])||empty($_POST['e1'])){
		$kosong = 1;
	}
	else
	{
		if($_POST['p']<>$_POST['kp'])
		{
			$notsame = 1;
		}
		else
		{
			$db->query("INSERT INTO `user` (
			`role`,`username`,`password`,`nama`,`inisial`,`regional`,`posisi`,`pangkat`,`email1`,`email2`,`email3`,`active`) VALUES 
			('".strtoupper($_POST['roleu'])."','".$_POST['u']."','".$_POST['p']."','".$_POST['n']."','".$_POST['i']."','".$_POST['r']."',
			'".strtoupper($_POST['po'])."','".$_POST['j']."','".$_POST['e1']."','".$_POST['e2']."','".$_POST['e3']."','1')");
			$sukses=1;
		}
	}
}
?>
<table width="800" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto;" id="badan">
		<?php include "webheader.php"; ?>
		<tr valign="top">
		<td style="width:140px">
			<ul style="list-style:none;padding-left:5px">
				<?php include "menusuper.php" ?>
			</ul>
		</td>
		<td>
			<h3>DAISY SITE</h3>
			<h3>Input Data Site Baru</h3>
				<?php if($kosong==1 || $notsame==1 || $error==1): ?>
				<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;;width:450px;margin:30px auto;"> 
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
					<?php if($kosong==1) echo 'Ada kolom yang belum diisi'; ?>
					<?php if($notsame==1) echo 'Password baru dan konfirmasinya harus sama'; ?>				
					</p>
				</div>
				<?php endif; ?>
				<?php if($sukses==1): ?>
				<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;;width:450px;margin:30px auto;"> 
					<p>User baru berhasil ditambahkan !<br />
					<a href="su_user.php">Kembali ke list user</a></p>
				</div>
				<?php else: ?>
                <?php $site = $db->get_row("SELECT * FROM `site` WHERE st_site_id='".$_GET['i']."'");?> <!--ADDED-->
						<form method="post" action="">
						<table width="80%" border="0" style="border:1px solid #ccc;margin:0px auto;margin-bottom:50px;">
							<tr class="even">
								<th>SITE ID</th>
								<td>
									<input type="radio" name="roleu" size="10" value="su" id="rsu" /><label for="rsu">Super User</label>
									<input type="radio" name="roleu" size="10" value="u" id="ru" checked="1" /><label for="ru">User</label>
								</td>
							</tr>
							<tr class="odd">
								<th>SITE NAME</th>
								<td><input name="u" type="text" value="<?= $site->st_name;//$_POST['u']?>" size="30"  /></td>
							</tr>
							<tr class="even">
								<th>NET ELEMENT ID</th>
								<td><input type="password" name="p" size="30" value="" /></td>
							</tr>
							<tr class="odd">
								<td nowrap="nowrap">SITE ELEMENT TYPE</td>
								<td><input type="password" name="kp" size="30" value="" /></td>
							</tr>
							<tr class="even">
								<th>REGION</th>
								<td><input type="text" name="n" size="40" value="<?= $site->st_region//$_POST['n']?>" /> / <input type="text" name="i" size="4" maxlength="5" value="<?=$site->kode_region?>" /></td>
							</tr>
							<tr class="odd">
								<td>SITE POSITION (longitude/ latitude)</td>
								<td>
									<?php $res = $db->get_results("SELECT * FROM region ORDER BY kode_region ASC")?>
									<select name="r">
                                         <option value="<?=$site->kode_region?>"> <?=$site->kode_region?> - <?=$site->st_region?> </option>
										<?php foreach($res as $x):?>                                      
										<option value="<?=$x->kode_region?>"> <?=$x->kode_region?> - <?=$x->region?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr class="even">
								<td>SITE ADDRESS</td>
								<td> <input name="s_adr" type="text" value="<?= $site->st_address;//$_POST['u']?>" size="30"  /></td>
								<!--	<select name="j">
										<option value="staff">Staff</option>
										<option value="spv">Supervisor</option>
										<option value="mgr">Manager</option>
										<option value="gm">G M</option>
									</select> -->
								</td>
							</tr>
							<tr class="odd">
								<td>AREA</td>
								<td><input type="text" name="po" size="50" value="<?=$_POST['po']?>" />
								<div class="keterangan">
								Contoh: SPV Site Mgnt Reg. Sumbagut, Poh Mgr NOS Reg. Sumbagteng 
								</div>
								</td>
							</tr>
							<tr class="even">
								<td>VENDOR</td>
								<td>
									#1: <input type="text" name="e1" size="30" value="<?=$_POST['e1']?>" /><br />
									#2: <input type="text" name="e2" size="30" value="<?=$_POST['e2']?>" /><br />
									#3: <input type="text" name="e3" size="30" value="<?=$_POST['e3']?>" />
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><input value="Simpan" type="submit"/></td>
							</tr>
						</table>
						
						</form>
				<?php endif; ?>									
		</td>
	</tr>
</table>
<?php include "footer.php"?>