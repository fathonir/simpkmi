{extends file='site_layout.tpl'}
{block name='content'}
	<h1 class="page-header">Edit Syarat Upload</h1>
	
	<div class="row">
		<div class="col-lg-12">
			
			<form class="form-horizontal" method="post" action="{current_url()}" id="editSyaratForm">
				<fieldset>
					
					<!-- Static -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="kegiatan">Kegiatan</label>  
						<div class="col-md-8">
							<p class="form-control-static">{$kegiatan->nama_program} {$kegiatan->tahun}</p>
						</div>
					</div>

					<!-- Select Box -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="tahapan_id">Tahapan</label>
						<div class="col-md-3">
							<select name="tahapan_id" class="form-control input-md">
								{html_options options=$tahapan_set selected=$data->tahapan_id}
							</select>
						</div>
					</div>
						
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="urutan">Urutan</label>  
						<div class="col-md-1">
							<input id="urutan" name="urutan" placeholder="" class="form-control input-md" type="text" value="{$data->urutan}">
						</div>
					</div>
					
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="syarat">Syarat</label>  
						<div class="col-md-3">
							<input id="syarat" name="syarat" placeholder="" class="form-control input-md" type="text" value="{$data->syarat}">
						</div>
					</div>
					
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="keterangan">Keterangan</label>  
						<div class="col-md-6">
							<input id="keterangan" name="keterangan" placeholder="" class="form-control input-md" type="text" value="{htmlentities($data->keterangan)}">
						</div>
					</div>
					
					<!-- Select Box -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="is_wajib">Status Wajib</label>
						<div class="col-md-3">
							<select name="is_wajib" class="form-control input-md">
							{html_options options=$wajib_set selected=$data->is_wajib}
							</select>
						</div>
					</div>
							
					<!-- Select Box -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="is_upload">File Upload</label>
						<div class="col-md-3">
							<select name="is_upload" class="form-control input-md">
							{html_options options=$upload_set selected=$data->is_upload}
							</select>
						</div>
					</div>
							
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="allowed_types">Jenis File</label>  
						<div class="col-md-6">
							<input id="allowed_types" name="allowed_types" placeholder="" class="form-control input-md" type="text" value="{$data->allowed_types}">
							<span class="help-block">Jenis file ekstensi, dipisahkan dengan koma. Contoh: pdf,doc</span>
						</div>
					</div>
							
					<!-- Button -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="singlebutton"></label>
						<div class="col-md-4">
							<a href="{site_url('kegiatan/syarat')}?kegiatan_id={$data->kegiatan_id}" class="btn btn-default">Kembali</a>
							<input type="submit" value="Simpan" class="btn btn-primary"/>
							<input type="hidden" name="kegiatan_id" value="{$data->kegiatan_id}" />
						</div>
					</div>
						
				</fieldset>
			</form>
			
		</div>
	</div>
{/block}
