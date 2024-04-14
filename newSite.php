<?php
require 'init.php';
require 'priviledges.php';

$err = array();
$region = $db->get_row("SELECT region from region WHERE kode_region='{$user->regional}'");
if($_POST){
	if($_POST['sid']=='') $err[]="Site ID";
	if($_POST['sname']=='') $err[]="site Name";
	if($_POST['address']=='') $err[]="alamat";
 
 if(empty($err))
 {		
		$db->query("INSERT INTO `site` (
		`st_site_id`, `st_name`,  `kode_region`,  `st_region`,  `st_longitude`,  `st_latitude`, `st_address`) VALUES 
		('".$_POST['sid']."','".$_POST['sname']."','".$user->region."','".$region->region."','".$_POST['longi']."','".$_POST['lat']."',
		'".$_POST['address']."')");
		$sukses=1;
	}
}
include "header.php";
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto;" id="badan">
	<tr valign="top">
		<td>
			<?php if(!empty($err)){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
				<p>Mohon isi/ perbaiki data berikut:
					<ul>
					<?php foreach($err as $e):
							?>
							<li><?=ucfirst($e)?></li>
							<?php
							endforeach;
					?>
					</ul>
				</p>
			</div>
			<?php } ?>
				<?php if($sukses==1): ?>
				<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;;width:450px;margin:30px auto;"> 
					<p>Site berhasil ditambahkan !<br />
					<a href="javascript:void(0)" onclick="window.opener.location.reload(true);window.close()">Kembali ke buat laporan CGL</a></p>
				</div>
				<?php else: ?>
						<form method="post" action="">
						<table width="80%" border="0" style="border:1px solid #ccc;margin:0px auto;margin-bottom:50px;" cellpadding="3" cellspacing="0">
							<tr class="odd">
								<td colspan="2"><h3 style="margin:0px;">Input Site Baru di regional <?=$region->region?></h3></td>
							</tr>
							<tr class="even">
								<td>SITE ID</td>
								<td>
									<input type="text" name="sid" size="10" /> *
								</td>
							</tr>
							<tr class="odd">
								<td>Nama Site</td>
								<td><input type="text" name="sname" size="30" value="<?=$_POST['sname']?>" /> *</td>
							</tr>
							<tr class="odd">
								<td nowrap="nowrap">Posisi Site<br />(Longitude/ Latitude)</td>
								<td nowrap="nowrap">
									<input type="hidden" name="r" value="<?=$user->regional?>" />
									<input type="text" name="longi" size="20" value="<?=$_POST['longi']?>" /> / <input type="text" name="lat" size="20" value="<?=$_POST['lat']?>" />
								</td>
							</tr>
							<tr class="even">
								<td nowrap="nowrap">Alamat Site</td>
								<td>
									<textarea name="address" cols="30"><?=$_POST['address']?></textarea> * 									
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