{extends file='site_layout.tpl'}
{block name='content'}
	<h2 class="page-header">Tambah Proposal Baru</h2>
	<div class="row">
		<div class="col-lg-12">
			
			<form action="{current_url()}" method="post" class="form-horizontal">
				<input type="hidden" name="mode" value="search" />
				<fieldset>
					<legend>Ketua Kelompok</legend>
					
					<div class="form-group">
						<label class="col-lg-2 control-label">Program Studi</label>
						<div class="col-lg-6 col-md-8 col-sm-10">
							<select class="form-control" name="program_studi_id">
								{html_options options=$program_studi_set selected=$smarty.post.program_studi_id}
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-lg-2 control-label">NIM</label>
						<div class="col-lg-3">
							<input type="text" name="nim" class="form-control" value="{set_value('nim')}" />
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-2">
							<button class="btn btn-primary">Cari</button>
						</div>
					</div>
					
				</fieldset>
			</form>
			
			{if $smarty.server.REQUEST_METHOD == 'POST'}
				{if $error_type == ''}
					<form action="{current_url()}" method="post" class="form-horizontal">
						<input type="hidden" name="mode" value="add" />
						<input type="hidden" name="mahasiswa_id" value="{$mahasiswa->id}" />
						<fieldset>

							<div class="form-group">
								<label class="col-lg-2 control-label">Nama</label>
								<div class="col-lg-3">
									<p class="form-control-static">{$mahasiswa->nama}</p>
								</div>
								<label class="col-lg-2 control-label">Program Studi</label>
								<div class="col-lg-3">
									<p class="form-control-static">{$mahasiswa->program_studi->nama}</p>
								</div>
								<label class="col-lg-1 control-label">Angkatan</label>
								<div class="col-lg-1">
									<p class="form-control-static">{$mahasiswa->angkatan}</p>
								</div>
							</div>
								
							<div class="form-group">
								<label class="col-lg-2 control-label">Email</label>
								<div class="col-lg-3">
									<input type="email" name="email" class="form-control" value="{$mahasiswa->email}" required />
								</div>
								<label class="col-lg-2 control-label">No HP</label>
								<div class="col-lg-3">
									<input type="text" name="no_hp" class="form-control" value="{$mahasiswa->no_hp}" required />
								</div>
							</div>
								
							<div class="form-group">
								<label class="col-lg-2 control-label">Nama (Calon) Perusahaan</label>
								<div class="col-lg-10">
									<input type="text" name="judul" class="form-control" placeholder="Nama (Calon) Perusahaan atau nama usaha atau nama produk yang diusulkan" required />
								</div>
							</div>

							<div class="form-group">
								<div class="col-lg-offset-2 col-lg-4">
									<a href="{site_url('proposal-kbmi')}" class="btn btn-default">Kembali</a>
									<button class="btn btn-success">Tambahkan</button>
								</div>
							</div>

						</fieldset>
					</form>
				{/if}
				{if $error_type == 'MHS_TIDAK_DITEMUKAN'}
					<div class="alert alert-warning alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<p>Mahasiswa tidak ditemukan di sistem maupun di PDDIKTI. Silahkan ulangi pencarian.</p>
						<p>Untuk memastikan mahasiswa terdaftar di PDDIKTI buka laman <a href="https://forlap.ristekdikti.go.id/mahasiswa" target="_blank">https://forlap.ristekdikti.go.id/mahasiswa</a></p>
					</div>
				{/if}
				{if $error_type == 'MHS_SEARCH_EXCEPTION'}
					<div class="alert alert-warning alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<p>{$error_message} [Exc]</p>
						<p>Silahkan ulangi pencarian</p>
					</div>
				{/if}
			{/if}
			
		</div>
	</div>
{/block}
