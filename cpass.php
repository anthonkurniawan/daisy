<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
if($_POST){
	if(!empty($_POST['l']) && !empty($_POST['b']) && !empty($_POST['kb'])){
		if($_POST['b']==$_POST['kb']){
			if($user->password==$_POST['l']){
				$db->query("UPDATE `user` SET `password`='".$_POST['b']."' WHERE user_id='".$user->user_id."'");
				$sukses = 1;
			}else{
				$err = 1;
			}
		}else{
			$notsame=1;
		}
	}else{
		$kosong=1;
	}
}
?>
<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
		<td style="width:250px">
			<ul style="list-style:none;padding-left:5px">
				<?php include $user->role=='spvr' || $user->role=='mgrr'?"menu.php":"menusuper.php" ?>
			</ul>
		</td>
		<td>
		<h3>Ganti password</h3>
			<?php if($kosong==1 || $notsame==1 || $error==1): ?>
			<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;;width:450px;margin:30px auto;"> 
				<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
				<?php if($kosong==1) echo 'Ada kolom yang belum diisi, silahkan coba kembali'; ?>
				<?php if($notsame==1) echo 'Password baru dan konfirmasinya harus sama, silahkan coba kembali'; ?>
				<?php if($err==1) echo 'Password saat ini salah, silahkan coba kembali'; ?>				
				</p>
			</div>
			<?php endif; ?>
			<?php if($sukses==1): ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;;width:450px;margin:30px auto;"> 
				<p>Password berhasil diganti !</p>
			</div>
			<?php endif; ?>
			
			<form method="post" action="" autocomplete="off">
				<table width="50%" cellpadding="5" cellspacing="0" style="border:1px solid #ccc;margin:0px auto;margin-bottom:50px;">
					<tr class="odd">
						<td>Password saat ini</td>
						<td><input type="password" name="l" value="" /></td>
					</tr>
					<tr class="even">
						<td>Password baru</td>
						<td><input type="password" name="b" value="" /></td>
					</tr>
					<tr class="odd">
						<td>Konfirmasi password baru</td>
						<td><input type="password" name="kb" value="" /></td>
					</tr>
					<tr class="even">
						<td>&nbsp;</td>
						<td><input type="submit" value=" Ganti password" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php include "footer.php"?>