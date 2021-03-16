{extends file='site_layout.tpl'}
{block name='content'}
	<h2 class="page-header">Daftar Dosen PWMI</h2>
	<div class="row">
		<div class="col-lg-12">
			<form class="form-horizontal" method="post" action="{current_url()}">
				<fieldset>
					<legend>Apakah usulan dosen PWMI ini akan dihapus ?</legend>
					<div class="form-group">
						<label class="control-label col-lg-2">NIDN</label>
						<div class="col-lg-3">
							<p class="form-control-static">{$dosen->nidn}</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-2">Nama</label>
						<div class="col-lg-4">
							<p class="form-control-static">{$dosen->nama}</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-2">Program Studi</label>
						<div class="col-lg-4">
							<p class="form-control-static">{$prodi->nama}</p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-4">
							<a href="{site_url('pwmi')}" class="btn btn-default">Kembali</a>
							<button type="submit" class="btn btn-danger">Hapus</button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
{/block}
