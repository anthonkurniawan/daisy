<?php
require 'init.php';
require 'priviledges.php';

//if($_SESSION['gets']['p']=='1') 
//if($_GET['p']=='1') 
if($_SESSION['gets']['p']=='1') 
					$and .=" AND sebab<>'etv' AND tgl_kejadian BETWEEN '".$_SESSION['gets']['t1']."-".str_pad($_SESSION['gets']['m1'],2,'0',STR_PAD_LEFT)."-01 00:00:00' AND '".$_SESSION['gets']['t2']."-".str_pad($_SESSION['gets']['m2'],2,'0',STR_PAD_LEFT)."-31 23:59:59'";
					//$and .=" AND created_at BETWEEN '".($_GET['t1']-1)."-12-01 00:00:00' AND '".$_GET['t2']."-06-31 23:59:59'";
				else
					$and .=" AND sebab='etv' AND tgl_kejadian BETWEEN '".$_SESSION['gets']['t1']."-".str_pad($_SESSION['gets']['m1'],2,'0',STR_PAD_LEFT)."-02 00:00:00' AND '".$_SESSION['gets']['t2']."-".str_pad($_SESSION['gets']['m2'],2,'0',STR_PAD_LEFT)."-31 23:59:59'";
					
if($_SESSION['gets']['k']<>''){$and.= " AND status_progress='".$_SESSION['gets']['k']."'";} 

if($_SESSION['gets']['r']<>'')
				{
					$and.= " AND kode_region='".$_SESSION['gets']['r']."'";
					$rx = $db->get_row("SELECT * FROM region WHERE kode_region='".$_SESSION['gets']['r']."'");
					$status.= " Regional <strong>".$rx->region."</strong>";
				}
if($_SESSION['gets']['cod']<>''){$and.= " AND sebab='".$_SESSION['gets']['cod']."'";} 
				
switch($_SESSION['gets']['s1']){
			case '1':$order.=" st_site_id ASC,";break;
			//case '2':$order.=" region ASC ASC,";break;
			//case '3':$order.=" submit_at DESC,";break;
			case '4':$order.=" tgl_kejadian DESC,";break;
			case '5':$order.=" sebab DESC,";break;  // ------- EDITED------- FROM TGL_TUNTUTAN
			case '6':$order.=" status ASC,";break;
			}

				$SQL1 = "SELECT *,
					(if(status='UNAPPROVED',1,
						if(status='APPROVED',2,
							if(status='SUBMITTED',3,
								if(status='SURVEY',4,
									if(status='PAYMENT',5,
										if(status='INVOICE',6,
											if(status='SETTLED',7,
												if(status='CLOSED',8,0)
											)
										)
									)
								)
							)
						)
					)
				) as st FROM `ast2` 
        
				 WHERE 1 ".$and." ORDER BY {$order} ast_id ASC";
//JOIN ast_detail2 v ON v.no_laporan = ast2.no_laporan         
         
//echo $SQL1;
if($_SESSION['gets']['l']!='3') $ast = $db->get_results($SQL1);

 if($ast){
//$db->debug();
require_once 'PHPExcel.php';
	
if($_SESSION['gets']['l']=='1'){	

	if($_SESSION['gets']['r']<>''){
		$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_SESSION['gets']['r']}'");
		$regional =$reg->region;
	}else $regional ='NASIONAL';
 
	$objPHPExcel = new PHPExcel();
	$filename = $user->inisial.'-REKAPITULASI KLAIM AST '.$_SESSION['gets']['r'].'.'.$_SESSION['gets']['m1'].' '.$_SESSION['gets']['t1'].'-'.$_SESSION['gets']['m2'].' '.$_SESSION['gets']['t2'];
	
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'REKAPITULASI KLAIM AST PT TELKOMSEL')
            ->setCellValue('A2', "REGIONAL: {$regional}")
			->setCellValue('A3', "NO Polis: ".($_SESSION['gets']['p']=='1'?'All Risk[202.204.300.10.00017]':'Earthquake[202.203.300.10.00032]')."")			
			->setCellValue('A5', "No.")
			->setCellValue('B5', "Nomor Laporan")
			->setCellValue('C5', "Regional")
			->setCellValue('D5', "Site Name")
			->setCellValue('E5', "Site ID")
			->setCellValue('F5', "PIC Regional")
			->setCellValue('G5', "Tanggal")
			->setCellValue('G7', "Kejadian")
			->setCellValue('H7', "Lapor HO")
      ->setCellValue('I7', "Lapor SJU")
      ->setCellValue('J5', "Penyebab Kerugian")
      ->setCellValue('K5', "Deductible")
      ->setCellValue('L5', "Aset Tetap")
      ->setCellValue('L7', "Kategori1")
      ->setCellValue('M7', "Kategori2")
      ->setCellValue('N7', "Kategori3")
      ->setCellValue('O7', "Kategori4(Item)")
      ->setCellValue('P7', "Kategori5(Merk)")
      ->setCellValue('Q7', "Kategori6(Type)")
      ->setCellValue('R7', "Kategori7")
      ->setCellValue('S5', "Quantity")
      ->setCellValue('T5', "Satuan")
      ->setCellValue('U5', "Or.curr")
      ->setCellValue('U7', "IDR")
      ->setCellValue('V7', "USD")
      ->setCellValue('W7', "EUR")
      ->setCellValue('X5', "Rate IDR")
      ->setCellValue('Y5', "Total Amount")
      ->setCellValue('Z5', "Dokumen")
      ->setCellValue('Z7', "Surat Tuntutan")
      ->setCellValue('AA7', "Lap awal")
      ->setCellValue('AB7', "BA Kehilangan")
      ->setCellValue('AC7', "BA Kronologi")
      ->setCellValue('AD7', "Rincian Kerugian")
      ->setCellValue('AE7', "BA Kepolisian")
      ->setCellValue('AF7', "Surat PMK")
      ->setCellValue('AG7', "Surat BMKG")
      ->setCellValue('AH7', "Foto")
      ->setCellValue('AI7', "PO")
      ->setCellValue('AJ5', "Proposed Adjustment 1")
      ->setCellValue('AJ7', "Tanggal")
      ->setCellValue('AK7', "Amount")
      ->setCellValue('AL5', "Konfirmasi Prop Ajustment 1")
      ->setCellValue('AL7', "Tanggal")
      ->setCellValue('AM7', "Amount")
      
      ->setCellValue('AN5', "Proposed Adjustment 2")
      ->setCellValue('AN7', "Tanggal")
      ->setCellValue('AO7', "Amount")
      ->setCellValue('AP5', "Konfirmasi Prop Ajustment 2")
      ->setCellValue('AP7', "Tanggal")
      ->setCellValue('AQ7', "Amount")
      
      ->setCellValue('AR5', "Proposed Adjustment 3")
      ->setCellValue('AR7', "Tanggal")
      ->setCellValue('AS7', "Amount")
      ->setCellValue('AT5', "Konfirmasi Prop Ajustment 3")
      ->setCellValue('AT7', "Tanggal")
      ->setCellValue('AU7', "Amount")
      
      ->setCellValue('AV5', "Status Klaim")
      ->setCellValue('AV6', "Under Deductible")
      ->setCellValue('AW6', "Outstanding")
      ->setCellValue('AX6', "Setlement")
      ->setCellValue('AX7', "Tanggal")
      ->setCellValue('AY7', "Amount")
      
      ->MergeCells('A1:D1')
      ->MergeCells('A5:A7')
      ->MergeCells('B5:B7')
      ->MergeCells('C5:C7')
      ->MergeCells('D5:D7')
      ->MergeCells('E5:E7')
      ->MergeCells('F5:F7')
      ->MergeCells('G5:I6')
      ->MergeCells('J5:J7')
      ->MergeCells('K5:K7')
      ->MergeCells('L5:R6')
      ->MergeCells('S5:S7')
      ->MergeCells('T5:T7')
      ->MergeCells('U5:W6')
      ->MergeCells('X5:X7')
      ->MergeCells('Y5:Y7')
      ->MergeCells('Z5:AI6')
      ->MergeCells('AJ5:AK6')
      ->MergeCells('AL5:AM6')
      
      ->MergeCells('AN5:AO6')
      ->MergeCells('AP5:AQ6')
      
      ->MergeCells('AR5:AS6')
      ->MergeCells('AT5:AU6')
      
      ->MergeCells('AV5:AY5')
      ->MergeCells('AV6:AV7')
      ->MergeCells('AW6:AW7')
      ->MergeCells('AX6:AY6');
			
	$objPHPExcel->getActiveSheet()->getStyle('A5:AY5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A7:AY7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A5:A7')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A5:A7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('B5:B7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('C5:C7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('D5:D7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('E5:E7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('F5:F7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('I5:I7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('J5:J7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('K5:K7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('R5:R7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('S5:S7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('T5:T7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('W5:W7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('X5:X7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('Y5:Y7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AI5:AI7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AK5:AK7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AM5:AM7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AQ5:AQ7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('G7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('H7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('L7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('M7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('N7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('O7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('P7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('Q7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('R7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('U7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('V7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  $objPHPExcel->getActiveSheet()->getStyle('Z7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AA7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AB7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AC7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AD7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AE7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AF7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AG7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AH7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AJ7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AL7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  $objPHPExcel->getActiveSheet()->getStyle('AM7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AN7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AO5:AO7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AP7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AQ5:AQ7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AR7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AS5:AS7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AT7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AU5:AU7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  
  $objPHPExcel->getActiveSheet()->getStyle('AV6:AV7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AW6:AW7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AY5:AY7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  $objPHPExcel->getActiveSheet()->getStyle('G6:I6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('L6:R6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('U6:W6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('Z5:A16')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AJ6:AU6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AV5:AY5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AX6:AY6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  
	$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
	$objPHPExcel->getActiveSheet()->getStyle('G5:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  
  

    //$objPHPExcel->mergeCells('G5:H5');

			
	$i=1;
	$row=8;  
	foreach($ast as $c):
    $ast_detail=$db->get_results ("SELECT * from ast_detail2  where no_laporan='".$c->no_laporan."';");
		//join category n on n.item1=ast_detail2.item1
    if ($c->sebab=="nds") {$sebab="Natural Dissaster (Bencana Alam)";}
    elseif ($c->sebab=="rio") {$sebab="Riots/ Strikes, Malicious Damage (Kerusuhan)";}
		elseif ($c->sebab=="thf") {$sebab="Theft (Pencurian)";}
		elseif ($c->sebab=="lit") {$sebab="Lightning (Petir)";}
		elseif ($c->sebab=="etv") {$sebab="Earthquake, Tsunami, Volcano Erruption";}
	  elseif ($c->sebab=="fre") {$sebab="Fire (Terbakar/ Kebakaran)";}
		elseif ($c->sebab=="3p") {$sebab="Third Party (Tuntutan Pihak ketiga)";}
		else {$sebab="Other Losses (Lainnya..)";}	
	 
    			
   foreach($ast_detail as $x):
    $cat=$db->get_row ("SELECT * from category  where item1='".$x->item1."';");
     
		$objPHPExcel->setActiveSheetIndex(0) 
		->setCellValue('A'.$row, $i.'.')
		->setCellValue('B'.$row, $c->no_laporan)
		->setCellValue('C'.$row, $c->region)
		->setCellValue('D'.$row, $c->st_name)
		->setCellValue('E'.$row, $c->st_site_id)
		->setCellValue('F'.$row, $c->pic_region)
		->setCellValue('G'.$row, ($c->tgl_kejadian<>''&&$c->tgl_kejadian<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_kejadian)):'-'))
		->setCellValue('H'.$row, $c->approve_at<>''&&$c->approve_at<>'0000-00-00'?date("d/m/Y",strtotime($c->approve_at)):'-')
		->setCellValue('I'.$row, $c->submit_at<>''&&$c->submit_at<>'0000-00-00'?date("d/m/Y",strtotime($c->submit_at)):'-')
    ->setCellValue('J'.$row, $sebab)
    ->setCellValue('K'.$row, number_format($c->deduct));
    

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('L'.$row,$cat->group_name)
    ->setCellValue('M'.$row,$cat->sub_cat1)
    ->setCellValue('N'.$row,$cat->sub_cat2)
    ->setCellValue('O'.$row,$x->item1)
    ->setCellValue('P'.$row,$x->merk)
    ->setCellValue('Q'.$row,$x->type)
    ->setCellValue('R'.$row,'-')
    ->setCellValue('S'.$row,$x->quantity)
    ->setCellValue('T'.$row,$x->satuan)
    ->setCellValue('U'.$row,$x->currency!='idr'?'-': ($x->currency ) )
    ->setCellValue('V'.$row,$x->currency!='usd'?'-': ($x->currency ) )
    ->setCellValue('W'.$row,$x->currency!='eur'?'-': ($x->currency ) )
    ->setCellValue('X'.$row, '' ) //rate IDR
    ->setCellValue('Y'.$row, number_format($c->estimasi) )
    ->setCellValue('Z'.$row, $c->doc_tun=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_tun ) )
    ->setCellValue('AA'.$row, $c->doc_lap=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_lap ) )
    ->setCellValue('AB'.$row, $c->doc_hil=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_hil ) )
    ->setCellValue('AC'.$row, $c->doc_kro=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_kro ) )
    ->setCellValue('AD'.$row, $c->doc_rinci=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_rinci ) )
    ->setCellValue('AE'.$row, $c->doc_pol=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_pol ) )
    ->setCellValue('AF'.$row, $c->doc_pmk=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_pmk ) )
    ->setCellValue('AG'.$row, $c->doc_bmkg=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_bmkg ) )
    ->setCellValue('AH'.$row, $c->doc_fo=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_fo ) )
    ->setCellValue('AI'.$row, $c->doc_po=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_po ) )
    
    ->setCellValue('AJ'.$row, $c->tgl_proadj1=='0000-00-00'&&'NULL'?'-': ($c->tgl_proadj1 ) )
    ->setCellValue('AK'.$row, number_format($c->pro_adj1))
    ->setCellValue('AL'.$row, $c->tgl_konadj1=='0000-00-00'&&'NULL'?'-': ($c->tgl_konadj1 ) )
    ->setCellValue('AM'.$row, number_format($c->kon_adj1))
    
    ->setCellValue('AN'.$row, $c->tgl_proadj2=='0000-00-00'&&'NULL'?'-': ($c->tgl_proadj2 ) )
    ->setCellValue('AO'.$row, number_format($c->pro_adj2))
    ->setCellValue('AP'.$row, $c->tgl_konadj2=='0000-00-00'&&'NULL'?'-': ($c->tgl_konadj2 ) )
    ->setCellValue('AQ'.$row,number_format($c->kon_adj2))
    
    ->setCellValue('AR'.$row, $c->tgl_proadj3=='0000-00-00'&&'NULL'?'-': ($c->tgl_proadj3 ) )
    ->setCellValue('AS'.$row, number_format($c->pro_adj3))
    ->setCellValue('AT'.$row, $c->tgl_konadj3=='0000-00-00'&&'NULL'?'-': ($c->tgl_konadj3 ) )
    ->setCellValue('AU'.$row, number_format($c->kon_adj3))
    
    ->setCellValue('AV'.$row, $c->estimasi <= $c->deduct && $c->status_progress=='2' ? 'Ya' : 'Tidak' )  
    ->setCellValue('AW'.$row, $c->estimasi >= $c->deduct && $c->status_progress=='1' ? 'Ya' : 'Tidak' )
    ->setCellValue('AX'.$row, $c->tgl_settled=='0000-00-00'&&'NULL'?'-': ($c->tgl_settled ) )
    ->setCellValue('AY'.$row, number_format($c->settled))    ;
    
    
     $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':'.'AQ'.$row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
		$i++;
		$row++;
	endforeach;
  
  endforeach;     
 
  
	$objPHPExcel->getActiveSheet()->setTitle('REKAPITULASI KLAIM AST ');
	$objPHPExcel->setActiveSheetIndex(0);

	
	if($_GET['f']='xlsx'){
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');		
	}else{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');		
	}
	$objWriter->save('php://output');	
	exit;
}

}
else {echo "Tidak Ada laporan";}

//unset($_SESSION['gets']);
//#2
if($_SESSION['gets']['l']=='2'){ 
 if($ast){
	$objPHPExcel = new PHPExcel();
	$filename = $user->inisial.'-DETAIL PROGRESS AST '.$_SESSION['gets']['r'].'.'.$_SESSION['gets']['m1'].' '.$_SESSION['gets']['t1'].'-'.$_SESSION['gets']['m2'].' '.$_SESSION['gets']['t2'];
	
	if($_SESSION['gets']['r']<>''){
		$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_SESSION['gets']['r']}'");
		$regional =$reg->region;
	}else $regional ='NASIONAL';
	
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'REKAPITULASI KLAIM AST PT TELKOMSEL')
            ->setCellValue('A2', "REGIONAL: {$regional}")
			->setCellValue('A3', "NO Polis: ".($_SESSION['gets']['p']=='1'?'All Risk[202.204.300.10.00017]':'Earthquake[202.203.300.10.00032]')."")			
			->setCellValue('A5', "No.")
			->setCellValue('B5', "Nomor Laporan")
			->setCellValue('C5', "Regional")
			->setCellValue('D5', "Site Name")
			->setCellValue('E5', "Site ID")
			->setCellValue('F5', "PIC Regional")
			->setCellValue('G5', "Tanggal")
			->setCellValue('G7', "Kejadian")
			->setCellValue('H7', "Lapor HO")
      ->setCellValue('I7', "Lapor SJU")
      ->setCellValue('J5', "Penyebab Kerugian")
      ->setCellValue('K5', "Deductible")
      ->setCellValue('L5', "Aset Tetap")
      ->setCellValue('L7', "Kategori1")
      ->setCellValue('M7', "Kategori2")
      ->setCellValue('N7', "Kategori3")
      ->setCellValue('O7', "Kategori4(Item)")
      ->setCellValue('P7', "Kategori5(Merk)")
      ->setCellValue('Q7', "Kategori6(Type)")
      ->setCellValue('R7', "Kategori7")
      ->setCellValue('S5', "Quantity")
      ->setCellValue('T5', "Satuan")
      ->setCellValue('U5', "Or.curr")
      ->setCellValue('U7', "IDR")
      ->setCellValue('V7', "USD")
      ->setCellValue('W7', "EUR")
      ->setCellValue('X5', "Rate IDR")
      ->setCellValue('Y5', "Total Amount")
      ->setCellValue('Z5', "Dokumen")
      ->setCellValue('Z7', "Surat Tuntutan")
      ->setCellValue('AA7', "Lap awal")
      ->setCellValue('AB7', "BA Kehilangan")
      ->setCellValue('AC7', "BA Kronologi")
      ->setCellValue('AD7', "Rincian Kerugian")
      ->setCellValue('AE7', "BA Kepolisian")
      ->setCellValue('AF7', "Surat PMK")
      ->setCellValue('AG7', "Surat BMKG")
      ->setCellValue('AH7', "Foto")
      ->setCellValue('AI7', "PO")
      ->setCellValue('AJ5', "Proposed Adjustment 1")
      ->setCellValue('AJ7', "Tanggal")
      ->setCellValue('AK7', "Amount")
      ->setCellValue('AL5', "Konfirmasi Prop Ajustment 1")
      ->setCellValue('AL7', "Tanggal")
      ->setCellValue('AM7', "Amount")
      
      ->setCellValue('AN5', "Proposed Adjustment 2")
      ->setCellValue('AN7', "Tanggal")
      ->setCellValue('AO7', "Amount")
      ->setCellValue('AP5', "Konfirmasi Prop Ajustment 2")
      ->setCellValue('AP7', "Tanggal")
      ->setCellValue('AQ7', "Amount")
      
      ->setCellValue('AR5', "Proposed Adjustment 3")
      ->setCellValue('AR7', "Tanggal")
      ->setCellValue('AS7', "Amount")
      ->setCellValue('AT5', "Konfirmasi Prop Ajustment 3")
      ->setCellValue('AT7', "Tanggal")
      ->setCellValue('AU7', "Amount")
      
      ->setCellValue('AV5', "Status Klaim")
      ->setCellValue('AV6', "Under Deductible")
      ->setCellValue('AW6', "Outstanding")
      ->setCellValue('AX6', "Setlement")
      ->setCellValue('AX7', "Tanggal")
      ->setCellValue('AY7', "Amount")
      
      ->MergeCells('A1:D1')
      ->MergeCells('A5:A7')
      ->MergeCells('B5:B7')
      ->MergeCells('C5:C7')
      ->MergeCells('D5:D7')
      ->MergeCells('E5:E7')
      ->MergeCells('F5:F7')
      ->MergeCells('G5:I6')
      ->MergeCells('J5:J7')
      ->MergeCells('K5:K7')
      ->MergeCells('L5:R6')
      ->MergeCells('S5:S7')
      ->MergeCells('T5:T7')
      ->MergeCells('U5:W6')
      ->MergeCells('X5:X7')
      ->MergeCells('Y5:Y7')
      ->MergeCells('Z5:AI6')
      ->MergeCells('AJ5:AK6')
      ->MergeCells('AL5:AM6')
      
      ->MergeCells('AN5:AO6')
      ->MergeCells('AP5:AQ6')
      
      ->MergeCells('AR5:AS6')
      ->MergeCells('AT5:AU6')
      
      ->MergeCells('AV5:AY5')
      ->MergeCells('AV6:AV7')
      ->MergeCells('AW6:AW7')
      ->MergeCells('AX6:AY6');
			
	$objPHPExcel->getActiveSheet()->getStyle('A5:AY5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A7:AY7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A5:A7')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A5:A7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('B5:B7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('C5:C7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('D5:D7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('E5:E7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('F5:F7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('I5:I7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('J5:J7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('K5:K7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('R5:R7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('S5:S7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('T5:T7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('W5:W7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('X5:X7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('Y5:Y7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AI5:AI7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AK5:AK7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AM5:AM7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AQ5:AQ7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('G7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('H7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('L7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('M7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('N7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('O7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('P7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('Q7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('R7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('U7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('V7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  $objPHPExcel->getActiveSheet()->getStyle('Z7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AA7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AB7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AC7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AD7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AE7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AF7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AG7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AH7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AJ7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AL7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  $objPHPExcel->getActiveSheet()->getStyle('AM7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AN7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AO5:AO7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AP7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AQ5:AQ7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AR7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AS5:AS7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AT7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AU5:AU7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  
  $objPHPExcel->getActiveSheet()->getStyle('AV6:AV7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AW6:AW7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AY5:AY7')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  $objPHPExcel->getActiveSheet()->getStyle('G6:I6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('L6:R6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('U6:W6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('Z5:A16')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AJ6:AU6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AV5:AY5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AX6:AY6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
  
	$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
	$objPHPExcel->getActiveSheet()->getStyle('G5:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  
  

    //$objPHPExcel->mergeCells('G5:H5');

			
	$i=1;
	$row=8;  
	foreach($ast as $c):
    $ast_detail=$db->get_results ("SELECT * from ast_detail2  where no_laporan='".$c->no_laporan."';");
		//join category n on n.item1=ast_detail2.item1
    if ($c->sebab=="nds") {$sebab="Natural Dissaster (Bencana Alam)";}
    elseif ($c->sebab=="rio") {$sebab="Riots/ Strikes, Malicious Damage (Kerusuhan)";}
		elseif ($c->sebab=="thf") {$sebab="Theft (Pencurian)";}
		elseif ($c->sebab=="lit") {$sebab="Lightning (Petir)";}
		elseif ($c->sebab=="etv") {$sebab="Earthquake, Tsunami, Volcano Erruption";}
	  elseif ($c->sebab=="fre") {$sebab="Fire (Terbakar/ Kebakaran)";}
		elseif ($c->sebab=="3p") {$sebab="Third Party (Tuntutan Pihak ketiga)";}
		else {$sebab="Other Losses (Lainnya..)";}	
	 
    			
   foreach($ast_detail as $x):
    $cat=$db->get_row ("SELECT * from category  where item1='".$x->item1."';");
     
		$objPHPExcel->setActiveSheetIndex(0) 
		->setCellValue('A'.$row, $i.'.')
		->setCellValue('B'.$row, $c->no_laporan)
		->setCellValue('C'.$row, $c->region)
		->setCellValue('D'.$row, $c->st_name)
		->setCellValue('E'.$row, $c->st_site_id)
		->setCellValue('F'.$row, $c->pic_region)
		->setCellValue('G'.$row, ($c->tgl_kejadian<>''&&$c->tgl_kejadian<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_kejadian)):'-'))
		->setCellValue('H'.$row, $c->approve_at<>''&&$c->approve_at<>'0000-00-00'?date("d/m/Y",strtotime($c->approve_at)):'-')
		->setCellValue('I'.$row, $c->submit_at<>''&&$c->submit_at<>'0000-00-00'?date("d/m/Y",strtotime($c->submit_at)):'-')
    ->setCellValue('J'.$row, $sebab)
    ->setCellValue('K'.$row, number_format($c->deduct));
    

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('L'.$row,$cat->group_name)
    ->setCellValue('M'.$row,$cat->sub_cat1)
    ->setCellValue('N'.$row,$cat->sub_cat2)
    ->setCellValue('O'.$row,$x->item1)
    ->setCellValue('P'.$row,$x->merk)
    ->setCellValue('Q'.$row,$x->type)
    ->setCellValue('R'.$row,'-')
    ->setCellValue('S'.$row,$x->quantity)
    ->setCellValue('T'.$row,$x->satuan)
    ->setCellValue('U'.$row,$x->currency!='idr'?'-': ($x->currency ) )
    ->setCellValue('V'.$row,$x->currency!='usd'?'-': ($x->currency ) )
    ->setCellValue('W'.$row,$x->currency!='eur'?'-': ($x->currency ) )
    ->setCellValue('X'.$row, '' ) //rate IDR
    ->setCellValue('Y'.$row, number_format($c->estimasi) )
    ->setCellValue('Z'.$row, $c->doc_tun=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_tun ) )
    ->setCellValue('AA'.$row, $c->doc_lap=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_lap ) )
    ->setCellValue('AB'.$row, $c->doc_hil=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_hil ) )
    ->setCellValue('AC'.$row, $c->doc_kro=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_kro ) )
    ->setCellValue('AD'.$row, $c->doc_rinci=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_rinci ) )
    ->setCellValue('AE'.$row, $c->doc_pol=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_pol ) )
    ->setCellValue('AF'.$row, $c->doc_pmk=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_pmk ) )
    ->setCellValue('AG'.$row, $c->doc_bmkg=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_bmkg ) )
    ->setCellValue('AH'.$row, $c->doc_fo=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_fo ) )
    ->setCellValue('AI'.$row, $c->doc_po=='0000-00-00'&&'NULL'?'Tidak Ada': ($c->doc_po ) )
    
    ->setCellValue('AJ'.$row, $c->tgl_proadj1=='0000-00-00'&&'NULL'?'-': ($c->tgl_proadj1 ) )
    ->setCellValue('AK'.$row, number_format($c->pro_adj1))
    ->setCellValue('AL'.$row, $c->tgl_konadj1=='0000-00-00'&&'NULL'?'-': ($c->tgl_konadj1 ) )
    ->setCellValue('AM'.$row, number_format($c->kon_adj1))
    
    ->setCellValue('AN'.$row, $c->tgl_proadj2=='0000-00-00'&&'NULL'?'-': ($c->tgl_proadj2 ) )
    ->setCellValue('AO'.$row, number_format($c->pro_adj2))
    ->setCellValue('AP'.$row, $c->tgl_konadj2=='0000-00-00'&&'NULL'?'-': ($c->tgl_konadj2 ) )
    ->setCellValue('AQ'.$row,number_format($c->kon_adj2))
    
    ->setCellValue('AR'.$row, $c->tgl_proadj3=='0000-00-00'&&'NULL'?'-': ($c->tgl_proadj3 ) )
    ->setCellValue('AS'.$row, number_format($c->pro_adj3))
    ->setCellValue('AT'.$row, $c->tgl_konadj3=='0000-00-00'&&'NULL'?'-': ($c->tgl_konadj3 ) )
    ->setCellValue('AU'.$row, number_format($c->kon_adj3))
    
    ->setCellValue('AV'.$row, $c->estimasi <= $c->deduct && $c->status_progress=='2' ? 'Ya' : 'Tidak' )  
    ->setCellValue('AW'.$row, $c->estimasi >= $c->deduct && $c->status_progress=='1' ? 'Ya' : 'Tidak' )
    ->setCellValue('AX'.$row, $c->tgl_settled=='0000-00-00'&&'NULL'?'-': ($c->tgl_settled ) )
    ->setCellValue('AY'.$row, number_format($c->settled))    ;
    
    
     $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':'.'AQ'.$row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
		$i++;
		$row++;
	endforeach;  
  endforeach;     

	$objPHPExcel->getActiveSheet()->setTitle('REPORT DETAIL PROGRESS AST');
	$objPHPExcel->setActiveSheetIndex(0);

	if($_GET['f']='xlsx'){
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');		
	}else{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');		
	}
	$objWriter->save('php://output');	
	exit;
}  
//else {echo "Tidak Ada laporan";}
} 

//#3
if($_SESSION['gets']['l']=='3'){ 
	$SQL3  = "SELECT COUNT(1) jml, `status`,EXTRACT(MONTH FROM `created_at`) bln FROM ast2 					
					WHERE 1 ".$and." 
					GROUP BY `status`, bln";
	$res3 = $db->get_results($SQL3);
	//$db->debug();

	$summ = array();
	foreach($res3 as $r3){
		$summ[$r3->status][$r3->bln] = $r3->jml>0?$r3->jml:'0';
	}
	$filename = $user->inisial.'-SUMMARY REPORT KLAIM AST '.$_SESSION['gets']['r'].'.'.$_SESSION['gets']['m1'].' '.$_SESSION['gets']['t1'].'-'.$_SESSION['gets']['m2'].' '.$_SESSION['gets']['t2'].'.xls';

	$objPHPExcel = new PHPExcel();

	if($_SESSION['gets']['r']<>''){
		$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_SESSION['gets']['r']}'");
		$regional =$reg->region;
	}else $regional ='NASIONAL';


	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'SUMMARY REPORT KLAIM AST PT TELKOMSEL')
				->setCellValue('A2', "REGIONAL: {$regional}")
				->setCellValue('A3', "NO. POLIS: ".($_SESSION['gets']['p']=='1'?'All Risk[202.204.300.10.00017]':'Earthquake[202.203.300.10.00032]')."")
				->setCellValue('A5','Status')
				->setCellValue('B5','Bulan')
        ->setCellValue('N5','Total')
				->MergeCells('A5:A6')
        ->MergeCells('B5:M5')
        ->MergeCells('A1:C1')
        ->MergeCells('A3:C3')
        ->MergeCells('N5:N6');
        
        
  $objPHPExcel->getActiveSheet()->getStyle('A5:N5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('B6:N6')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A6:N6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A5:A6')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A5:A6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('B6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('C6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('D6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('E6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('F6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('G6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('H6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('I6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('J6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('K6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('L6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('M5:M6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('N5:N6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  
	$r = 66;//B
	foreach($months as $m):
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'6',$m);
			$r++;
	endforeach;

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7',"Created(UnApproved)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A8',"Approve");
  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A9',"Rejected");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A10',"Under Deductible");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A11',"Outstanding(Submitted)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A12',"Settled");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A13',"Total");
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'7',(int)$summ['UNAPPROVED'][$i]);
    $r++;  
    $un+=$summ['UNAPPROVED'][$i];
	} 
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N7',$un);
  
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'8',(int)$summ['APPROVED'][$i]);
		$r++;
    $ap+=$summ['APPROVED'][$i];
	} 
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N8',$ap);

  $r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'9',(int)$summ['REJECTED'][$i]);
		$r++;
	  $rej+=$summ['REJECTED'][$i];
	} 
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N9',$rej); 

	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'10',(int)$summ['UNDER DEDUCTIBLE'][$i]);
		$r++;
	  $dec+=$summ['UNDER DEDUCTIBLE'][$i];
	} 
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N10',$dec);

	$r = 66;//B
		for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'11',(int)$summ['UNDER DEDUCTIBLE'][$i]);
		$r++;
	  $dec+=$summ['UNDER DEDUCTIBLE'][$i];
	} 
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N11',$dec);
	  
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'12',(int)$summ['SUBMITTED'][$i]);
		$r++;
    $sub+=$summ['SUBMITTED'][$i];
	}
   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N12',$sub);

	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'13',$summ['UNAPPROVED'][$i]+$summ['APPROVED'][$i]+$summ['REJECTED'][$i]+$summ['UNDER DEDUCTIBLE'][$i]+$summ['SUBMITTED'][$i]+$summ['SETTLED'][$i]);
		$r++;
    $tot+=$summ['UNAPPROVED'][$i]+$summ['APPROVED'][$i]+$summ['REJECTED'][$i]+$summ['UNDER DEDUCTIBLE'][$i]+$summ['SUBMITTED'][$i]+$summ['SETTLED'][$i];
	}
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N13',$tot);
  
	$objPHPExcel->getActiveSheet()->setTitle('SUMMARY REPORT KLAIM AST');
	$objPHPExcel->setActiveSheetIndex(0);

	if($_GET['f']='xlsx'){
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');		
	}else{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');		
	}
	$objWriter->save('php://output');	
	exit;
}    

?>