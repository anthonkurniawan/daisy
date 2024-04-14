<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
?>
<table width="100%" border="0" cellpadding="5" cellspacing="0" style="margin:0px auto" id="badan">
  <tr valign="top">
    <td>
      <?php if ($invoiceSet == 1) { ?>
        <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;">
          <p>Invoice Laporan CGL berhasil di input!<br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL
              &raquo;</a></p>
        </div>
        <?php exit();
      }
      if ($paymentSet == 1) { ?>
        <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;">
          <p>Payment Laporan CGL berhasil di input!<br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL
              &raquo;</a></p>
        </div>
        <?php exit();
      }
      if ($upd == 1) { ?>
        <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;">
          <p>Klaim CGL berhasil di
            <?= $newStatus ?>! <br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL &raquo;</a>
          </p>
        </div>
        <?php exit();
      }
      if ($issurvey === 1) { ?>
        <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;">
          <p>Survey CGL berhasil di submit! <br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL &raquo;</a></p>
        </div>
        <?php exit();
      }
      if ($inscgl === 1) { ?>
        <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;">
          <p>Revisi laporan CGL berhasil di submit! <br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL
              &raquo;</a></p>
        </div>
      <?php } else { ?>
        <h3>Revisi Laporan CGL [
          <?= $rcgl->no_laporan ?> ]
        </h3>
        <?php if ($rcgl->status == 'SUBMITTED') { ?>
          <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;">
            <p>Proses survey tidak bisa berjalan sebelum melampirkan:<br /> <strong>- Surat Tuntutan</strong>.</p>
          </div>
        <?php } ?>
        <?php if (!empty($err)) { ?>
          <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;">
            <p>Mohon isi / perbaiki data berikut:
            <ul>
              <?php foreach ($err as $e): ?>
                <li>
                  <?= ucfirst($e) ?>
                </li>
              <?php endforeach; ?>
            </ul>
            </p>
          </div>
        <?php } ?>

        <form method="post" action="" enctype="multipart/form-data" autocomplete="off">
          <input type="hidden" name="i" value="<?= $rcgl->cgl_id ?>" />
          <table id="fcgl">
            <tr class="even">
              <td><strong>Hari / tanggal kejadian</strong></td>
              <td>
                <?php if ($mode == 'revisi') { ?>
                  <input type="text" name="tgl_kejadian_show" id="tgl_kejadian_show"
                    value="<?= date('l/ j F Y', strtotime($rcgl->tgl_kejadian)) ?>" class="narr" /><input type="hidden"
                    name="tgl_kejadian" id="tgl_kejadian" value="<?= $rcgl->tgl_kejadian ?>" />
                  <div class="keterangan">[ctrl+panah]:untuk pindah tanggal, [pageUp/pageDown]:untuk pindah bulan,
                    [Enter]:accept</div>
                <?php } else { ?>
                  <?= date("l, d F Y", strtotime($rcgl->tgl_kejadian)) ?>
                <?php } ?>
              </td>
            </tr>
            <tr class="odd">
              <td><strong>Hari / tanggal diketahui Telkomsel</strong></td>
              <td>
                <?php if ($mode == 'revisi') { ?>
                  <input type="text" name="tgl_tuntutan_show" id="tgl_tuntutan_show" class="narr"
                    value="<?= date('l/ j F Y', strtotime($rcgl->tgl_tuntutan)) ?>" /><input type="hidden"
                    name="tgl_tuntutan" id="tgl_tuntutan" value="<?= $rcgl->tgl_kejadian ?>" />
                  <div class="keterangan">[ctrl+panah]:untuk pindah tanggal, [pageUp/pageDown]:untuk pindah bulan,
                    [Enter]:accept</div>
                <?php } else { ?>
                  <?= date("l, d F Y", strtotime($rcgl->tgl_tuntutan)) ?>
                <?php } ?>
              </td>
            </tr>
            <tr class="even">
              <td><strong>Tempat / lokasi kerugian</strong></td>
              <td>
                <?php if ($mode == 'revisi') { ?>
                  <select name="lokasi" id="lokasite">
                    <option value="">-Pilih Site ID-</option>
                    <?php $resSite = $db->get_results("SELECT st_site_id,st_name FROM `site` WHERE kode_region='" . $user->regional . "' GROUP BY st_site_id ORDER BY st_site_id ASC");
                    foreach ($resSite as $site): ?>
                      <option <?= ($site->st_site_id == $rcgl->st_site_id ? 'selected="selected"' : '') ?>
                        value="<?= $site->st_site_id ?>">
                        <?= $site->st_site_id ?> /
                        <?= $site->st_name ?>
                      </option>
                    <?php endforeach;
                    ?>
                    <script>
                      $("#lokasite").ready(function () {
                        $('#siteDetail').load('./siteDetailCGL.php?a=<?= $rcgl->st_address ?>&lat=<?= $rcgl->st_latitude ?>&long=<?= $rcgl->st_longitude ?>&siteId=<?= $rcgl->st_site_id ?>&c=<?= $rcgl->catatan ?>');
                      })</script>
                  </select>
                <?php }
                $r = $db->get_row("SELECT * FROM `site` WHERE st_site_id='" . $rcgl->st_site_id . "'");
                if ($mode == 'view')
                  echo $rcgl->st_site_id . ' / ' . $r->st_name;
                ?>
              </td>
            </tr>
            <tr class="odd" valign="top">
              <td><strong>Detail lokasi kerugian<strong></td>
              <td>
                <div id="siteDetail">
                  <table width="100%">
                    <tr class="odd">
                      <td>Site ID</td>
                      <td>
                        <?= $r->st_site_id ?>
                      </td>
                    </tr>
                    <tr class="even">
                      <td>Site Name</td>
                      <td>
                        <?= $r->st_name ?>
                      </td>
                    </tr>
                    <tr class="odd">
                      <td>Region</td>
                      <td>
                        <?= $r->st_region ?>
                      </td>
                    </tr>
                    <tr class="even">
                      <td nowrap="nowrap">Longitude</td>
                      <td>
                        <?= $rcgl->st_longitude ?>
                      </td>
                    </tr>
                    <tr class="odd">
                      <td nowrap="nowrap">Latitude</td>
                      <td>
                        <?= $rcgl->st_latitude ?>
                      </td>
                    </tr>
                    <tr valign="top" class="even">
                      <td>Alamat Site</td>
                      <td>
                        <?= $rcgl->st_address ?>
                      </td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
            <tr class="even" valign="top">
              <td><strong>Penyebab kerugian</strong></td>
              <td>
                <?php if ($mode == 'revisi') { ?>
                  <select name="sebab" onchange="cekOth(this.form)">
                    <option <?= ("lit" == $_POST['sebab'] ? 'selected="selected"' : '') ?> value="lit"> CGL Imbas Petir
                    </option>
                    <option <?= ("oth" == $_POST['sebab'] ? 'selected="selected"' : '') ?> value="oth"> Other Losses
                      (Lainnya..)
                    </option>
                  </select>
                  <br /><input type="text" name="oth_sebab" value="<?= $rcgl->other_sebab ?>" /> *
                  <div class="keterangan">*) Diisi jika penyebab kerugian: Lainnya.. </div>
                <?php } else { ?>
                  <?= $rcgl->sebab ?>
                <?php } ?>
              </td>
            </tr>
            <tr valign="top" class="odd">
              <td><strong>Rincian kerusakan</strong></td>
              <td>
                <?php if ($mode == 'revisi') { ?>
                  <textarea name="rincian" style="width:320px;"><?= $rcgl->rincian ?></textarea>
                <?php } else { ?>
                  <?= $rcgl->rincian ?>
                <?php } ?>
              </td>
            </tr>
            <tr valign="top" class="even">
              <td><strong>Estimasi kerugian</strong></td>
              <td>
                <?php if ($mode == 'revisi') { ?>
                  <input type="text" name="estimasi" value="<?= $rcgl->kerugian_survey ?>" />
                <?php } else { ?>
                  <?= $rcgl->kerugian_survey ?>
                <?php } ?>
              </td>
            </tr>
            <tr valign="top" class="odd">
              <td><strong>Contact person</strong></td>
              <td>
                <table>
                  <tr>
                    <td>Nama</td>
                    <td>:
                      <?php if ($mode == 'revisi') { ?>
                        <input type="text" style="width:220px" name="cp_nama" value="<?= $rcgl->cp_nama ?>" />
                      </td>
                    <?php } else { ?>
                      <?= $rcgl->cp_nama ?>
                    <?php } ?>
                  </tr>
                  <tr>
                    <td>No telepon</td>
                    <td>:
                      <?php if ($mode == 'revisi') { ?>
                        <input type="text" style="width:220px" name="cp_telp" value="<?= $rcgl->cp_telp ?>" />
                      <?php } else { ?>
                        <?= $rcgl->cp_telp ?>
                      <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td>No HP</td>
                    <td>:
                      <?php if ($mode == 'revisi') { ?>
                        <input type="text" style="width:220px" name="cp_hp" value="<?= $rcgl->cp_hp ?>" />
                      <?php } else { ?>
                        <?= $rcgl->cp_hp ?>
                      <?php } ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr valign="top" class="even">
              <td><strong>Vendor Pelaksana</strong></td>
              <td>
                <table>
                  <?php if ($mode == 'revisi') { ?>
                    <select name="cglv" id="cglv">
                      <?php $res = $db->get_results("SELECT * FROM `cgl_vendor` WHERE kode_regional='" . $user->regional . "' ORDER BY nama_vendor ASC");
                      foreach ($res as $r): ?>
                        <option <?= ($r->id_cglv == $rcgl->id_cglv ? 'selected="selected"' : '') ?> value="<?= $r->id_cglv ?>">
                          <?= $r->nama_vendor ?>
                        </option>
                      <?php endforeach;
                      ?>
                    </select>
                  <?php } else {
                    $resVendor = $db->get_row("SELECT * FROM cgl_vendor WHERE `id_cglv`='" . $rcgl->id_cglv . "'"); ?>
                    <strong>
                      <?= $resVendor->nama_vendor; ?>
                    </strong>
                  <?php } ?>
                  <tr>
                    <td>Nama</td>
                    <td>:
                      <?php if ($mode == 'revisi') { ?>
                        <input type="text" style="width:220px" name="vendor_pic" value="<?= $rcgl->vendor_pic ?>" />
                      </td>
                    <?php } else { ?>
                      <?= $rcgl->vendor_pic ?>
                    <?php } ?>
                  </tr>
                  <tr>
                    <td>No telepon</td>
                    <td>:
                      <?php if ($mode == 'revisi') { ?>
                        <input type="text" style="width:220px" name="vendor_telp" value="<?= $rcgl->vendor_telp ?>" />
                      <?php } else { ?>
                        <?= $rcgl->vendor_telp ?>
                      <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td>No HP</td>
                    <td>:
                      <?php if ($mode == 'revisi') { ?>
                        <input type="text" style="width:220px" name="vendor_hp" value="<?= $rcgl->vendor_hp ?>" />
                      <?php } else { ?>
                        <?= $rcgl->vendor_hp ?>
                      <?php } ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <?php if ($mode == 'survey') { ?>
              <tr class="odd">
                <td>Tanggal Survey</td>
                <td>
                  <input type="text" name="survey_date_show" id="survey_date_show" style="width:200px"
                    value="<?= $_POST['survey_date_show'] ?>" />
                  <input type="hidden" name="survey_date" id="survey_date" value="<?= $_POST['survey_date'] ?>" />
                  <div class="keterangan"><strong>Diisi tanggal perintah survey</strong></div>
                </td>
              </tr>
            <?php } ?>
            <?php if ($rcgl->file_surat_tuntutan == '' && $mode == 'survey') { ?>
              <tr class="odd">
                <td>File Surat Tuntutan</td>
                <td><input type="file" name="tuntutan" />
                  <div class="keterangan"><strong>*) Lampirkan file surat tuntutan</strong></div>
                </td>
              </tr>
            <?php } ?>
            <?php if ($mode == 'payment') { ?>
              <tr class="even">
                <td>Nilai BoQ yang telah disetujui<br />oleh Telkomsel</td>
                <td><input type="text" name="kerugian_survey"></td>
              </tr>
            <?php } ?>
            <?php if (($mode == 'payment') && $rcgl->file_boq == '') { ?>
              <tr class="odd">
                <td>File BoQ</td>
                <td><input type="file" name="s_boq" />
                  <div class="keterangan"><strong>*) Lampirkan BoQ yang telah disetujui Telkomsel </strong></div>
                </td>
              </tr>
            <?php } ?>
            <?php if ($mode == 'payment') { ?>
              <tr class="even">
                <td>Tanggal Payment</td>
                <td>
                  <input type="text" id="payment_show" name="payment_show" class="narr"
                    value="<?= $_POST['payment_show'] ?>" />
                  <input type="hidden" name="payment" id="payment" value="<?= $_POST['payment'] ?>" />
                  <div class="keterangan"><strong>*) Diisi tanggal payment kepada warga</strong></div>
                </td>
              </tr>
              <tr class="odd">
                <td>Lampiran</td>
                <td>
                  <table>
                    <tr>
                      <td>Foto</td>
                      <td><input type="file" name="foto" /></td>
                    </tr>
                    <tr>
                      <td>Kwitansi</td>
                      <td><input type="file" name="kwi" /></td>
                    </tr>
                    <tr>
                      <td>SPS</td>
                      <td><input type="file" name="sps" /></td>
										</tr>
										<tr>
											<td>Kronologis</td>
											<td><input type="file" name="kro" /></td>
										</tr>
									</table>
								</td>
							</tr>
    <?php } ?>
    <?php if ($mode == 'invoice') { ?>
      <tr class="odd">
        <td>Tanggal Invoice
        </td>
        <td>
          <input type="text" name="invoice_show" id="invoice_show" value="<?= $_POST['invoice_show'] ?>" class="narr" />
          <input type="hidden" name="invoice" id="invoice" value="<?= $_POST['invoice'] ?>" />
          <div class="keterangan"><strong>*) Diisi tanggal pengiriman Invoice ke perusahaan Asuransi</strong></div>
        </td>
      </tr>
      <tr class="even" valign="top">
        <td>Nilai Invoice</td>
        <td>
          <input type="text" name="besaran_invoice" value="<?= $_POST['besaran_invoice'] ?>" class="narr" />
          <div class="keterangan"><strong>*) Besaran nilai invoice adalah sejumlah total Nilai Invoice yang ditagihkan oleh
              vendor ke perusahaan asuransi.</div>
        </td>
      </tr>
    <?php } ?>
    <tr colspan="2" class="odd">
      <td>&nbsp;</td>
      <td style="text-align:right">
        <?php if ($_GET['m'] == 'revisi') { ?>
          <input type="button" onclick="document.location.href='revisicgl.php?revisi=<?= $_GET['revisi'] ?>'"
            value="Edit Kembali" />
          <input type="button" onclick="document.location.href='user.php'" value="Submit Laporan CGL" />
          <?php
        } else { ?>
          <input style="cursor:pointer;" type="button" onclick="document.location.href='laporan_cgl.php'" value="Kembali" />
          <input style="cursor:pointer;" type="button" value="Print"
            onclick="window.open ('printDetailCGL.php?cgl=<?= $rcgl->cgl_id ?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
          <?php if ($caption != '') { ?>
            <?php if ($mode == 'approval') { ?>
              <input type="hidden" value="0" name="isReject" /> <input onclick="isReject.value=1" type="submit"
                value="REJECT Laporan CGL" />
            <?php } ?>
            <input type="submit" value="<?= $caption ?>" />
          <?php } ?>
        <?php } ?>
      </td>
    </tr>
  </table>
  </form>
<?php } ?>
</td>
</tr>
</table>
<?php include "footer.php" ?>