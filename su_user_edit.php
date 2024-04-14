<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
$eu = $db->get_row("SELECT * FROM `user` WHERE `user_id`='".$_GET['edit']."'"); 
if($_POST){
	if($_POST['roleu']=='') $err[]="Wewenang";
	if($_POST['u']=='') $err[]="Username";
	if($_POST['n']=='') $err[]="Nama";
	if($_POST['i']=='') $err[]="Inisial";
	if($_POST['r']=='') $err[]="Regional";
	if($_POST['po']=='') $err[]="Posisi";
	if($_POST['e1']=='') $err[]="Email";
	
	if(empty($err)){
		if(!empty($_POST['p'])&&($_POST['p']<>$_POST['kp'])){
			$notsame = 1;
		}else{
			$db->query("UPDATE `user` SET
			`role`='".$_POST['roleu']."',
			`username`='".$_POST['u']."',
			".(empty($_POST['p'])?'':"`password`='".$_POST['p']."',")."
			`nama`='".$_POST['n']."',
			`inisial`='".$_POST['i']."',
			`regional`='".$_POST['r']."',
			`posisi`='".$_POST['po']."',
			`email1`='".$_POST['e1']."'
			WHERE `user_id`='".$_POST['id']."'");
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
			<h3>SI-LIA USERS</h3>
			<h3>Input User Baru</h3>
				<?php if(!empty($err) || $notsame){ ?>
				<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
					<p>Mohon isi/ perbaiki data berikut:
						<ul>
						<?php foreach($err as $e):
								?>
								<li><?=ucfirst($e)?></li>
								<?
								endforeach;
						?>
						<?php if($notsame==1) echo '<li>Password baru dan konfirmasinya harus sama</li>'; ?>				
						</ul>
					</p>
				</div>
				<?php } ?>
				
				<?php if($sukses==1): ?>
				<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;;width:450px;margin:30px auto;"> 
					<p>User berhasil diupdate !<br />
					<a href="su_user.php">Kembali ke list user</a></p>
				</div>
				<?php else: ?>
						<form method="post" action="">
						<input type="hidden" name="id" value="<?=$eu->user_id?>" />
						<table width="80%" border="0" style="border:1px solid #ccc;margin:0px auto;margin-bottom:50px;">
							<tr class="even">
								<td>Wewenang</td>
								<td>
									<select name="roleu">										
										<option value="spvr" <?php if($eu->role=='spvr') echo 'selected="selected"'; ?>>Staff/ Supervisor Regional</option>
										<option value="mgrr" <?php if($eu->role=='mgrr') echo 'selected="selected"'; ?>>Manager Regional</option>
										<option value="stfp" <?php if($eu->role=='stfp') echo 'selected="selected"'; ?>>Staff Pusat</option>
										<option value="spvp" <?php if($eu->role=='spvp') echo 'selected="selected"'; ?>>Supervisor Pusat</option>
										<option value="gmp" <?php if($eu->role=='gmp') echo 'selected="selected"'; ?>>GM Pusat</option>																				
									</select>
								</td>
							</tr>
							<tr class="odd">
								<td>Username</td>
								<td><input type="text" name="u" size="10" value="<?=$eu->username ?>" /></td>
							</tr>
							<tr class="even">
								<td>Password
								</td>
								<td><input type="password" name="p" size="30" value="" /><div class="keterangan">Diisi jika ingin mengganti password. Kosongkan untuk tidak merubah password.</div>
								</td>
							</tr>
							<tr class="odd">
								<td nowrap="nowrap">Konfirmasi Password</td>
								<td><input type="password" name="kp" size="30" value="" /></td>
							</tr>
							<tr class="even">
								<td>Nama/ Inisial</td>
								<td><input type="text" name="n" size="40" value="<?=$eu->nama ?>" /> / <input type="text" name="i" size="4" maxlength="5" value="<?=$eu->inisial ?>" /></td>
							</tr>
							<tr class="odd">
								<td>Regional</td>
								<td>
									<?php $res = $db->get_results("SELECT * FROM region ORDER BY kode_region ASC")?>
									<select name="r">
										<?php foreach($res as $x):?>
										<option <?php if($eu->regional==$x->kode_region) echo 'selected="selected"'; ?>value="<?=$x->kode_region?>"><?=$x->kode_region?> - <?=$x->region?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>							
							<tr class="odd">
								<td>Posisi</td>
								<td><input type="text" name="po" size="50" value="<?=$eu->posisi?>" />
								<div class="keterangan">
								Contoh: SPV Site Mgnt Reg. Sumbagut, Poh Mgr NOS Reg. Sumbagteng 
								</div>
								</td>
							</tr>
							<tr class="even">
								<td>Email</td>
								<td>
									<input type="text" name="e1" size="70" value="<?=$eu->email1?>" />
									<div class="keterangan">Untuk beberapa email, pisahkan dengan tanda koma ","</div>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><input value="Simpan" type="submit"/> <a href="su_user.php">Kembali ke list user &raquo;</a></td>
							</tr>
						</table>
						
						</form>
				<?php endif; ?>									
		</td>
	</tr>
</table>
<?php include "footer.php"?>