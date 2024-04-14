<ul style="list-style:none;padding-left:5px;background:#d2f78a;margin:5px;padding:2px;border:1px solid #6c9b0f;">
  <li>
    <a href="user.php">H O M E</a>
  </li>
  <li>
    <hr />
  </li>
  <li>Laporan CGL / Tuntutan Warga
    <ul style="padding-left:20px;">
      <?php if ($user['role'] == 'spvr') { ?>
        <li><a href="cgl.php">Buat Laporan Awal CGL</a></li>
      <?php } ?>
      <?php if ($user['role'] == 'spvr') { ?>
        <li><a href="laporan_cgl.php">Update / Revisi CGL<br /></a></li>
      <?php } ?>
      <?php if ($user['role'] == 'mgrr') { ?>
        <li><a href="laporan_cgl.php">Approved Laporan Awal CGL<br /></a></li>
      <?php } ?>
      <?php if ($user['role'] == 'guest') { ?>
        <li><a href="laporan_cgl.php">Lihat laporan CGL<br /></a></li>
      <?php } ?>
    </ul>
  </li>
  <li>Laporan AST / Asset
    <ul style="padding-left:20px;">
      <?php if ($user['role'] == 'spvr') { ?>
        <li><a href="ast.php">Buat Laporan AST Baru</a></li>
      <?php } ?>
      <?php if ($user['role'] == 'spvr') { ?>
        <li><a href="laporan_ast.php">Update / Revisi AST</a></li>
      <?php } ?>
      <?php if ($user['role'] == 'mgrr') { ?>
        <li><a href="laporan_ast.php">Approved Laporan Awal AST<br /></a></li>
      <?php } ?>
      <?php if ($user['role'] == 'guest') { ?>
        <li><a href="laporan_ast.php">Lihat laporan AST<br /></a></li>
      <?php } ?>
    </ul>
  </li>
  <li>Reporting
    <ul style="padding-left:20px;list-style:none;">
      <li><a href="u_lap_cgl.php">CGL</a></li>
      <li><a href="u_lap_ast.php">AST (Asset)</a></li>
    </ul>
  </li>
  <li>
    <hr />
  </li>
  <li><a href="forum.php">Forum tanya jawab</a></li>
</ul>