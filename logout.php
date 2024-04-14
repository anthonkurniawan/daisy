<?php
require 'init.php';
unset($_SESSION['u']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="refresh" content="1;url=index.php">
	<title>Daisy</title>
	<link type="text/css" href="style.css" rel="stylesheet" />
</head>
<body>
<div style="text-align:center;margin:80px auto;border:1px solid #333;width:400px;padding:50px;">
Anda telah logout.<br />Silahkan klik <a href="index.php">link ini</a> jika anda tidak ter redirect ke halaman login
</div>
<?php include "footer.php"?>