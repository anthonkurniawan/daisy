<?php
session_start();
$user = unserialize($_SESSION['u']);
if($user==''){
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Daisy</title>
		<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.9.custom.css" rel="stylesheet" />	
	</head>
	<body>
	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
		<h1 style="text-align:center">Anda tidak berhak mengakses halaman ini!</h1>
	</div>
	</body></html>
<?php exit();
}
?>