<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

ini_set("session.gc_maxlifetime",1);
ini_set("session.gc_probability",1);
ini_set("session.gc_divisor",1); 

session_start();

# ADODB
include( "lib/adodb5/adodb.inc.php" );
$ADODB_COUNTRECS = false;
$database = "pdo";  
$db = ADONewConnection($database);
$db->Connect( "mysql:dbname=daisy", "admin", "belang@9");
// $db->debug=true;
if(!$db->IsConnected()){
	header("Location: databaseError.php"); die();
}

$months = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember');
$shortMonths = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nop','Des');
?>
