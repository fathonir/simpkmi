{extends file='site_layout.tpl'}
{block name='content'}
	<div class="row">
		<div class="col-lg-12">
			{if $ci->session->flashdata('success')}
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<strong>Data berhasil disimpan!</strong>
				</div>
			{/if}

			{if $ci->session->flashdata('hapus_attachment_success')}
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<strong>File attachment berhasil dihapus!</strong>
				</div>
			{/if}
			<form method="post" action="{current_url()}" enctype="multipart/form-data">
				<div class="form-group">
					<label class="control-label">Judul</label>
					<p class="form-control-static">{$proposal->judul}</p>
				</div>
				<div class="form-group">
					<textarea class="form-control" name="laporan" rows="10" id="richtext"
							  required>{$lap_pendampingan->laporan}</textarea>
				</div>
				{if $lap_pendampingan->attachment_nama_file == ''}
					{if $ci->session->flashdata('upload_error_msg')}
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							{$ci->session->flashdata('upload_error_msg')}
						</div>
					{/if}
					<div class="form-group">
						<label class="control-label">Foto atau Dokumen Pendukung Lainnya (pdf)</label>
						<input type="file" name="file" class="form-control"/>
					</div>
				{else}
					<div class="form-group">
						<label class="control-label">Foto atau Dokumen Pendukung Lainnya (pdf)</label>
						<p class="form-control-static">
							<a href="{site_url('../../upload/laporan-pendampingan')}/{$lap_pendampingan->attachment_nama_file}">
								{$lap_pendampingan->attachment_nama_asli}
							</a>
							{if $is_masa_laporan}
								<a href="{site_url('pwmi/hapus-attachment')}/{$proposal->id}/{$lap_pendampingan->tahapan_pendampingan_id}" class="btn btn-xs btn-danger">
									<i class="glyphicon glyphicon-remove"></i>
								</a>
							{/if}
						</p>
					</div>
				{/if}
				<div class="form-group">
					<a class="btn btn-default" href="{site_url('pwmi/view')}/{$proposal->id}">Kembali</a>
					{if $is_masa_laporan}
						<input class="btn btn-primary" type="submit" value="Simpan"/>
					{/if}
				</div>
			</form>
		</div>
	</div>
{/block}
{block name='footer-script'}
	{if ENVIRONMENT == 'development'}
		<script src="{base_url('../vendor/ckeditor/ckeditor/ckeditor.js')}"></script>
		<script src="{base_url('../vendor/ckeditor/ckeditor/adapters/jquery.js')}"></script>
	{/if}
	{if ENVIRONMENT == 'production'}
		<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
		<script src="https://cdn.ckeditor.com/4.14.1/standard/adapters/jquery.js"></script>
	{/if}
	<script type='text/javascript'>
		$(document).ready(function () {
			$('#richtext').ckeditor({
				height: 300
			});
		});
	</script>
{/block}
