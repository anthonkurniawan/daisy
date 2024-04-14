<?php
if(isset($_GET['docid']))
	echo "docid  :  " . $_GET['docid'] . "<BR/>"; 
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <?php if ($_GET['docid'] == 'doc2') { ?>
    <tr class="even">
      <td>
        <div>Dokumen #1</div>Berita acara kerusakan atau kehilangan Aktiva Tetap (Rincian Objek yang Mengalami Kerugian)
        <div class="keterangan"><?= $rast->doc2_file == '' ? '-belum ada-' : $rast->doc2_file ?></div>
      </td>
      <td><input type="file" name="dok2" /></td>
    </tr>
  <?php } ?>

  <?php if ($_GET['docid'] == 'doc3') { ?>
    <tr class="odd">
      <td>
        <div>Dokumen #2</div>Kronologi kejadian/ kerugian
        <div class="keterangan"><?= $rast->doc3_file == '' ? '-belum ada-' : $rast->doc3_file ?></div>
      </td>
      <td><input type="file" name="dok3" /></td>
    </tr>
  <?php } ?>

  <?php if ($_GET['docid'] == 'doc5') { ?>
    <tr class="even">
      <td>
        <div>Dokumen #3</div>Foto Objek Kerugian
        <div class="keterangan"><?= $rast->doc5_file == '' ? '-belum ada-' : $rast->doc5_file ?></div>
      </td>
      <td><input type="file" name="dok5" /></td>
    </tr>
  <?php } ?>

  <?php if ($_GET['docid'] == 'doc_rinci') { ?>
    <tr class="odd">
      <td>
        <div>Dokumen #4 </div>Rincian Kerugian
        <div class="keterangan"><?= $rast->doc_rinci_file == '' ? '-belum ada-' : $rast->doc_rinci_file ?></div>
      </td>
      <td><input type="file" name="dok10" /></td>
    </tr>
  <?php } ?>

  <tr>
    <td>&nbsp;</td>
  </tr>

  <?php if ($rast->sebab == 'thf') { ?>
    <tr class="even">
      <td>Dokumen Khusus (BA Kepolisian)
        <div class="keterangan">Disesuaikan dengan "Cause of Damage"</div>
        <div class="keterangan"><?= $rast->doc_pol_file == '' ? '-belum ada-' : $rast->doc_pol_file ?></div>
      </td>
      <td><input type="file" name="dok7" /></td>
    </tr>
  <?php } ?>

  <?php if ($rast->sebab == 'fre') { ?>
    <tr class="even">
      <td>Dokumen Khusus (Surat PMK)
        <div class="keterangan">Disesuaikan dengan "Cause of Damage"</div>
        <div class="keterangan"><?= $rast->doc_pmk_file == '' ? '-belum ada-' : $rast->doc_pmk_file ?></div>
      </td>
      <td><input type="file" name="dok8" /></td>
    </tr>
  <?php } ?>

  <?php if ($rast->sebab == 'lit' || $rast->sebab == 'nds' || $rast->sebab == 'etv') { ?>
    <tr class="even">
      <td>Dokumen Khusus (Surat BMKG)
        <div class="keterangan">Disesuaikan dengan "Cause of Damage"</div>
        <div class="keterangan"><?= $rast->doc_bmkg_file == '' ? '-belum ada-' : $rast->doc_bmkg_file ?></div>
      </td>
      <td><input type="file" name="dok9" /></td>
    </tr>
  <? } ?>

</table>
<?php } ?>