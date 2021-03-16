{extends file='site_layout.tpl'}
{block name='content'}
	<h2 class="page-header">Daftar Dosen PWMI</h2>
	<form action="{current_url()}" method="post" enctype="multipart/form-data">

		<fieldset>
			<legend><h3>Upload Syarat</h3></legend>

			<div class="form-group">
				<p class="form-control-static">Pastikan mengupload berkas yang sudah sesuai dengan panduan.
					Berkas yang tidak sesuai dengan panduan tidak akan disetujui.</p>
			</div>

			{foreach $syarat_set as $syarat}

				{if $syarat->is_aktif}

					<div class="form-group fg-upload-{$syarat->id} {if isset($syarat->upload_error_msg)}has-error{/if}"
						 {if $syarat->file_usulan_pendamping_id != ''}style="display: none"{/if}>
						<label class="control-label">{$syarat->syarat} {if $syarat->is_wajib}(Wajib){/if}</label>
						<input type="file" name="file_syarat_{$syarat->id}" class="filestyle" />
						<span class="help-block">{$syarat->keterangan} ({$syarat->allowed_types}) - Maks. {$syarat->max_size}MB</span>
						{if isset($syarat->upload_error_msg)}
							<span class="help-block">ERROR: {$syarat->upload_error_msg}</span>
						{/if}
					</div>

					<div class="form-group fg-view-{$syarat->id}" {if $syarat->file_usulan_pendamping_id == ''}style="display: none"{/if}>
						<label class="control-label">{$syarat->syarat} {if $syarat->is_wajib}(Wajib){/if}</label>
						<p class="form-control-static">
							<a href="{base_url()}/../upload/lampiran-usulan-pendamping/{$syarat->nama_file}" target="_blank">{$syarat->nama_file}</a>
							{if $is_in_jadwal}
								<a class="btn btn-xs btn-default btn-edit" data-id="{$syarat->id}" title="Ubah file"><i class="glyphicon glyphicon-edit"></i> Ubah</a>
							{/if}
						</p>
						{if isset($syarat->upload_success)}
							<span class="help-block upload-success">
								<div class="alert alert-info">
									Upload berhasil
								</div>
							</span>
						{/if}
					</div>
					
				{/if}

			{/foreach}

			<div class="form-group">
				<a href="{site_url('pwmi')}" class="btn btn-default">Kembali</a>
				{if $is_in_jadwal}
					<button class="btn btn-primary"><i class="glyphicon glyphicon-cloud-upload"></i> Upload</button>
				{/if}
			</div>
			
		</fieldset>

	</form>
{/block}
{block name='footer-script'}
	<script src="{base_url('../assets/js/bootstrap-filestyle.min.js')}" type='text/javascript'></script>
	<script type="text/javascript">
		$(':file').filestyle();

		$('.btn-edit').on('click', function() {
			var syarat_id = $(this).data('id');
			$('.fg-view-' + syarat_id).hide();
			$('.fg-upload-' + syarat_id).show();
		});
		$('.upload-success').hide(5000);
	</script>
{/block}
