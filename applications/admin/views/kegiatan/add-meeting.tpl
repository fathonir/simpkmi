{extends file='site_layout.tpl'}
{block name='content'}
	<h1 class="page-header">Tambah Sesi Meeting</h1>

	<div class="row">
		<div class="col-lg-12">
			
			<form class="form-horizontal" method="post" action="{current_url()}?kegiatan_id={$smarty.get.kegiatan_id}" id="addMeetingForm">
				<fieldset>
					
					<!-- Static -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="kegiatan">Kegiatan</label>  
						<div class="col-md-10">
							<p class="form-control-static">{$kegiatan->nama_program} {$kegiatan->tahun}</p>
						</div>
					</div>
						
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="topik">Topik</label>  
						<div class="col-md-10">
							<input id="topik" name="topik" placeholder="" class="form-control input-md" type="text" value="">
						</div>
					</div>
					
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="pemateri">Pemateri</label>  
						<div class="col-md-4">
							<input id="pemateri" name="pemateri" placeholder="" class="form-control input-md" type="text" value="">
						</div>
					</div>
					
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="waktu_mulai_time">Waktu Meeting</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="waktu_mulai_" all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" id="waktu_mulai_time" name="waktu_mulai_time" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="meeting_url">Meeting URL</label>  
						<div class="col-md-4">
							<input id="meeting_url" name="meeting_url" placeholder="" class="form-control input-md" type="text" value="">
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="meeting_password">Password</label>  
						<div class="col-md-3">
							<input id="meeting_password" name="meeting_password" placeholder="" class="form-control input-md" type="text" value="">
						</div>
					</div>
						
					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="awal_registrasi_time">Awal Registrasi</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="awal_registrasi_" all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" id="awal_registrasi_time" name="awal_registrasi_time" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="akhir_registrasi_time">Akhir Registrasi</label>
						<div class="col-md-5">
							{html_select_date field_order="DMY" prefix="akhir_registrasi_" all_extra='class="form-control input-md" style="display: inline-block; width: auto;"'}
							<input type="text" id="akhir_registrasi_time" name="akhir_registrasi_time" value="" placeholder="00:00:00" class="form-control input-md" style="display: inline-block; width: 85px" />
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="kode_kehadiran">Kode Kehadiran</label>
						<div class="col-md-2">
							<input id="kode_kehadiran" name="kode_kehadiran" placeholder="" class="form-control input-md" type="text" value="" maxlength="5">
						</div>
					</div>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-2 control-label" for="kapasitas">Kapasitas</label>  
						<div class="col-md-2">
							<input id="kapasitas" name="kapasitas" placeholder="" class="form-control input-md" type="number" value="0">
						</div>
					</div>
							
					<!-- Button -->
					<div class="form-group">
						<label class="col-md-2 control-label" for="singlebutton"></label>
						<div class="col-md-4">
							<a href="{site_url('kegiatan/meeting')}?{$smarty.server.QUERY_STRING}" class="btn btn-default">Kembali</a>
							<input type="submit" value="Simpan" class="btn btn-primary"/>
							<input type="hidden" name="kegiatan_id" value="{$smarty.get.kegiatan_id}" />
						</div>
					</div>
						
				</fieldset>
			</form>
			
		</div>
	</div>
{/block}
