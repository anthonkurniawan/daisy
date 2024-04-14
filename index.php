<?php
require 'init.php';
if($_POST){
	$res = $db->getRow("SELECT * FROM user WHERE UPPER(`username`)='".strtoupper($_POST['u'])."' AND password='".$_POST['p']."' AND active='1'");  //var_dump($res);
	if($res){
		$_SESSION['u'] = serialize($res);
		$_SESSION['flash'] = 'login';
		header('location:redirect.php');
	}else{
		$_SESSION['flash']='Incorrect Username / Password!';
	}
}
?>
<?php include "header.php"?>
<div id="login">
<form method="post" action=""  autocomplete="off">
<table cellpadding="0" cellspacing="5" border="0">
	
	<tr valign="top">
		<td rowspan="3"><img src="images/bts.jpg" style="height:350px; margin:0px auto;" align="middle" alt="DAISY TELKOMSEL" /></td>
		<td>
			<h2>
				DAISY Telkomsel
			</h2>
			<h3>
				(DAta Insurance SYstem)
			</h3>
			<br />
			<?php
				if($_SESSION['flash'])
				{
				?>
				<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
					<?=$_SESSION['flash']; ?></p>
				</div>
				<?php	
				unset($_SESSION['flash']);
				}
			?>
			<table>
				<tr>
					<td>Username</td>
					<td><input type="text" name="u" value="" /></td>
				</tr>
				<tr>
					<td style="text-align:right">Password</td>
					<td><input type="password" name="p" value="" /></td>
				</tr>
				<tr>
					<td></td>
					<td style="text-align:center"><input type="submit" style="width:100px" value="login" /></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
</div>
<?php include "footer.php"?>
