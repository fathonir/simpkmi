{extends file='site_layout.tpl'}
{block name='content'}
	<h1 class="page-header">Tambah Syarat Upload</h1>
	
	<div class="row">
		<div class="col-lg-12">
			
			<form class="form-horizontal" method="post" action="{current_url()}?kegiatan_id={$smarty.get.kegiatan_id}" id="addSyaratForm">
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
								{html_options options=$tahapan_set}
							</select>
						</div>
					</div>
						
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="urutan">Urutan</label>  
						<div class="col-md-1">
							<input id="urutan" name="urutan" placeholder="" class="form-control input-md" type="text" value="">
						</div>
					</div>
					
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="syarat">Syarat</label>  
						<div class="col-md-3">
							<input id="syarat" name="syarat" placeholder="" class="form-control input-md" type="text" value="">
						</div>
					</div>
					
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="keterangan">Keterangan</label>  
						<div class="col-md-6">
							<input id="keterangan" name="keterangan" placeholder="" class="form-control input-md" type="text" value="">
						</div>
					</div>
					
					<!-- Select Box -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="is_wajib">Status Wajib</label>
						<div class="col-md-3">
							<select name="is_wajib" class="form-control input-md">
							{html_options options=$wajib_set}
							</select>
						</div>
					</div>
							
					<!-- Select Box -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="is_upload">File Upload</label>
						<div class="col-md-3">
							<select name="is_upload" class="form-control input-md">
							{html_options options=$upload_set}
							</select>
						</div>
					</div>
							
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="allowed_types">Jenis File</label>  
						<div class="col-md-6">
							<input id="allowed_types" name="allowed_types" placeholder="" class="form-control input-md" type="text" value="">
							<span class="help-block">Jenis file ekstensi, dipisahkan dengan koma. Contoh: pdf,doc</span>
						</div>
					</div>
							
					<!-- Button -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="singlebutton"></label>
						<div class="col-md-4">
							<a href="{site_url('kegiatan/syarat')}?{$smarty.server.QUERY_STRING}" class="btn btn-default">Kembali</a>
							<input type="submit" value="Simpan" class="btn btn-primary"/>
							<input type="hidden" name="kegiatan_id" value="{$smarty.get.kegiatan_id}" />
						</div>
					</div>
						
				</fieldset>
			</form>
			
		</div>
	</div>
{/block}
