{extends file='site_layout.tpl'}
{block name='content'}
	<div class="row">
		<div class="col-lg-12">

			<form action="{current_url()}" method="post" enctype="multipart/form-data">

				<fieldset>
					<legend><h2>Unggah Pitchdeck Tahap 2</h2></legend>

					{if $syarat != null}

						{if $syarat->is_aktif and $syarat->is_upload}

							<div class="form-group fg-upload-{$syarat->id} {if isset($syarat->upload_error_msg)}has-error{/if}"
								 {if $syarat->file_proposal_id != ''}style="display: none"{/if}>
								<label class="control-label">{$syarat->syarat} {if $syarat->is_wajib}(Wajib){/if}</label>
								<input type="file" name="file_syarat_{$syarat->id}" class="filestyle" />
								<span class="help-block">{$syarat->keterangan} ({$syarat->allowed_types}) - Maks. {$syarat->max_size}MB</span>
								{if isset($syarat->upload_error_msg)}
									<span class="help-block">ERROR: {$syarat->upload_error_msg}</span>
								{/if}
							</div>

							<div class="form-group fg-view-{$syarat->id}" {if $syarat->file_proposal_id == ''}style="display: none"{/if}>
								<label class="control-label">{$syarat->syarat} {if $syarat->is_wajib}(Wajib){/if}</label>
								<p class="form-control-static">
									<a href="{base_url()}/../../upload/lampiran/{$syarat->nama_file}" target="_blank">{$syarat->nama_file}</a>
									<a class="btn btn-xs btn-default btn-edit" data-id="{$syarat->id}" title="Ubah file"><i class="glyphicon glyphicon-edit"></i> Ubah</a>
								</p>
							</div>

						{elseif $syarat->is_aktif and not $syarat->is_upload}

							<div class="form-group fg-upload-{$syarat->id} {if isset($syarat->upload_error_msg)}has-error{/if}"
								 {if $syarat->file_proposal_id != ''}style="display: none"{/if}>
								<label class="control-label">Link {$syarat->syarat} {if $syarat->is_wajib}(Wajib){/if}</label>
								<input type="text" name="file_syarat_{$syarat->id}" class="form-control" />
								<span class="help-block">{$syarat->keterangan}</span>
								{if isset($syarat->upload_error_msg)}
									<span class="help-block">ERROR: {$syarat->upload_error_msg}</span>
								{/if}
							</div>

							<div class="form-group fg-view-{$syarat->id}" {if $syarat->file_proposal_id == ''}style="display: none"{/if}>
								<label class="control-label">Link {$syarat->syarat} {if $syarat->is_wajib}(Wajib){/if}</label>
								<p class="form-control-static">
									<a href="{$syarat->nama_file}" target="_blank">{$syarat->nama_file}</a>
									{if $proposal->is_submited == FALSE}
										<a class="btn btn-xs btn-default btn-edit" data-id="{$syarat->id}" title="Ubah file"><i class="glyphicon glyphicon-edit"></i> Ubah</a>
									{/if}
								</p>
							</div>

						{/if}

						<div class="form-group">
							<a href="{site_url('home')}" class="btn btn-default">Kembali</a>
							<input type="submit" class="btn btn-primary" name="tombol" value="Simpan" />
						</div>

					{else}

						<div class="form-group">
							<a href="{site_url('home')}" class="btn btn-default">Kembali</a>
						</div>

					{/if}


				</fieldset>

			</form>

		</div>
	</div>
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
	</script>
{/block}
