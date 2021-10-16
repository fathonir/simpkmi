{extends file='site_layout.tpl'}
{block name='content'}
	<div class="row">
		<div class="col-lg-12">
			
			<form action="{current_url()}" method="post" enctype="multipart/form-data">
				
				<fieldset>
					{if $tahapan_id == TAHAPAN_EVALUASI}
						<legend><h2>Unggah Pitchdeck dan Produk</h2></legend>

						<h4>Jadwal Unggah Usulan</h4>
						<p>{$kegiatan->tgl_awal_upload|date_format:"%d %B %Y %T"} s/d
							{$kegiatan->tgl_akhir_upload|date_format:"%d %B %Y %T"}</p>

					{/if}

					{if $tahapan_id == TAHAPAN_MONEV}
						<legend><h2>Unggah Kemajuan</h2></legend>

						<h4>Jadwal Unggah Kemajuan</h4>
						<p>{$kegiatan->tgl_awal_upload_kemajuan|date_format:"%d %B %Y %T"} s/d
							{$kegiatan->tgl_akhir_upload_kemajuan|date_format:"%d %B %Y %T"}</p>

						<div class="form-group">
							<label class="control-label">Judul</label>
							<p class="form-control-static">{$proposal->judul}</p>
						</div>

						<div class="form-group">
							<label class="control-label">Total Pendanaan</label>
							<p class="form-control-static">Rp {$proposal->dana_disetujui|number_format:0:',':'.'}</p>
						</div>

						<div class="row">
							<div class="form-group col-md-3 {if form_error('dana_dipakai_t1')}has-error{/if}">
								<label class="control-label">Penggunaan Anggaran Termin 1</label>
								<input type="text" class="form-control number" name="dana_dipakai_t1" value="{$proposal->dana_dipakai_t1}"/>
								{if form_error('dana_dipakai_t1')}<span class="help-block">{form_error('dana_dipakai_t1')}</span>{/if}
							</div>
						</div>

					{/if}
					
					{foreach $syarat_set as $syarat}
						
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
									{if $tahapan_id == TAHAPAN_EVALUASI}
										{if $is_jadwal_upload_usulan}
											{if $proposal->is_submited == FALSE}
												<a class="btn btn-xs btn-default btn-edit" data-id="{$syarat->id}" title="Ubah file"><i class="glyphicon glyphicon-edit"></i> Ubah</a>
											{/if}
										{/if}
									{/if}
									{if $tahapan_id == TAHAPAN_MONEV}
										{if $is_jadwal_upload_kemajuan}
											<a class="btn btn-xs btn-default btn-edit" data-id="{$syarat->id}" title="Ubah file"><i class="glyphicon glyphicon-edit"></i> Ubah</a>
										{/if}
									{/if}
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
						
					{/foreach}

					{if $tahapan_id == TAHAPAN_EVALUASI}
						<div class="form-group">
							<a href="{site_url('home')}" class="btn btn-default">Kembali</a>
							{if $is_jadwal_upload_usulan}
								{if $proposal->is_submited == FALSE}
									<input type="submit" class="btn btn-primary" name="tombol" value="Simpan" />
								{/if}
							{/if}
						</div>
						{if $proposal->is_submited}
							<div class="form-group">
								<p class="text-danger">Proposal sudah disubmit, perubahan tidak akan disimpan dalam sistem.</p>
							</div>
						{/if}
					{/if}

					{if $tahapan_id == TAHAPAN_MONEV}
						<div class="form-group">
							<a href="{site_url('home')}" class="btn btn-default">Kembali</a>
							{if $is_jadwal_upload_kemajuan}
								<input type="submit" class="btn btn-primary" name="tombol" value="Simpan" />
							{/if}
						</div>
					{/if}

				</fieldset>
				
			</form>
			
		</div>
	</div>
{/block}
{block name='footer-script'}
	<script src="{base_url('../assets/js/bootstrap-filestyle.min.js')}" type='text/javascript'></script>
	<script src="{base_url('../assets/js/jquery-number-2.1.6/jquery.number.js')}" type="text/javascript"></script>
	<script type="text/javascript">
		$(':file').filestyle();
		
		$('.btn-edit').on('click', function() {
			var syarat_id = $(this).data('id');
			
			$('.fg-view-' + syarat_id).hide();
			$('.fg-upload-' + syarat_id).show();
			
		});

		$('.number').number(true, 0, ',', '.');
	</script>
{/block}
