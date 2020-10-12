{extends file='site_layout.tpl'}
{block name='content'}
	<div class="panel panel-default">
		<div class="panel-body">
			
			<form action="{current_url()}" method="post" enctype="multipart/form-data">

				<fieldset>
					<legend class="text-center"><h2>Upload Berkas Kemajuan dan Penggunaan Dana</h2></legend>

					<div class="form-group">
						<label class="control-label">Judul</label>
						<p class="form-control-static">{$proposal->judul}</p>
					</div>

					<div class="form-group">
						<label class="control-label">Total Pendanaan</label>
						<p class="form-control-static">Rp {$proposal->dana_disetujui|number_format:0:',':'.'}</p>
					</div>

					<div class="row">
						<div class="col-lg-4">
							<div class="form-group">
								<label class="control-label">Total Penggunaan Dana Termin 1</label>
								<input type="text" name="dana_dipakai_t1" class="form-control" value="{$proposal->dana_dipakai_t1}"/>
								{if $ci->session->flashdata('simpan_success')}
									<span class="help-block">Simpan Berhasil</span>
								{/if}
							</div>
						</div>
					</div>

					<div class="form-group">
						<input type="submit" class="btn btn-primary" name="tombol" value="Simpan">
					</div>

					{foreach $syarat_set as $syarat}
						
						{if $syarat->is_aktif}
							
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
									
									{if $proposal->is_submited == FALSE}
										<a class="btn btn-xs btn-default btn-edit" data-id="{$syarat->id}" title="Ubah file"><i class="glyphicon glyphicon-edit"></i> Ubah</a>
									{/if}
								</p>
							</div>
						{/if}
						
					{/foreach}
					
					<div class="form-group">
						<div class="col-lg-12 text-center">
							{if $proposal->is_didanai}
								<input type="submit" class="btn btn-info" name="tombol" value="Unggah" />
							{/if}
						</div>
					</div>
					
				</fieldset>
				
			</form>
			
		</div>
	</div>
{/block}
{block name='footer-script'}
	<script src="{base_url('../assets/js/bootstrap-filestyle.min.js')}" type='text/javascript'></script>
	<script src="{base_url('../assets/js/jquery-number-2.1.6/jquery.number.min.js')}" type='text/javascript'></script>
	<script type="text/javascript">
		$(':file').filestyle();
		
		$('.btn-edit').on('click', function() {
			var syarat_id = $(this).data('id');
			
			$('.fg-view-' + syarat_id).hide();
			$('.fg-upload-' + syarat_id).show();
			
		});

		$('input[name="dana_dipakai_t1"]').number(true, 0, ',', '.');
	</script>
{/block}
