<?php
require 'init.php';
require 'priviledges.php';
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
		 <h2>Forum tanya jawab</h2>		
			<a href="newpost.php">Buat posting baru</a>
			<?php $posts = $db->getArray("SELECT (SELECT COUNT(1) FROM posts WHERE reply_of=p.id) AS jmlr, (SELECT id FROM posts WHERE reply_of=p.id ORDER BY created_at DESC LIMIT 1) lr,  id, title,nama,created_at FROM `posts` p JOIN USER u ON u.user_id=p.user_id WHERE p.reply_of IS NULL ORDER BY created_at DESC");
			?>
			<table width="98%" border="0" style="border:1px solid #ccc">
			<tr style="background:#ccc">
				<th>Post</th>
				<th style="width:50px">Jml Balasan</th>
				<th>Balasan terkahir</th>
			</tr>
			<?php 
			if($posts): $i=0;
			foreach($posts as $post){ ?>
			<tr class="<?=$i%2==0?'odd':'even'?>" valign="top">
				<td><strong><a href="showthread.php?p=<?=$post->id?>"><?=$post->title?></a></strong><br />
				<small>by: <?=$post->nama?>&nbsp;&nbsp;&nbsp; at: <?=date("Y-m-d H:i",strtotime($post->created_at))?> </small></td>
				<td style="text-align:center"><?=$post->jmlr?></td>
				<td>
					<?php 
					if($post->lr){ $po = $db->getRow("SELECT p.*,nama FROM posts p JOIN USER u ON u.user_id=p.user_id WHERE id='".$post->lr."'")?>
					<strong><a href="showthread.php?p=<?=$post->id?>#<?=$po->id?>"><?=$po->title?></a></strong><br />
					<small>by: <?=$po->nama?>&nbsp;&nbsp;&nbsp; at:<?=date("Y-m-d H:i",strtotime($po->created_at))?></small><br />
					<?=$po->post?>
					<?php }else{ echo '-'; } ?>
				</td>
			</tr>
			<?php $i++;} endif; ?>
			</table>
			<a href="newpost.php">Buat posting baru</a>
		</td>
	</tr>
</table>
<?php include "footer.php"?>
