<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
if($_POST){
	$db->query("INSERT INTO posts (reply_of,user_id,title,post,created_at) VALUES 
	('".$_POST['reply']."','".$user->user_id."','".$_POST['title']."','".$_POST['post']."',NOW())");
}
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
			<?php
				$p = $db->get_row("SELECT * FROM `posts` p JOIN USER u ON u.user_id=p.user_id WHERE id='".$_GET['p']."'");
			?>
		<a href="forum.php">&laquo; Kembali ke forum</a> 
		 <h3><?=$p->title?></h3>	
		 by: <?=$p->nama?>&nbsp;&nbsp;&nbsp; at: <?=date("Y-m-d H:i",strtotime($p->created_at))?>
		 <hr />
		<?=$p->post?>
		<?php
				$replies = $db->get_results("SELECT * FROM `posts` p JOIN USER u ON u.user_id=p.user_id WHERE reply_of='".$_GET['p']."'");
		?>
		<table width="98%">
			<?php if($replies): $i=0;foreach($replies as $rep): ?>
			<tr class="<?=$i%2==0?'odd':'even'?>">
				<td>
					<strong><?=$rep->title?></strong> 
					<small>by: <?=$rep->nama?>&nbsp;&nbsp;&nbsp; at: <?=date("Y-m-d H:i",strtotime($rep->created_at))?> </small>
					<br />
					<?=$rep->post?>
				</td>
			</tr>
			<?php $i++;endforeach;endif; ?>
		</table>
		<hr />
			<form method="post" action="">
				<fieldset>
				<legend>Reply</legend>
				<table>
				<tr>
					<td>Title: <input type="text" size="60" name="title" /><input type="hidden" name="reply" value="<?=$p->id?>" /></td>
				</tr>
				<tr>
					<td>
					<textarea rows="5" cols="60" name="post"></textarea>
					</td>
				</tr>
				<tr>
					<td style="text-align:right"> <input type="submit" value="Submit post" /></td>
				</tr>
				</table>
				</fieldset>
			</form>
			<a href="forum.php">&laquo; Kembali ke forum</a> 
		</td>
	</tr>
</table>
<?php include "footer.php"?>