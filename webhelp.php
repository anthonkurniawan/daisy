<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
?>
<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
		<td style="width:250px">
			<ul style="list-style:none;padding-left:5px">
				<?php include $user->role=='spvr'|| $user->role=='mgrr'?"menu.php":"menusuper.php" ?>
			</ul>
		</td>
		<td>
		<h3>Bantuan penggunaan Daisy</h3>
			<ul>
				<li><a href="docs/<?=htmlentities("Proses Bisnis Penyelesaian Klaim CGL Telkomsel - NETT DRAFT.pdf")?>">Proses Bisnis Penyelesaian Klaim CGL Telkomsel</a></li>
				<li><a href="docs/<?=htmlentities("JUKLAK KLAIM CGL TELKOMSEL 2011 - NETT DRAFT - Tsel.pdf")?>">Juklak Klaim CGL TELKOMSEL 2011</a></li>
				<li><a href="docs/<?=htmlentities("Lampiran 1 - Juklak Klaim CGL Telkomsel 2011.pdf")?>">Format Laporan Awal Klaim</a></li>
				<li><a href="docs/<?=htmlentities("Lampiran 2 - Juklak Klaim CGL Telkomsel 2011.pdf")?>">Format Surat Tuntutan Warga (individu)</a></li>
				<li><a href="docs/<?=htmlentities("Lampiran 3 - Juklak Klaim CGL Telkomsel 2011.pdf")?>">Format Surat Tuntutan Warga (kolektif)</a></li>
				<li><a href="docs/<?=htmlentities("Lampiran 4 - juklak klaim CGL Telkomsel 2011.pdf")?>">Surat Permintaan Survey (SPS)</a></li>
				<li><a href="docs/<?=htmlentities("Lampiran 5 - juklak klaim CGL Telkomsel 2011.pdf")?>">Bill of Quantity</a></li>
				<li><a href="docs/<?=htmlentities("Lampiran 6 - juklak klaim CGL Telkomsel 2011.pdf")?>">Berita Acara Serah Terima Klaim CGL</a></li>
				<li><a href="docs/<?=htmlentities("Lampiran 7 - juklak klaim CGL Telkomsel 2011.pdf")?>">Berita Acara Pemusnahan Scrap</a></li>
			<ul>
		</td>
	</tr>
</table>
<?php include "footer.php"?>