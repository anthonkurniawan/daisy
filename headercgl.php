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
		$("#tgl_kejadian_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_kejadian",
			altFormat: 	'yy-mm-dd'
		});

		$("#lokasite").change(function(){
			$('#siteDetail').load('./siteDetailCGL.php?siteId=' + $('#lokasite').val());			
		});
		
		$("#tgl_tuntutan_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#tgl_tuntutan",
			altFormat: 'yy-mm-dd'
		});
		
		$("#payment_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#payment",
			altFormat: 	'yy-mm-dd'
		});
		$("#invoice_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#invoice",
			altFormat: 	'yy-mm-dd'
		});
		$("#survey_date_show").datepicker({ 
			autoSize: true,
			dateFormat: 'DD/ d MM yy',
			altField: "#survey_date",
			altFormat: 	'yy-mm-dd'
		});
		$("#tgl_settlement_show").datepicker({ 
			autoSize: true,
			dateFormat: 'dd MM yy',
			altField: "#tgl_settlement",
			altFormat: 	'yy-mm-dd'
		});
	});
	</script>
</head>
<body>
