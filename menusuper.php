				<ul style="list-style:none;padding-left:5px;background:#83eefe;margin:5px;padding:2px;border:1px solid #0a6363;">					
					<li>
						<a href="super.php">H O M E</a>
					</li>
					<li><hr /></li>
					<li>Daftar Klaim
						<ul style="padding-left:20px;list-style:none;">
							<li><a href="lap_cgl.php">CGL (Tuntutan Warga)</a></li>
							<li><a href="lap_ast.php">AST (Asset)</a></li>
						</ul>						
					</li>
					<li>Reporting
						<ul style="padding-left:20px;list-style:none;">
							<li><a href="su_lap_cgl.php">CGL</a></li>
							<li><a href="su_lap_ast.php">AST (Asset)</a></li>
						</ul>
					</li>
					<li><hr /></li>
					<li><a href="forum.php">Forum tanya jawab</a></li>
					<?php if($user->role=='gmp'): ?>
					<li><hr /></li>					
					<li>Data Induk
						<ul style="padding-left:20px;list-style:none;">
							<li><a href="su_cglvendor.php">Vendor CGL</a></li>
							<li><a href="su_gaset.php">Grup Aset</a></li>
							<li><a href="su_aset.php">Data Aset</a></li>
							<li><a href="su_user.php">Daftar User</a></li>
							<li><a href="su_site.php">Daftar Site</a></li>
							<li><a href="su_region.php">Daftar Region</a></li>
						</ul>					
					</li>
					<?php endif; ?>
				</ul>