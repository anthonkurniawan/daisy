<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
$err = array();

if($_POST){
	if($_POST['roleu']=='') $err[]="wewenang";
	if($_POST['u']=='') $err[]="login";
	if($_POST['p']=='') $err[]="password";
	if($_POST['kp']=='') $err[]="konfirmasi password";
	if($_POST['p']<>$_POST['kp']) $err[]="password dan konfirmasi harus sama";
	if($_POST['n']=='') $err[]="nama";
	if($_POST['i']=='') $err[]="inisial";
	if($_POST['po']=='') $err[]="posisi/ jabatan lengkap";
	if($_POST['e1']=='') $err[]="Email harus diisi";
	
	if(empty($err)){	
		$db->query("INSERT INTO `user` (
		`role`,`username`,`password`,`nama`,`inisial`,`regional`,`posisi`,`email1`,`active`) VALUES 
		('".$_POST['roleu']."','".$_POST['u']."','".$_POST['p']."','".$_POST['n']."','".$_POST['i']."','".$_POST['r']."',
		'".strtoupper($_POST['po'])."','".$_POST['e1']."','1')");
		$sukses=1;
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
			<h3>DAISY USERS</h3>
			<h3>Input User Baru</h3>
				<?php if(!empty($err)){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
				<p>Mohon isi/ perbaiki data berikut:
					<ul>
					<?php foreach($err as $e):
							?>
							<li><?=ucfirst($e)?></li>
							<?
							endforeach;
					?>
					</ul>
				</p>
			</div>
			<?php } ?>
			
					<?php if($sukses==1): ?>
				<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;;width:450px;margin:30px auto;"> 
					<p>User baru berhasil ditambahkan !<br />
					<a href="su_user.php">Kembali ke list user</a></p>
				</div>
				<?php else: ?>
						<form method="post" action="">
						<table width="80%" border="0" style="border:1px solid #ccc;margin:0px auto;margin-bottom:50px;">
							<tr class="even">
								<td>Wewenang</td>
								<td>
									<select name="roleu">
										<option value="spvr">Staff/ Supervisor Regional</option>
										<option value="mgrr">Manager Regional</option>
										<option value="stfp">Staff Pusat</option>
										<option value="spvp">Supervisor Pusat</option>						
										<option value="gmp">GM Pusat</option>
									</select>
								</td>
							</tr>
							<tr class="odd">
								<td>Login</td>
								<td><input type="text" name="u" size="10" value="<?=$_POST['u']?>" /></td>
							</tr>
							<tr class="even">
								<td>Password</td>
								<td><input type="password" name="p" size="30" value="" /></td>
							</tr>
							<tr class="odd">
								<td nowrap="nowrap">Konfirmasi Password</td>
								<td><input type="password" name="kp" size="30" value="" /></td>
							</tr>
							<tr class="even">
								<td>Nama/ Inisial</td>
								<td><input type="text" name="n" size="40" value="<?=$_POST['n']?>" /> / <input type="text" name="i" size="4" maxlength="5" value="<?=$_POST['i']?>" /></td>
							</tr>
							<tr class="odd">
								<td>Regional</td>
								<td>
									<?php $res = $db->get_results("SELECT * FROM region ORDER BY kode_region ASC")?>
									<select name="r">
										<?php foreach($res as $x):?>
										<option value="<?=$x->kode_region?>"><?=$x->kode_region?> - <?=$x->region?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr class="even">
								<td>Posisi/ Jabatan lengkap</td>
								<td><input type="text" name="po" size="50" value="<?=$_POST['po']?>" />
								<div class="keterangan">
								Contoh: SPV Site Mgnt Reg. Sumbagut
								</div>
								</td>
							</tr>
							<tr class="odd" valign="top">
								<td>Email</td>
								<td>
									<input type="text" name="e1" size="70" value="<?=$_POST['e1']?>" />
									<div class="keterangan">Untuk pengiriman notifikasi</div>
									<div class="keterangan">Untuk beberapa email, pisahkan dengan tanda koma ","</div>
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