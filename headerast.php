<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>D A I S Y (DAta Insurance SYstem)</title>
	<link type="text/css" href="style.css" rel="stylesheet" />
	<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.9.custom.css" rel="stylesheet" />	
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>		
	<script>
	$(function() {		
		$("#lokasite").change(function(){
			$('#siteDetailAST').load('./siteDetailAST.php?siteId=' + $('#lokasite').val());			
		});
		
		$("#partial").change(function(){
			$('#partial_lost').load('./partial_lost.php?catId=' + $('#partial').val());			
		});
		
		$("#total").change(function(){
			$('#total_lost').load('./total_lost.php?catId=' + $('#total').val());			
		});
		$("#tgl_kejadian_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_kejadian",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_estimasi_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_estimasi",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_proadj1_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_proadj1",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_proadj2_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_proadj2",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_proadj3_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_proadj3",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_konadj1_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_konadj1",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_konadj2_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_konadj2",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_konadj3_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_konadj3",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_settled_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_settled",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_budget_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_budget",
			altFormat: 	'yy-mm-dd'
		});
	});
	</script>
</head>
<body>
