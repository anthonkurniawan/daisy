<?php
require 'init.php';
require 'priviledges.php';

if($_POST){
	$db->query("INSERT INTO posts (user_id,title,post,created_at) VALUES 
	('".$user->user_id."','".$_POST['title']."','".$_POST['post']."',NOW())");
	header("location:forum.php");
}
include "header.php";
?>
<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
	<?php include "webheader.php" ?>
	<tr valign="top">
		<td style="width:250px">
			<ul style="list-style:none;padding-left:5px">
				<?php include "menu.php" ?>
			</ul>
		</td>
		<td style="padding-left:30px;">
		 <h2>Buat posting forum baru</h2>		
			<form method="post" action="">
				<table>
				<tr>
					<td>Title: <input type="text" size="60" name="title" /></td>
				</tr>
				<tr>
					<td>
					<textarea rows="10" cols="60" name="post"></textarea>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"><a href="forum.php">Kembali ke forum</a> <input type="submit" value="Submit post" /></td>
				</tr>
			</form>
		</td>
	</tr>
</table>
<?php include "footer.php"?>