<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
if($_GET['delete']){
$eu = $db->get_row("SELECT * FROM `user` WHERE `user_id`='".$_GET['delete']."'"); 
$resdel=1;
}
if($_GET['delok']){
$eu = $db->get_row("UPDATE `user` SET `active`='0' WHERE `user_id`='".$_GET['delok']."'"); 
unset($resdel);
$delok=1;
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
			<h3>DAISY USERS</h3>
			<a href="su_user_add.php">+ Tambah User Baru</a>
			<?php
				if($resdel)
				{
				?>
				<div class="ui-state-highlight ui-corner-all" style="width:80%;margin:0px auto;padding: 0 .7em;"> 
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
Anda akan menghapus data user: <strong>"<?=$eu->username?>"</strong>
<br />
Role: <?=$eu->role=='SU'?'Super User':'User'?><br />
Nama: <?=$eu->nama?>
<div style="text-align:right">
<a href="?delok=<?=$eu->user_id?>" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">OK</a>
<a href="?" class="ui-state-default ui-corner-all" style="color:#5273cd;padding:1px">Cancel</a>
</div>	</p>
				</div>
				<?php } ?>			
				<table width="98%" class="tabel" cellspacing="0" cellpadding="0">
					<tr>
						<th>Role</th>
						<th>Username</th>
						<th>Nama</th>
						<th>Regional</th>
						<th>Posisi</th>
						<th>Email</th>						
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>		
			<?php
				$res= $db->get_results("SELECT * FROM `user` WHERE `active`='1' ORDER BY `user_id` ASC");
				if(is_array($res)&&!empty($res))
				{?>
				<?php	$i=0;foreach($res as $r){ ?>
				<tr class="<?=($i%2==0?'even':'odd')?>">					
					<td><?php switch($r->role){
						case 'spvr':echo 'Spv Reg';break;
						case 'mgrr':echo 'Mgr Reg';break;
						case 'stfp':echo 'Staff Pusat';break;
						case 'spvp':echo 'SPV Pusat';break;
						case 'gmp':echo 'Mgr/ GM Pusat';break;
					}?></td>
					<td><?=$r->username?></td>
					<td><?=$r->nama?></td>
					<td><?=$r->regional?></td>
					<td><?=$r->posisi?></td>
					<td title="<?=$r->email1?>"><?=$r->email1!=''?'Ada':'-'?></td>
					<td><a href="su_user_edit.php?edit=<?=$r->user_id?>">Edit</a></td>
					<td><a href="?delete=<?=$r->user_id?>">Delete</a></td>
					</tr>
					<?php $i++;} ?>
				<?php
				}
			?>	
				</table>						
		</td>
	</tr>
</table>
<?php include "footer.php"?>